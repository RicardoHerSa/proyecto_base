@extends('layouts.app')
@section('content')
<div class="container">
    @if (Session::has('msj') && Session::get('msj') == "ok")
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Portería asociada con éxito.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(Session::has('msj') && Session::get('msj') == "err")
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Error al asociar portería.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12 col-md-3 col-lg-3"></div>
        <div class="col-xs-12 col-md-6 col-lg-6">
            <form action="{{route('guardar.asociacion')}}" method="POST" onsubmit="return validarFormulario()">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <strong> Asociar Porterías</strong>
                    </div>
                    <div class="card-body">
                        <a href="{{ url('/Porteros') }}" title="Volver"><button type="button" class="btn btn-warning mb-3"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                        <div class="form-group">
                           <label for="usuario">Seleccione Portero: </label><br>
                            <select name="usuario" id="usuario" class="form-control" onchange="consultarPorterias()">
                                @foreach ($porteros as $por)
                                    <option value="{{$por->id}}">{{$por->usuario}}</option>   
                                @endforeach
                            </select>  
                        </div>
        
                        <div class="form-group">
                            <label for="porteria" id="lblPorteria">Seleccione Portería: </label>
                            <select name="porteria" id="porteria" class="form-control">
                              
                            </select>  
                        </div>
        
                    </div>
                    <div class="card-footer">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" id="btnRegistrar">
                                <span id="loadBtn" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span id="txtRegistrar">Asociar Portería</span>
                            </button>
                        </div>
        
                    </div>
                </div>
                
                
            </form>
        </div>
        <div class="col-xs-12 col-md-3 col-lg-3"></div>
    </div>
</div>

<script>
    $(document).ready(function(){
        consultarPorterias();
    });
    function consultarPorterias()
    {
        var usuario = $("#usuario").val();
        var token = '{{csrf_token()}}';
        $("#lblPorteria").text('Buscando Porterías...');

        
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('porterias.disponibles')}}", 
                    data: {'id':usuario, _token:token},
                    cache: false,
                    success: function(response){
                         document.getElementById("porteria").innerHTML = response;
                         $("#lblPorteria").text('Seleccione Portería:');
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        $("#lblPorteria").text('Seleccione Portería:');
                        }
             });

             
      
    }
    function validarFormulario()
    {
        var porteria = $("#porteria").val();
        if(porteria == ""){
            alert('Campos incompletos');
            return false;
        }else{
            $("#loadBtn").fadeIn();
            $("#txtRegistrar").text('Procesando...');
            return true;
        }
    }

</script>


@endsection