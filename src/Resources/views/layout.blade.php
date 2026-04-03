<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Replay • Engine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #020617;
            --surface: #0f172a;
            --surface-hover: #1e293b;
            --primary: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.4);
            --text: #f8fafc;
            --text-muted: #64748b;
            --border: rgba(255, 255, 255, 0.05);
            --success: #10b981;
            --error: #f43f5e;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

        .app-shell {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            flex: 1;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: -0.01em;
        }

        .brand-icon {
            width: 32px; height: 32px;
            background: var(--text);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: var(--bg);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.02), 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.625rem 1.25rem; border-radius: 0.625rem;
            font-weight: 600; font-size: 0.8125rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer; border: none; text-decoration: none;
        }

        .btn-primary { background: var(--text); color: var(--bg); }
        .btn-primary:hover { background: var(--primary); color: var(--bg); transform: translateY(-1px); }

        .btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--surface-hover); color: var(--text); }

        .badge {
            padding: 0.375rem 0.625rem; border-radius: 0.5rem;
            font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.05em;
        }
        .method-get { background: rgba(56, 189, 248, 0.08); color: var(--primary); }
        .method-post { background: rgba(16, 185, 129, 0.08); color: var(--success); }
        .method-delete { background: rgba(244, 63, 94, 0.08); color: var(--error); }

        pre {
            font-family: 'JetBrains Mono', monospace; font-size: 0.8125rem;
            background: rgba(0, 0, 0, 0.3); padding: 1.5rem; border-radius: 0.75rem;
            overflow: auto; color: #bae6fd; border: 1px solid var(--border);
        }

        footer {
            text-align: center;
            padding: 4rem 1.5rem;
            margin-top: auto;
            border-top: 1px solid var(--border);
        }
        .author-tag {
            font-size: 0.75rem; color: var(--text-muted); text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: color 0.2s;
        }
        .author-tag:hover { color: var(--text); }
        .author-tag strong { color: var(--text); font-weight: 700; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .animate-fade { animation: fadeIn 0.8s ease-out forwards; }
    </style>
    @yield('styles')
</head>
<body class="animate-fade">
    <div class="app-shell">
        <header>
            <a href="{{ route('api-replay.index') }}" class="brand">
                <div class="brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </div>
                API REPLAY <span style="opacity: 0.2; font-weight: 400; margin-left: 0.5rem;">ENGINE</span>
            </a>
            <div class="nav-actions">
                @yield('nav')
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <footer>
        <a href="#" class="author-tag">
            Powered by <strong>Nathan Do</strong> &middot; V1.0.4 &middot; Developed for Laravel
        </a>
    </footer>

    @yield('scripts')
</body>
</html>
