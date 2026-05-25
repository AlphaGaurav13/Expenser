@extends('layouts.app')
@section('title', 'Create Group')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('groups.index') }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Back to Groups
        </a>
        <h1 class="page-title">Create Group</h1>
        <p class="page-subtitle">Start a new expense sharing group</p>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('groups.store') }}">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Group Name</label>
            <input type="text" id="name" name="name" class="form-input" placeholder="e.g., Trip to Goa, Roommates" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description <span class="form-optional">(optional)</span></label>
            <textarea id="description" name="description" class="form-input form-textarea" placeholder="What's this group about?" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('groups.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create Group
            </button>
        </div>
    </form>
</div>
@endsection
