@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('groups.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Group
    </a>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card--green">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                <polyline points="17 6 23 6 23 12"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">You're Owed</span>
            <span class="stat-value">₹{{ number_format($totalOwed, 2) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
                <polyline points="17 18 23 18 23 12"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">You Owe</span>
            <span class="stat-value">₹{{ number_format($totalOwing, 2) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--blue">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Groups</span>
            <span class="stat-value">{{ $groups->count() }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--purple">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Net Balance</span>
            <span class="stat-value {{ ($totalOwed - $totalOwing) >= 0 ? 'text-green' : 'text-red' }}">
                {{ ($totalOwed - $totalOwing) >= 0 ? '+' : '' }}₹{{ number_format($totalOwed - $totalOwing, 2) }}
            </span>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="dashboard-grid">
    <!-- Groups List -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Your Groups</h2>
            <a href="{{ route('groups.index') }}" class="card-action">View All →</a>
        </div>
        <div class="card-body">
            @forelse($groups as $group)
                <a href="{{ route('groups.show', $group) }}" class="group-item">
                    <div class="group-item-avatar" style="background: hsl({{ ($group->id * 73) % 360 }}, 60%, 35%);">
                        {{ strtoupper(substr($group->name, 0, 2)) }}
                    </div>
                    <div class="group-item-info">
                        <span class="group-item-name">{{ $group->name }}</span>
                        <span class="group-item-meta">{{ $group->members_count }} members · {{ $group->expenses_count }} expenses</span>
                    </div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" class="group-item-arrow">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            @empty
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    <p>No groups yet</p>
                    <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm">Create Your First Group</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Activity</h2>
        </div>
        <div class="card-body">
            @forelse($recentExpenses as $expense)
                <div class="activity-item">
                    <div class="activity-avatar" style="background: hsl({{ ($expense->payer->id * 97) % 360 }}, 55%, 35%);">
                        {{ $expense->payer->initials }}
                    </div>
                    <div class="activity-info">
                        <span class="activity-text">
                            <strong>{{ $expense->payer->name }}</strong> paid ₹{{ number_format($expense->amount, 2) }}
                        </span>
                        <span class="activity-meta">{{ $expense->description }} · {{ $expense->group->name }}</span>
                    </div>
                    <span class="activity-time">{{ $expense->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <p>No recent activity</p>
                    <span class="empty-state-hint">Add an expense in any group to get started</span>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
