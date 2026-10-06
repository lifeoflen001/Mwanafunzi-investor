<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101c24">
    <meta name="font-family" content="DM Sans">
    <title>{{ $title }} — Mwanafunzi Investor</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
    <style>
        :root { --error-night:#101c24; --error-ink:#121c24; --error-paper:#f6f3ed; --error-copper:#ff6a00; --error-muted:#aeb8b9; }
        * { box-sizing:border-box; }
        html,body { min-height:100%; }
        body { margin:0; background:var(--error-night); color:var(--error-paper); font-family:'DM Sans',Arial,sans-serif; }
        .error-page { position:relative; display:grid; min-height:100vh; overflow:hidden; padding:clamp(28px,5vw,70px); background:var(--error-night); }
        .error-page::before { content:''; position:absolute; inset:0; opacity:.72; background:radial-gradient(circle at 85% 22%,rgba(255,106,0,.16),transparent 30%),linear-gradient(135deg,rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(45deg,rgba(255,255,255,.02) 1px,transparent 1px); background-size:auto,76px 76px,76px 76px; }
        .error-page::after { content:''; position:absolute; right:-12vw; bottom:-27vw; width:55vw; height:55vw; border:1px solid rgba(255,106,0,.22); border-radius:50%; box-shadow:0 0 0 28px rgba(255,106,0,.035),0 0 0 58px rgba(255,106,0,.02); }
        .error-shell { position:relative; z-index:1; display:flex; width:min(100%,1320px); min-height:calc(100vh - clamp(56px,10vw,140px)); margin:0 auto; flex-direction:column; justify-content:space-between; }
        .error-header { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; }
        .error-logo { display:block; width:min(220px,48vw); height:auto; max-height:52px; object-fit:contain; object-position:left center; }
        .error-status { display:inline-flex; align-items:center; gap:9px; padding:8px 11px; border:1px solid rgba(255,255,255,.18); color:var(--error-muted); font-size:11px; }
        .error-status i { width:7px; height:7px; border-radius:50%; background:var(--error-copper); box-shadow:0 0 0 4px rgba(255,106,0,.13); }
        .error-content { max-width:780px; padding:clamp(70px,12vh,150px) 0; }
        .error-eyebrow { display:flex; align-items:center; gap:12px; margin:0 0 24px; color:var(--error-copper); font:600 11px var(--sans, 'DM Sans', Arial, sans-serif); letter-spacing:.13em; text-transform:uppercase; }
        .error-eyebrow::before { content:''; width:36px; height:1px; background:currentColor; }
        .error-content h1 { max-width:760px; margin:0 0 22px; color:var(--error-paper); font:600 clamp(43px,7vw,92px)/.96 'DM Sans',Arial,sans-serif; letter-spacing:-.075em; }
        .error-message { max-width:540px; margin:0; color:var(--error-muted); font-size:clamp(15px,1.5vw,19px); line-height:1.65; }
        .error-button { display:inline-flex; align-items:center; gap:18px; min-height:50px; margin-top:32px; padding:0 20px; background:var(--error-copper); color:#fff; font-size:12px; font-weight:700; text-decoration:none; transition:background .18s ease,transform .18s ease; }
        .error-button:hover { background:#d95300; transform:translateY(-2px); }
        .error-button:focus-visible { outline:2px solid #fff; outline-offset:4px; }
        .error-footer { display:flex; justify-content:space-between; gap:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,.14); color:rgba(174,184,185,.75); font-size:11px; }
        .error-footer strong { color:var(--error-copper); font-weight:600; letter-spacing:.08em; text-transform:uppercase; }
        @media (max-width:600px) { .error-page { padding:24px 20px; }.error-shell { min-height:calc(100vh - 48px); }.error-header { align-items:flex-start; flex-direction:column; }.error-status { align-self:flex-start; }.error-content { padding:75px 0 95px; }.error-content h1 { font-size:clamp(40px,13vw,62px); }.error-footer { align-items:flex-start; flex-direction:column; gap:8px; }.error-page::after { right:-42vw; bottom:-25vw; width:90vw; height:90vw; } }
    </style>
</head>
<body>
    <main class="error-page">
        <div class="error-shell">
            <header class="error-header">
                <a href="{{ route('home') }}" aria-label="Mwanafunzi Investor home"><img class="error-logo" src="{{ asset('images/brand/mwanafunzi-logo-light.png') }}" alt="Mwanafunzi Investor"></a>
                <span class="error-status"><i aria-hidden="true"></i> Mwanafunzi Investor</span>
            </header>
            <section class="error-content" aria-labelledby="error-heading">
                <p class="error-eyebrow">{{ $eyebrow }}</p>
                <h1 id="error-heading">{{ $heading }}</h1>
                <p class="error-message">{{ $message }}</p>
                <a class="error-button" href="{{ route('home') }}">Return home <span aria-hidden="true">↗</span></a>
            </section>
            <footer class="error-footer"><span>Student of Money. Systems. Discipline.</span><strong>Always a Mwanafunzi.</strong></footer>
        </div>
    </main>
</body>
</html>
