<x-guest-layout>
    <h2 class="auth-title">Forgot Password</h2>
    <p class="auth-subtitle">No problem. Enter your email and we'll send you a password reset link.</p>

    @if(session('status'))
        <div style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#10b981;font-size:.85rem">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-actions">
            <a href="{{ route('login') }}" class="auth-link">Back to Sign In</a>
            <button type="submit" class="btn-auth btn-primary">Email Reset Link</button>
        </div>
    </form>
</x-guest-layout>
