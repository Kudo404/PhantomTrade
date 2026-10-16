@extends('layouts.landing')

@section('title', 'Phantom Trade — Paranormal Operations Network')

@section('content')
<div class="pt-landing">

    {{-- HERO --}}
    <section class="pt-hero">
        <div class="pt-grid-pattern" aria-hidden="true"></div>

        <div class="pt-hero-inner">
            <div class="pt-hero-copy">
                <div class="pt-eyebrow">
                    <span class="pt-dot"></span>
                    INDEPENDENT PARANORMAL OPERATIONS NETWORK
                </div>

                <h1>
                    The right gear.<br>
                    <span>The other side.</span>
                </h1>

                <p class="pt-hero-description">
                    Equip your next investigation, trade with trusted operators,
                    and build a loadout ready for whatever answers back.
                </p>

                <div class="pt-actions">
                    <a href="{{ route('register') }}" class="pt-button pt-button-primary">
                        Create your account
                        <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                    </a>

                    <a href="{{ route('login') }}" class="pt-button pt-button-secondary">
                        Sign in
                        <i data-lucide="log-in" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="pt-note">
                    <i data-lucide="shield-check" aria-hidden="true"></i>
                    <span>Start as a Civilian. Earn your place in the field.</span>
                </div>
            </div>

            {{-- OPERATIONS PANEL --}}
            <aside class="pt-console" aria-label="Phantom Trade operations overview">
                <div class="pt-console-header">
                    <div class="pt-console-brand">
                        <div class="pt-console-logo">
                            <i data-lucide="scan-eye" aria-hidden="true"></i>
                        </div>
                        <div>
                            <strong>PHANTOM TRADE</strong>
                            <span>OPERATIONS CONSOLE</span>
                        </div>
                    </div>

                    <div class="pt-online">
                        <span class="pt-dot"></span>
                        ONLINE
                    </div>
                </div>

                <div class="pt-rule"></div>

                <div class="pt-console-heading">
                    <div>
                        <span class="pt-label">FIELD READINESS</span>
                        <h2>Your next mission starts here.</h2>
                    </div>
                    <i data-lucide="crosshair" aria-hidden="true"></i>
                </div>

                <a href="{{ route('login') }}" class="pt-console-item">
                    <span class="pt-item-icon pt-purple">
                        <i data-lucide="package-search" aria-hidden="true"></i>
                    </span>
                    <span class="pt-item-copy">
                        <strong>Gear &amp; equipment</strong>
                        <small>Find tools for the unknown</small>
                    </span>
                    <i data-lucide="arrow-up-right" class="pt-item-arrow" aria-hidden="true"></i>
                </a>

                <a href="{{ route('login') }}" class="pt-console-item">
                    <span class="pt-item-icon pt-green">
                        <i data-lucide="arrow-left-right" aria-hidden="true"></i>
                    </span>
                    <span class="pt-item-copy">
                        <strong>Operator marketplace</strong>
                        <small>Trade with the community</small>
                    </span>
                    <i data-lucide="arrow-up-right" class="pt-item-arrow" aria-hidden="true"></i>
                </a>

                <a href="{{ route('login') }}" class="pt-console-item">
                    <span class="pt-item-icon pt-amber">
                        <i data-lucide="activity" aria-hidden="true"></i>
                    </span>
                    <span class="pt-item-copy">
                        <strong>Loadout &amp; readiness</strong>
                        <small>Prepare before deployment</small>
                    </span>
                    <i data-lucide="arrow-up-right" class="pt-item-arrow" aria-hidden="true"></i>
                </a>

                <div class="pt-console-footer">
                    <span>NETWORK STATUS</span>
                    <span class="pt-system-status">
                        <span class="pt-dot"></span>
                        Systems nominal
                    </span>
                </div>
            </aside>
        </div>

        <div class="pt-hero-bottom">
            <span>01 / EQUIP</span>
            <span>02 / EXCHANGE</span>
            <span>03 / INVESTIGATE</span>
            <span class="pt-bottom-caption">BUILT FOR THE UNEXPLAINED</span>
        </div>
    </section>

    {{-- FEATURE CARDS --}}
    <section class="pt-section">
        <div class="pt-section-heading">
            <div>
                <span class="pt-section-kicker">THE NETWORK</span>
                <h2>Everything for the work ahead.</h2>
            </div>
            <p>
                A shared space for hunters, merchants, and independent
                investigators to prepare, exchange equipment, and progress.
            </p>
        </div>

        <div class="pt-feature-grid">

            {{-- CARD 1: FULL CARD IS CLICKABLE --}}
            <a href="{{ route('login') }}"
               class="pt-feature-card"
               aria-label="Sign in to build your gear kit">
                <div class="pt-feature-top">
                    <span class="pt-index">01</span>
                    <span class="pt-feature-icon pt-purple">
                        <i data-lucide="package" aria-hidden="true"></i>
                    </span>
                </div>

                <h3>Build your kit</h3>
                <p>
                    Browse specialized gear, manage your inventory, and
                    assemble equipment suited to your next investigation.
                </p>

                <div class="pt-feature-bottom">
                    <span class="pt-feature-tag"><span></span> GEAR CATALOG</span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </div>
            </a>

            {{-- CARD 2: FULL CARD IS CLICKABLE --}}
            <a href="{{ route('login') }}"
               class="pt-feature-card"
               aria-label="Sign in to access the operator marketplace">
                <div class="pt-feature-top">
                    <span class="pt-index">02</span>
                    <span class="pt-feature-icon pt-green">
                        <i data-lucide="repeat-2" aria-hidden="true"></i>
                    </span>
                </div>

                <h3>Make the exchange</h3>
                <p>
                    Discover marketplace listings, connect with other
                    operators, and propose trades for the equipment you need.
                </p>

                <div class="pt-feature-bottom">
                    <span class="pt-feature-tag"><span></span> COMMUNITY MARKET</span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </div>
            </a>

            {{-- CARD 3: FULL CARD IS CLICKABLE --}}
            <a href="{{ route('login') }}"
               class="pt-feature-card"
               aria-label="Sign in to explore operator progression">
                <div class="pt-feature-top">
                    <span class="pt-index">03</span>
                    <span class="pt-feature-icon pt-amber">
                        <i data-lucide="badge-check" aria-hidden="true"></i>
                    </span>
                </div>

                <h3>Earn your clearance</h3>
                <p>
                    Start your journey, submit role applications, and work
                    toward becoming a certified Hunter or Merchant.
                </p>

                <div class="pt-feature-bottom">
                    <span class="pt-feature-tag"><span></span> OPERATOR PROGRESSION</span>
                    <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                </div>
            </a>

        </div>

        <p class="pt-card-hint">
            <i data-lucide="mouse-pointer-2" aria-hidden="true"></i>
            Select a feature to sign in and continue to the network.
        </p>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="pt-journey">
        <div class="pt-journey-inner">
            <div class="pt-journey-intro">
                <span class="pt-section-kicker">YOUR FIRST DEPLOYMENT</span>
                <h2>New to the network?<br>Start with the essentials.</h2>
                <p>
                    Every experienced operator starts somewhere. Set up your
                    account and take the first step toward the field.
                </p>

                <a href="{{ route('register') }}" class="pt-text-link">
                    Get started
                    <i data-lucide="arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="pt-steps">
                <div class="pt-step">
                    <span class="pt-step-number">01</span>
                    <div>
                        <h3>Register</h3>
                        <p>Create your account and enter the network as a Civilian.</p>
                    </div>
                    <i data-lucide="user-round-plus" aria-hidden="true"></i>
                </div>

                <div class="pt-step">
                    <span class="pt-step-number">02</span>
                    <div>
                        <h3>Explore</h3>
                        <p>Discover gear, listings, and the tools available to you.</p>
                    </div>
                    <i data-lucide="compass" aria-hidden="true"></i>
                </div>

                <div class="pt-step">
                    <span class="pt-step-number">03</span>
                    <div>
                        <h3>Advance</h3>
                        <p>Prepare your application and work toward a field role.</p>
                    </div>
                    <i data-lucide="shield-check" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CALL TO ACTION --}}
    <section class="pt-closing-section">
        <div class="pt-closing">
            <div class="pt-closing-art" aria-hidden="true">
                <i data-lucide="orbit"></i>
            </div>

            <div class="pt-closing-content">
                <span class="pt-section-kicker">THE NETWORK IS WAITING</span>
                <h2>Ready for what<br>you might find?</h2>
                <p>
                    Get your account ready. The next investigation begins
                    with the right preparation.
                </p>

                <div class="pt-actions">
                    <a href="{{ route('register') }}" class="pt-button pt-closing-button">
                        Join Phantom Trade
                        <i data-lucide="arrow-up-right" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('login') }}" class="pt-closing-login">
                        Already registered? Sign in
                    </a>
                </div>
            </div>

            <div class="pt-closing-footer">
                <span>PHANTOM TRADE</span>
                <span>PARANORMAL OPERATIONS NETWORK</span>
            </div>
        </div>
    </section>

