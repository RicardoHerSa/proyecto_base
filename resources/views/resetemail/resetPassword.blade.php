@extends('layouts.app')

@section('content')
<div class="container reset-password">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><strong> {{ __('Restablecer Contraseña ') }} </strong></div>
                
                <div class="card-body">
                    @include('layouts.message')
                    @if (session('danger'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('danger') }}
                    </div>
                    @endif
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="form-group row">
                        <div class="col-md-12">
                            <h3 class="row justify-content-center"><strong>Cambio de contraseña</strong></h3>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <h5 class="row justify-content-center">Si es la primera vez que ingresas al portal ó ya han pasado más de 90 días utilizando la misma contraseña. Por tú seguirdad es necesario cambiarla!</h5>
                            <p class="row justify-content-center" style="color:red">Recuerda que la contraseña debe estar compuesta por minimo 8 caracteres, al menos una letra mayuscula (A-Z), una letra minuscula (a-z), un número (0-9) y por lo menos uno de los siguientes caracteres especiales:  #´`?!@$%^:&*~-</p>
                        </div>  
                        <div class="col-md-1"></div>
                    </div>
                    <br>    
                    <form method="GET" action="{{ route('reset.verify') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Nueva Contraseña') }}</label>
                            
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