<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phantom Trade')</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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
            --danger:          #e11d48;
            --radius:          8px;
            --radius-sm:       6px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrap {
            width: 100%;
            max-width: 400px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-brand-mark {
            display: inline-flex;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 14px;
        }
        .auth-brand-name {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--text);
        }
        .auth-brand-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 32px;
        }

        .auth-card h2 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--text);
            margin-bottom: 4px;
        }
        .auth-card .sub {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.12s;
        }
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06);
        }
        input::placeholder { color: var(--text-muted); }

        .field { margin-bottom: 18px; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: var(--accent);
            color: #fff;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            border: none;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.12s;
            font-family: inherit;
        }
        .btn:hover { background: var(--accent-hover); }

        .error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
        }

        .auth-status {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            padding: 10px 12px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: var(--radius-sm);
            color: #065f46;
            font-size: 12.5px;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .auth-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 16px 0;
            font-size: 12.5px;
        }
        .auth-row label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 400;
            color: var(--text-secondary);
            cursor: pointer;
        }
        .auth-row input[type=checkbox] {
            width: 14px;
            height: 14px;
            accent-color: var(--accent);
        }
        .auth-row a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
        }
        .auth-row a:hover { color: var(--text); }

        .auth-alt {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        .auth-alt a {
            color: var(--text);
            font-weight: 500;
            text-decoration: none;
        }
        .auth-alt a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="auth-wrap">
        <div class="auth-brand">
            <div class="auth-brand-mark">P</div>
            <div class="auth-brand-name">Phantom Trade</div>
            <div class="auth-brand-sub">Marketplace</div>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>

</body>
</html>