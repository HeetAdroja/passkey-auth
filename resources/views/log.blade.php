@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Laravel Logs</h2>

            <form method="POST" action="{{ route('admin.logs.clear') }}" onsubmit="return confirm('Clear logs?')">
                @csrf
                <button class="btn btn-danger">Clear Log</button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-3">Total: <strong>{{ $stats['total'] }}</strong></div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">Errors: <strong>{{ $stats['error'] }}</strong></div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">Warnings: <strong>{{ $stats['warning'] }}</strong></div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">Info: <strong>{{ $stats['info'] }}</strong></div>
            </div>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Search message">
            </div>
            <div class="col-md-3">
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="error" @selected(request('level') === 'error')>ERROR</option>
                    <option value="warning" @selected(request('level') === 'warning')>WARNING</option>
                    <option value="info" @selected(request('level') === 'info')>INFO</option>
                    <option value="debug" @selected(request('level') === 'debug')>DEBUG</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="card w-100">
            <div class="card-body p-0">
                <div class="table-responsive w-100">
                    <table class="table table-striped mb-0 w-100">
                        <thead>
                            <tr>
                                <th style="width: 180px;">Date</th>
                                <th style="width: 110px;">Level</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log['date'] }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $log['level'] === 'error' ? 'danger' : ($log['level'] === 'warning' ? 'warning' : 'secondary') }}">
                                            {{ strtoupper($log['level']) }}
                                        </span>
                                    </td>
                                    <td class="text-wrap">{{ $log['message'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center p-4">No valid logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
