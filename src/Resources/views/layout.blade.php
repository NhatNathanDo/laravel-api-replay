<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Replay Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --primary: #38bdf8;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #22c55e;
            --error: #ef4444;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-main);
            line-height: 1.5;
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
        }
        
        .brand { font-size: 1.5rem; font-weight: 700; color: var(--primary); text-decoration: none; }
        
        .card { 
            background: var(--card-bg); border-radius: 0.75rem; border: 1px solid var(--border);
            overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #1e293b; padding: 1rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 1rem; border-bottom: 1px solid var(--border); font-size: 0.875rem; }
        
        .badge { 
            padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;
            display: inline-block;
        }
        .badge-get { background: #0ea5e9; color: white; }
        .badge-post { background: #10b981; color: white; }
        .badge-put { background: #f59e0b; color: white; }
        .badge-delete { background: #ef4444; color: white; }
        
        .status-success { color: var(--success); }
        .status-error { color: var(--error); }
        
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: 0.2s;
            cursor: pointer; border: none; text-decoration: none; font-size: 0.875rem;
        }
        .btn-primary { background: var(--primary); color: #0f172a; }
        .btn-primary:hover { opacity: 0.9; }
        
        code { font-family: 'JetBrains Mono', monospace; font-size: 0.875rem; background: #0f172a; padding: 0.2rem 0.4rem; border-radius: 0.25rem; }
        pre { background: #0f172a; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; color: #bae6fd; }

        .pagination { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .pagination a { color: var(--text-main); text-decoration: none; padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.5rem; }
        .pagination .active { background: var(--primary); color: #0f172a; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeIn 0.4s ease-out forwards; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container animate-in">
        <header>
            <a href="{{ route('api-replay.index') }}" class="brand">API Replay</a>
            <div class="nav">
                @yield('nav')
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>
