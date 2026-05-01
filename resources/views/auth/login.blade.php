@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top:60px">
        <h1>🔐 Login</h1>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- PASSKEY LOGIN BUTTON --}}
        <button type="button" id="passkey-login-btn" class="btn btn-dark" onclick="loginWithPasskey()">
            <span id="passkey-spinner">⏳</span>
            🔑 Login with Passkey
        </button>

        <div class="divider">— or login with password —</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary">Login →</button>
        </form>

        <p class="text-center mt-4">
            No account? <a class="link" href="{{ route('register') }}">Register here</a>
        </p>
    </div>

    <script>
        async function loginWithPasskey() {
            const btn = document.getElementById('passkey-login-btn');
            const spinner = document.getElementById('passkey-spinner');
            const email = document.getElementById('email').value.trim();

            if (!email) {
                alert('Enter your email first.');
                return;
            }

            if (!window.startAuthentication) {
                await new Promise(resolve => document.addEventListener('webauthn-ready', resolve, {
                    once: true
                }));
            }

            if (!window.browserSupportsWebAuthn()) {
                alert('Your browser does not support passkeys.');
                return;
            }

            try {
                btn.disabled = true;
                spinner.classList.add('show');

                const optRes = await fetch('{{ route('passkeys.authentication_options') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        email
                    })
                });

                if (!optRes.ok) {
                    const errText = await optRes.text();
                    throw new Error('Could not get passkey options: ' + errText);
                }

                const options = await optRes.json();

                const authentication = await window.startAuthentication({
                    optionsJSON: options
                });

                console.log('Sent authentication response to server', authentication);

                const verifyRes = await fetch('{{ route('passkeys.authenticate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        start_authentication_response: JSON.stringify(authentication)
                    })
                });

                if (verifyRes.ok) {
                    window.location.href = '{{ route('dashboard') }}';
                } else {
                    const err = await verifyRes.json();
                    alert('Login failed: ' + (err.message ?? 'Please try again.'));
                }
            } catch (err) {
                alert('Passkey error: ' + err.message);
            } finally {
                btn.disabled = false;
                spinner.classList.remove('show');
            }
        }
    </script>
@endsection
