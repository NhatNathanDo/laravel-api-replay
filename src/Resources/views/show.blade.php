@extends('api-replay::layout')

@php
    $formatJson = function($json) {
        if (!$json) return '—';
        $decoded = json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE) 
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) 
            : $json;
    };
@endphp

@section('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2rem; transition: color 0.2s; }
    .back-link:hover { color: var(--text); }

    .grid-main { display: grid; grid-template-columns: 1fr 450px; gap: 3rem; align-items: start; }
    .page-title { font-size: 2.25rem; font-weight: 800; letter-spacing: -0.04em; line-height: 1.1; margin-bottom: 3rem; word-break: break-all; }

    .card-label { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: var(--text-muted); margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; }
    
    .data-card { background: var(--surface); border: 1px solid var(--border); border-radius: 1.25rem; overflow: hidden; margin-bottom: 3rem; }
    .data-header { padding: 1.25rem 1.5rem; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .data-header span { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); }

    pre { margin: 0; padding: 1.5rem; max-height: 600px; overflow: auto; font-size: 0.85rem; line-height: 1.6; white-space: pre-wrap; word-break: break-all; color: #e2e8f0; }

    .header-grid { padding: 1rem 1.5rem; }
    .header-row { display: flex; padding: 0.875rem 0; border-bottom: 1px solid var(--border); gap: 2rem; }
    .header-row:last-child { border-bottom: none; }
    .header-key { width: 180px; flex-shrink: 0; font-family: 'JetBrains Mono'; font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: lowercase; }
    .header-value { font-size: 0.825rem; color: var(--text); opacity: 0.8; word-break: break-all; }

    .panel-replay { position: sticky; top: 2rem; }
    .control-inner { padding: 2rem; }
    
    .input-box { margin-bottom: 1.5rem; }
    .input-box label { display: block; font-size: 0.65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.625rem; }
    .input-box input { width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--border); padding: 0.825rem 1rem; border-radius: 0.75rem; color: var(--text); font-family: inherit; font-size: 0.875rem; }
    .input-box input:focus { outline: none; border-color: var(--primary); }

    .header-editor-row { display: grid; grid-template-columns: 1fr 1fr 32px; gap: 0.5rem; margin-bottom: 0.5rem; }
    .header-editor-row input { font-size: 0.75rem; padding: 0.5rem 0.75rem; }
    .remove-row { color: var(--error); cursor: pointer; display: flex; align-items: center; justify-content: center; background: none; border: none; }

    .switch-bar { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem; background: rgba(16, 185, 129, 0.05); border-radius: 1rem; border: 1px solid rgba(16, 185, 129, 0.1); margin-bottom: 2rem; }
    .switch-bar-txt h4 { font-size: 0.875rem; font-weight: 800; margin-bottom: 0.125rem; }
    .switch-bar-txt p { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; }

    .switch { position: relative; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #334155; transition: .4s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: var(--text); transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--success); }
    input:checked + .slider:before { transform: translateX(20px); }

    @media (max-width: 1200px) { .grid-main { grid-template-columns: 1fr; } .panel-replay { position: relative; top: 0; } }
</style>
@endsection

@section('content')
<a href="{{ route('api-replay.index') }}" class="back-link">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Activity Feed
</a>

<div class="page-title animate-fade">
    {{ $log->url }}
</div>

<div class="grid-main">
    {{-- Main Column --}}
    <div class="animate-fade" style="animation-delay: 0.1s;">
        <div class="card-label">Execution Details</div>
        <div class="data-card">
            <div class="header-grid">
                <div class="header-row">
                    <span class="header-key">Status Code</span>
                    <span class="header-value" style="font-weight: 800; color: {{ $log->response_status >= 400 ? 'var(--error)' : 'var(--success)' }}">{{ $log->response_status }}</span>
                </div>
                <div class="header-row">
                    <span class="header-key">Response Latency</span>
                    <span class="header-value">{{ $log->duration_ms }} ms</span>
                </div>
                <div class="header-row">
                    <span class="header-key">Authenticated User</span>
                    <span class="header-value">{{ $log->user_id ?? 'ANONYMOUS' }}</span>
                </div>
                <div class="header-row">
                    <span class="header-key">Client Origin (IP)</span>
                    <span class="header-value">{{ $log->ip ?? 'HIDDEN' }}</span>
                </div>
            </div>
        </div>

        <div class="card-label">Response Data <button class="btn-ghost" style="padding: 2px 8px; font-size: 0.6rem;" onclick="copyToClipboard('res-body', this)">COPY_RAW</button></div>
        <div class="data-card" style="border-color: {{ $log->response_status >= 400 ? 'var(--error)' : 'var(--border)' }}">
            <pre id="res-body">{{ $formatJson($log->response_body) }}</pre>
        </div>

        <div class="card-label">Request Payload</div>
        <div class="data-card">
            <pre id="req-body">{{ $formatJson($log->request_body) }}</pre>
        </div>

        <div class="card-label">Captured Headers</div>
        <div class="data-card">
            <div class="header-grid">
                @foreach($log->headers as $key => $values)
                <div class="header-row">
                    <span class="header-key">{{ $key }}</span>
                    <span class="header-value">{{ implode(', ', $values) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Side Dashboard --}}
    <div class="panel-replay animate-fade" style="animation-delay: 0.2s;">
        <div class="card card-replay">
            <div class="control-inner">
                <div class="card-label">REPLAY DASHBOARD</div>

                <form id="replay-form">
                    @csrf
                    <div class="switch-bar">
                        <div class="switch-bar-txt">
                            <h4>Simulation Mode</h4>
                            <p>Safe DB rollback is active</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="dry_run" name="dry_run" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="input-box">
                        <label>Endpoint Replacement</label>
                        <input type="text" id="base_url" name="base_url" placeholder="http://target.local">
                    </div>

                    <div class="input-box">
                        <label>Custom Header Overrides</label>
                        <div id="header-overrides-list">
                            {{-- Rows added via JS --}}
                        </div>
                        <button type="button" class="btn btn-ghost" style="width: 100%; font-size: 0.7rem; border-style: dashed;" onclick="addHeaderRow()">
                            + Add Custom Header
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary" id="replay-btn" style="width: 100%; margin-top: 1rem; padding: 1rem;">
                        FIRE REQUEST ENGINE
                    </button>
                </form>

                <div id="replay-outcome" style="display: none; margin-top: 2rem;">
                    <div class="card-label" style="border-top: 1px solid var(--border); padding-top: 2rem;">REPLAY OUTCOME</div>
                    <div class="data-card" id="outcome-card">
                        <div class="data-header">
                            <span id="outcome-status"></span>
                            <span id="outcome-latency"></span>
                        </div>
                        <pre id="outcome-body"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function addHeaderRow() {
    const list = document.getElementById('header-overrides-list');
    const div = document.createElement('div');
    div.className = 'header-editor-row';
    div.innerHTML = `
        <input type="text" name="header_keys[]" placeholder="Key" style="background: rgba(0,0,0,0.3); border: 1px solid var(--border); color: white; border-radius: 4px;">
        <input type="text" name="header_values[]" placeholder="Value" style="background: rgba(0,0,0,0.3); border: 1px solid var(--border); color: white; border-radius: 4px;">
        <button type="button" class="remove-row" onclick="this.parentElement.remove()">&times;</button>
    `;
    list.appendChild(div);
}

async function copyToClipboard(id, btn) {
    const text = document.getElementById(id).textContent;
    await navigator.clipboard.writeText(text);
    btn.textContent = 'COPIED';
    setTimeout(() => btn.textContent = 'COPY_RAW', 2000);
}

document.getElementById('replay-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('replay-btn');
    const dryRun = document.getElementById('dry_run').checked;

    if (!dryRun) {
        if (!confirm('DANGER: Live execution will persist data changes to production. Proceed?')) return;
    }

    const outcomeDiv = document.getElementById('replay-outcome');
    const statusSpan = document.getElementById('outcome-status');
    const latencySpan = document.getElementById('outcome-latency');
    const bodyPre = document.getElementById('outcome-body');
    const outcomeCard = document.getElementById('outcome-card');

    btn.innerHTML = '<span style="opacity: 0.4;">TRANSMITTING...</span>';
    btn.disabled = true;

    try {
        const formData = new FormData(e.target);
        const response = await fetch('{{ route('api-replay.replay', $log->id) }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if (data.success) {
            outcomeDiv.style.display = 'block';
            statusSpan.textContent = `CODE: ${data.replay.response_status}`;
            latencySpan.textContent = `LATENCY: ${data.replay.duration_ms} ms`;
            
            try {
                bodyPre.textContent = JSON.stringify(JSON.parse(data.replay.response_body), null, 2);
            } catch (e) {
                bodyPre.textContent = data.replay.response_body;
            }

            const status = data.replay.response_status;
            statusSpan.style.color = status >= 400 ? 'var(--error)' : 'var(--success)';
            outcomeCard.style.borderColor = status >= 400 ? 'var(--error)' : 'var(--success)';
            
            outcomeDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            alert('Engine Failure: ' + data.message);
        }
    } catch (err) {
        alert('Connectivity Error: ' + err.message);
    } finally {
        btn.innerHTML = 'FIRE REQUEST ENGINE';
        btn.disabled = false;
    }
});
</script>
@endsection
