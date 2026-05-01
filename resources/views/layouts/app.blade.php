<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    {{-- ✅ NO npm build needed - plain CSS only --}}
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        nav {
            background: #0f172a;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #e2e8f0;
            text-decoration: none;
            font-size: 0.9rem;
        }

        nav .nav-right {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        nav form button {
            background: #ef4444;
            border: none;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .container {
            max-width: 520px;
            margin: 48px auto;
            background: white;
            padding: 36px;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .container.wide {
            max-width: 720px;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 24px;
            color: #0f172a;
        }

        h2 {
            font-size: 1.1rem;
            margin-bottom: 14px;
            color: #334155;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #475569;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #1e293b;
        }

        input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
            font-weight: 600;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: #6366f1;
            color: white;
        }

        .btn-dark {
            background: #0f172a;
            color: white;
        }

        .btn-red {
            background: #ef4444;
            color: white;
        }

        .btn-outline {
            background: white;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-sm {
            width: auto;
            padding: 6px 14px;
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .btn:hover {
            opacity: 0.88;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .divider {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 16px 0;
        }

        .alert {
            padding: 11px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        .alert-info {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1d4ed8;
        }

        .link {
            color: #6366f1;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .mt-5 {
            margin-top: 20px;
        }

        .passkey-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .passkey-item:last-child {
            border-bottom: none;
        }

        .passkey-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .passkey-date {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-gray {
            background: #f1f5f9;
            color: #64748b;
        }

        #passkey-spinner {
            display: none;
        }

        #passkey-spinner.show {
            display: inline-block;
        }
    </style>
</head>

<body>

    @auth
        <nav>
            <a href="{{ route('dashboard') }}">🔐 {{ config('app.name') }}</a>
            <div class="nav-right">
                <a href="{{ route('admin.logs.index') }}">Logs</a>
                <a href="{{ route('profile') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </nav>
    @endauth

    @yield('content')

    {{-- ✅ SimpleWebAuthn from CDN — NO npm build needed --}}
    <script type="module">
        import {
            startRegistration,
            startAuthentication,
            browserSupportsWebAuthn
        } from 'https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@13.1.0/+esm';

        window.startRegistration = startRegistration;
        window.startAuthentication = startAuthentication;
        window.browserSupportsWebAuthn = browserSupportsWebAuthn;
        window.webAuthnReady = true;
        document.dispatchEvent(new Event('webauthn-ready'));
    </script>

</body>

</html>
