<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Expenser') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
            body{font-family:'Inter',sans-serif;background:#06060b;color:#f0f0f5;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem}
            .auth-logo{margin-bottom:2rem;text-align:center}
            .auth-logo a{display:flex;align-items:center;justify-content:center;gap:.75rem;text-decoration:none;color:#f0f0f5}
            .auth-logo-icon{width:48px;height:48px;background:linear-gradient(135deg,#10b981,#06b6d4);border-radius:14px;display:flex;align-items:center;justify-content:center}
            .auth-logo-icon svg{width:28px;height:28px;color:#fff}
            .auth-logo-text{font-size:1.5rem;font-weight:700;letter-spacing:-.02em}
            .auth-card{width:100%;max-width:440px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:20px;padding:2.5rem;backdrop-filter:blur(20px)}
            .auth-title{font-size:1.25rem;font-weight:600;margin-bottom:.5rem;text-align:center}
            .auth-subtitle{font-size:.85rem;color:rgba(255,255,255,0.5);margin-bottom:1.5rem;text-align:center}
            .form-group{margin-bottom:1.25rem}
            .form-label{display:block;font-size:.85rem;font-weight:500;margin-bottom:.5rem}
            .form-input{width:100%;padding:.7rem 1rem;border-radius:10px;border:1px solid rgba(255,255,255,0.07);background:rgba(255,255,255,0.05);color:#f0f0f5;font-size:.875rem;font-family:'Inter',sans-serif;outline:none;transition:all .2s}
            .form-input:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,0.15)}
            .form-input::placeholder{color:rgba(255,255,255,0.3)}
            .form-error{font-size:.75rem;color:#ef4444;margin-top:.35rem}
            .form-check{display:flex;align-items:center;gap:.5rem;font-size:.85rem;color:rgba(255,255,255,0.5)}
            .form-check input{accent-color:#10b981;width:16px;height:16px}
            .auth-actions{display:flex;align-items:center;justify-content:space-between;margin-top:1.5rem;gap:1rem}
            .btn-auth{padding:.7rem 1.75rem;border-radius:10px;font-size:.875rem;font-weight:500;font-family:'Inter',sans-serif;border:none;cursor:pointer;transition:all .2s}
            .btn-primary{background:linear-gradient(135deg,#10b981,#0d9488);color:#fff;box-shadow:0 0 20px rgba(16,185,129,0.15)}
            .btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 25px rgba(16,185,129,0.3)}
            .auth-link{font-size:.85rem;color:rgba(255,255,255,0.5);text-decoration:none;transition:color .2s}
            .auth-link:hover{color:#10b981}
            .ambient{position:fixed;top:-20%;left:50%;transform:translateX(-50%);width:600px;height:400px;background:radial-gradient(ellipse,rgba(16,185,129,0.06) 0%,transparent 70%);filter:blur(80px);pointer-events:none;z-index:-1}
        </style>
    </head>
    <body>
        <div class="ambient"></div>
        <div class="auth-logo">
            <a href="/">
                <div class="auth-logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="auth-logo-text">Expenser</span>
            </a>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </body>
</html>
