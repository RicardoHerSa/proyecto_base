@extends('layouts.app')
@section('content')
<div class="container">
        @if (Session::has('msj') && Session::get('msj') == "ok")
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <strong>Información!</strong> Portero registrado con éxito.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @elseif(Session::has('msj') && Session::get('msj') == "err")
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <strong>Información!</strong> Error al registrar portero.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                   <strong> Registrar Nuevo Portero </strong>
                </div>
            <form method="POST" action="{{url('/porteros')}}" onsubmit="return validarFormulario()">
                @csrf
                <div class="card-body">
                    <a href="{{ url('/porteros') }}" title="Volver"><button type="button" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <div class="row mt-3">
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorUsuario">
                            <div class="form-group">
                                <label for="">Usuario: *</label>
                                <input id="usuario" type="text" class="form-control" name="usuario">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorNombre">
                            <div class="form-group">
                                <label for="" id="lblTipo">Tipo: *</label>
                                <div class="spinner-grow text-primary" role="status" style="display: none" id="carga">
                                    <span class="sr-only">Buscando usuario...</span>
                                </div>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="MOVIL">MOVIL</option>
                                    <option value="FIJA">FIJA</option>
                                    <option value="TORNO">TORNO</option>
                                    <option value="PUERTA">PUERTA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorPertenece">
                            <label for="selectSedes" id="lblSede">Asociar Sede: *</label>
                                <select id="selectSedes" class="form-control" name="sede">
                                    @foreach ($sedes as $sed)
                                        <option value="{{$sed->id_ubicacion}}">{{$sed->descripcion}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorEstado">
                            <label for="">Estado</label>
                            <select class="form-control" name="estado" id="estado">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" id="encontrada">
                    <button type="submit" class="btn btn-success" id="btnRegistrar" onclick="registrarEmpresa()">
                        <span id="loadBtn" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
  
                        <span id="txtBtnRegistrar">Registrar Portero</span></button>
                    </div>
            </form>
            </div>
        </div>
    </div>

</div>

<script>
    $("#usuario").blur(function(){
        var usuario = $("#usuario").val();
        if(usuario.length != 0){
            $("#lblTipo").text("Verificando usuario...");
            $("#tipo").attr('disabled', true);
            $("#carga").fadeIn();
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('consult.usuario')}}", 
                    data: {'usuario':usuario, _token:token},
                    cache: false,
                    success: function(response){
                        if(response != "n"){
                            alert('El usuario ingresado y existe.');
                            $("#btnRegistrar").attr('disabled', true);
                            $("#usuario").addClass('is-invalid');
                        }else{
                            $("#btnRegistrar").removeAttr('disabled');
                            $("#usuario").removeClass('is-invalid');
                        }

                        $("#lblTipo").text("Tipo:");
                        $("#tipo").removeAttr('disabled');
                        $("#carga").fadeOut();
                        
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
        }else{
            $("#btnRegistrar").attr('disabled', true);
        }
    });

   
    function validarFormulario()
    {
        var usuario = $("#usuario").val();

        if(usuario.length == 0){
            alert('Campos Incompletos');
            return false;
        }else{
            
            $("#loadBtn").fadeIn();
            $("#txtBtnRegistrar").text('Procesando...');
            return true;
        }
    }


</script>

@endsection