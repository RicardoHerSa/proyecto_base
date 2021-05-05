@extends('layouts.app')

@section('content')
<div class="container reset-password">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <br>
            <br>
            <div class="card">
                <div class="card-header"><strong>{{ __('Restablecer Contraseña ') }}</strong></div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (session('danger'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('danger') }}
                    </div>
                    @endif
                    <div>
                        <h3 class="row justify-content-center"><strong>¿Olvidaste tu contraseña?</strong></h3>
                        <h4 class="row justify-content-center">Te enviaremos un enlace a tu correo electrónico para que puedas cambiar la contraseña</h4>
                    </div>
                    <br>  
                    <form method="GET" action="{{ route('reset.mail') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Usuario o dirección de correo electrónico *') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Restablecer Contraseña') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection