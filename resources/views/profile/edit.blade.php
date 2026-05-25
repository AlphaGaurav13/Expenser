@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Profile</h1>
        <p class="page-subtitle">Manage your account settings</p>
    </div>
</div>

<div class="profile-grid">
    <!-- Update Profile Info -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Profile Information</h2>
        </div>
        <div class="card-body">
            <p class="form-hint" style="margin-bottom:1.25rem">Update your name and email address.</p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions" style="border:none;padding-top:0.75rem">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

                @if (session('status') === 'profile-updated')
                    <p class="form-success">Profile updated successfully.</p>
                @endif
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Update Password</h2>
        </div>
        <div class="card-body">
            <p class="form-hint" style="margin-bottom:1.25rem">Use a long, random password to stay secure.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="form-input" autocomplete="current-password">
                    @error('current_password', 'updatePassword')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" name="password" type="password" class="form-input" autocomplete="new-password">
                    @error('password', 'updatePassword')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password">
                </div>

                <div class="form-actions" style="border:none;padding-top:0.75rem">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>

                @if (session('status') === 'password-updated')
                    <p class="form-success">Password updated successfully.</p>
                @endif
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card card-danger">
        <div class="card-header">
            <h2 class="card-title text-red">Delete Account</h2>
        </div>
        <div class="card-body">
            <p class="danger-text">Once your account is deleted, all data will be permanently removed. Please enter your password to confirm.</p>

            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you absolutely sure? This cannot be undone.')">
                @csrf
                @method('delete')

                <div class="form-group">
                    <label for="delete_password" class="form-label">Password</label>
                    <input id="delete_password" name="password" type="password" class="form-input" placeholder="Enter your password to confirm">
                    @error('password', 'userDeletion')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-danger">Delete Account</button>
            </form>
        </div>
    </div>
</div>
@endsection
