<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Expenser — Smart group expense sharing. Split bills, track payments, settle debts effortlessly.">
    <title>Expenser — Split Expenses, Not Friendships</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #06060b;
            --bg-secondary: #0d0d14;
            --bg-card: rgba(255, 255, 255, 0.03);
            --bg-card-hover: rgba(255, 255, 255, 0.06);
            --border-color: rgba(255, 255, 255, 0.06);
            --text-primary: #f0f0f5;
            --text-secondary: rgba(255, 255, 255, 0.5);
            --accent: #10b981;
            --accent-glow: rgba(16, 185, 129, 0.15);
            --accent-2: #06b6d4;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Ambient background */
        .ambient-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .ambient-bg::before {
            content: '';
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 600px;
            background: radial-gradient(ellipse, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            filter: blur(80px);
        }
        .ambient-bg::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: -5%;
            width: 600px;
            height: 500px;
            background: radial-gradient(ellipse, rgba(6, 182, 212, 0.06) 0%, transparent 70%);
            filter: blur(80px);
        }

        /* Grid pattern */
        .grid-pattern {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 60% 60% at 50% 30%, black, transparent);
        }

        /* Nav */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(20px);
            background: rgba(6, 6, 11, 0.7);
            border-bottom: 1px solid var(--border-color);
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
        }
        .nav-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-logo-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }
        .nav-logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .nav-actions { display: flex; gap: 0.75rem; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }
        .btn-ghost:hover {
            color: var(--text-primary);
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.05);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #0d9488);
            color: white;
            box-shadow: 0 0 20px var(--accent-glow);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 30px rgba(16, 185, 129, 0.3);
        }

        /* Hero */
        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease;
        }
        .hero-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            animation: pulse 2s infinite;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            max-width: 800px;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.6s ease 0.1s both;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            max-width: 550px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            animation: fadeInUp 0.6s ease 0.2s both;
        }
        .hero-cta {
            display: flex;
            gap: 1rem;
            animation: fadeInUp 0.6s ease 0.3s both;
        }

        /* Features */
        .features {
            position: relative;
            z-index: 1;
            padding: 4rem 2rem 6rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .features-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .features-header h2 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }
        .features-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.25rem;
        }
        .feature-card {
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        .feature-card:hover {
            background: var(--bg-card-hover);
            border-color: rgba(16, 185, 129, 0.15);
            transform: translateY(-2px);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--accent-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .feature-icon svg {
            width: 24px;
            height: 24px;
            color: var(--accent);
        }
        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Footer */
        .footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
            font-size: 0.8rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 640px) {
            .nav { padding: 1rem; }
            .hero { padding: 7rem 1.5rem 3rem; }
            .hero-cta { flex-direction: column; align-items: center; }
            .features { padding: 2rem 1.5rem 4rem; }
        }
    </style>
</head>
<body>
    <div class="ambient-bg"></div>
    <div class="grid-pattern"></div>

    <nav class="nav">
        <a href="/" class="nav-logo">
            <div class="nav-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <span class="nav-logo-text">Expenser</span>
        </a>
        <div class="nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            @endauth
        </div>
    </nav>

    <section class="hero">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Smart Expense Sharing
        </div>
        <h1>Split Expenses,<br><span class="gradient-text">Not Friendships</span></h1>
        <p class="hero-subtitle">
            Track shared expenses, automatically calculate who owes what, and settle debts with ease. Perfect for trips, roommates, and group activities.
        </p>
        <div class="hero-cta">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard →</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Start Splitting Free →</a>
                <a href="{{ route('login') }}" class="btn btn-ghost">Already have an account?</a>
            @endauth
        </div>
    </section>

    <section class="features">
        <div class="features-header">
            <h2>Everything you need to split fairly</h2>
            <p>No more awkward money conversations</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Group Management</h3>
                <p>Create groups for trips, roommates, dinners — anything. Add members by email and manage everything in one place.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <h3>Flexible Splitting</h3>
                <p>Split equally, by exact amounts, or by percentage. Our algorithm handles the math so you don't have to.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <h3>Smart Debt Simplification</h3>
                <p>Our algorithm minimizes the number of transactions needed to settle all debts. Fewer payments, less hassle.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3>Settlement Tracking</h3>
                <p>Record payments between members and watch debts disappear. Full history of every transaction.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>
                <h3>Live Dashboard</h3>
                <p>See your total balances at a glance. Know exactly how much you owe and how much others owe you.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <h3>Expense History</h3>
                <p>Full log of every expense with details on who paid, how it was split, and when it happened.</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>© {{ date('Y') }} Expenser. Built with ❤️ for fair sharing.</p>
    </footer>
</body>
</html>
