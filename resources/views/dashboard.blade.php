@extends('layouts.app')

@section('content')
    <div class="container wide" style="margin-top:40px">
        <h1>🎉 Dashboard</h1>
        <p style="margin-top:10px; color:#475569">Welcome back, <strong>{{ auth()->user()->name }}</strong>!</p>

        <div class="alert alert-success mt-4">
            ✅ You are logged in successfully.
        </div>

        @php $passkeys = auth()->user()->passkeys; @endphp

        <div class="mt-5">
            <h2>🔑 Your Passkeys ({{ $passkeys->count() }})</h2>

            @if ($passkeys->count() > 0)
                @foreach ($passkeys as $pk)
                    <div class="passkey-item">
                        <div>
                            <div class="passkey-name">🔑 {{ $pk->name ?? 'Passkey' }}</div>
                            <div class="passkey-date">Added {{ $pk->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="badge badge-green">Active</span>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info mt-3">
                    No passkeys yet. <a class="link" href="{{ route('profile') }}">Add one from your Profile →</a>
                </div>
            @endif
        </div>
    </div>
@endsection
