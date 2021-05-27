@include('layouts.app', ['modulo' => 'unitario'])
<div class="container">
    <div class="row my-5">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h1 class="text-center">Upps..</h1>
            <hr>
            <h3 class="text-center">Esta solicitud aún no ha sido validada por alguno de los aprobadores anteriores.Por favor espere a que le sea notificado el cambio de estado o comuníquese con el personal a cargo del flujo antecedente. <br><br>  <a href="{{url('inicio')}}" class="btn btn-primary">Volver al Inicio</a> </h3>
        
        </div>
    </div>
</div>
@include('layouts.footer', ['modulo' => 'unitario'])