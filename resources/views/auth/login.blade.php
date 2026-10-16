<x-guest-layout>
    <h2>Welcome back</h2>
    <p class="sub">Sign in to your account</p>

    {{-- Success / status message (e.g. "Account created!") --}}
    @if (session('status'))
        <div class="auth-status">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="auth-row">
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>

    <div class="auth-alt">
        Don't have an account?
        <a href="{{ route('register') }}">Create one</a>
    </div>
</x-guest-layout>