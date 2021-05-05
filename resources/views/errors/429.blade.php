@extends('layouts.app')

@section('content')

<div class="container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" aling='center'>

                <div class="jumbotron card card-block" style="color:rgb(0, 0, 0);">
                    <h2 class="text-center">
                        429 - Too Many Requests. 
                    </h2>
                    <p class="text-center" style="color:rgb(0, 0, 0); ">
                        <br>
                        <br>
                        <br>

                    </p>
                    <p class="text-center">
                        <a class="btn btn-primary btn-large" href={{ url('home') }}><i class="fa fa-home" aria-hidden="true"></i> Inicio</a>
                        <a class="btn btn-primary btn-large" href="javascript:history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i> Ir Atras</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection
