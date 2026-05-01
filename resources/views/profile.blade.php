@extends('layouts.app')

@section('content')
    <div class="container wide" style="margin-top:40px">
        <h1>👤 Profile</h1>
        <p class="text-muted">{{ auth()->user()->name }} — {{ auth()->user()->email }}</p>

        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <hr style="margin: 24px 0; border-color: #f1f5f9;">

        {{-- ADD PASSKEY SECTION --}}
        <h2>➕ Add New Passkey</h2>

        <div class="form-group mt-3">
            <label for="passkey-name">Passkey Name</label>
            <input id="passkey-name" type="text" placeholder="e.g. My iPhone, Work Laptop">
        </div>

        <button id="create-passkey-btn" class="btn btn-dark" onclick="createPasskey()">
            <span id="create-spinner">⏳</span>
            🔑 Create Passkey (Face ID / Fingerprint)
        </button>

        <div id="passkey-message" class="mt-3" style="display:none"></div>

        <hr style="margin: 24px 0; border-color: #f1f5f9;">

        {{-- EXISTING PASSKEYS --}}
        <h2>🔑 Your Passkeys</h2>

        @php $passkeys = auth()->user()->passkeys; @endphp

        @if ($passkeys->count() > 0)
            @foreach ($passkeys as $pk)
                <div class="passkey-item">
                    <div>
                        <div class="passkey-name">🔑 {{ $pk->name ?? 'Passkey #' . $loop->iteration }}</div>
                        <div class="passkey-date">
                            Added: {{ $pk->created_at->format('d M Y, h:i A') }}
                            ({{ $pk->created_at->diffForHumans() }})
                        </div>
                    </div>
                    <form method="POST" action="{{ route('passkeys.destroy', $pk->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red btn-sm" onclick="return confirm('Delete this passkey?')">
                            🗑 Delete
                        </button>
                    </form>
                </div>
            @endforeach
        @else
            <p class="text-muted mt-3">No passkeys yet. Create one above.</p>
        @endif

        <hr style="margin: 24px 0; border-color: #f1f5f9;">

        <h2>🧹 Cache Management</h2>
        <form method="POST" action="{{ route('profile.clearcache') }}" onsubmit="return confirm('Clear all caches?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                Clear Cache
            </button>
        </form>
        @if (session('cache_cleared'))
            <div class="alert alert-success mt-3">{{ session('cache_cleared') }}</div>
        @endif
    </div>

    <script>
        async function createPasskey() {
            const btn = document.getElementById('create-passkey-btn');
            const spinner = document.getElementById('create-spinner');
            const msgBox = document.getElementById('passkey-message');
            const name = document.getElementById('passkey-name').value.trim() || 'My Passkey';

            function showMsg(msg, type = 'info') {
                msgBox.style.display = 'block';
                msgBox.className = 'alert alert-' + type + ' mt-3';
                msgBox.textContent = msg;
                console.log('[PASSKEY]', msg);
            }

            console.log('[PASSKEY] createPasskey clicked', {
                name
            });

            if (!window.startRegistration) {
                console.log('[PASSKEY] waiting for webauthn-ready');
                await new Promise(resolve => document.addEventListener('webauthn-ready', resolve, {
                    once: true
                }));
            }

            if (!window.browserSupportsWebAuthn()) {
                showMsg('⚠ Your browser does not support passkeys.', 'error');
                console.error('[PASSKEY] browser does not support WebAuthn');
                return;
            }

            try {
                btn.disabled = true;
                spinner.textContent = '⏳ ';

                console.log('[PASSKEY] requesting register options');

                const optRes = await fetch('{{ route('passkeys.register_options') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        name
                    })
                });

                console.log('[PASSKEY] register options response status', optRes.status);

                if (!optRes.ok) {
                    const err = await optRes.json();
                    console.error('[PASSKEY] register options error', err);
                    throw new Error(err.message ?? 'Failed to get registration options.');
                }

                const options = await optRes.json();
                console.log('[PASSKEY] register options payload', options);

                showMsg('👆 Follow the browser prompt to verify your identity...', 'info');

                const attestation = await window.startRegistration({
                    optionsJSON: options
                });
                console.log('[PASSKEY] attestation result', attestation);

                showMsg('⏳ Saving passkey...', 'info');

                const storeRes = await fetch('{{ route('passkeys.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        name: name,
                        passkey: attestation
                    })
                });

                console.log('[PASSKEY] store response status', storeRes.status);

                if (storeRes.ok) {
                    showMsg('✅ Passkey created successfully! Refreshing...', 'success');
                    console.log('[PASSKEY] passkey stored successfully');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    const err = await storeRes.json();
                    console.error('[PASSKEY] store error', err);
                    throw new Error(err.message ?? 'Failed to save passkey.');
                }
            } catch (err) {
                console.error('[PASSKEY] createPasskey failed', err);
                showMsg('❌ Error: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
                spinner.textContent = '🔑 ';
            }
        }
    </script>
@endsection
