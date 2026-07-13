<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FlexBatir — aplikasi fitness tracker untuk mencatat, menganalisis, dan berbagi aktivitasmu bersama komunitas.">
    <title>FlexBatir — Track. Analyze. Share.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #B5FF2D;
            --bg:        #0A0A0A;
            --surface:   #1A1A1A;
            --text:      #FFFFFF;
            --text-sec:  #888888;
            --divider:   #2A2A2A;
            --shadow-card: 0 2px 16px rgba(0,0,0,.4);
            --shadow-glow: 0 0 24px rgba(181,255,45,.15);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        /* -- NAV -- */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,10,10,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--divider);
            padding: 0 5%;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px;
        }
        .nav-logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -.02em;
        }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a {
            color: var(--text-sec); text-decoration: none; font-size: .9rem; font-weight: 500;
            transition: color .2s;
        }
        .nav-links a:hover { color: var(--primary); }
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .65rem 1.4rem; border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: .9rem; cursor: pointer;
            border: none; transition: transform .15s, box-shadow .15s;
            text-decoration: none;
        }
        .btn:active { transform: translateY(1px); }
        .btn-primary {
            background: var(--primary);
            color: #000;
            box-shadow: var(--shadow-glow);
        }
        .btn-primary:hover { box-shadow: 0 0 32px rgba(181,255,45,.3); }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover { background: rgba(181,255,45,.08); }

        /* -- HERO -- */
        .hero {
            min-height: calc(100vh - 64px);
            display: flex; align-items: center; justify-content: center;
            padding: 5% 5%;
            text-align: center;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(181,255,45,.06) 0%, transparent 70%);
        }
        .hero-inner { max-width: 760px; }
        .hero-badge {
            display: inline-block;
            background: rgba(181,255,45,.1); color: var(--primary);
            border: 1px solid rgba(181,255,45,.25);
            border-radius: 999px; padding: .3rem 1rem;
            font-size: .8rem; font-weight: 600; letter-spacing: .06em;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: clamp(2.8rem, 7vw, 5rem);
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 1.25rem;
            letter-spacing: -.03em;
        }
        .hero h1 span { color: var(--primary); }
        .hero p {
            font-size: clamp(1rem, 2.5vw, 1.2rem);
            color: var(--text-sec); max-width: 560px; margin: 0 auto 2.5rem;
        }
        .hero-cta { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .hero-stats {
            display: flex; gap: 2.5rem; justify-content: center; flex-wrap: wrap;
            margin-top: 4rem;
            padding-top: 2.5rem;
            border-top: 1px solid var(--divider);
        }
        .stat-item { text-align: center; }
        .stat-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2rem; font-weight: 600;
            color: var(--primary);
        }
        .stat-label { font-size: .8rem; color: var(--text-sec); margin-top: .25rem; }

        /* -- SECTION -- */
        section { padding: 6rem 5%; }
        .section-label {
            font-size: .8rem; font-weight: 700; letter-spacing: .1em;
            color: var(--primary); text-transform: uppercase; margin-bottom: .75rem;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            margin-bottom: 1rem; line-height: 1.2;
            letter-spacing: -.02em;
        }
        .section-sub { color: var(--text-sec); max-width: 560px; font-size: 1.05rem; }

        /* -- FEATURES -- */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem; margin-top: 3rem;
        }
        .feature-card {
            background: var(--surface);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--divider);
            transition: transform .2s, border-color .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(181,255,45,.25);
        }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: rgba(181,255,45,.1);
            color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
        }
        .feature-card h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: .5rem; color: var(--text); }
        .feature-card p { font-size: .9rem; color: var(--text-sec); line-height: 1.6; }

        /* -- HOW IT WORKS -- */
        .steps { display: flex; flex-direction: column; gap: 0; margin-top: 3rem; max-width: 680px; }
        .step {
            display: flex; gap: 1.5rem; align-items: flex-start;
            padding-bottom: 2.5rem; position: relative;
        }
        .step:not(:last-child)::before {
            content: ''; position: absolute;
            left: 22px; top: 48px; bottom: 0; width: 2px;
            background: var(--divider);
        }
        .step-num {
            min-width: 44px; height: 44px; border-radius: 50%;
            background: var(--primary);
            color: #000; font-weight: 800; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step-content h3 { font-weight: 700; margin-bottom: .4rem; color: var(--text); }
        .step-content p { font-size: .9rem; color: var(--text-sec); }

        /* -- API SECTION -- */
        .api-section {
            background: var(--surface);
            border: 1px solid var(--divider);
            border-radius: 24px; padding: 3rem; color: var(--text);
            display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;
        }
        @media (max-width: 700px) { .api-section { grid-template-columns: 1fr; } }
        .api-section h2 { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 2rem; margin-bottom: 1rem; }
        .api-section p { color: var(--text-sec); margin-bottom: 1.5rem; }
        .code-block {
            background: var(--bg);
            border: 1px solid var(--divider);
            border-radius: 14px; padding: 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: .8rem; line-height: 1.8; color: var(--text-sec);
            overflow-x: auto;
        }
        .code-key   { color: var(--primary); }
        .code-str   { color: #88c0d0; }
        .code-num   { color: #7dd3fc; }
        .code-bool  { color: #86efac; }

        /* -- CTA -- */
        .cta-section {
            text-align: center;
            background: var(--surface);
            border-top: 1px solid var(--divider);
        }
        .cta-section h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: clamp(2rem, 5vw, 3.2rem); margin-bottom: 1rem;
            letter-spacing: -.02em;
        }
        .cta-section p { color: var(--text-sec); max-width: 480px; margin: 0 auto 2.5rem; }

        /* -- FOOTER -- */
        footer {
            background: #111111;
            border-top: 1px solid var(--divider);
            color: var(--text-sec);
            padding: 4rem 5% 2rem;
            font-size: .85rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr 1fr 1.4fr;
            gap: 2.5rem;
            max-width: 1100px;
            margin: 0 auto 3rem;
        }
        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 500px) {
            .footer-grid { grid-template-columns: 1fr; }
        }
        .footer-brand .footer-logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 1.4rem;
            color: var(--primary);
            display: block; margin-bottom: .4rem;
        }
        .footer-brand .footer-tagline {
            font-size: .8rem; color: var(--text-sec);
            margin-bottom: 1rem; font-weight: 500;
        }
        .footer-brand p {
            font-size: .82rem; color: var(--text-sec);
            line-height: 1.7; max-width: 240px;
        }
        .footer-col h4 {
            font-size: .8rem; font-weight: 700;
            color: var(--text); letter-spacing: .06em;
            text-transform: uppercase; margin-bottom: 1rem;
        }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: .55rem; }
        .footer-col ul a {
            color: var(--text-sec); text-decoration: none;
            font-size: .82rem; transition: color .2s;
        }
        .footer-col ul a:hover { color: var(--primary); }
        .footer-contact-item {
            display: flex; align-items: center; gap: .6rem;
            color: var(--text-sec); font-size: .82rem;
            margin-bottom: .6rem;
        }
        .footer-contact-item svg { color: var(--primary); flex-shrink: 0; }
        .footer-contact-item a { color: var(--text-sec); text-decoration: none; }
        .footer-contact-item a:hover { color: var(--primary); }
        .footer-bottom {
            border-top: 1px solid var(--divider);
            padding-top: 1.5rem;
            max-width: 1100px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 1rem;
            font-size: .78rem; color: var(--text-sec);
        }
        .footer-bottom a { color: var(--primary); text-decoration: none; }

        @media (max-width: 600px) {
            .nav-links { display: none; }
            .hero-stats { gap: 1.5rem; }
        }
    </style>
