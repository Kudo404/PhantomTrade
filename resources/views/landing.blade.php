@extends('layouts.landing')

@section('title', 'Phantom Trade — The Marketplace for Hunters')

@section('content')

    {{-- ═══ HERO ═══ --}}
    <section style="position:relative; overflow:hidden; padding:80px 32px 60px;">
        {{-- Subtle gradient background --}}
        <div style="position:absolute; inset:0; background:linear-gradient(180deg, #f8fafc 0%, #eef2f7 60%, #f8fafc 100%); z-index:-1;"></div>
        <div style="position:absolute; top:-200px; right:-200px; width:600px; height:600px; background:radial-gradient(circle, rgba(167,139,250,0.18), transparent 70%); z-index:-1;"></div>
        <div style="position:absolute; bottom:-200px; left:-200px; width:500px; height:500px; background:radial-gradient(circle, rgba(52,211,153,0.12), transparent 70%); z-index:-1;"></div>

        <div style="max-width:840px; margin:0 auto; text-align:center; position:relative;">

            {{-- Eyebrow --}}
            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 14px; background:var(--surface); border:1px solid var(--border); border-radius:20px; font-size:12.5px; font-weight:600; color:var(--text-secondary); margin-bottom:24px;">
                <span style="width:6px; height:6px; border-radius:50%; background:#10b981;"></span>
                <span>Now open for hunters, merchants, and collectors</span>
            </div>

            {{-- Headline --}}
            <h1 style="font-size:clamp(36px, 6vw, 64px); font-weight:800; letter-spacing:-0.03em; line-height:1.1; color:var(--text); margin-bottom:20px;">
                The Marketplace<br>for Hunters
            </h1>

            {{-- Subtext --}}
            <p style="font-size:clamp(15px, 1.8vw, 18px); color:var(--text-secondary); line-height:1.6; margin-bottom:36px; max-width:600px; margin-left:auto; margin-right:auto;">
                Buy, trade, and sell spectral gear. Equip your loadout. Track hauntings on the map. Become a certified Hunter or Merchant.
            </p>

            {{-- CTAs --}}
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i data-lucide="sword" style="width:16px; height:16px;"></i>
                    <span>Join the Hunt</span>
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    Log In
                </a>
            </div>

        </div>
    </section>


    {{-- ═══ HOW IT WORKS ═══ --}}
    <section style="padding:40px 32px 20px;">
        <div style="max-width:1000px; margin:0 auto;">

            {{-- Section header --}}
            <div style="text-align:center; margin-bottom:44px;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.15em; color:var(--text-muted); margin-bottom:10px;">
                    How It Works
                </div>
                <h2 style="font-size:clamp(24px, 3.5vw, 32px); font-weight:700; letter-spacing:-0.02em; color:var(--text);">
                    Three steps to start trading
                </h2>
            </div>

            {{-- Steps grid --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px;">

                {{-- Step 1 --}}
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:28px 24px; position:relative;">
                    <div style="width:44px; height:44px; border-radius:10px; background:var(--bg); display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                        <i data-lucide="user-plus" style="width:20px; height:20px; color:var(--text);"></i>
                    </div>
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); margin-bottom:6px;">
                        Step 1
                    </div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text); margin-bottom:8px;">
                        Create your account
                    </h3>
                    <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.6;">
                        Sign up as a Civilian. Every hunter starts here.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:28px 24px; position:relative;">
                    <div style="width:44px; height:44px; border-radius:10px; background:var(--bg); display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                        <i data-lucide="shopping-bag" style="width:20px; height:20px; color:var(--text);"></i>
                    </div>
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); margin-bottom:6px;">
                        Step 2
                    </div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text); margin-bottom:8px;">
                        Buy, trade, and sell
                    </h3>
                    <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.6;">
                        Browse the shop and marketplace. Build your inventory.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:28px 24px; position:relative;">
                    <div style="width:44px; height:44px; border-radius:10px; background:var(--bg); display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                        <i data-lucide="badge-check" style="width:20px; height:20px; color:var(--text);"></i>
                    </div>
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); margin-bottom:6px;">
                        Step 3
                    </div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text); margin-bottom:8px;">
                        Earn certifications
                    </h3>
                    <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.6;">
                        Apply to become a Merchant or Ghost Hunter and unlock new tools.
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══ FINAL CTA ═══ --}}
    <section style="padding:80px 32px 40px;">
        <div style="max-width:800px; margin:0 auto;">

            <div style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius:16px; padding:60px 40px; text-align:center; position:relative; overflow:hidden;">
                {{-- Decorative glow --}}
                <div style="position:absolute; top:-100px; right:-100px; width:300px; height:300px; background:radial-gradient(circle, rgba(167,139,250,0.35), transparent 70%);"></div>
                <div style="position:absolute; bottom:-100px; left:-100px; width:300px; height:300px; background:radial-gradient(circle, rgba(52,211,153,0.2), transparent 70%);"></div>

                <div style="position:relative; z-index:1;">
                    <h2 style="font-size:clamp(24px, 3.5vw, 32px); font-weight:700; letter-spacing:-0.02em; color:#fff; margin-bottom:12px;">
                        Ready to start hunting?
                    </h2>
                    <p style="font-size:15px; color:#94a3b8; margin-bottom:28px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.6;">
                        Join Phantom Trade today and meet hunters, merchants, and collectors from across the realm.
                    </p>

                    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                        <a href="{{ route('register') }}" class="btn" style="background:#fff; color:#0f172a; padding:13px 28px;">
                            <i data-lucide="sword" style="width:16px; height:16px;"></i>
                            <span>Create Account</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn" style="background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.25); padding:13px 28px;">
                            Log In
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection 