@extends('api-replay::layout')

@section('styles')
<style>
    .log-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .log-item { 
        display: grid; grid-template-columns: 100px 1fr 100px 120px 100px 140px; 
        align-items: center; padding: 1.25rem 1.5rem; background: var(--surface); 
        border: 1px solid var(--border); border-radius: 1rem; transition: all 0.2s ease;
        text-decoration: none; color: inherit;
    }
    .log-item:hover { border-color: var(--primary-glow); background: rgba(56, 189, 248, 0.02); transform: scale(1.002); }
    
    .log-method { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; }
    .log-path { font-family: 'JetBrains Mono'; font-size: 0.825rem; font-weight: 500; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    .log-status { display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-family: 'JetBrains Mono'; font-size: 0.875rem; }
    .status-indicator { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .indicator-2xx { background: var(--success); box-shadow: 0 0 10px var(--success); }
    .indicator-4xx { background: var(--warning); box-shadow: 0 0 10px var(--warning); }
    .indicator-5xx { background: var(--error); box-shadow: 0 0 10px var(--error); }

    .log-latency { font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-align: right; margin-right: 1.5rem; }
    .log-user { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; }
    .log-time { font-size: 0.775rem; color: var(--text-muted); text-align: right; }

    .nav-pagination { margin-top: 3rem; }

    @media (max-width: 1024px) {
        .log-item { grid-template-columns: 80px 1fr 100px 120px; }
        .log-user, .log-latency { display: none; }
    }
</style>
@endsection

@section('content')
<div class="log-list animate-fade">
    @forelse($logs as $log)
    <a href="{{ route('api-replay.show', $log->id) }}" class="log-item">
        <div class="log-method">
            <span class="badge method-{{ strtolower($log->method) }}">{{ $log->method }}</span>
        </div>
        <div class="log-path">/{{ $log->path }}</div>
        <div class="log-status">
            <span class="status-indicator indicator-{{ substr((string)$log->response_status, 0, 1) }}xx"></span>
            {{ $log->response_status }}
        </div>
        <div class="log-latency">{{ $log->duration_ms }} ms</div>
        <div class="log-user">{{ $log->user_id ?? 'NO_AUTH' }}</div>
        <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
    </a>
    @empty
    <div style="padding: 10rem 2rem; text-align: center; border: 1px dashed var(--border); border-radius: 1.5rem; color: var(--text-muted);">
        <p style="font-weight: 500;">No activity logs found in storage.</p>
    </div>
    @endforelse
</div>

<div class="nav-pagination">
    {{ $logs->links() }}
</div>
@endsection
