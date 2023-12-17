@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    @stack('css')
    @yield('css')
@stop

@section('classes_body', 'register-page')

@section('body')
    <div class="register-box">
        <div class="register-logo">
            <a href="{{ url('/') }}"><b>Admin</b>LTE</a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">Register a new membership</p>

                <form method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror" placeholder="Login" value="{{ old('login') }}" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('login')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password_confirmation')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required placeholder="First Name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('first_name')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required placeholder="Last Name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('last_name')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate" value="{{ old('birthdate') }}" placeholder="Birthdate">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-calendar"></span>
                            </div>
                        </div>
                        @error('birthdate')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>

                <a href="{{ route('login') }}" class="text-center">I already have a membership</a>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @section('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const userData = {
                        login: form.querySelector('input[name="login"]').value,
                        password: form.querySelector('input[name="password"]').value,
                        password_confirmation: form.querySelector('input[name="password_confirmation"]').value,
                        first_name: form.querySelector('input[name="first_name"]').value,
                        last_name: form.querySelector('input[name="last_name"]').value,
                        birthdate: form.querySelector('input[name="birthdate"]').value
                    };

                    fetch('{{ route('api.register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(userData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                            } else {
                                localStorage.setItem('auth_token', data.result.token);
                                window.location.href = '/dashboard';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });
        </script>
    @stop
    @yield('js')
@stop
