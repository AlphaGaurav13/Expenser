<?php

namespace App\Services;

use App\Models\Group;

class BalanceCalculator
{
    /**
     * Calculate net balances for each member in a group.
     * Positive = owed money, Negative = owes money
     */
    public function getNetBalances(Group $group): array
    {
        $balances = [];
        $members = $group->members;

        // Initialize all members with 0 balance
        foreach ($members as $member) {
            $balances[$member->id] = 0;
        }

        // Process expenses
        $expenses = $group->expenses()->with('shares')->get();
        foreach ($expenses as $expense) {
            // The payer gets credited for the full amount
            if (isset($balances[$expense->paid_by])) {
                $balances[$expense->paid_by] += floatval($expense->amount);
            }

            // Each person who shares the expense gets debited for their share
            foreach ($expense->shares as $share) {
                if (isset($balances[$share->user_id])) {
                    $balances[$share->user_id] -= floatval($share->share_amount);
                }
            }
        }

        // Process settlements
        $settlements = $group->settlements;
        foreach ($settlements as $settlement) {
            $amount = floatval($settlement->amount);
            // Payer's balance increases (they paid their debt, moving closer to 0/positive)
            if (isset($balances[$settlement->paid_by])) {
                $balances[$settlement->paid_by] += $amount;
            }
            // Payee's balance decreases (they received their money, moving closer to 0/negative)
            if (isset($balances[$settlement->paid_to])) {
                $balances[$settlement->paid_to] -= $amount;
            }
        }

        return $balances;
    }

    /**
     * Simplify debts using a greedy min-cash-flow algorithm.
     * Returns an array of [from_id, to_id, amount] transactions.
     */
    public function getSimplifiedDebts(Group $group): array
    {
        $balances = $this->getNetBalances($group);
        $members = $group->members->keyBy('id');
        $debts = [];

        // Separate into creditors (positive balance) and debtors (negative balance)
        $creditors = [];
        $debtors = [];

        foreach ($balances as $userId => $balance) {
            $rounded = round($balance, 2);
            if ($rounded > 0.01) {
                $creditors[] = ['id' => $userId, 'amount' => $rounded];
            } elseif ($rounded < -0.01) {
                $debtors[] = ['id' => $userId, 'amount' => abs($rounded)];
            }
        }

        // Sort both by amount descending for better simplification
        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);

        $i = 0;
        $j = 0;

        while ($i < count($creditors) && $j < count($debtors)) {
            $transferAmount = min($creditors[$i]['amount'], $debtors[$j]['amount']);

            if ($transferAmount > 0.01) {
                $debts[] = [
                    'from' => $members[$debtors[$j]['id']] ?? null,
                    'to' => $members[$creditors[$i]['id']] ?? null,
                    'from_id' => $debtors[$j]['id'],
                    'to_id' => $creditors[$i]['id'],
                    'amount' => round($transferAmount, 2),
                ];
            }

            $creditors[$i]['amount'] -= $transferAmount;
            $debtors[$j]['amount'] -= $transferAmount;

            if ($creditors[$i]['amount'] < 0.01) $i++;
            if ($debtors[$j]['amount'] < 0.01) $j++;
        }

        return $debts;
    }

    /**
     * Get a summary of balances for display.
     */
    public function getBalanceSummary(Group $group): array
    {
        $balances = $this->getNetBalances($group);
        $members = $group->members->keyBy('id');
        $summary = [];

        foreach ($balances as $userId => $balance) {
            $member = $members[$userId] ?? null;
            if ($member) {
                $summary[] = [
                    'user' => $member,
                    'balance' => round($balance, 2),
                    'status' => $balance > 0.01 ? 'gets_back' : ($balance < -0.01 ? 'owes' : 'settled'),
                ];
            }
        }

        return $summary;
    }
}
