@extends('layouts.app')

@section('content')
<div class="container reset-password">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card">
                <div class="card-header"><strong> {{ __('Restablecer Contraseña ') }} </strong></div>

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
                        <h3 class="row justify-content-center"><strong>Estás a punto de cambiar tu contraseña</strong></h3>
                        <h4 class="row justify-content-center">Al terminar, te enviaremos a iniciar sesión de nuevo con tu nueva contraseña</h4>
                    </div>
                    <br>    
                    <form method="GET" action="{{ route('reset.token') }}">
                        @csrf
                            <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirmar Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <input id="token" type="hidden" class="form-control" name="token" value="{{$token[0]->token}}">
                                <input id="email" type="hidden" class="form-control" name="email" value="{{$token[0]->email}}">
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