@extends('layouts.app')
@section('title', 'Groups')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Groups</h1>
        <p class="page-subtitle">Manage your expense groups</p>
    </div>
    <a href="{{ route('groups.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Group
    </a>
</div>

<div class="groups-grid">
    @forelse($groups as $group)
        <a href="{{ route('groups.show', $group) }}" class="group-card">
            <div class="group-card-header">
                <div class="group-card-avatar" style="background: linear-gradient(135deg, hsl({{ ($group->id * 73) % 360 }}, 60%, 35%), hsl({{ ($group->id * 73 + 40) % 360 }}, 60%, 45%));">
                    {{ strtoupper(substr($group->name, 0, 2)) }}
                </div>
                <div class="group-card-badges">
                    <span class="badge">{{ $group->members_count }} members</span>
                </div>
            </div>
            <h3 class="group-card-name">{{ $group->name }}</h3>
            @if($group->description)
                <p class="group-card-desc">{{ Str::limit($group->description, 80) }}</p>
            @endif
            <div class="group-card-footer">
                <span class="group-card-meta">{{ $group->expenses_count }} expenses</span>
                <span class="group-card-meta">{{ $group->created_at->diffForHumans() }}</span>
            </div>
        </a>
    @empty
        <div class="empty-state-full">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
            <h3>No groups yet</h3>
            <p>Create your first group to start tracking shared expenses</p>
            <a href="{{ route('groups.create') }}" class="btn btn-primary">Create Group</a>
        </div>
    @endforelse
</div>
@endsection
