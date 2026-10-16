<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phantom Trade — The Marketplace for Hunters')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

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
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a { color: inherit; text-decoration: none; }

        /* ─── Top Navigation ─────────────────── */
        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 16px;
            color: var(--text);
        }
        .nav-brand svg { display: block; }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-link {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            border-radius: var(--radius-sm);
            transition: all 0.12s;
        }
        .nav-link:hover {
            background: var(--bg);
            color: var(--text);
        }
        .nav-btn {
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            background: var(--accent);
            color: #fff;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: background 0.12s;
            font-family: inherit;
        }
        .nav-btn:hover { background: var(--accent-hover); }

        /* ─── Buttons ────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.15);
        }
        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: var(--bg);
            border-color: var(--text-muted);
        }

        /* ─── Footer ─────────────────────────── */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 40px 32px 32px;
            margin-top: 80px;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }
        .footer-copy {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ─── Mobile ─────────────────────────── */
        @media (max-width: 720px) {
            .nav-inner { padding: 12px 20px; }
            .nav-brand { font-size: 15px; }
            .nav-link { padding: 6px 12px; font-size: 13px; }
            .nav-btn  { padding: 7px 14px; font-size: 13px; }
        }
    </style>
</head>

<body>

    {{-- ═══ TOP NAV ═══ --}}
    <nav class="nav">
        <div class="nav-inner">

            {{-- Logo + Brand --}}
            <a href="/" class="nav-brand">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="28" height="28" rx="6" fill="#0f172a"/>
                    <path d="M9 20V8h7.5c2.5 0 4 1.5 4 3.8 0 2-1.2 3.3-3 3.6l3.2 4.6h-2.9l-2.8-4.2h-3.5V20H9zm2.5-6.2h4.4c1.2 0 2-.8 2-1.9 0-1.2-.8-1.9-2-1.9h-4.4v3.8z" fill="#a78bfa"/>
                </svg>
                <span>Phantom Trade</span>
            </a>

            {{-- Actions --}}
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="nav-link">Log In</a>
                <a href="{{ route('register') }}" class="nav-btn">Sign Up</a>
            </div>
        </div>
    </nav>

    {{-- ═══ PAGE CONTENT ═══ --}}
    @yield('content')

    {{-- ═══ FOOTER ═══ --}}
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <svg width="20" height="20" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="28" height="28" rx="6" fill="#0f172a"/>
                    <path d="M9 20V8h7.5c2.5 0 4 1.5 4 3.8 0 2-1.2 3.3-3 3.6l3.2 4.6h-2.9l-2.8-4.2h-3.5V20H9zm2.5-6.2h4.4c1.2 0 2-.8 2-1.9 0-1.2-.8-1.9-2-1.9h-4.4v3.8z" fill="#a78bfa"/>
                </svg>
                <span>Phantom Trade</span>
            </div>
            <div class="footer-copy">
                © {{ date('Y') }} Phantom Trade · The Marketplace for Hunters
            </div>
        </div>
    </footer>

    {{-- ═══ Lucide Icons ═══ --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        if (window.lucide) lucide.createIcons();
    </script>

</body>
</html>