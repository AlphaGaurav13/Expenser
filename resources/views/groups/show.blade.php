@extends('layouts.app')
@section('title', $group->name)

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('groups.index') }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Back to Groups
        </a>
        <h1 class="page-title">{{ $group->name }}</h1>
        @if($group->description)
            <p class="page-subtitle">{{ $group->description }}</p>
        @endif
    </div>
    <div class="page-actions">
        <a href="{{ route('expenses.create', $group) }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Expense
        </a>
    </div>
</div>

<!-- Tabs -->
<div class="tabs" id="groupTabs">
    <button class="tab active" data-tab="balances">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
        Balances
    </button>
    <button class="tab" data-tab="expenses">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
        Expenses ({{ $expenses->count() }})
    </button>
    <button class="tab" data-tab="settlements">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        Settlements ({{ $settlements->count() }})
    </button>
    <button class="tab" data-tab="members">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
        </svg>
        Members ({{ $group->members->count() }})
    </button>
</div>

<!-- Balances Tab -->
<div class="tab-content active" id="tab-balances">
    <div class="balance-grid">
        <!-- Balance Summary -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Member Balances</h2>
            </div>
            <div class="card-body">
                @forelse($balanceSummary as $item)
                    <div class="balance-item">
                        <div class="balance-user">
                            <div class="avatar-sm" style="background: hsl({{ ($item['user']->id * 97) % 360 }}, 55%, 35%);">
                                {{ $item['user']->initials }}
                            </div>
                            <span class="balance-name">
                                {{ $item['user']->name }}
                                @if($item['user']->id === auth()->id())
                                    <span class="badge badge-you">You</span>
                                @endif
                            </span>
                        </div>
                        <div class="balance-amount {{ $item['status'] }}">
                            @if($item['status'] === 'gets_back')
                                <span class="balance-label">gets back</span>
                                <span class="balance-value text-green">+₹{{ number_format(abs($item['balance']), 2) }}</span>
                            @elseif($item['status'] === 'owes')
                                <span class="balance-label">owes</span>
                                <span class="balance-value text-red">₹{{ number_format(abs($item['balance']), 2) }}</span>
                            @else
                                <span class="balance-value text-settled">Settled ✓</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>No balances to show yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Simplified Debts -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Simplified Debts</h2>
                <span class="card-header-badge">{{ count($simplifiedDebts) }} payment{{ count($simplifiedDebts) !== 1 ? 's' : '' }}</span>
            </div>
            <div class="card-body">
                @forelse($simplifiedDebts as $debt)
                    <div class="debt-item">
                        <div class="debt-flow">
                            <div class="debt-person">
                                <div class="avatar-sm" style="background: hsl({{ ($debt['from_id'] * 97) % 360 }}, 55%, 35%);">
                                    {{ $debt['from']->initials ?? '?' }}
                                </div>
                                <span>{{ $debt['from']->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="debt-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <polyline points="12 5 19 12 12 19"/>
                                </svg>
                                <span class="debt-amount">₹{{ number_format($debt['amount'], 2) }}</span>
                            </div>
                            <div class="debt-person">
                                <div class="avatar-sm" style="background: hsl({{ ($debt['to_id'] * 97) % 360 }}, 55%, 35%);">
                                    {{ $debt['to']->initials ?? '?' }}
                                </div>
                                <span>{{ $debt['to']->name ?? 'Unknown' }}</span>
                            </div>
                        </div>
                        <!-- Quick settle button -->
                        <form method="POST" action="{{ route('settlements.store', $group) }}" class="debt-settle-form">
                            @csrf
                            <input type="hidden" name="paid_by" value="{{ $debt['from_id'] }}">
                            <input type="hidden" name="paid_to" value="{{ $debt['to_id'] }}">
                            <input type="hidden" name="amount" value="{{ $debt['amount'] }}">
                            <input type="hidden" name="note" value="Settlement for group balance">
                            <button type="submit" class="btn btn-settle btn-sm">Settle</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <p>All settled up! 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Expenses Tab -->
<div class="tab-content" id="tab-expenses">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Expenses</h2>
            <a href="{{ route('expenses.create', $group) }}" class="btn btn-primary btn-sm">+ Add Expense</a>
        </div>
        <div class="card-body">
            @forelse($expenses as $expense)
                <div class="expense-item">
                    <div class="expense-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div class="expense-info">
                        <span class="expense-desc">{{ $expense->description }}</span>
                        <span class="expense-meta">
                            Paid by <strong>{{ $expense->payer->name }}</strong> · {{ $expense->date->format('M d, Y') }} · {{ ucfirst($expense->split_type) }} split
                        </span>
                        <div class="expense-shares-mini">
                            @foreach($expense->shares as $share)
                                <span class="share-chip" title="{{ $share->user->name }}: ₹{{ number_format($share->share_amount, 2) }}">
                                    {{ $share->user->initials }}: ₹{{ number_format($share->share_amount, 2) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="expense-right">
                        <span class="expense-amount">₹{{ number_format($expense->amount, 2) }}</span>
                        <form method="POST" action="{{ route('expenses.destroy', [$group, $expense]) }}" onsubmit="return confirm('Delete this expense?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-danger-icon" title="Delete expense">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <p>No expenses yet</p>
                    <a href="{{ route('expenses.create', $group) }}" class="btn btn-primary btn-sm">Add First Expense</a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Settlements Tab -->
<div class="tab-content" id="tab-settlements">
    <div class="settlement-grid">
        <!-- Record Settlement Form -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Record Settlement</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settlements.store', $group) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Who paid?</label>
                        <select name="paid_by" class="form-input" required>
                            <option value="">Select payer</option>
                            @foreach($group->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Paid to?</label>
                        <select name="paid_to" class="form-input" required>
                            <option value="">Select receiver</option>
                            @foreach($group->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" class="form-input" step="0.01" min="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Note <span class="form-optional">(optional)</span></label>
                        <input type="text" name="note" class="form-input" placeholder="e.g., UPI payment">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Record Settlement</button>
                </form>
            </div>
        </div>

        <!-- Settlement History -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Settlement History</h2>
            </div>
            <div class="card-body">
                @forelse($settlements as $settlement)
                    <div class="settlement-item">
                        <div class="settlement-flow">
                            <div class="avatar-sm" style="background: hsl({{ ($settlement->paid_by * 97) % 360 }}, 55%, 35%);">
                                {{ $settlement->payer->initials }}
                            </div>
                            <div class="settlement-info">
                                <span class="settlement-text">
                                    <strong>{{ $settlement->payer->name }}</strong> paid <strong>{{ $settlement->payee->name }}</strong>
                                </span>
                                <span class="settlement-meta">
                                    {{ $settlement->created_at->format('M d, Y h:i A') }}
                                    @if($settlement->note)
                                        · {{ $settlement->note }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="settlement-right">
                            <span class="settlement-amount text-green">₹{{ number_format($settlement->amount, 2) }}</span>
                            <form method="POST" action="{{ route('settlements.destroy', [$group, $settlement]) }}" onsubmit="return confirm('Delete this settlement?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger-icon" title="Delete settlement">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>No settlements recorded yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Members Tab -->
<div class="tab-content" id="tab-members">
    <div class="members-grid">
        <!-- Add Member -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add Member</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('groups.addMember', $group) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="friend@example.com" required>
                        <span class="form-hint">The user must be registered on Expenser</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        Add Member
                    </button>
                </form>
            </div>
        </div>

        <!-- Members List -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Current Members</h2>
                <span class="card-header-badge">{{ $group->members->count() }}</span>
            </div>
            <div class="card-body">
                @foreach($group->members as $member)
                    <div class="member-item">
                        <div class="member-left">
                            <div class="avatar-sm" style="background: hsl({{ ($member->id * 97) % 360 }}, 55%, 35%);">
                                {{ $member->initials }}
                            </div>
                            <div class="member-info">
                                <span class="member-name">
                                    {{ $member->name }}
                                    @if($member->id === $group->created_by)
                                        <span class="badge badge-creator">Creator</span>
                                    @endif
                                    @if($member->id === auth()->id())
                                        <span class="badge badge-you">You</span>
                                    @endif
                                </span>
                                <span class="member-email">{{ $member->email }}</span>
                            </div>
                        </div>
                        @if(auth()->id() === $group->created_by && $member->id !== $group->created_by)
                            <form method="POST" action="{{ route('groups.removeMember', [$group, $member]) }}" onsubmit="return confirm('Remove {{ $member->name }} from the group?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger-icon" title="Remove member">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(auth()->id() === $group->created_by)
        <div class="danger-zone">
            <div class="card card-danger">
                <div class="card-header">
                    <h2 class="card-title text-red">Danger Zone</h2>
                </div>
                <div class="card-body">
                    <p class="danger-text">Deleting this group will permanently remove all expenses, shares, and settlements.</p>
                    <form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Group</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });
</script>
@endsection
