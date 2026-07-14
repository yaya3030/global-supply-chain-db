<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogisticsCtrl — Global Supply Chain Risk Intelligence Platform</title>
    <meta name="description" content="Real-time supply chain risk intelligence platform. Monitor global logistics, weather impacts, currency fluctuations, and risk scoring across international ports.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

    <style>
        *, *::before, *::after {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        :root {
            --violet-50: #f5f3ff;
            --violet-100: #ede9fe;
            --violet-200: #ddd6fe;
            --violet-300: #c4b5fd;
            --violet-400: #a78bfa;
            --violet-500: #8b5cf6;
            --violet-600: #7c3aed;
            --violet-700: #6d28d9;
            --violet-800: #5b21b6;
            --violet-900: #4c1d95;
            --violet-950: #2e1065;
        }

        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            color: #1e293b;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .landing-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 40px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.08);
            transition: all 0.3s ease;
        }

        .landing-nav.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--violet-500), var(--violet-700));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.3);
        }

        .nav-logo-text {
            font-size: 18px;
            font-weight: 800;
            color: var(--violet-900);
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link-item {
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .nav-link-item:hover {
            color: var(--violet-700);
            background: var(--violet-50);
        }

        .nav-cta {
            padding: 9px 22px;
            background: linear-gradient(135deg, var(--violet-600), var(--violet-700));
            color: white !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.25);
        }

        .nav-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.35);
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 40px 80px;
            position: relative;
            overflow: hidden;
        }

        /* Animated grid background */
        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.7)),
                radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(167, 139, 250, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(139, 92, 246, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
        }

        /* Animated gradient orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: orbFloat 20s ease-in-out infinite;
            pointer-events: none;
        }

        .hero-orb-1 {
            width: 500px;
            height: 500px;
            background: var(--violet-300);
            top: -10%;
            right: -5%;
            animation-delay: 0s;
        }

        .hero-orb-2 {
            width: 400px;
            height: 400px;
            background: var(--violet-200);
            bottom: -10%;
            left: -5%;
            animation-delay: -7s;
        }

        .hero-orb-3 {
            width: 300px;
            height: 300px;
            background: var(--violet-400);
            top: 40%;
            left: 50%;
            opacity: 0.15;
            animation-delay: -14s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(20px, 10px) scale(1.02); }
        }

        .hero-content {
            max-width: 720px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: var(--violet-50);
            border: 1px solid var(--violet-200);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: var(--violet-700);
            margin-bottom: 28px;
            animation: fadeInUp 0.6s ease both;
        }

        .hero-badge i {
            font-size: 14px;
        }

        .hero-badge .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--violet-500);
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            color: #0f172a;
            animation: fadeInUp 0.6s ease 0.1s both;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--violet-600), var(--violet-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: #64748b;
            max-width: 560px;
            margin: 0 auto 36px;
            line-height: 1.7;
            font-weight: 400;
            animation: fadeInUp 0.6s ease 0.2s both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            animation: fadeInUp 0.6s ease 0.3s both;
        }

        .btn-hero-primary {
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--violet-600), var(--violet-700));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
        }

        .btn-hero-secondary {
            padding: 14px 32px;
            background: white;
            color: var(--violet-700);
            border: 1.5px solid var(--violet-200);
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-hero-secondary:hover {
            background: var(--violet-50);
            border-color: var(--violet-400);
            transform: translateY(-2px);
        }

        /* ===== STATS BAR ===== */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 72px;
            padding-top: 40px;
            border-top: 1px solid #f1f5f9;
            animation: fadeInUp 0.6s ease 0.5s both;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--violet-700);
            letter-spacing: -1px;
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 6px;
        }

        /* ===== FEATURES ===== */
        .features {
            padding: 100px 40px;
            background: #f8fafc;
            position: relative;
        }

        .features::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--violet-200), transparent);
        }

        .section-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 60px;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background: var(--violet-100);
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            color: var(--violet-600);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.8px;
            margin-bottom: 14px;
        }

        .section-desc {
            font-size: 1.05rem;
            color: #64748b;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px 28px;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--violet-400), var(--violet-600));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.07);
            border-color: var(--violet-200);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .feature-icon.violet {
            background: var(--violet-100);
            color: var(--violet-600);
        }

        .feature-icon.emerald {
            background: #ecfdf5;
            color: #10b981;
        }

        .feature-icon.amber {
            background: #fffbeb;
            color: #f59e0b;
        }

        .feature-icon.sky {
            background: #eff6ff;
            color: #3b82f6;
        }

        .feature-icon.rose {
            background: #fef2f2;
            color: #ef4444;
        }

        .feature-icon.indigo {
            background: #eef2ff;
            color: #6366f1;
        }

        .feature-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .feature-desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            padding: 100px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: white;
        }

        .cta-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, rgba(139,92,246,0.04) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.8px;
            margin-bottom: 14px;
        }

        .cta-desc {
            font-size: 1.05rem;
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        /* ===== FOOTER ===== */
        .landing-footer {
            padding: 40px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .footer-text {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        .footer-text a {
            color: var(--violet-600);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .stats-bar {
                gap: 32px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 640px) {
            .landing-nav {
                padding: 0 20px;
            }

            .nav-links .nav-link-item {
                display: none;
            }

            .hero {
                padding: 100px 24px 60px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-desc {
                font-size: 1rem;
            }

            .hero-actions {
                flex-direction: column;
            }

            .btn-hero-primary,
            .btn-hero-secondary {
                width: 100%;
                justify-content: center;
            }

            .stats-bar {
                flex-direction: column;
                gap: 24px;
                align-items: center;
            }

            .features {
                padding: 60px 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .cta-section {
                padding: 60px 20px;
            }

            .cta-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="landing-nav" id="landingNav">
        <div class="nav-logo">
            <div class="nav-logo-icon">
                <i class="ti ti-world"></i>
            </div>
            <span class="nav-logo-text">LogisticsCtrl</span>
        </div>
        <div class="nav-links">
            <a href="#features" class="nav-link-item">Features</a>
            <a href="{{ url('/dashboard') }}" class="nav-cta">
                Open Dashboard <i class="ti ti-arrow-right" style="font-size: 16px;"></i>
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>

        <div class="hero-content">
            <div class="hero-badge">
                <span class="pulse-dot"></span>
                Live Intelligence Platform
            </div>
            <h1 class="hero-title">
                Global Supply Chain<br><span class="highlight">Risk Intelligence</span>
            </h1>
            <p class="hero-desc">
                Monitor real-time logistics data, weather disruptions, currency impacts, and risk scores across international supply chain hubs — all in one powerful dashboard.
            </p>
            <div class="hero-actions">
                <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                    <i class="ti ti-layout-dashboard"></i> Enter Dashboard
                </a>
                <a href="#features" class="btn-hero-secondary">
                    <i class="ti ti-sparkles"></i> Explore Features
                </a>
            </div>

            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Countries Monitored</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Active Modules</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Real-time Data</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5s</div>
                    <div class="stat-label">Refresh Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features" id="features">
        <div class="section-header reveal">
            <div class="section-tag"><i class="ti ti-sparkles" style="font-size: 13px;"></i> Platform Capabilities</div>
            <h2 class="section-title">Everything You Need to Manage Global Supply Chains</h2>
            <p class="section-desc">Comprehensive analytics modules designed for real-time logistics monitoring, risk assessment, and strategic decision-making.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon violet"><i class="ti ti-layout-dashboard"></i></div>
                <h3 class="feature-title">Global Dashboard</h3>
                <p class="feature-desc">Unified command center with real-time KPIs, interactive maps, and dynamic charts for instant global overview.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon rose"><i class="ti ti-shield-exclamation"></i></div>
                <h3 class="feature-title">Risk Scoring Engine</h3>
                <p class="feature-desc">Multi-dimensional risk calculation covering infrastructure, financial, and geopolitical factors per country.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon sky"><i class="ti ti-cloud-storm"></i></div>
                <h3 class="feature-title">Weather Monitor</h3>
                <p class="feature-desc">Live maritime weather conditions across major international ports with safety alerts and navigation advisories.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon amber"><i class="ti ti-currency-dollar"></i></div>
                <h3 class="feature-title">Currency Impact</h3>
                <p class="feature-desc">Track exchange rate volatility and estimate cost surge impacts on cross-border logistics operations.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon emerald"><i class="ti ti-news"></i></div>
                <h3 class="feature-title">News Intelligence</h3>
                <p class="feature-desc">AI-curated news feed with sentiment analysis and disruption impact categorization for supply chain events.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon indigo"><i class="ti ti-anchor"></i></div>
                <h3 class="feature-title">Port Geospatial Map</h3>
                <p class="feature-desc">Interactive world map plotting all monitored port locations with real-time status and geographic intelligence.</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-bg"></div>
        <div class="cta-content reveal">
            <h2 class="cta-title">Ready to Take Control?</h2>
            <p class="cta-desc">Access your global supply chain intelligence dashboard and start making data-driven decisions today.</p>
            <a href="{{ url('/dashboard') }}" class="btn-hero-primary">
                <i class="ti ti-rocket"></i> Launch Dashboard
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="landing-footer">
        <p class="footer-text">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">LogisticsCtrl</a> — Global Supply Chain Risk Intelligence Platform. Built with Laravel.
        </p>
    </footer>

    <!-- Scripts -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('landingNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Scroll reveal
        const revealElements = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        revealElements.forEach(el => observer.observe(el));

        // Stagger feature cards
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card, i) => {
            card.style.transitionDelay = `${i * 80}ms`;
        });
    </script>

</body>
</html>
