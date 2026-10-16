<x-guest-layout>
    <h2>Create an account</h2>
    <p class="sub">Join Phantom Trade to start trading gear</p>

    {{-- Info box --}}
    <div class="flex gap-2 pt-2.5 pr-3 pb-2.5 pl-3 bg-[var(--bg)] rounded-[var(--radius-sm)] text-[12.5px] text-[var(--text-secondary)] leading-[1.5] mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5 text-[var(--text-muted)]"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
        <span>New accounts start as <strong class="text-[var(--text)] font-semibold">Civilians</strong>. You can be promoted to a Hunter or Merchant later.</span>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your name">
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <div class="auth-alt">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>