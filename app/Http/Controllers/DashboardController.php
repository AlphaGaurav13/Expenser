<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\BalanceCalculator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $groups = $user->groups()->withCount('members', 'expenses')->latest()->get();

        $calculator = new BalanceCalculator();
        $totalOwed = 0;
        $totalOwing = 0;

        foreach ($groups as $group) {
            $balances = $calculator->getNetBalances($group);
            $userBalance = $balances[$user->id] ?? 0;
            if ($userBalance > 0) {
                $totalOwed += $userBalance;
            } else {
                $totalOwing += abs($userBalance);
            }
        }

        // Recent activity: last 10 expenses across all groups
        $recentExpenses = \App\Models\Expense::whereIn('group_id', $groups->pluck('id'))
            ->with(['payer', 'group'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('groups', 'totalOwed', 'totalOwing', 'recentExpenses'));
    }
}
