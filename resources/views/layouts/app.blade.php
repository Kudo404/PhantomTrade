<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phantom Trade')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ─── Reset ────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ─── Design Tokens ────────────────────────── */
        :root {
            --bg:              #f8fafc;
            --surface:         #ffffff;
            --border:          #e2e8f0;
            --border-subtle:   #f1f5f9;
            --text:            #0f172a;
            --text-secondary:  #64748b;
            --text-muted:      #94a3b8;
            --accent:          #0f172a;
            --accent-hover:    #1e293b;
            --success:         #059669;
            --warning:         #d97706;
            --danger:          #e11d48;
            --radius:          8px;
            --radius-sm:       6px;
            --shadow-sm:       0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md:       0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-lg:       0 12px 32px rgba(15, 23, 42, 0.10);
        }

        /* ─── Base ─────────────────────────────────── */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; }

        /* ─── Sidebar ──────────────────────────────── */
        .sidebar {
            width: 248px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 24px;
        }
        .sidebar-brand-mark {
            width: 32px;
            height: 32px;
            background: var(--accent);
            color: #fff;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .sidebar-brand-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .sidebar-section {
            padding: 0 12px;
            margin-bottom: 20px;
        }
        .sidebar-section-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 0 8px 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.12s ease;
            margin-bottom: 2px;
        }
        .sidebar-link:hover {
            background: var(--bg);
            color: var(--text);
        }
        .sidebar-link.active {
            background: var(--bg);
            color: var(--text);
            font-weight: 600;
        }
        .sidebar-link i {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding: 16px;
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px;
            border-radius: var(--radius-sm);
        }
        .sidebar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            flex-shrink: 0;
        }
        .sidebar-user-info { min-width: 0; flex: 1; }
        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ─── Main ─────────────────────────────────── */
        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: var(--bg);
            min-height: 100vh;
        }

        /* ─── Topbar ───────────────────────────────── */
        .topbar {
            height: 60px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 1100;
            flex-shrink: 0;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            cursor: pointer;
            padding: 6px;
            border-radius: var(--radius-sm);
        }
        .hamburger-btn:hover { background: var(--bg); }

        .search-wrapper {
            position: relative;
            max-width: 420px;
            width: 100%;
        }
        .search-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }
        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--text);
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.12s;
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        /* ─── Icon buttons ─────────────────────────── */
        .icon-btn {
            position: relative;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            background: none;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.12s;
        }
        .icon-btn:hover {
            background: var(--bg);
            color: var(--text);
        }
        .icon-btn i { width: 18px; height: 18px; }

        .icon-btn .dot {
            position: absolute;
            top: 4px;
            right: 4px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            background: var(--danger);
            color: #fff;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--surface);
            line-height: 1;
            z-index: 2;
        }

        /* ─── Profile trigger ──────────────────────── */
        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            cursor: pointer;
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.12s;
        }
        .profile-trigger:hover {
            background: var(--bg);
            border-color: var(--text-muted);
        }
        .profile-trigger-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 11px;
        }
        .profile-trigger i { width: 14px; height: 14px; color: var(--text-muted); }

        /* ─── Dropdowns ───────────────────────────── */
        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: none;
            z-index: 9999;
        }
        .dropdown.open { display: block; }

        /* Profile dropdown */
        #profileMenu { width: 260px; }

        .dropdown-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .dropdown-user-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .dropdown-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 15px;
            flex-shrink: 0;
        }
        .dropdown-user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dropdown-user-email {
            font-size: 11.5px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dropdown-user-meta {
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
        }
        .dropdown-role {
            background: var(--bg);
            color: var(--text-secondary);
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            font-size: 10px;
        }
        .dropdown-ecto {
            color: var(--text);
            font-weight: 600;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            color: var(--text-secondary);
            font-size: 13.5px;
            transition: background 0.12s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-item:hover {
            background: var(--bg);
            color: var(--text);
        }
        .dropdown-item i {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }
        .dropdown-item.danger { color: var(--danger); }
        .dropdown-item.danger:hover { background: #fef2f2; }

        .dropdown-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 4px 0;
        }

        /* Bell dropdown */
        #bellMenu { width: 360px; max-height: 480px; }

        .bell-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bell-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }
        .bell-action {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 11.5px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
        }
        .bell-action:hover { background: var(--bg); color: var(--text); }

        .bell-list {
            max-height: 360px;
            overflow-y: auto;
        }
        .bell-list::-webkit-scrollbar { width: 6px; }
        .bell-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        .bell-list::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        .bell-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.12s;
            text-decoration: none;
            color: inherit;
        }
        .bell-item:hover { background: var(--bg); }
        .bell-item.unread { background: rgba(15, 23, 42, 0.02); }
        .bell-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .bell-content { min-width: 0; flex: 1; }
        .bell-item-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }
        .bell-item-body {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.4;
        }
        .bell-item-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .bell-empty {
            padding: 32px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
        }
        .bell-footer {
            padding: 8px 16px;
            border-top: 1px solid var(--border-subtle);
            text-align: center;
        }

        /* ─── Content ──────────────────────────────── */
        .content {
            padding: 32px 24px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* ─── Shared components ───────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 13.5px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.12s;
            font-family: inherit;
        }
        .btn:hover { background: var(--accent-hover); }
        .btn i { width: 15px; height: 15px; }

        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--bg); }

        .success-banner {
            background: #ecfdf5;
            color: var(--success);
            border: 1px solid #a7f3d0;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 13.5px;
        }

        .error-banner {
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 13.5px;
        }

        .type-tag {
            display: inline-block;
            font-size: 10.5px;
            padding: 2px 8px;
            background: var(--bg);
            color: var(--text-secondary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        /* ─── Mobile ───────────────────────────────── */
        @media (max-width: 900px) {
            .hamburger-btn { display: flex; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                z-index: 200;
                transition: left 0.25s ease;
                box-shadow: var(--shadow-lg);
            }
            .sidebar.open { left: 0; }
            .search-wrapper { display: none; }
            .content { padding: 20px 16px; }
        }
    </style>
</head>

<body>

    {{-- ═══ SIDEBAR ═══════════════════════════════════ --}}
        <aside class="sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">P</div>
            <div>
                <div class="sidebar-brand-name">Phantom Trade</div>
                <div class="sidebar-brand-sub">Marketplace</div>
            </div>
        </div>

        @auth
            @php
                $sidebarUnread = auth()->user()->unreadMessagesCount();
            @endphp

            @if (auth()->user()->isAdmin())
                {{-- ═══ ADMIN SIDEBAR ═══ --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Management</div>
                    <a href="/admin" class="sidebar-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Overview</span>
                    </a>
                    <a href="/admin/applications" class="sidebar-link {{ request()->is('admin/applications*') ? 'active' : '' }}">
                        <i data-lucide="inbox"></i>
                        <span>Applications</span>
                    </a>
                    <a href="/admin/users" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i data-lucide="users"></i>
                        <span>Users</span>
                    </a>
                    <a href="/gears" class="sidebar-link {{ request()->is('gears') ? 'active' : '' }}">
                        <i data-lucide="package"></i>
                        <span>Gear Catalog</span>
                    </a>
                    <a href="/marketplace" class="sidebar-link {{ request()->is('marketplace') ? 'active' : '' }}">
                        <i data-lucide="store"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="/messages" class="sidebar-link {{ request()->is('messages*') ? 'active' : '' }}">
                        <i data-lucide="message-circle"></i>
                        <span>Messages</span>
                        @if ($sidebarUnread > 0)
                            <span class="ml-auto bg-[#e11d48] text-[#fff] text-[10px] font-bold pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px] min-w-[18px] text-center">
                                {{ $sidebarUnread > 99 ? '99+' : $sidebarUnread }}
                            </span>
                        @endif
                    </a>
                    <a href="/admin/settings" class="sidebar-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <i data-lucide="settings"></i>
                        <span>Settings</span>
                    </a>
                </div>
            @else
                {{-- ═══ NORMAL USER SIDEBAR ═══ --}}
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Main</div>
                    <a href="/" class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/gears" class="sidebar-link {{ request()->is('gears') ? 'active' : '' }}">
                        <i data-lucide="cog"></i>
                        <span>Gear Shop</span>
                    </a>
                    <a href="/marketplace" class="sidebar-link {{ request()->is('marketplace') ? 'active' : '' }}">
                        <i data-lucide="store"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="/messages" class="sidebar-link {{ request()->is('messages*') ? 'active' : '' }}">
                        <i data-lucide="message-circle"></i>
                        <span>Messages</span>
                        @if ($sidebarUnread > 0)
                            <span class="ml-auto bg-[#e11d48] text-[#fff] text-[10px] font-bold pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px] min-w-[18px] text-center">
                                {{ $sidebarUnread > 99 ? '99+' : $sidebarUnread }}
                            </span>
                        @endif
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Field Ops</div>
                    <a href="/loadout" class="sidebar-link {{ request()->is('loadout*') ? 'active' : '' }}">
                        <i data-lucide="sliders"></i>
                        <span>Loadout</span>
                    </a>
                    <a href="/drain" class="sidebar-link {{ request()->is('drain*') ? 'active' : '' }}">
                        <i data-lucide="zap"></i>
                        <span>Drain Chamber</span>
                    </a>
                    <a href="/map" class="sidebar-link {{ request()->is('map*') ? 'active' : '' }}">
                        <i data-lucide="map-pin"></i>
                        <span>Haunt Map</span>
                    </a>
                </div>
            @endif
        @endauth

        @guest
            <div class="sidebar-section">
                <div class="sidebar-section-title">Account</div>
                <a href="{{ route('login') }}" class="sidebar-link {{ request()->is('login') ? 'active' : '' }}">
                    <i data-lucide="log-in"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="sidebar-link {{ request()->is('register') ? 'active' : '' }}">
                    <i data-lucide="user-plus"></i>
                    <span>Register</span>
                </a>
            </div>
        @endguest

        @auth
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                        <div class="sidebar-user-role">{{ auth()->user()->roleLabel() }}</div>
                    </div>
                </div>
            </div>
        @endauth
    </aside>

    {{-- ═══ MAIN ══════════════════════════════════════ --}}
    <div class="main">

        {{-- ── TOPBAR ───────────────────────────────── --}}
        @auth
            <header class="topbar">
                <div class="topbar-left">
                    <button class="hamburger-btn" onclick="document.getElementById('appSidebar').classList.toggle('open')">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                </div>

                <div class="topbar-right">
                    {{-- Notification bell --}}
                    <div class="relative">
                        <button class="icon-btn" id="bellBtn"
                                onclick="toggleDropdown('bellMenu', 'bellBtn')">
                            <i data-lucide="bell"></i>
                            <span class="dot" id="bellDot">0</span>
                        </button>

                        <div class="dropdown" id="bellMenu">
                            <div class="bell-header">
                                <div class="bell-title">Notifications</div>
                                <button class="bell-action" onclick="markAllRead()">Mark all read</button>
                            </div>
                            <div class="bell-list" id="bellList">
                                <div class="bell-empty">Loading…</div>
                            </div>
                            <div class="bell-footer">
                                <button class="bell-action" onclick="clearAll()">Clear all</button>
                            </div>
                        </div>
                    </div>

                    {{-- Profile --}}
                    <div class="relative">
                        <button class="profile-trigger" id="profileBtn"
                                onclick="toggleDropdown('profileMenu', 'profileBtn')">
                            <div class="profile-trigger-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <i data-lucide="chevron-down"></i>
                        </button>

                        <div class="dropdown" id="profileMenu">
                            <div class="dropdown-header">
                                <div class="dropdown-user-row">
                                    <div class="dropdown-avatar">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
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
                                {{-- ═══ ADMIN DROPDOWN ═══ --}}
                                <a href="/admin" class="dropdown-item">
                                    <i data-lucide="shield"></i> <span>Admin Panel</span>
                                </a>
                                <a href="/messages" class="dropdown-item flex justify-between">
                                    <span class="flex items-center gap-2.5">
                                        <i data-lucide="message-circle"></i>
                                        <span>Messages</span>
                                    </span>
                                    @php $unreadMsgDrop = auth()->user()->unreadMessagesCount(); @endphp
                                    @if ($unreadMsgDrop > 0)
                                        <span class="bg-[#e11d48] text-[#fff] text-[10px] font-bold pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px]">
                                            {{ $unreadMsgDrop > 99 ? '99+' : $unreadMsgDrop }}
                                        </span>
                                    @endif
                                </a>
                                <a href="/profile" class="dropdown-item">
                                    <i data-lucide="user"></i> <span>My Profile</span>
                                </a>
                            @else
                                {{-- ═══ NORMAL USER DROPDOWN ═══ --}}
                                <a href="/profile" class="dropdown-item">
                                    <i data-lucide="user"></i> <span>My Profile</span>
                                </a>
                                <a href="/inventory" class="dropdown-item">
                                    <i data-lucide="package"></i> <span>My Inventory</span>
                                </a>
                                <a href="/messages" class="dropdown-item flex justify-between">
                                    <span class="flex items-center gap-2.5">
                                        <i data-lucide="message-circle"></i>
                                        <span>Messages</span>
                                    </span>
                                    @php $unreadMsgDrop = auth()->user()->unreadMessagesCount(); @endphp
                                    @if ($unreadMsgDrop > 0)
                                        <span class="bg-[#e11d48] text-[#fff] text-[10px] font-bold pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px]">
                                            {{ $unreadMsgDrop > 99 ? '99+' : $unreadMsgDrop }}
                                        </span>
                                    @endif
                                </a>
                                <a href="/marketplace/my-proposals" class="dropdown-item">
                                    <i data-lucide="arrow-left-right"></i> <span>My Sent Proposals</span>
                                </a>
                                @if (auth()->user()->isCivilian())
                                    <a href="/applications/status" class="dropdown-item">
                                        <i data-lucide="file-text"></i> <span>Application Status</span>
                                    </a>
                                @endif
                                <a href="/achievements" class="dropdown-item">
                                    <i data-lucide="trophy"></i> <span>Achievements</span>
                                </a>
                                <a href="/marketplace/wanted/mine" class="dropdown-item">
                                    <i data-lucide="tag"></i> <span>My Wanted Ads</span>
                                </a>

                                @if (auth()->user()->isGhostHunter())
                                    <a href="/loadout" class="dropdown-item">
                                        <i data-lucide="backpack"></i> <span>My Loadout</span>
                                    </a>
                                    @if (auth()->user()->license_number)
                                        <a href="/profile/license" class="dropdown-item">
                                            <i data-lucide="shield-check"></i> <span>Hunter License</span>
                                        </a>
                                    @endif
                                @endif

                                @if (auth()->user()->isMerchant())
                                    <div class="dropdown-divider"></div>
                                    <a href="/marketplace/proposals" class="dropdown-item">
                                        <i data-lucide="inbox"></i> <span>Incoming Proposals</span>
                                    </a>
                                    <a href="/marketplace/create" class="dropdown-item">
                                        <i data-lucide="plus-circle"></i> <span>Create Listing</span>
                                    </a>
                                    <a href="/marketplace/mine" class="dropdown-item">
                                        <i data-lucide="list"></i> <span>My Listings</span>
                                    </a>
                                @endif

                                <div class="dropdown-divider"></div>
                                <a href="/purchases" class="dropdown-item">
                                    <i data-lucide="rotate-ccw-clock"></i> <span>Recent Purchases</span>
                                </a>
                            @endif

                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="mt-0 mr-0 mb-0 ml-0">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <i data-lucide="log-out"></i> <span>Log out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
        @endauth

        {{-- ── CONTENT ──────────────────────────────── --}}
        <main class="content">
            @if (session('success'))
                <div class="success-banner">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-banner">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- ═══ SCRIPTS ═══════════════════════════════════ --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        // ── Init Lucide icons ─────────────────────
        function renderIcons() { if (window.lucide) lucide.createIcons(); }
        document.addEventListener('DOMContentLoaded', renderIcons);
        renderIcons();

        // ── Dropdown toggle ────────────────────────
        function toggleDropdown(menuId, btnId) {
            const menu = document.getElementById(menuId);
            const isOpen = menu.classList.contains('open');

            document.querySelectorAll('.dropdown').forEach(m => m.classList.remove('open'));

            if (!isOpen) {
                menu.classList.add('open');
                if (menuId === 'bellMenu') loadNotifications();
            }
        }

        document.addEventListener('click', function (e) {
            if (e.target.closest('#profileBtn') || e.target.closest('#bellBtn')) return;
            if (e.target.closest('#profileMenu') || e.target.closest('#bellMenu')) return;
            document.querySelectorAll('.dropdown').forEach(m => m.classList.remove('open'));
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.dropdown').forEach(m => m.classList.remove('open'));
            }
        });

        // ── Notifications ──────────────────────────
        const NOTIF_POLL_MS = 60000;

        async function loadNotifications() {
            try {
                const res = await fetch('/notifications', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();

                const dot = document.getElementById('bellDot');
                if (dot) {
                    if (data.unread_count > 0) {
                        dot.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        dot.style.display = 'flex';
                    } else {
                        dot.style.display = 'none';
                    }
                }

                const list = document.getElementById('bellList');
                if (!list) return;

                if (!data.notifications.length) {
                    list.innerHTML = '<div class="bell-empty">No notifications yet</div>';
                    return;
                }

                // Emoji → Lucide icon name map
                const emojiToIcon = {
                    '🔔': 'bell',
                    '👤': 'user',
                    '👥': 'users',
                    '🛒': 'shopping-cart',
                    '💰': 'wallet',
                    '₱': 'gem',
                    '✅': 'check-circle-2',
                    '❌': 'x-circle',
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
                    '📊': 'bar-chart-3',
                    '🛡️': 'shield',
                    '☰': 'menu',
                    '📋': 'list',
                    '🚪': 'log-out',
                    '💱': 'arrow-left-right',
                };

                list.innerHTML = data.notifications.map(n => {
                    // Determine icon name (emoji → lucide name, or fallback to bell)
                    const iconName = emojiToIcon[n.icon] || (n.icon && !/\p{Emoji}/u.test(n.icon) ? n.icon : 'bell');

                    return `
                        <a href="${n.url || '#'}"
                           class="bell-item ${n.unread ? 'unread' : ''}"
                           onclick="markNotifRead(${n.id})">
                            <div class="bell-icon">
                                <i data-lucide="${iconName}" style="width:14px; height:14px; color:var(--text-secondary);"></i>
                            </div>
                            <div class="bell-content">
                                <div class="bell-item-title">${n.title}</div>
                                <div class="bell-item-body">${n.body}</div>
                                <div class="bell-item-time">${n.time_ago}</div>
                            </div>
                        </a>
                    `;
                }).join('');

                // Re-render Lucide icons after inserting new DOM
                if (window.lucide) lucide.createIcons();
            } catch (err) {
                console.warn('loadNotifications failed:', err);
            }
        }

        async function markNotifRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (err) {}
        }

        async function markAllRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                loadNotifications();
            } catch (err) {}
        }

        async function clearAll() {
            try {
                await fetch('/notifications/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                loadNotifications();
            } catch (err) {}
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadNotifications();
            setInterval(loadNotifications, NOTIF_POLL_MS);
        });
    </script>
</body>
</html>