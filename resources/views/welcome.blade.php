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
            background: var(--bg);
            border-top: 1px solid var(--divider);
            color: var(--text-sec);
            padding: 2.5rem 5%; text-align: center;
            font-size: .85rem;
        }
        footer a { color: var(--primary); text-decoration: none; }
        .footer-logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 1.4rem;
            color: var(--primary);
            display: block; margin-bottom: .75rem;
        }

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
        <span class="hero-badge"><i data-lucide="activity" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:4px"></i> Fitness Tracker App</span>
        <h1>Track. Analyze.<br>Share.</h1>
        <p>FlexBatir membantu kamu mencatat setiap aktivitas, menganalisis performa, dan berbagi pencapaian bersama komunitas pelari dan pesepeda.</p>
        <div class="hero-cta">
            <a href="#fitur" class="btn btn-primary">Lihat Fitur</a>
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
        <h2 class="section-title">Semua yang kamu butuhkan<br>untuk fitness tracking</h2>
        <p class="section-sub">Dari pencatatan aktivitas manual hingga analisis statistik mingguan &mdash; semua tersedia di FlexBatir.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="activity"></i></div>
                <h3>Lacak Aktivitas</h3>
                <p>Catat lari, bersepeda, hiking, renang, dan banyak lagi. Simpan jarak, durasi, kalori, dan elevasi secara akurat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="bar-chart-3"></i></div>
                <h3>Statistik &amp; Grafik</h3>
                <p>Visualisasi progres mingguan dan bulanan. Lihat tren jarak, durasi, dan kalori dalam chart yang informatif.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="map"></i></div>
                <h3>Jalur Tersimpan</h3>
                <p>Simpan rute favoritmu. Bagikan ke komunitas atau jadikan template untuk sesi latihan berikutnya.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="trophy"></i></div>
                <h3>Segmen &amp; Leaderboard</h3>
                <p>Bersaing di segmen populer. Lihat posisimu di leaderboard global maupun mingguan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="target"></i></div>
                <h3>Target &amp; Goals</h3>
                <p>Buat target jarak, durasi, atau kalori. Pantau progress secara real-time dan raih setiap pencapaian.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="users"></i></div>
                <h3>Sosial &amp; Komunitas</h3>
                <p>Ikuti teman, beri like, komentar aktivitas, gabung klub, dan ikut challenge bersama.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="calendar"></i></div>
                <h3>Kalender Latihan</h3>
                <p>Rencanakan sesi latihan ke depan. Tandai sesi yang selesai, dilewati, atau perlu dijadwal ulang.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="bell"></i></div>
                <h3>Notifikasi</h3>
                <p>Dapatkan notifikasi saat ada yang mengikutimu, menyukai aktivitasmu, atau mengajakmu bergabung ke klub.</p>
            </div>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section id="cara-kerja" style="background: var(--surface); border-top: 1px solid var(--divider); border-bottom: 1px solid var(--divider);">
    <div style="max-width:1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
        <div>
            <div class="section-label">Cara Kerja</div>
            <h2 class="section-title">Mulai dalam<br>beberapa langkah</h2>
            <p class="section-sub">FlexBatir dirancang untuk kemudahan. Download app, buat akun, dan langsung mulai tracking.</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h3>Buat Akun</h3>
                    <p>Daftar dengan email dan mulai perjalanan fitness kamu. Gratis selamanya.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h3>Catat Aktivitas</h3>
                    <p>Rekam aktivitas manual atau upload file GPX dari perangkat GPS kamu.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h3>Analisis Performa</h3>
                    <p>Lihat statistik, grafik tren, dan bandingkan performa antar minggu.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-content">
                    <h3>Bagikan ke Komunitas</h3>
                    <p>Post aktivitas ke feed, ikut challenge, dan bersaing di leaderboard.</p>
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
                <h2>Bangun di atas<br>FlexBatir API</h2>
                <p>Semua fitur tersedia via REST API dengan autentikasi Sanctum. Cocok untuk integrasi dengan perangkat wearable, web app, atau proyek riset.</p>
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
        <h2>Siap mulai tracking?</h2>
        <p>Bergabung dengan komunitas FlexBatir dan mulai catat setiap langkah perjalanan fitnessmu hari ini.</p>
        <div class="hero-cta">
            <a href="https://flexbatir.web.id/api" class="btn btn-primary" target="_blank">Explore API</a>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer>
    <span class="footer-logo">FlexBatir</span>
    <p>Built with Laravel &amp; Flutter &mdash; <a href="https://github.com/pariiee/web-flexbatir" target="_blank">GitHub</a></p>
    <p style="margin-top: .5rem;">API: <a href="https://flexbatir.web.id/api">flexbatir.web.id/api</a></p>
</footer>

<script>
    lucide.createIcons();
</script>
</body>
</html>
