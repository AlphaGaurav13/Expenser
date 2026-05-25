<x-guest-layout>
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Sign in to your Expenser account</p>

    @if(session('status'))
        <div style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#10b981;font-size:.85rem">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
            <label class="form-check">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 0.8rem;">Forgot password?</a>
            @endif
        </div>

        <div class="auth-actions">
            <a href="{{ route('register') }}" class="auth-link">Don't have an account?</a>
            <button type="submit" class="btn-auth btn-primary">Sign In</button>
        </div>
    </form>
</x-guest-layout>
