<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FlexBatir — aplikasi fitness tracker untuk mencatat, menganalisis, dan berbagi aktivitasmu bersama komunitas.">
    <title>FlexBatir — Track. Analyze. Share.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Germania+One&family=Roboto:wght@400;500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #FA3C00;
            --secondary: #F08321;
            --bg:        #F9FAFB;
            --surface:   #FFFFFF;
            --text:      #1A1A1A;
            --text-sec:  #6B7280;
            --divider:   #E5E7EB;
            --shadow-raised: 4px 4px 8px rgba(0,0,0,.12), -2px -2px 6px rgba(255,255,255,.9);
            --shadow-card:   0 2px 12px rgba(0,0,0,.08);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        /* -- NAV -- */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(249,250,251,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--divider);
            padding: 0 5%;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px;
        }
        .nav-logo {
            font-family: 'Germania One', serif;
            font-size: 1.6rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none;
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
            font-weight: 600; font-size: .9rem; cursor: pointer;
            border: none; transition: transform .15s, box-shadow .15s;
        }
        .btn:active { transform: translateY(1px); }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            box-shadow: var(--shadow-raised);
        }
        .btn-primary:hover { box-shadow: 6px 6px 12px rgba(0,0,0,.18), -3px -3px 8px rgba(255,255,255,.95); }
        .btn-outline {
            background: var(--surface); color: var(--primary);
            border: 2px solid var(--primary);
            box-shadow: var(--shadow-card);
        }

        /* -- HERO -- */
        .hero {
            min-height: calc(100vh - 64px);
            display: flex; align-items: center; justify-content: center;
            padding: 5% 5%;
            text-align: center;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(250,60,0,.08) 0%, transparent 70%);
        }
        .hero-inner { max-width: 760px; }
        .hero-badge {
            display: inline-block;
            background: rgba(250,60,0,.1); color: var(--primary);
            border: 1px solid rgba(250,60,0,.25);
            border-radius: 999px; padding: .3rem 1rem;
            font-size: .8rem; font-weight: 600; letter-spacing: .06em;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-family: 'Germania One', serif;
            font-size: clamp(2.8rem, 7vw, 5rem);
            line-height: 1.1;
            background: linear-gradient(135deg, var(--primary) 20%, var(--secondary) 80%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 1.25rem;
        }
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
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .stat-label { font-size: .8rem; color: var(--text-sec); margin-top: .25rem; }

        /* -- SECTION -- */
        section { padding: 6rem 5%; }
        .section-label {
            font-size: .8rem; font-weight: 700; letter-spacing: .1em;
            color: var(--primary); text-transform: uppercase; margin-bottom: .75rem;
        }
        .section-title {
            font-family: 'Germania One', serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            margin-bottom: 1rem; line-height: 1.2;
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
            transition: transform .2s, box-shadow .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
        }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, rgba(250,60,0,.12), rgba(240,131,33,.12));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 1.25rem;
        }
        .feature-card h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: .5rem; }
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
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            opacity: .25;
        }
        .step-num {
            min-width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff; font-weight: 700; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: var(--shadow-raised); flex-shrink: 0;
        }
        .step-content h3 { font-weight: 700; margin-bottom: .4rem; }
        .step-content p { font-size: .9rem; color: var(--text-sec); }

        /* -- API SECTION -- */
        .api-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1a00 100%);
            border-radius: 24px; padding: 3rem; color: #fff;
            display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;
        }
        @media (max-width: 700px) { .api-section { grid-template-columns: 1fr; } }
        .api-section h2 { font-family: 'Germania One', serif; font-size: 2rem; margin-bottom: 1rem; }
        .api-section p { color: rgba(255,255,255,.7); margin-bottom: 1.5rem; }
        .code-block {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 14px; padding: 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: .8rem; line-height: 1.8; color: rgba(255,255,255,.85);
            overflow-x: auto;
        }
        .code-key   { color: #FA3C00; }
        .code-str   { color: #F08321; }
        .code-num   { color: #7dd3fc; }
        .code-bool  { color: #86efac; }

        /* -- CTA -- */
        .cta-section {
            text-align: center;
            background: linear-gradient(135deg, rgba(250,60,0,.06), rgba(240,131,33,.06));
            border-top: 1px solid var(--divider);
        }
        .cta-section h2 {
            font-family: 'Germania One', serif;
            font-size: clamp(2rem, 5vw, 3.2rem); margin-bottom: 1rem;
        }
        .cta-section p { color: var(--text-sec); max-width: 480px; margin: 0 auto 2.5rem; }

        /* -- FOOTER -- */
        footer {
            background: #1a1a1a; color: rgba(255,255,255,.5);
            padding: 2.5rem 5%; text-align: center;
            font-size: .85rem;
        }
        footer a { color: var(--secondary); text-decoration: none; }
        .footer-logo {
            font-family: 'Germania One', serif; font-size: 1.4rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
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
        <span class="hero-badge">&#x1F3C3; Fitness Tracker App</span>
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
                <div class="feature-icon">&#x1F3C3;</div>
                <h3>Lacak Aktivitas</h3>
                <p>Catat lari, bersepeda, hiking, renang, dan banyak lagi. Simpan jarak, durasi, kalori, dan elevasi secara akurat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F4CA;</div>
                <h3>Statistik &amp; Grafik</h3>
                <p>Visualisasi progres mingguan dan bulanan. Lihat tren jarak, durasi, dan kalori dalam chart yang informatif.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F5FA;&#xFE0F;</div>
                <h3>Jalur Tersimpan</h3>
                <p>Simpan rute favoritmu. Bagikan ke komunitas atau jadikan template untuk sesi latihan berikutnya.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F3C6;</div>
                <h3>Segmen &amp; Leaderboard</h3>
                <p>Bersaing di segmen populer. Lihat posisimu di leaderboard global maupun mingguan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F3AF;</div>
                <h3>Target &amp; Goals</h3>
                <p>Buat target jarak, durasi, atau kalori. Pantau progress secara real-time dan raih setiap pencapaian.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F465;</div>
                <h3>Sosial &amp; Komunitas</h3>
                <p>Ikuti teman, beri like, komentar aktivitas, gabung klub, dan ikut challenge bersama.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F4C5;</div>
                <h3>Kalender Latihan</h3>
                <p>Rencanakan sesi latihan ke depan. Tandai sesi yang selesai, dilewati, atau perlu dijadwal ulang.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#x1F514;</div>
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
                <div style="color: var(--secondary); font-size: .8rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; margin-bottom: .75rem;">REST API</div>
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

</body>
</html>
