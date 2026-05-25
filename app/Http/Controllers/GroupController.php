<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Services\BalanceCalculator;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = $request->user()->groups()->withCount('members', 'expenses')->latest()->get();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $request->user()->id,
        ]);

        // Add creator as a member
        $group->members()->attach($request->user()->id);

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully!');
    }

    public function show(Group $group, Request $request)
    {
        // Ensure user is a member
        if (!$group->members->contains($request->user()->id)) {
            abort(403, 'You are not a member of this group.');
        }

        $group->load(['members', 'expenses.payer', 'expenses.shares', 'settlements.payer', 'settlements.payee']);

        $calculator = new BalanceCalculator();
        $balanceSummary = $calculator->getBalanceSummary($group);
        $simplifiedDebts = $calculator->getSimplifiedDebts($group);

        $expenses = $group->expenses()->with(['payer', 'shares.user'])->latest()->get();
        $settlements = $group->settlements()->with(['payer', 'payee'])->latest()->get();

        return view('groups.show', compact('group', 'balanceSummary', 'simplifiedDebts', 'expenses', 'settlements'));
    }

    public function addMember(Request $request, Group $group)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'No user found with that email. They need to register first.');
        }

        if ($group->members->contains($user->id)) {
            return back()->with('error', 'This user is already a member of the group.');
        }

        $group->members()->attach($user->id);

        return back()->with('success', $user->name . ' has been added to the group!');
    }

    public function removeMember(Request $request, Group $group, User $user)
    {
        if ($group->created_by !== $request->user()->id) {
            abort(403, 'Only the group creator can remove members.');
        }

        if ($user->id === $group->created_by) {
            return back()->with('error', 'Cannot remove the group creator.');
        }

        $group->members()->detach($user->id);

        return back()->with('success', $user->name . ' has been removed from the group.');
    }

    public function destroy(Request $request, Group $group)
    {
        if ($group->created_by !== $request->user()->id) {
            abort(403, 'Only the group creator can delete the group.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
