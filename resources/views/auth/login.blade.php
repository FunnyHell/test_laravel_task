@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    @stack('css')
    @yield('css')
@stop

@section('classes_body', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Admin</b>LTE</a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                               placeholder="Login" value="{{ old('login') }}" required autofocus>
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
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                               required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                        <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </form>

                <p class="mb-0">
                    <a href="{{ route('register') }}" class="text-center">Register a new membership</a>
                </p>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @section('adminlte_js')
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
        @stack('js')
        @yield('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const loginData = {
                        login: form.querySelector('input[name="login"]').value,
                        password: form.querySelector('input[name="password"]').value,
                    };

                    fetch('{{ route('api.login') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(loginData)
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
