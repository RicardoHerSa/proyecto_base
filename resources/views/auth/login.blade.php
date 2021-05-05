@extends('layouts.app')

@section('content')
<div class="container">
    @include('layouts.message')

    <div class="container-fluid container2">
        @if(Session::has('Confirmado'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{Session::get('Confirmado')}}
        </div>
        @endif
        <div class="row">

            <div class="col-md-6">


                <form class='container2 ' method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group row ">
                        <div class="col-md-12">
                            <div class="input-group flex-nowrap">
                                <div class="col-md-10">
                                    <input placeholder="Usuario" id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                    @error('username')

                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group flex-nowrap">
                                <div class="col-md-10">
                                    <input placeholder="Contraseña" id="password" data-toggle="password" type="password" class="form-control pass @error('password') is-invalid @enderror" name="password" required autocomplete="off">

                                    @error('password')

                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Ingresar</button>
                        </div>
                        <div class="col-md-6">
                            <a class="btn link1" href="{{ url('/resetemail') }}">
                                Recuperar Contraseña
                            </a>
                        </div>

                    </div>
                </form>
            </div>
            {{-- <div class="col-md-4 ">

                <div class="parent">
                    <div class="image1"></div>
                    <img class="image3" src="{{ asset('/resources/tema1/Bubble 1.png') }}" />
            <img class="image4" src="{{ asset('/resources/tema1/Bubble 4.png') }}" />
            <img class="image5" src="{{ asset('/resources/tema1/Bubble 3.png') }}" />

            <img class="image2" src="{{ asset('/resources/tema1/Persona 3.png') }}" />

        </div>





    </div> --}}
</div>
</div>



<div class="row center-content-lefth">

    <!--<div class="col-md-5">
            <div class="form-group row">
                <div class="col-md-12 offset-md-1" align = ''>
                    <h2 class ='titulo3'> 
                        {{ trans('home.lbl_bienvenido') }}   
                    </h2>
                    <h5> 
                         <br>Somos el portal de transacciones que respalda a los colaboradores 
                         de la Organización Carvajal en la autogestión de 
                         su información laboral. En este espacio podrás gestionar
                         tus certificados laborales, recibos de pago, solicitud de
                         vacaciones, entre otros servicios. 
                    </h5>
                    <h5 style="color: #003A5D; font-size: 16px; font-weight: normal;  font-family: 'Trebuchet MS', Trebuchet MS">
                        

                    </h5>
                  
                    <img src="{{ asset('/resources/INICIO.png') }}">
                    <hr>
                </div>
            </div>
        </div>-->
</div>
</div>

<script>
    $(document).ready(function() {
        $("#ayuda").click(function() {
            $("#mostrarmodal").modal("show");
        });
    });

</script>

@endsection
