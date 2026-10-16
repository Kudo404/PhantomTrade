@props(['user'])

@if ($user)
    @php
        $role = $user->role;
        $badgeIcon  = null;
        $badgeColor = null;
        $badgeTitle = null;

        if ($role === 'merchant') {
            $badgeIcon  = 'badge-check';
            $badgeColor = '#7c3aed';
            $badgeTitle = 'Certified Merchant';
        } elseif ($role === 'ghost_hunter') {
            $badgeIcon  = 'shield-check';
            $badgeColor = '#059669';
            $badgeTitle = 'Licensed Hunter';
        }
    @endphp

    <span class="inline-flex items-center gap-[5px] align-middle">
        <span>{{ $user->name }}</span>
        @if ($badgeIcon)
            <i data-lucide="{{ $badgeIcon }}"
               title="{{ $badgeTitle }}" style="--tw-inline-color:{{ $badgeColor }}" class="w-3.5 h-3.5 text-[var(--tw-inline-color)] shrink-0"
              ></i>
        @endif
    </span>
@endif