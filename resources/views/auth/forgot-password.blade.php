<x-guest-layout>
    <h2>Reset your password</h2>
    <p class="sub">Enter your email and we'll send a reset link.</p>

    @if (session('status'))
        <div class="auth-status">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn">Send Reset Link</button>
    </form>

    <div class="auth-alt">
        Remembered it?
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>
</x-guest-layout>