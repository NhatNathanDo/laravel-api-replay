@extends('api-replay::layout')

@section('styles')
<style>
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
    .header-info { display: flex; align-items: center; gap: 1rem; }
    .section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-muted); }
    .replay-card { border: 2px solid var(--primary); }
    .loading { opacity: 0.5; pointer-events: none; }
</style>
@endsection

@section('content')
<div class="header-info">
    <span class="badge badge-{{ strtolower($log->method) }}">{{ $log->method }}</span>
    <h2>{{ $log->url }}</h2>
</div>

<div class="grid">
    <div>
        <div class="section-title">Request Headers</div>
        <div class="card" style="padding: 1rem;">
            @foreach($log->headers as $key => $values)
            <div style="margin-bottom: 0.5rem;">
                <strong>{{ $key }}:</strong> {{ implode(', ', $values) }}
            </div>
            @endforeach
        </div>

        <div class="section-title" style="margin-top: 2rem;">Request Body</div>
        <div class="card">
            <pre>{{ $log->request_body ?: 'No Body' }}</pre>
        </div>
    </div>

    <div id="replay-section">
        <div class="section-title">Original Response ({{ $log->response_status }})</div>
        <div class="card">
            <pre id="original-response">{{ $log->response_body ?: 'Empty Response' }}</pre>
        </div>

        <div class="section-title" style="margin-top: 2rem;">Replay Action</div>
        <div class="card" style="padding: 1.5rem;">
            @if(app()->environment('production'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: var(--error); padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <strong>⚠️ Warning:</strong> You are in PRODUCTION. Actions may have real side effects.
            </div>
            @endif

            <p style="margin-bottom: 1rem; color: var(--text-muted);">
                Replay this request. Use <strong>Dry Run</strong> to simulate without saving DB changes.
            </p>
            
            <form id="replay-form">
                @csrf
                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; background: #0f172a; padding: 0.75rem; border-radius: 0.5rem;">
                    <input type="checkbox" id="dry_run" name="dry_run" checked style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <label for="dry_run" style="cursor: pointer;">
                        <strong>Dry Run Mode</strong> (Auto DB Rollback)
                    </label>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Base URL Override</label>
                    <input type="text" id="base_url" name="base_url" placeholder="{{ $log->url }}" style="width: 100%; height: 2.5rem; background: #0f172a; border: 1px solid var(--border); color: white; border-radius: 0.5rem; padding: 0 0.5rem;">
                </div>

                @if(!in_array(strtoupper($log->method), ['GET', 'HEAD', 'OPTIONS']))
                <div id="danger-warning" style="margin-bottom: 1rem; font-size: 0.75rem; color: var(--warning);">
                    * This is a <strong>{{ $log->method }}</strong> request. Disabling Dry Run will execute real logic.
                </div>
                @endif

                <button type="submit" class="btn btn-primary" id="replay-btn" style="width: 100%; height: 3rem;">
                    Execute Replay
                </button>
            </form>
        </div>

        <div id="replay-result" style="display: none; margin-top: 2rem;">
            <div class="section-title">Replay Result</div>
            <div class="card" style="border: 2px solid var(--success);">
                <div style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                    <span id="replay-status" class="status-success"></span>
                    <span id="replay-duration"></span>
                </div>
                <pre id="replay-body"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('replay-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('replay-btn');
    const dryRun = document.getElementById('dry_run').checked;

    if (!dryRun) {
        const method = '{{ $log->method }}';
        if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
            if (!confirm('WARNING: You are about to execute a real ' + method + ' request WITHOUT Dry Run. This will change data in your database. Continue?')) {
                return;
            }
        }
    }
    const statusSpan = document.getElementById('replay-status');
    const durationSpan = document.getElementById('replay-duration');
    const bodyPre = document.getElementById('replay-body');

    btn.textContent = 'Executing...';
    btn.classList.add('loading');

    try {
        const formData = new FormData(e.target);
        const response = await fetch('{{ route('api-replay.replay', $log->id) }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if (data.success) {
            resultDiv.style.display = 'block';
            statusSpan.textContent = `Status: ${data.replay.response_status}`;
            durationSpan.textContent = `${data.replay.duration_ms}ms`;
            bodyPre.textContent = data.replay.response_body;
            
            statusSpan.className = data.replay.response_status >= 400 ? 'status-error' : 'status-success';
        } else {
            alert('Replay failed: ' + data.message);
        }
    } catch (err) {
        alert('An error occurred: ' + err.message);
    } finally {
        btn.textContent = 'Execute Replay';
        btn.classList.remove('loading');
    }
});
</script>
@endsection
