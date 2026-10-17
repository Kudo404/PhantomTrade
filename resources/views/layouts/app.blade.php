
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">

    <title>@yield('title', 'Phantom Trade')</title>

    <script>
        (function () {
            try {
                const saved = localStorage.getItem('phantomtrade-theme');
                document.documentElement.dataset.theme =
                    saved === 'dark' ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --surface-hover: #eef2ff;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;
            --text: #172033;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --accent: #4f46e5;
            --accent-hover: #4338ca;
            --accent-soft: #eef2ff;
            --cyan: #0891b2;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --warning: #b45309;
            --danger: #be123c;
            --danger-bg: #fff1f2;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, .04);
            --shadow-md: 0 5px 18px rgba(15, 23, 42, .06);
            --shadow-lg: 0 18px 45px rgba(15, 23, 42, .13);
            --sidebar-width: 252px;
            --topbar-height: 68px;
            --radius: 12px;
            --radius-sm: 8px;
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --bg: #0d111b;
            --surface: #141b29;
            --surface-soft: #192234;
            --surface-hover: #222b43;
            --border: #2a354a;
            --border-strong: #3b4962;
            --text: #edf2ff;
            --text-secondary: #b0bdd1;
            --text-muted: #8190a8;
            --accent: #a5b4fc;
            --accent-hover: #c7d2fe;
            --accent-soft: #252748;
            --cyan: #67e8f9;
            --success: #86efac;
            --success-bg: #142c29;
            --warning: #fcd34d;
            --danger: #fda4af;
            --danger-bg: #351b2a;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, .15);
            --shadow-md: 0 8px 22px rgba(0, 0, 0, .18);
            --shadow-lg: 0 20px 55px rgba(0, 0, 0, .35);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
            font-size: 14px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            transition: background-color .2s, color .2s;
        }

        body, button, input, select, textarea {
            font-family: inherit;
        }

        button, a, input, select, textarea {
            -webkit-tap-highlight-color: transparent;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            color: inherit;
        }

        button:focus-visible, a:focus-visible,
        input:focus-visible, select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid color-mix(in srgb, var(--accent) 40%, transparent);
            outline-offset: 2px;
        }

        .sidebar {
            position: sticky;
            top: 0;
            z-index: 1200;
            display: flex;
            flex: 0 0 var(--sidebar-width);
            flex-direction: column;
            width: var(--sidebar-width);
            height: 100vh;
            overflow-y: auto;
            background: var(--surface);
            border-right: 1px solid var(--border);
            transition: background-color .2s, border-color .2s, transform .25s;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 23px 20px 27px;
        }

        .sidebar-brand-mark {
            display: grid;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            place-items: center;
            border-radius: 12px;
            background: linear-gradient(145deg, #4f46e5, #0891b2);
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            box-shadow: 0 5px 14px rgba(79, 70, 229, .2);
        }

        .sidebar-brand-name {
            color: var(--text);
            font-size: 15px;
            font-weight: 750;
            letter-spacing: -.025em;
            line-height: 1.3;
        }

        .sidebar-brand-sub {
            margin-top: 3px;
            color: var(--text-muted);
            font-size: 10px;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .sidebar-section {
            margin-bottom: 23px;
            padding: 0 13px;
        }

        .sidebar-section-title {
            padding: 0 10px 9px;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 750;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sidebar-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 41px;
            margin: 3px 0;
            padding: 9px 11px;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 550;
            transition: background .16s, color .16s, border-color .16s;
        }

        .sidebar-link:hover {
            background: var(--surface-soft);
            color: var(--text);
        }

        .sidebar-link.active {
            border-color: color-mix(in srgb, var(--accent) 15%, transparent);
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
        }

        .sidebar-link.active::before {
            position: absolute;
            top: 9px;
            bottom: 9px;
            left: -14px;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: var(--accent);
            content: "";
        }

        .sidebar-link i {
            width: 17px;
            height: 17px;
            flex: 0 0 17px;
            stroke-width: 1.8;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 8px;
            border-radius: var(--radius-sm);
        }

        .sidebar-avatar, .profile-trigger-avatar, .dropdown-avatar {
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 50%;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 750;
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            font-size: 12px;
        }

        .sidebar-user-info {
            min-width: 0;
            flex: 1;
        }

        .sidebar-user-name {
            overflow: hidden;
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-user-role {
            color: var(--text-muted);
            font-size: 11px;
        }

        .main {
            display: flex;
            min-width: 0;
            min-height: 100vh;
            flex: 1;
            flex-direction: column;
            background: var(--bg);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            height: var(--topbar-height);
            flex: 0 0 var(--topbar-height);
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 28px;
            border-bottom: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface) 94%, transparent);
            backdrop-filter: blur(14px);
        }

        .topbar-left, .topbar-right {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 9px;
        }

        .topbar-right {
            margin-left: auto;
        }

        .hamburger-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            cursor: pointer;
        }

        .icon-btn {
            position: relative;
            display: inline-flex;
            width: 39px;
            height: 39px;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 10px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: background .16s, color .16s, border-color .16s;
        }

        .icon-btn:hover {
            border-color: var(--border);
            background: var(--surface-soft);
            color: var(--text);
        }

        .icon-btn i {
            width: 18px;
            height: 18px;
        }

        .dot {
            position: absolute;
            top: 2px;
            right: 1px;
            display: none;
            min-width: 17px;
            height: 17px;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid var(--surface);
            border-radius: 10px;
            background: #e11d48;
            color: white;
            font-size: 9px;
            font-weight: 800;
            line-height: 1;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 5px 10px 5px 5px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            font-size: 12px;
            font-weight: 650;
            transition: border-color .16s, background .16s;
        }

        .profile-trigger:hover {
            border-color: var(--border-strong);
            background: var(--surface-soft);
        }

        .profile-trigger-avatar {
            width: 29px;
            height: 29px;
            background: linear-gradient(145deg, #4f46e5, #0891b2);
            color: white;
            font-size: 11px;
        }

        .profile-trigger i {
            width: 14px;
            height: 14px;
            color: var(--text-muted);
        }

        .dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1500;
            display: none;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            box-shadow: var(--shadow-lg);
        }

        .dropdown.open {
            display: block;
            animation: dropdown-in .14s ease-out;
        }

        @keyframes dropdown-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #profileMenu {
            width: min(290px, calc(100vw - 24px));
        }

        #bellMenu {
            width: min(370px, calc(100vw - 24px));
            max-height: min(500px, calc(100vh - 100px));
        }

        .dropdown-header {
            padding: 17px;
            border-bottom: 1px solid var(--border);
        }

        .dropdown-user-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dropdown-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(145deg, #4f46e5, #0891b2);
            color: white;
            font-size: 14px;
        }

        .dropdown-user-name {
            overflow: hidden;
            color: var(--text);
            font-size: 13px;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dropdown-user-email {
            overflow: hidden;
            color: var(--text-muted);
            font-size: 11px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dropdown-user-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 13px;
            font-size: 11px;
        }

        .dropdown-role {
            padding: 4px 8px;
            border-radius: 6px;
            background: var(--surface-soft);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .dropdown-ecto {
            color: var(--accent);
            font-weight: 750;
        }

        .dropdown-item {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 12.5px;
            text-align: left;
            transition: background .15s, color .15s;
        }

        .dropdown-item:hover {
            background: var(--surface-soft);
            color: var(--text);
        }

        .dropdown-item i {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
        }

        .dropdown-item.danger {
            color: var(--danger);
        }

        .dropdown-item.danger:hover {
            background: var(--danger-bg);
        }

        .dropdown-divider {
            height: 1px;
            margin: 5px 0;
            background: var(--border);
        }

        .bell-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }

        .bell-title {
            color: var(--text);
            font-size: 13px;
            font-weight: 750;
        }

        .bell-action {
            padding: 5px 7px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: var(--accent);
            cursor: pointer;
            font-size: 11px;
            font-weight: 650;
        }

        .bell-action:hover {
            background: var(--accent-soft);
        }

        .bell-list {
            max-height: 350px;
            overflow-y: auto;
        }

        .bell-item {
            display: flex;
            gap: 11px;
            padding: 13px 15px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            transition: background .15s;
        }

        .bell-item:hover {
            background: var(--surface-soft);
        }

        .bell-item.unread {
            background: var(--accent-soft);
        }

        .bell-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            color: var(--accent);
        }

        .bell-icon i {
            width: 16px;
            height: 16px;
        }

        .bell-content {
            min-width: 0;
            flex: 1;
        }

        .bell-item-title {
            margin-bottom: 3px;
            color: var(--text);
            font-size: 12px;
            font-weight: 750;
        }

        .bell-item-body {
            overflow-wrap: anywhere;
            color: var(--text-secondary);
            font-size: 11px;
            line-height: 1.5;
        }

        .bell-item-time {
            margin-top: 5px;
            color: var(--text-muted);
            font-size: 10px;
        }

        .bell-empty {
            padding: 30px 18px;
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
        }

        .bell-footer {
            padding: 9px;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .content {
            width: 100%;
            max-width: 1500px;
            flex: 1;
            margin: 0 auto;
            padding: 30px clamp(18px, 3vw, 38px);
        }

        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            background: var(--accent);
            color: white;
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 700;
            transition: background .16s, transform .16s;
        }

        .btn:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn i {
            width: 16px;
            height: 16px;
        }

        .btn-secondary {
            border-color: var(--border);
            background: var(--surface);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--surface-soft);
        }

        .auto-dismiss-alert {
            position: relative;
            animation: alert-enter .2s ease-out;
            transition: opacity .25s ease, transform .25s ease, margin .25s ease, padding .25s ease;
        }

        .auto-dismiss-alert.is-dismissing {
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
        }

        @keyframes alert-enter {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-banner, .error-banner {
            margin-bottom: 20px;
            padding: 12px 15px;
            border: 1px solid;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
        }

        .success-banner {
            border-color: #bbf7d0;
            background: var(--success-bg);
            color: var(--success);
        }

        .error-banner {
            border-color: #fecdd3;
            background: var(--danger-bg);
            color: var(--danger);
        }

        html[data-theme="dark"] .success-banner {
            border-color: #285848;
        }

        html[data-theme="dark"] .error-banner {
            border-color: #673043;
        }

        .type-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--surface-soft);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .mobile-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1100;
            display: none;
            border: 0;
            background: rgba(15, 23, 42, .48);
            backdrop-filter: blur(2px);
        }

        .mobile-backdrop.open {
            display: block;
        }

        @media (max-width: 900px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: min(var(--sidebar-width), 86vw);
                height: 100dvh;
                transform: translateX(-105%);
                box-shadow: var(--shadow-lg);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .hamburger-btn {
                display: inline-flex;
            }

            .topbar {
                height: 60px;
                flex-basis: 60px;
                padding: 0 16px;
            }

            .content {
                padding: 22px 16px;
            }
        }

        @media (max-width: 520px) {
            .topbar {
                gap: 8px;
                padding: 0 10px;
            }

            .topbar-right {
                gap: 3px;
            }

            .profile-trigger {
                gap: 5px;
                padding-right: 7px;
            }

            .profile-trigger > span {
                max-width: 75px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .content {
                padding: 18px 12px;
            }

            #bellMenu {
                position: fixed;
                top: 66px;
                right: 8px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
    <button
        type="button"
        class="mobile-backdrop"
        id="mobileBackdrop"
        aria-label="Close navigation menu"
        onclick="closeSidebar()"
    ></button>

    <aside class="sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">P</div>
            <div>
                <div class="sidebar-brand-name">Phantom Trade</div>
                <div class="sidebar-brand-sub">Paranormal Operations Network</div>
            </div>
        </div>

        @auth
            @php
                $sidebarUnread = auth()->user()->unreadMessagesCount();
            @endphp

            @if (auth()->user()->isAdmin())
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Management</div>

                    <a href="/admin" class="sidebar-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i><span>Overview</span>
                    </a>
                    <a href="/admin/applications" class="sidebar-link {{ request()->is('admin/applications*') ? 'active' : '' }}">
                        <i data-lucide="inbox"></i><span>Applications</span>
                    </a>
                    <a href="/admin/users" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i data-lucide="users"></i><span>Users</span>
                    </a>
                    <a href="/gears" class="sidebar-link {{ request()->is('gears*') ? 'active' : '' }}">
                        <i data-lucide="cog"></i><span>Gear Catalog</span>
                    </a>
                    <a href="/marketplace" class="sidebar-link {{ request()->is('marketplace*') ? 'active' : '' }}">
                        <i data-lucide="store"></i><span>Marketplace</span>
                    </a>
                    <a href="/messages" class="sidebar-link {{ request()->is('messages*') ? 'active' : '' }}">
                        <i data-lucide="message-circle"></i><span>Messages</span>
                        @if ($sidebarUnread > 0)
                            <span class="ml-auto rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                {{ $sidebarUnread > 99 ? '99+' : $sidebarUnread }}
                            </span>
                        @endif
                    </a>
                    <a href="/admin/settings" class="sidebar-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <i data-lucide="settings"></i><span>Settings</span>
                    </a>
                </div>
            @else
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Main</div>

                    <a href="/" class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
                    </a>
                    <a href="/gears" class="sidebar-link {{ request()->is('gears*') ? 'active' : '' }}">
                        <i data-lucide="cog"></i><span>Gear Shop</span>
                    </a>
                    <a href="/marketplace" class="sidebar-link {{ request()->is('marketplace*') ? 'active' : '' }}">
                        <i data-lucide="store"></i><span>Marketplace</span>
                    </a>
                    <a href="/messages" class="sidebar-link {{ request()->is('messages*') ? 'active' : '' }}">
                        <i data-lucide="message-circle"></i><span>Messages</span>
                        @if ($sidebarUnread > 0)
                            <span class="ml-auto rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                {{ $sidebarUnread > 99 ? '99+' : $sidebarUnread }}
                            </span>
                        @endif
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Field Operations</div>

                    @if (auth()->user()->isGhostHunter())
                        <a href="/loadout" class="sidebar-link {{ request()->is('loadout*') ? 'active' : '' }}">
                            <i data-lucide="sliders-horizontal"></i><span>Loadout</span>
                        </a>
                        <a href="/drain" class="sidebar-link {{ request()->is('drain*') ? 'active' : '' }}">
                            <i data-lucide="zap"></i><span>Drain Chamber</span>
                        </a>
                    @endif
                    <a href="/map" class="sidebar-link {{ request()->is('map*') ? 'active' : '' }}">
                        <i data-lucide="map-pin"></i><span>Haunt Map</span>
                    </a>
                </div>
            @endif
        @endauth

        @guest
            <div class="sidebar-section">
                <div class="sidebar-section-title">Account</div>
                <a href="{{ route('login') }}" class="sidebar-link {{ request()->is('login') ? 'active' : '' }}">
                    <i data-lucide="log-in"></i><span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="sidebar-link {{ request()->is('register') ? 'active' : '' }}">
                    <i data-lucide="user-plus"></i><span>Register</span>
                </a>
            </div>
        @endguest

        @auth
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                        <div class="sidebar-user-role">{{ auth()->user()->roleLabel() }}</div>
                    </div>
                </div>
            </div>
        @endauth
    </aside>

    <div class="main">
        @auth
            <header class="topbar">
                <div class="topbar-left">
                    <button
                        type="button"
                        class="hamburger-btn"
                        id="sidebarToggle"
                        aria-label="Open navigation menu"
                        aria-expanded="false"
                        onclick="toggleSidebar()"
                    >
                        <i data-lucide="menu"></i>
                    </button>
                </div>

                <div class="topbar-right">
                    <button
                        type="button"
                        class="icon-btn"
                        id="themeToggle"
                        aria-label="Switch to dark mode"
                        title="Switch theme"
                        onclick="toggleTheme()"
                    >
                        <i data-lucide="moon" id="themeToggleIcon"></i>
                    </button>

                    <div class="relative">
                        <button
                            type="button"
                            class="icon-btn"
                            id="bellBtn"
                            aria-label="Notifications"
                            aria-expanded="false"
                            onclick="toggleDropdown('bellMenu', 'bellBtn')"
                        >
                            <i data-lucide="bell"></i>
                            <span class="dot" id="bellDot">0</span>
                        </button>

                        <div class="dropdown" id="bellMenu">
                            <div class="bell-header">
                                <div class="bell-title">Notifications</div>
                                <button type="button" class="bell-action" onclick="markAllRead()">Mark all read</button>
                            </div>
                            <div class="bell-list" id="bellList">
                                <div class="bell-empty">Loading notifications…</div>
                            </div>
                            <div class="bell-footer">
                                <button type="button" class="bell-action" onclick="clearAll()">Clear all</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button
                            type="button"
                            class="profile-trigger"
                            id="profileBtn"
                            aria-expanded="false"
                            onclick="toggleDropdown('profileMenu', 'profileBtn')"
                        >
                            <div class="profile-trigger-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <i data-lucide="chevron-down"></i>
                        </button>

                        <div class="dropdown" id="profileMenu">
                            <div class="dropdown-header">
                                <div class="dropdown-user-row">
                                    <div class="dropdown-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="dropdown-user-name"><x-user-name :user="auth()->user()" /></div>
                                        <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                                <div class="dropdown-user-meta">
                                    <span class="dropdown-role">{{ auth()->user()->roleLabel() }}</span>
                                    <span class="dropdown-ecto">₱ {{ auth()->user()->ecto }}</span>
                                </div>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <a href="/admin" class="dropdown-item"><i data-lucide="shield"></i><span>Admin Panel</span></a>
                                <a href="/messages" class="dropdown-item"><i data-lucide="message-circle"></i><span>Messages</span></a>
                                <a href="/profile" class="dropdown-item"><i data-lucide="user-round"></i><span>My Profile</span></a>
                            @else
                                <a href="/profile" class="dropdown-item"><i data-lucide="user-round"></i><span>My Profile</span></a>
                                <a href="/inventory" class="dropdown-item"><i data-lucide="package"></i><span>My Inventory</span></a>
                                <a href="/messages" class="dropdown-item"><i data-lucide="message-circle"></i><span>Messages</span></a>
                                <a href="/marketplace/my-proposals" class="dropdown-item"><i data-lucide="arrow-left-right"></i><span>My Sent Proposals</span></a>

                                @if (auth()->user()->isCivilian())
                                    <a href="/applications/status" class="dropdown-item"><i data-lucide="file-text"></i><span>Application Status</span></a>
                                @endif

                                <a href="/achievements" class="dropdown-item"><i data-lucide="trophy"></i><span>Achievements</span></a>
                                <a href="/marketplace/wanted/mine" class="dropdown-item"><i data-lucide="tag"></i><span>My Wanted Ads</span></a>

                                @if (auth()->user()->isGhostHunter())
                                    <a href="/loadout" class="dropdown-item"><i data-lucide="backpack"></i><span>My Loadout</span></a>
                                    @if (auth()->user()->license_number)
                                        <a href="/profile/license" class="dropdown-item"><i data-lucide="shield-check"></i><span>Hunter License</span></a>
                                    @endif
                                @endif

                                @if (auth()->user()->isMerchant())
                                    <div class="dropdown-divider"></div>
                                    <a href="/marketplace/proposals" class="dropdown-item"><i data-lucide="inbox"></i><span>Incoming Proposals</span></a>
                                    <a href="/marketplace/create" class="dropdown-item"><i data-lucide="plus-circle"></i><span>Create Listing</span></a>
                                    <a href="/marketplace/mine" class="dropdown-item"><i data-lucide="list"></i><span>My Listings</span></a>
                                @endif

                                <div class="dropdown-divider"></div>
                                <a href="/purchases" class="dropdown-item"><i data-lucide="receipt"></i><span>Recent Purchases</span></a>
                            @endif

                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <i data-lucide="log-out"></i><span>Log out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
        @else
            <header class="topbar">
                <div class="topbar-left">
                    <div class="sidebar-brand-mark">P</div>
                    <strong>Phantom Trade</strong>
                </div>
                <div class="topbar-right">
                    <button type="button" class="icon-btn" id="themeToggle" aria-label="Switch to dark mode" title="Switch theme" onclick="toggleTheme()">
                        <i data-lucide="moon" id="themeToggleIcon"></i>
                    </button>
                </div>
            </header>
        @endauth

        <main class="content">
            @if (session('success'))
                <div class="success-banner auto-dismiss-alert" role="status">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-banner auto-dismiss-alert" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <script>
        function renderIcons() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        function applyTheme(theme, persist = true) {
            const selected = theme === 'dark' ? 'dark' : 'light';
            document.documentElement.dataset.theme = selected;

            const toggle = document.getElementById('themeToggle');
            const icon = document.getElementById('themeToggleIcon');

            if (toggle) {
                const nextLabel = selected === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
                toggle.setAttribute('aria-label', nextLabel);
                toggle.setAttribute('title', nextLabel);
            }

            if (icon) {
                icon.setAttribute('data-lucide', selected === 'dark' ? 'sun' : 'moon');
                renderIcons();
            }

            if (persist) {
                try {
                    localStorage.setItem('phantomtrade-theme', selected);
                } catch (error) {
                    // Theme still applies for this page if storage is unavailable.
                }
            }
        }

        function toggleTheme() {
            const current = document.documentElement.dataset.theme || 'light';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            const button = document.getElementById('sidebarToggle');
            const open = !sidebar.classList.contains('open');

            sidebar.classList.toggle('open', open);
            backdrop.classList.toggle('open', open);

            if (button) {
                button.setAttribute('aria-expanded', String(open));
                button.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
            }
        }

        function closeSidebar() {
            document.getElementById('appSidebar')?.classList.remove('open');
            document.getElementById('mobileBackdrop')?.classList.remove('open');

            const button = document.getElementById('sidebarToggle');
            if (button) {
                button.setAttribute('aria-expanded', 'false');
                button.setAttribute('aria-label', 'Open navigation menu');
            }
        }

        function toggleDropdown(menuId, btnId) {
            const menu = document.getElementById(menuId);
            const button = document.getElementById(btnId);

            if (!menu) return;

            const wasOpen = menu.classList.contains('open');

            document.querySelectorAll('.dropdown').forEach(item => item.classList.remove('open'));
            document.querySelectorAll('[aria-expanded="true"]').forEach(item => item.setAttribute('aria-expanded', 'false'));

            if (!wasOpen) {
                menu.classList.add('open');
                if (button) button.setAttribute('aria-expanded', 'true');

                if (menuId === 'bellMenu') loadNotifications();
            }
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#profileBtn, #bellBtn, #profileMenu, #bellMenu')) {
                document.querySelectorAll('.dropdown').forEach(menu => menu.classList.remove('open'));
                document.querySelectorAll('#profileBtn, #bellBtn').forEach(button => button.setAttribute('aria-expanded', 'false'));
            }

            if (event.target.closest('.sidebar-link') && window.innerWidth <= 900) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.dropdown').forEach(menu => menu.classList.remove('open'));
                closeSidebar();
            }
        });

        const NOTIF_POLL_MS = 60000;

        // Convert existing notification icon values to consistent Lucide icons.
        const notificationIcons = {
            '🔔': 'bell',
            '👤': 'user-round',
            '👥': 'users',
            '🛒': 'shopping-cart',
            '💰': 'wallet',
            '₱': 'gem',
            '✅': 'circle-check',
            '❌': 'circle-x',
            '⏳': 'clock',
            '🔄': 'arrow-left-right',
            '📨': 'send',
            '📬': 'inbox',
            '📥': 'inbox',
            '🔍': 'tag',
            '✋': 'hand',
            '🎒': 'backpack',
            '📦': 'package',
            '🏪': 'store',
            '📍': 'map-pin',
            '📅': 'calendar',
            '🗺️': 'map',
            '🔋': 'battery',
            '⚡': 'zap',
            '📊': 'chart-no-axes-column',
            '🛡️': 'shield-check',
            '☰': 'menu',
            '📋': 'list',
            '🚪': 'log-out',
            '💱': 'arrow-left-right'
        };

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);
        }

        async function loadNotifications() {
            try {
                const response = await fetch('/notifications', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) return;

                const data = await response.json();
                const dot = document.getElementById('bellDot');

                if (dot) {
                    const unread = Number(data.unread_count || 0);
                    dot.textContent = unread > 99 ? '99+' : String(unread);
                    dot.style.display = unread > 0 ? 'flex' : 'none';
                }

                const list = document.getElementById('bellList');
                if (!list) return;

                const notifications = Array.isArray(data.notifications) ? data.notifications : [];

                if (!notifications.length) {
                    list.innerHTML = '<div class="bell-empty">No notifications yet</div>';
                    return;
                }

                list.innerHTML = notifications.map(notification => {
                    const rawIcon = String(notification.icon || 'bell');
                    const iconName = notificationIcons[rawIcon] ||
                        (/^[a-z0-9-]+$/i.test(rawIcon) ? rawIcon : 'bell');

                    const url = String(notification.url || '#');
                    const safeUrl = /^(\/|https?:\/\/)/i.test(url) ? url : '#';

                    return `
                        <a href="${escapeHtml(safeUrl)}"
                           class="bell-item ${notification.unread ? 'unread' : ''}"
                           onclick="markNotifRead(${Number(notification.id) || 0})">
                            <div class="bell-icon">
                                <i data-lucide="${escapeHtml(iconName)}"></i>
                            </div>
                            <div class="bell-content">
                                <div class="bell-item-title">${escapeHtml(notification.title)}</div>
                                <div class="bell-item-body">${escapeHtml(notification.body)}</div>
                                <div class="bell-item-time">${escapeHtml(notification.time_ago)}</div>
                            </div>
                        </a>
                    `;
                }).join('');

                renderIcons();
            } catch (error) {
                console.warn('Could not load notifications:', error);
            }
        }

        async function markNotifRead(id) {
            if (!id) return;

            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (error) {
                console.warn('Could not mark notification as read:', error);
            }
        }

        async function markAllRead() {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) await loadNotifications();
            } catch (error) {
                console.warn('Could not mark all notifications as read:', error);
            }
        }

        async function clearAll() {
            try {
                const response = await fetch('/notifications/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) await loadNotifications();
            } catch (error) {
                console.warn('Could not clear notifications:', error);
            }
        }

        function autoDismissAlerts() {
            document.querySelectorAll('.auto-dismiss-alert').forEach(alert => {
                window.setTimeout(() => {
                    alert.classList.add('is-dismissing');
                    window.setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            autoDismissAlerts();
            renderIcons();
            applyTheme(document.documentElement.dataset.theme || 'light', false);

            @auth
                loadNotifications();
                window.setInterval(loadNotifications, NOTIF_POLL_MS);
            @endauth
        });
    </script>
</body>
</html>
