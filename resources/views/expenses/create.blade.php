@extends('layouts.app')
@section('title', 'Add Expense')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('groups.show', $group) }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Back to {{ $group->name }}
        </a>
        <h1 class="page-title">Add Expense</h1>
        <p class="page-subtitle">Split a new expense with group members</p>
    </div>
</div>

<div class="form-card form-card--wide">
    <form method="POST" action="{{ route('expenses.store', $group) }}" id="expenseForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <input type="text" id="description" name="description" class="form-input" placeholder="e.g., Dinner at restaurant" value="{{ old('description') }}" required>
            </div>
            <div class="form-group">
                <label for="amount" class="form-label">Amount (₹)</label>
                <input type="number" id="amount" name="amount" class="form-input" step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="paid_by" class="form-label">Paid by</label>
                <select id="paid_by" name="paid_by" class="form-input" required>
                    @foreach($group->members as $member)
                        <option value="{{ $member->id }}" {{ $member->id === auth()->id() ? 'selected' : '' }}>
                            {{ $member->name }} {{ $member->id === auth()->id() ? '(You)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Split Type</label>
            <div class="split-type-selector">
                <label class="split-option active" data-type="equal">
                    <input type="radio" name="split_type" value="equal" checked>
                    <div class="split-option-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <line x1="5" y1="9" x2="19" y2="9"/>
                            <line x1="5" y1="15" x2="19" y2="15"/>
                        </svg>
                        <span>Equal</span>
                    </div>
                </label>
                <label class="split-option" data-type="exact">
                    <input type="radio" name="split_type" value="exact">
                    <div class="split-option-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        <span>Exact</span>
                    </div>
                </label>
                <label class="split-option" data-type="percentage">
                    <input type="radio" name="split_type" value="percentage">
                    <div class="split-option-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                            <line x1="19" y1="5" x2="5" y2="19"/>
                            <circle cx="6.5" cy="6.5" r="2.5"/>
                            <circle cx="17.5" cy="17.5" r="2.5"/>
                        </svg>
                        <span>Percentage</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Split Members -->
        <div class="form-group">
            <label class="form-label">Split among</label>
            <div class="split-members" id="splitMembers">
                @foreach($group->members as $member)
                    <div class="split-member" data-user-id="{{ $member->id }}">
                        <label class="split-member-check">
                            <input type="checkbox" class="member-checkbox" data-user-id="{{ $member->id }}" checked>
                            <div class="avatar-sm" style="background: hsl({{ ($member->id * 97) % 360 }}, 55%, 35%);">
                                {{ $member->initials }}
                            </div>
                            <span class="split-member-name">{{ $member->name }}</span>
                        </label>
                        <div class="split-member-input">
                            <!-- For equal: hidden, for exact: amount input, for percentage: % input -->
                            <input type="hidden" name="shares[{{ $member->id }}]" value="1" class="share-input">
                            <span class="share-display equal-display">Equal</span>
                            <input type="number" class="form-input form-input-sm exact-input" step="0.01" min="0" placeholder="₹0.00" style="display:none">
                            <input type="number" class="form-input form-input-sm percentage-input" step="0.01" min="0" max="100" placeholder="0%" style="display:none">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="split-summary" id="splitSummary">
                <span>Total: ₹<span id="splitTotal">0.00</span></span>
                <span id="splitStatus" class="text-green">✓ Balanced</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('groups.show', $group) }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Add Expense
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const form = document.getElementById('expenseForm');
    const amountInput = document.getElementById('amount');
    const splitMembers = document.getElementById('splitMembers');
    const splitTotal = document.getElementById('splitTotal');
    const splitStatus = document.getElementById('splitStatus');

    // Split type switching
    document.querySelectorAll('.split-option').forEach(option => {
        option.addEventListener('click', () => {
            document.querySelectorAll('.split-option').forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            updateSplitInputs();
        });
    });

    // Checkbox changes
    document.querySelectorAll('.member-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const member = cb.closest('.split-member');
            if (!cb.checked) {
                member.classList.add('disabled');
                member.querySelector('.share-input').value = '';
            } else {
                member.classList.remove('disabled');
                member.querySelector('.share-input').value = '1';
            }
            updateSplitInputs();
        });
    });

    amountInput.addEventListener('input', updateSplitInputs);

    function getSelectedType() {
        return document.querySelector('input[name="split_type"]:checked').value;
    }

    function updateSplitInputs() {
        const type = getSelectedType();
        const totalAmount = parseFloat(amountInput.value) || 0;

        document.querySelectorAll('.split-member').forEach(member => {
            const checkbox = member.querySelector('.member-checkbox');
            const shareInput = member.querySelector('.share-input');
            const equalDisplay = member.querySelector('.equal-display');
            const exactInput = member.querySelector('.exact-input');
            const percentageInput = member.querySelector('.percentage-input');

            equalDisplay.style.display = type === 'equal' ? '' : 'none';
            exactInput.style.display = type === 'exact' ? '' : 'none';
            percentageInput.style.display = type === 'percentage' ? '' : 'none';

            if (!checkbox.checked) {
                shareInput.value = '';
                return;
            }

            if (type === 'equal') {
                shareInput.value = '1'; // Just a flag, server handles equal split
            } else if (type === 'exact') {
                shareInput.value = exactInput.value || '';
            } else if (type === 'percentage') {
                shareInput.value = percentageInput.value || '';
            }
        });

        updateSummary();
    }

    // Exact and percentage input changes
    document.querySelectorAll('.exact-input, .percentage-input').forEach(input => {
        input.addEventListener('input', () => {
            const member = input.closest('.split-member');
            const shareInput = member.querySelector('.share-input');
            shareInput.value = input.value || '';
            updateSummary();
        });
    });

    function updateSummary() {
        const type = getSelectedType();
        const totalAmount = parseFloat(amountInput.value) || 0;
        let sum = 0;

        if (type === 'equal') {
            const checked = document.querySelectorAll('.member-checkbox:checked').length;
            sum = totalAmount;
            splitTotal.textContent = totalAmount.toFixed(2);
            splitStatus.textContent = checked > 0 ? '✓ Split ' + checked + ' ways' : '⚠ Select members';
            splitStatus.className = checked > 0 ? 'text-green' : 'text-red';
        } else if (type === 'exact') {
            document.querySelectorAll('.exact-input').forEach(input => {
                const member = input.closest('.split-member');
                if (member.querySelector('.member-checkbox').checked) {
                    sum += parseFloat(input.value) || 0;
                }
            });
            splitTotal.textContent = sum.toFixed(2);
            const diff = Math.abs(sum - totalAmount);
            if (diff < 0.01 && totalAmount > 0) {
                splitStatus.textContent = '✓ Balanced';
                splitStatus.className = 'text-green';
            } else {
                splitStatus.textContent = '⚠ ₹' + (totalAmount - sum).toFixed(2) + ' remaining';
                splitStatus.className = 'text-red';
            }
        } else if (type === 'percentage') {
            document.querySelectorAll('.percentage-input').forEach(input => {
                const member = input.closest('.split-member');
                if (member.querySelector('.member-checkbox').checked) {
                    sum += parseFloat(input.value) || 0;
                }
            });
            splitTotal.textContent = (totalAmount * sum / 100).toFixed(2);
            if (Math.abs(sum - 100) < 0.01) {
                splitStatus.textContent = '✓ 100%';
                splitStatus.className = 'text-green';
            } else {
                splitStatus.textContent = '⚠ ' + sum.toFixed(1) + '% (need 100%)';
                splitStatus.className = 'text-red';
            }
        }
    }

    updateSplitInputs();
</script>
@endsection
