<x-guest-layout>
    <h2 class="auth-title">Create your account</h2>
    <p class="auth-subtitle">Start splitting expenses with friends</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="username">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="new-password">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required autocomplete="new-password">
        </div>

        <div class="auth-actions">
            <a href="{{ route('login') }}" class="auth-link">Already have an account?</a>
            <button type="submit" class="btn-auth btn-primary">Create Account</button>
        </div>
    </form>
</x-guest-layout>
