<x-guest-layout>
    <h2 class="auth-title">Reset Password</h2>
    <p class="auth-subtitle">Create a new secure password for your account</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email', $request->email) }}" required autocomplete="username">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="new-password">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required autocomplete="new-password">
            @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn-auth btn-primary" style="width: 100%;">Reset Password</button>
        </div>
    </form>
</x-guest-layout>