</head>
<body>

{{-- NAV --}}
<nav>
    <a href="/" class="nav-logo">FlexBatir</a>
    <ul class="nav-links">
        <li><a href="#fitur">Fitur</a></li>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#api">API</a></li>
    </ul>
    <a href="https://flexbatir.web.id/api" class="btn btn-outline" target="_blank">Docs API</a>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-inner">
        <span class="hero-badge"><i data-lucide="zap" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px"></i> Buat yang males jadi iri</span>
        <h1>Flex. Grind.<br><span>Pamer.</span></h1>
        <p>Bukan cuma tracker biasa. FlexBatir adalah tempat kamu buktiin ke dunia bahwa kamu lebih rajin dari mereka.</p>
        <div class="hero-cta">
            <a href="#fitur" class="btn btn-primary">Mulai Flex Sekarang</a>
            <a href="https://flexbatir.web.id/api" class="btn btn-outline" target="_blank">Explore API</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-value">REST</div>
                <div class="stat-label">API Architecture</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">JWT</div>
                <div class="stat-label">Auth via Sanctum</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">6+</div>
                <div class="stat-label">Sport Types</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">GPX</div>
                <div class="stat-label">Route Import</div>
            </div>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section id="fitur">
    <div style="max-width:1100px; margin: 0 auto;">
        <div class="section-label">Fitur Unggulan</div>
        <h2 class="section-title">Semua senjata<br>buat bikin orang iri</h2>
        <p class="section-sub">Dari GPS tracking sampai leaderboard — semua ada buat kamu buktiin siapa yang paling rajin.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="activity"></i></div>
                <h3>Lacak Aktivitas</h3>
                <p>Catat lari, sepeda, hiking, renang — semua masuk. Jarak, durasi, kalori, elevasi. Ga ada yang terlewat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="bar-chart-3"></i></div>
                <h3>Statistik & Grafik</h3>
                <p>Lihat progresmu dalam grafik yang bikin kamu makin semangat. Atau makin malu kalau males.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="map"></i></div>
                <h3>Rute Favorit</h3>
                <p>Simpan rute andalanmu. Pamer ke komunitas atau tantang teman buat ngalahin waktumu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="trophy"></i></div>
                <h3>Segmen & Leaderboard</h3>
                <p>Ada yang lebih cepat dari kamu? Ga mungkin. Buktiin di leaderboard global dan mingguan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="target"></i></div>
                <h3>Target & Goals</h3>
                <p>Set target, kejar, capai. Terus bikin yang baru. Batas itu cuma ada di pikiran orang yang males.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="users"></i></div>
                <h3>Sosial & Komunitas</h3>
                <p>Follow teman, like aktivitas, join klub, ikut challenge. Olahraga lebih seru kalau ada yang ngelihat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="calendar"></i></div>
                <h3>Kalender Latihan</h3>
                <p>Plan sesimu jauh-jauh hari. Konsistensi adalah flex yang paling kuat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="bell"></i></div>
                <h3>Notifikasi</h3>
                <p>Tau langsung kalau ada yang follow kamu, like aktivitasmu, atau ngajak gabung klub.</p>
            </div>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section id="cara-kerja" style="background: var(--surface); border-top: 1px solid var(--divider); border-bottom: 1px solid var(--divider);">
    <div style="max-width:1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
        <div>
            <div class="section-label">Cara Kerja</div>
            <h2 class="section-title">4 langkah<br>jadi yang terdepan</h2>
            <p class="section-sub">Setup cepat, langsung gas. Ga perlu tutorial panjang-panjang.</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h3>Buat Akun</h3>
                    <p>Daftar gratis. Selamanya. Ga ada yang disembunyiin di balik paywall.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h3>Catat Aktivitas</h3>
                    <p>Rekam manual atau upload GPX. Semua masuk, semua tercatat, ga ada yang terlewat.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h3>Lihat Progresmu</h3>
                    <p>Statistik lengkap, grafik tren, performa minggu ke minggu. Bukti nyata kerja kerasmu.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-content">
                    <h3>Pamer ke Semua Orang</h3>
                    <p>Post ke feed, ikut challenge, dominasi leaderboard. Biar pada tau siapa yang paling rajin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- API --}}
