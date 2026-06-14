<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostSaja — Marketing untuk yang tak sempat marketing</title>
    <meta name="description" content="Post automatik ke Google Business, Facebook, Instagram, WhatsApp. Cukup hantar gambar kerja. AI uruskan marketing.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/assets/app-VV1dNRmu.css') }}">
    <style>
        :root {
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --primary: #4F46E5;
            --primary-light: #EEF2FF;
            --secondary: #7C3AED;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --accent: #10B981;
            --border: #E2E8F0;
            --gradient: linear-gradient(135deg, #4F46E5, #7C3AED);
            --gradient-soft: linear-gradient(135deg, #EEF2FF, #F5F3FF);
            --font: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font); background: var(--bg); color: var(--text-main); -webkit-font-smoothing: antialiased; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* Nav */
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 20px; font-weight: 800; }
        .logo span { background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .nav-links a:hover { color: var(--text-main); }
        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-primary { background: var(--gradient); color: #fff; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-outline { border: 1.5px solid var(--border); color: var(--text-main); background: var(--surface); }
        .btn-outline:hover { border-color: var(--primary); }

        /* Hero */
        .hero { text-align: center; padding: 80px 24px 60px; }
        .hero h1 { font-size: clamp(32px, 6vw, 68px); font-weight: 800; line-height: 1.1; }
        .hero h1 span { background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: clamp(16px, 2vw, 20px); color: var(--text-muted); margin-top: 20px; line-height: 1.6; max-width: 640px; margin-left: auto; margin-right: auto; }
        .hero-actions { margin-top: 32px; display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .hero-actions .btn { padding: 14px 28px; font-size: 15px; }

        /* Features */
        .features { padding: 60px 0; }
        .features h2 { text-align: center; font-size: clamp(24px, 3.5vw, 38px); font-weight: 700; margin-bottom: 48px; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
        .feature-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 32px; transition: box-shadow 0.2s; }
        .feature-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .feature-card .icon { font-size: 32px; margin-bottom: 16px; }
        .feature-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--text-muted); line-height: 1.6; }

        /* CTA */
        .cta { text-align: center; padding: 80px 24px; }
        .cta h2 { font-size: clamp(24px, 3vw, 36px); font-weight: 700; }
        .cta p { color: var(--text-muted); margin-top: 12px; font-size: 16px; }
        .cta .btn { margin-top: 24px; padding: 16px 32px; font-size: 16px; }

        /* Footer */
        footer { text-align: center; padding: 32px 24px; font-size: 13px; color: var(--text-muted); border-top: 1px solid var(--border); }

        @media (max-width: 640px) {
            .nav-links { display: none; }
            .hero { padding: 48px 16px 40px; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="logo"><span>PostSaja</span></a>
        <div class="nav-links">
            <a href="#features">Ciri</a>
            <a href="#cta">Harga</a>
            <a href="{{ route('login') }}">Log Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Daftar Percuma</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Marketing untuk yang<br><span>tak sempat marketing</span></h1>
        <p>Post automatik ke Google Business, Facebook, Instagram & WhatsApp. Cukup hantar gambar kerja. AI uruskan caption, hashtag & posting.</p>
        <div class="hero-actions">
            <a href="{{ route('register') }}" class="btn btn-primary">🚀 Cuba Percuma 14 Hari</a>
            <a href="https://t.me/PostSajaBot" target="_blank" class="btn btn-outline">📸 Hantar Gambar</a>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <h2>Semua auto. <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Anda fokus kerja.</span></h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon">📸</div>
                    <h3>Hantar Gambar</h3>
                    <p>Staff hantar gambar kerja dekat @PostSajaBot dekat Telegram. Siap dalam 5 saat.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🤖</div>
                    <h3>AI Tulis Caption</h3>
                    <p>GPT-4o analyze gambar. Hasilkan caption + hashtag ikut bisnes anda. Natural, bukan spam.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📤</div>
                    <h3>Auto Post</h3>
                    <p>Post ke Google Business, Facebook, Instagram, WhatsApp Status. Satu hantar, semua dapat.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📊</div>
                    <h3>Dashboard Owner</h3>
                    <p>Pantau semua post, connect Google Business & WhatsApp. Ringkasan harian terus ke Telegram.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">👥</div>
                    <h3>Staff Management</h3>
                    <p>Setiap staff guna Business Code. Owner control apa yang post. Supervisor review sebelum naik.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">💰</div>
                    <h3>RM49/sebulan</h3>
                    <p>Flat fee. Semua platform. Free 14 hari. Sesuai untuk kedai, workshop, klinik, salon, restoran.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="cta" class="cta" style="background:var(--gradient-soft)">
        <div class="container">
            <h2>Bersedia untuk auto?</h2>
            <p>RM49/sebulan • Free 14 hari • Batal bila-bila</p>
            <a href="{{ route('register') }}" class="btn btn-primary">🚀 Daftar Sekarang</a>
        </div>
    </section>

    <footer>
        <p>PostSaja © {{ date('Y') }} — Marketing untuk yang tak sempat marketing</p>
    </footer>
</body>
</html>
