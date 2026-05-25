<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Group;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create(Group $group, Request $request)
    {
        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        $group->load('members');
        return view('expenses.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'paid_by' => 'required|exists:users,id',
            'split_type' => 'required|in:equal,exact,percentage',
            'shares' => 'required|array|min:1',
            'shares.*' => 'nullable|numeric|min:0',
        ]);

        $expense = Expense::create([
            'group_id' => $group->id,
            'paid_by' => $request->paid_by,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
            'split_type' => $request->split_type,
        ]);

        $shares = $request->shares;
        $splitType = $request->split_type;
        $totalAmount = floatval($request->amount);

        // Get selected members (those with non-null share values or checked)
        $selectedMembers = array_filter($shares, function ($value) {
            return $value !== null && $value !== '';
        });

        if ($splitType === 'equal') {
            $memberCount = count($selectedMembers);
            if ($memberCount === 0) {
                $expense->delete();
                return back()->with('error', 'Please select at least one member to split with.');
            }
            $shareAmount = round($totalAmount / $memberCount, 2);
            $remainder = round($totalAmount - ($shareAmount * $memberCount), 2);

            $i = 0;
            foreach ($selectedMembers as $userId => $value) {
                $amount = $shareAmount;
                if ($i === 0) $amount += $remainder; // Give remainder to first person
                ExpenseShare::create([
                    'expense_id' => $expense->id,
                    'user_id' => $userId,
                    'share_amount' => $amount,
                ]);
                $i++;
            }
        } elseif ($splitType === 'exact') {
            foreach ($selectedMembers as $userId => $amount) {
                if ($amount > 0) {
                    ExpenseShare::create([
                        'expense_id' => $expense->id,
                        'user_id' => $userId,
                        'share_amount' => $amount,
                    ]);
                }
            }
        } elseif ($splitType === 'percentage') {
            foreach ($selectedMembers as $userId => $percentage) {
                if ($percentage > 0) {
                    $shareAmount = round($totalAmount * $percentage / 100, 2);
                    ExpenseShare::create([
                        'expense_id' => $expense->id,
                        'user_id' => $userId,
                        'share_amount' => $shareAmount,
                    ]);
                }
            }
        }

        return redirect()->route('groups.show', $group)->with('success', 'Expense added successfully!');
    }

    public function destroy(Request $request, Group $group, Expense $expense)
    {
        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        if ($expense->group_id !== $group->id) {
            abort(404);
        }

        $expense->delete();

        return redirect()->route('groups.show', $group)->with('success', 'Expense deleted.');
    }
}
