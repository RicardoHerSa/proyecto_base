@extends('layouts.app')
@section('content')

<div class="container">
    @if (Session::has('msj') && Session::get('msj') == "ok")
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Portero actualizado con éxito.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(Session::has('msj') && Session::get('msj') == "err")
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Error al actualizar portero.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
        <div class="col-xs-12 col-md-10 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <strong>Portero: #{{$portero[0]->id}}</strong>
                    
                </div>
                <div class="card-body">
                    <a href="{{ url('/Porteros') }}" title="Volver"><button type="button" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <form method="POST" action="{{ url('Porteros' . '/' . $portero[0]->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger" title="Eliminar Portero" onclick="return confirm(&quot;¿Estás seguro de eliminar el portero {{ $portero[0]->id }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </form>
                    <br/>
                    <br/> 
        
                    <div class="table-responsive">
                        <table class="table">
                          <form  onsubmit="return validarFormulario()" method="POST" action="{{ url('Porteros' . '/' . $portero[0]->id) }}" accept-charset="UTF-8" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <tbody>
                                <input type="hidden" id="usuViej" value="{{$portero[0]->id}}" name="id">
                                <tr><th id="txtusuario">Usuario</th><td><input type="text" id="usuario" type="text" class="form-control" value="{{$portero[0]->usuario}}" name="usuario" onchange="validarusuario()"></td></tr>
                                <tr><th id="txttipo">Tipo</th>
                                    <td>
                                        <select name="tipo" id="tipo" class="form-control">
                                            <option {{$portero[0]->tipo=="MOVIL"?'selected':''}} value="MOVIL">MOVIL</option>
                                            <option {{$portero[0]->tipo=="FIJA"?'selected':''}} value="FIJA">FIJA</option>
                                            <option {{$portero[0]->tipo=="TORNO"?'selected':''}} value="TORNO">TORNO</option>
                                            <option {{$portero[0]->tipo=="PUERTA"?'selected':''}} value="PUERTA">PUERTA</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><th>Sede Asociada</th>
                                    <td>
                                        <select name="sede" id="sede" class="form-control">
                                           @foreach ($sedes as $sed)
                                               <option {{$portero[0]->id_sede==$sed->id_ubicacion?'selected':''}} value="{{$sed->id_ubicacion}}">{{$sed->descripcion}}</option>
                                           @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr><th>Estado</th>
                                    <td>
                                        <select name="estado" id="estado" class="form-control">
                                            <option {{$portero[0]->activo=='S'?'selected':''}} value="S">Activo</option>
                                            <option {{$portero[0]->activo=='N'?'selected':''}} value="N">Inactivo</option>
                                        </select>
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                  
                    <button type="submit" class="btn btn-success">
                        <span id="loadBtn" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span id="txtActu">Actualizar</span></button>
                </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
            <div class="col-xs-12 col-md-10 col-lg-10">
                <h5 class="text-center">Lista de Porterías Asociadas</h5>
                <table class="table table-light">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre Portería</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id="regPorterias">
                        @foreach ($porteriasAsociadas as $por)
                        <tr>
                            <td>{{$por->descripcion}}</td>
                            <td><button class="btn btn-danger" type="button" onclick="eliminarPorteria({{$por->id_ubicacion}})"> <i class="fa fa-trash"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>
</div>

@endsection
<script>
     function eliminarPortero(idPortero)
    {
      var confirma = confirm('¿Está seguro de eliminar el portero ?');
      if(confirma){
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('eliminar.portero')}}", 
                    data: { _token:token, id:idPortero},
                    cache: false,
                    success: function(response){
                       if(response == 1){
                            toastr.success('Portero Eliminado');
                            listar();
                       }else{
                           alert('Error al eliminar');
                       }
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
         }
      
    }
        function validarFormulario()
        {
          var usuario = $("#usuario").val();
            if(usuario.length == 0 ){
                alert('Por favor complete los campos');
                return false;
            }else{
                $("#loadBtn").fadeIn();
                $("#txtActu").text('Procesando...');
                return true;
            }
        }

        function validarusuario()
        {
            var usuario  = $("#usuario").val();
                if(usuario.length != 0){
                    $("#txttipo").text("Verificando usuario...");
                    $("#tipo").attr('disabled', true);
                    var token = '{{csrf_token()}}';
                    $.ajax({
                            type:  'POST',
                            async: true,
                            url: "{{route('consult.usuario')}}", 
                            data: {'usuario':usuario, _token:token},
                            cache: false,
                            success: function(response){
                                if(response != "n"){
                                    alert('El usuario ingresado ya existe.');
                                }else{
                                    $("#txttipo").text("Tipo");
                                    $("#tipo").removeAttr('disabled');
                                }
                                $("#txttipo").text("Nombre");
                                $("#tipo").removeAttr('disabled');
                            },
                            error:function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError);
                                }
                            });
                }
        }

        function eliminarPorteria(idubicacion)
        {   
            var confirma = confirm('¿Eliminar esta portería?');
            var usuario = $("#usuViej").val();
            if(confirma){
                var token = '{{csrf_token()}}';
                $.ajax({
                        type:  'POST',
                        async: true,
                        url: "{{route('eliminar.porteria')}}", 
                        data: {'id':idubicacion, 'usu':usuario, _token:token},
                        cache: false,
                        success: function(response){
                            if(response != 2){
                                toastr.success('Portería Eliminada');
                                document.getElementById("regPorterias").innerHTML= response;
                            }else{
                                alert('Error al eliminar.');
                            }
                            },
                        error:function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError);
                            }
                        });
            }
         
        }

        function recargaTabla()
        {
            $.ajax({
                type:  'POST',
                async: true,
                url: "{{route('recarga.porterias')}}", 
                data: {'id':idubicacion, _token:token},
                cache: false,
                success: function(response){
                    toastr.success('Portería Eliminada');
                    },
                error:function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                    }
                });
        }
</script>