</div>

<style>
    .pt-landing {
        --pt-ink: #192238;
        --pt-muted: #69758b;
        --pt-border: #e3e8f0;
        --pt-purple: #7864bf;
        color: var(--pt-ink);
        overflow: hidden;
        background: #fff;
    }

    .pt-landing,
    .pt-landing * {
        box-sizing: border-box;
    }

    .pt-landing a {
        text-decoration: none;
    }

    .pt-landing h1,
    .pt-landing h2,
    .pt-landing h3,
    .pt-landing p {
        margin-top: 0;
    }

    .pt-landing .pt-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        flex: 0 0 auto;
        border-radius: 50%;
        background: #10a77a;
        box-shadow: 0 0 0 3px rgba(16, 167, 122, .12);
    }

    /* Hero */
    .pt-hero {
        position: relative;
        padding: 76px 32px 0;
        background:
            radial-gradient(ellipse at 78% 20%, rgba(196, 181, 253, .20), transparent 36%),
            radial-gradient(ellipse at 8% 65%, rgba(167, 243, 208, .16), transparent 30%),
            #fbfcfe;
        border-bottom: 1px solid var(--pt-border);
    }

    .pt-grid-pattern {
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .3;
        background-image:
            linear-gradient(rgba(148, 163, 184, .13) 1px, transparent 1px),
            linear-gradient(90deg, rgba(148, 163, 184, .13) 1px, transparent 1px);
        background-size: 42px 42px;
        -webkit-mask-image: linear-gradient(to bottom, #000, transparent 90%);
        mask-image: linear-gradient(to bottom, #000, transparent 90%);
    }

    .pt-hero-inner {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.04fr) minmax(340px, .86fr);
        align-items: center;
        gap: clamp(36px, 6vw, 88px);
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 76px;
    }

    .pt-hero-copy { max-width: 620px; }

    .pt-eyebrow,
    .pt-section-kicker,
    .pt-label {
        font-size: 10px;
        line-height: 1.5;
        font-weight: 800;
        letter-spacing: .15em;
        color: #667085;
    }

    .pt-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 8px 11px;
        margin-bottom: 26px;
        background: rgba(255, 255, 255, .85);
        border: 1px solid var(--pt-border);
        border-radius: 6px;
    }

    .pt-hero h1 {
        margin-bottom: 0;
        max-width: 680px;
        color: #151c2d;
        font-size: clamp(43px, 6vw, 76px);
        line-height: .99;
        letter-spacing: -.065em;
        font-weight: 800;
    }

    .pt-hero h1 span { color: var(--pt-purple); }

    .pt-hero-description {
        max-width: 520px;
        margin: 25px 0 29px;
        color: var(--pt-muted);
        font-size: 16px;
        line-height: 1.8;
    }

    .pt-actions {
        display: flex;
        align-items: center;
        gap: 11px;
        flex-wrap: wrap;
    }

    .pt-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 47px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 7px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 750;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .pt-button i { width: 16px; height: 16px; }

    .pt-button:hover { transform: translateY(-2px); }

    .pt-button-primary {
        color: #fff;
        background: #202b40;
        box-shadow: 0 7px 18px rgba(32, 43, 64, .14);
    }

    .pt-button-primary:hover {
        color: #fff;
        background: #303e57;
        box-shadow: 0 10px 24px rgba(32, 43, 64, .19);
    }

    .pt-button-secondary {
        color: #283247;
        background: rgba(255, 255, 255, .85);
        border-color: #dce2eb;
    }

    .pt-button-secondary:hover {
        color: #202b40;
        background: #fff;
    }

    .pt-note {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-top: 23px;
        color: #798397;
        font-size: 12px;
    }

    .pt-note i { width: 15px; height: 15px; color: var(--pt-purple); }

    /* Operations console */
    .pt-console {
        padding: 22px;
        background: rgba(255, 255, 255, .94);
        border: 1px solid #e0e5ee;
        border-radius: 13px;
        box-shadow: 0 22px 60px rgba(31, 41, 55, .09), 0 3px 9px rgba(31, 41, 55, .03);
    }

    .pt-console-header,
    .pt-console-brand,
    .pt-online,
    .pt-console-heading,
    .pt-console-item,
    .pt-console-footer,
    .pt-system-status {
        display: flex;
        align-items: center;
    }

    .pt-console-header,
    .pt-console-footer {
        justify-content: space-between;
        gap: 14px;
    }

    .pt-console-brand { gap: 11px; }

    .pt-console-logo {
        display: grid;
        place-items: center;
        width: 39px;
        height: 39px;
        color: #d9d1ff;
        background: #202b40;
        border-radius: 9px;
    }

    .pt-console-logo i { width: 20px; height: 20px; }

    .pt-console-brand strong {
        display: block;
        color: #202b40;
        font-size: 11px;
        font-weight: 850;
        letter-spacing: .09em;
    }

    .pt-console-brand span {
        display: block;
        margin-top: 3px;
        color: #929bad;
        font-size: 9px;
        letter-spacing: .12em;
    }

    .pt-online {
        gap: 8px;
        color: #07865e;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .1em;
    }

    .pt-rule {
        height: 1px;
        margin: 20px 0 22px;
        background: #edf0f5;
    }

    .pt-console-heading {
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 20px;
    }

    .pt-console-heading h2 {
        margin: 7px 0 0;
        color: #202b40;
        font-size: 19px;
        line-height: 1.35;
        letter-spacing: -.035em;
    }

    .pt-console-heading > i {
        width: 23px;
        height: 23px;
        color: #9ba5b7;
    }

    .pt-console-item {
        gap: 12px;
        padding: 13px 11px;
        margin-top: 9px;
        border: 1px solid #edf0f5;
        border-radius: 8px;
        transition: border-color .18s, transform .18s, background .18s;
    }

    .pt-console-item:hover {
        transform: translateX(2px);
        border-color: #d8ddea;
        background: #fbfcff;
    }

    .pt-item-icon,
    .pt-feature-icon {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        border-radius: 9px;
    }

    .pt-item-icon { width: 38px; height: 38px; }
    .pt-item-icon i { width: 18px; height: 18px; }

    .pt-purple { background: #f0edff; color: #7963c6; }
    .pt-green { background: #e8f8f1; color: #16845f; }
    .pt-amber { background: #fff4df; color: #b7791f; }

    .pt-item-copy { min-width: 0; flex: 1; }

    .pt-item-copy strong {
        display: block;
        color: #283247;
        font-size: 12px;
        font-weight: 750;
    }

    .pt-item-copy small {
        display: block;
        margin-top: 3px;
        color: #8a94a6;
        font-size: 11px;
    }

    .pt-item-arrow { width: 15px; height: 15px; color: #9ba5b7; }

    .pt-console-footer {
        padding-top: 18px;
        margin-top: 17px;
        border-top: 1px solid #edf0f5;
        color: #9ba3b2;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .1em;
    }

    .pt-system-status {
        gap: 8px;
        color: #16845f;
        font-size: 10px;
        letter-spacing: 0;
    }

    .pt-online .pt-dot,
    .pt-system-status .pt-dot {
        width: 6px;
        height: 6px;
        box-shadow: none;
    }

    .pt-hero-bottom {
        position: relative;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px 28px;
        max-width: 1180px;
        margin: 0 auto;
        padding: 17px 0;
        border-top: 1px solid #e8ebf1;
        color: #939bad;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .12em;
    }

    .pt-bottom-caption { margin-left: auto; color: #6e7890; }

    /* Feature section */
    .pt-section {
        max-width: 1244px;
        margin: 0 auto;
        padding: 86px 32px 30px;
    }

    .pt-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 32px;
        margin-bottom: 30px;
    }

    .pt-section-kicker { color: #8170be; }

    .pt-section-heading h2,
    .pt-journey-intro h2 {
        margin: 10px 0 0;
        color: #182237;
        font-size: clamp(27px, 3.5vw, 39px);
        line-height: 1.12;
        letter-spacing: -.05em;
        font-weight: 800;
    }

    .pt-section-heading > p {
        max-width: 410px;
        margin-bottom: 0;
        color: #727d90;
        font-size: 13px;
        line-height: 1.8;
    }

    .pt-feature-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
    }

    .pt-feature-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 270px;
        padding: 23px;
        color: inherit;
        background: #fff;
        border: 1px solid var(--pt-border);
        border-radius: 10px;
        transition: transform .18s, box-shadow .18s, border-color .18s;
    }

    .pt-feature-card:hover {
        transform: translateY(-4px);
        color: inherit;
        border-color: #cfc5f2;
        box-shadow: 0 15px 32px rgba(31, 41, 55, .07);
    }

    .pt-feature-card:focus-visible,
    .pt-console-item:focus-visible,
    .pt-landing .pt-button:focus-visible,
    .pt-landing .pt-text-link:focus-visible,
    .pt-landing .pt-closing-login:focus-visible {
        outline: 3px solid #9b8ae2;
        outline-offset: 4px;
    }

    .pt-feature-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pt-index {
        color: #9aa3b3;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .1em;
    }

    .pt-feature-icon { width: 42px; height: 42px; }
    .pt-feature-icon i { width: 20px; height: 20px; }

    .pt-feature-card h3 {
        margin: 23px 0 8px;
        color: #202b40;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .pt-feature-card p {
        margin: 0;
        color: #778195;
        font-size: 12.5px;
        line-height: 1.8;
    }

    .pt-feature-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: auto;
        padding-top: 23px;
    }

    .pt-feature-tag {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #8a94a6;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .1em;
    }

    .pt-feature-tag > span {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #8c7bc9;
    }

    .pt-feature-bottom > i {
        width: 16px;
        height: 16px;
        color: #8e98ab;
        transition: transform .18s, color .18s;
    }

    .pt-feature-card:hover .pt-feature-bottom > i {
        color: #7864bf;
        transform: translate(2px, -2px);
    }

    .pt-card-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 18px 0 0;
        color: #929bad;
        font-size: 11px;
    }

    .pt-card-hint i { width: 14px; height: 14px; }

    /* Getting started */
    .pt-journey {
        margin-top: 72px;
        padding: 70px 32px;
        background: #f3f5f9;
        border-top: 1px solid #e8ebf1;
        border-bottom: 1px solid #e8ebf1;
    }

    .pt-journey-inner {
        display: grid;
        grid-template-columns: .9fr 1.1fr;
        align-items: center;
        gap: clamp(38px, 7vw, 100px);
        max-width: 1116px;
        margin: 0 auto;
    }

    .pt-journey-intro h2 {
        font-size: clamp(28px, 3.6vw, 40px);
    }

    .pt-journey-intro > p {
        max-width: 390px;
        margin: 16px 0 0;
        color: #737e91;
        font-size: 13px;
        line-height: 1.85;
    }

    .pt-text-link {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-top: 22px;
        color: #5544a0;
        font-size: 13px;
        font-weight: 800;
    }

    .pt-text-link i {
        width: 16px;
        height: 16px;
        transition: transform .18s;
    }

    .pt-text-link:hover i { transform: translateX(3px); }

    .pt-steps {
        padding: 5px 20px;
        background: #fff;
        border: 1px solid #e2e7ef;
        border-radius: 11px;
    }

    .pt-step {
        display: grid;
        grid-template-columns: 35px minmax(0, 1fr) 22px;
        align-items: center;
        gap: 14px;
        padding: 20px 0;
    }

    .pt-step + .pt-step { border-top: 1px solid #edf0f5; }

    .pt-step-number {
        color: #8979c9;
        font-size: 11px;
        font-weight: 850;
    }

    .pt-step h3 {
        margin: 0;
        color: #283247;
        font-size: 13px;
    }

    .pt-step p {
        margin: 4px 0 0;
        color: #818b9c;
        font-size: 11.5px;
        line-height: 1.7;
    }

    .pt-step > i { width: 19px; height: 19px; color: #9aa3b3; }

    /* Final call to action */
    .pt-closing-section { padding: 82px 32px 0; }

    .pt-closing {
        position: relative;
        max-width: 1116px;
        min-height: 300px;
        overflow: hidden;
        margin: 0 auto;
        padding: 49px 54px 20px;
        color: #fff;
        background:
            radial-gradient(ellipse at 87% 25%, rgba(128, 105, 202, .22), transparent 32%),
            linear-gradient(120deg, #202b40, #172033 70%);
        border-radius: 13px;
    }

    .pt-closing-art {
        position: absolute;
        top: 50%;
        right: 9%;
        color: rgba(221, 215, 255, .10);
        transform: translateY(-50%);
    }

    .pt-closing-art i {
        width: clamp(130px, 19vw, 230px);
        height: auto;
        stroke-width: .7;
    }

    .pt-closing-content {
        position: relative;
        z-index: 1;
        max-width: 570px;
    }

    .pt-closing-content .pt-section-kicker { color: #c0b4fa; }

    .pt-closing-content h2 {
        margin: 12px 0 0;
        font-size: clamp(31px, 4.5vw, 48px);
        line-height: 1.03;
        letter-spacing: -.055em;
        font-weight: 800;
    }

    .pt-closing-content p {
        max-width: 430px;
        margin: 15px 0 23px;
        color: #bdc6d5;
        font-size: 13px;
        line-height: 1.8;
    }

    .pt-closing-button {
        color: #29233e;
        background: #f5f3ff;
    }

    .pt-closing-button:hover {
        color: #29233e;
        background: #fff;
        box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
    }

    .pt-closing-login {
        padding: 10px;
        color: #d1d8e5;
        font-size: 12px;
        font-weight: 650;
    }

    .pt-closing-login:hover { color: #fff; }

    .pt-closing-footer {
        position: relative;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 36px;
        padding-top: 15px;
        color: #98a4b8;
        border-top: 1px solid rgba(255, 255, 255, .13);
        font-size: 9px;
        font-weight: 750;
        letter-spacing: .12em;
    }

    /* Responsive layout */
    @media (max-width: 900px) {
        .pt-hero-inner {
            grid-template-columns: 1fr;
            max-width: 690px;
            gap: 42px;
        }

        .pt-hero-copy { max-width: 660px; }
        .pt-console { width: 100%; max-width: 600px; }

        .pt-section-heading {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
        }

        .pt-section-heading > p { max-width: 620px; }

        .pt-journey-inner {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .pt-journey-intro > p { max-width: 600px; }
    }

    @media (max-width: 640px) {
        .pt-hero { padding: 52px 20px 0; }

        .pt-hero-inner {
            padding-bottom: 48px;
            gap: 32px;
        }

        .pt-hero h1 { font-size: clamp(43px, 12vw, 62px); }

        .pt-hero-description {
            margin-top: 20px;
            font-size: 14px;
        }

        .pt-actions { align-items: stretch; }
        .pt-actions .pt-button { flex: 1 1 auto; }

        .pt-console { padding: 16px; }
        .pt-console-heading h2 { font-size: 17px; }

        .pt-hero-bottom { gap: 12px 18px; font-size: 8px; }
        .pt-bottom-caption { width: 100%; margin-left: 0; }

        .pt-section { padding: 60px 20px 20px; }
        .pt-feature-grid { grid-template-columns: 1fr; }
        .pt-feature-card { min-height: 0; }

        .pt-journey {
            margin-top: 52px;
            padding: 50px 20px;
        }

        .pt-steps { padding: 4px 14px; }

        .pt-step {
            grid-template-columns: 27px minmax(0, 1fr) 19px;
            gap: 10px;
        }

        .pt-closing-section { padding: 56px 20px 0; }

        .pt-closing { padding: 35px 23px 18px; }

        .pt-closing-art {
            top: 23px;
            right: -20px;
            transform: none;
        }

        .pt-closing-art i { width: 100px; }
        .pt-closing-content h2 { font-size: 36px; }

        .pt-closing-footer {
            flex-direction: column;
            gap: 6px;
            font-size: 8px;
        }

        .pt-closing-login { padding-left: 0; }
    }

    @media (prefers-reduced-motion: reduce) {
        .pt-landing *,
        .pt-landing *::before,
        .pt-landing *::after {
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection
