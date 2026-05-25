<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Settlement;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function store(Request $request, Group $group)
    {
        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        $request->validate([
            'paid_by' => 'required|exists:users,id',
            'paid_to' => 'required|exists:users,id|different:paid_by',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        Settlement::create([
            'group_id' => $group->id,
            'paid_by' => $request->paid_by,
            'paid_to' => $request->paid_to,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        return redirect()->route('groups.show', $group)->with('success', 'Settlement recorded!');
    }

    public function destroy(Request $request, Group $group, Settlement $settlement)
    {
        if (!$group->members->contains($request->user()->id)) {
            abort(403);
        }

        $settlement->delete();

        return redirect()->route('groups.show', $group)->with('success', 'Settlement deleted.');
    }
}