<section id="api">
    <div style="max-width:1100px; margin: 0 auto;">
        <div class="api-section">
            <div>
                <div style="color: var(--primary); font-size: .8rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; margin-bottom: .75rem;">REST API</div>
                <h2>API buat yang<br>mau build di atas FlexBatir</h2>
                <p>Semua fitur tersedia via REST API. Wearable, web app, proyek riset — tinggal connect dan gas.</p>
                <a href="https://flexbatir.web.id/api" class="btn btn-primary" target="_blank">Lihat Endpoint</a>
            </div>
            <div class="code-block">
<span class="code-key">GET</span> /api/activities<br>
<span class="code-key">Authorization:</span> <span class="code-str">Bearer {token}</span><br><br>
{<br>
&nbsp;&nbsp;<span class="code-key">"data"</span>: [<br>
&nbsp;&nbsp;&nbsp;&nbsp;{<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"id"</span>: <span class="code-num">42</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"title"</span>: <span class="code-str">"Morning Run"</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"type"</span>: <span class="code-str">"run"</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"distance"</span>: <span class="code-num">8420</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"duration"</span>: <span class="code-num">2640</span>,<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-key">"is_public"</span>: <span class="code-bool">true</span><br>
&nbsp;&nbsp;&nbsp;&nbsp;}<br>
&nbsp;&nbsp;]<br>
}
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div style="max-width: 680px; margin: 0 auto;">
        <h2>Masih nunggu apa lagi?</h2>
        <p>Orang lain udah lari 10km pagi ini. Kamu masih scroll. Yuk mulai sekarang — gratis, ga ada excuse.</p>
        <div class="hero-cta">
            <a href="https://flexbatir.web.id/api" class="btn btn-primary" target="_blank">Gas Sekarang</a>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer>
    <div class="footer-grid">
        {{-- Brand --}}
        <div class="footer-brand">
            <span class="footer-logo">FlexBatir</span>
            <div class="footer-tagline">Track. Analyze. Share.</div>
            <p>Aplikasi fitness tracker untuk mencatat, menganalisis, dan berbagi aktivitasmu bersama komunitas.</p>
        </div>

        {{-- Company --}}
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>

        {{-- Resources --}}
        <div class="footer-col">
            <h4>Resources</h4>
            <ul>
                <li><a href="https://flexbatir.web.id/api" target="_blank">API Docs</a></li>
                <li><a href="https://github.com/pariiee/web-flexbatir" target="_blank">GitHub</a></li>
                <li><a href="#">Changelog</a></li>
                <li><a href="#">Status</a></li>
            </ul>
        </div>

        {{-- Programs --}}
        <div class="footer-col">
            <h4>Programs</h4>
            <ul>
                <li><a href="#">Challenges</a></li>
                <li><a href="#">Leaderboard</a></li>
                <li><a href="#">Clubs</a></li>
                <li><a href="#">Goals</a></li>
            </ul>
        </div>

        {{-- Contact --}}
        <div class="footer-col">
            <h4>Contact Us</h4>
            <div class="footer-contact-item">
                <i data-lucide="mail" style="width:15px;height:15px"></i>
                <a href="mailto:hello@flexbatir.web.id">hello@flexbatir.web.id</a>
            </div>
            <div class="footer-contact-item">
                <i data-lucide="globe" style="width:15px;height:15px"></i>
                <a href="https://flexbatir.web.id" target="_blank">flexbatir.web.id</a>
            </div>
            <div class="footer-contact-item">
                <i data-lucide="github" style="width:15px;height:15px"></i>
                <a href="https://github.com/pariiee" target="_blank">@pariiee</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} FlexBatir. Built with Laravel &amp; Flutter.</span>
        <span>Made with <span style="color:var(--primary)">♥</span> by <a href="https://github.com/pariiee" target="_blank">pariiee</a></span>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>
</body>
</html>
