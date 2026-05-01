@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>📝 Create Account</h1>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required
                    autofocus>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Min 8 characters" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Create Account →</button>
        </form>

        <p class="text-center mt-4">
            Already have an account? <a class="link" href="{{ route('login') }}">Login</a>
        </p>
    </div>
@endsection
