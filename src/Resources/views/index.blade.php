@extends('api-replay::layout')

@section('content')
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Path</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th>User</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>
                        <span class="badge badge-{{ strtolower($log->method) }}">
                            {{ $log->method }}
                        </span>
                    </td>
                    <td><code>{{ $log->path }}</code></td>
                    <td class="{{ $log->response_status >= 400 ? 'status-error' : 'status-success' }}">
                        {{ $log->response_status }}
                    </td>
                    <td>{{ $log->duration_ms }}ms</td>
                    <td>{{ $log->user_id ?? '-' }}</td>
                    <td title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('api-replay.show', $log->id) }}" class="btn btn-primary">Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    {{ $logs->links() }}
</div>
@endsection
