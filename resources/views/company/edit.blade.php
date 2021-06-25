@extends('layouts.app')
@section('content')

<div class="container">
    @if (Session::has('msj') && Session::get('msj') == "ok")
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Empresa actualizada con éxito.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(Session::has('msj') && Session::get('msj') == "err")
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> Error al actualizar empresa.
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
                    <strong>Empresa: #{{$codigoEmpresa}}</strong>
                    
                </div>
                <div class="card-body">
                    <a href="{{ url('/company') }}" title="Volver"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <form method="POST" action="{{ url('company' . '/' . $codigoEmpresa) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger" title="Eliminar Empresa" onclick="return confirm(&quot;¿Estás seguro de eliminar la empresa {{ $codigoEmpresa }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </form>
                    <br/>
                    <br/>
        
                    <div class="table-responsive">
                        <table class="table">
                          <form  onsubmit="return validarFormulario()" method="POST" action="{{ url('company' . '/' . $codigoEmpresa) }}" accept-charset="UTF-8" style="display:inline">
                            @csrf
                            @method('PATCH')
                            <tbody>
                                <input type="hidden" id="codeemp" value="{{$codigoEmpresa}}" name="antiguo">
                                <tr><th id="txtCodigo">Código</th><td><input type="number" id="codigo" type="text" class="form-control" value="{{$empresa[0]->codigo_empresa}}" name="codigo" onchange="validarCodigo()"></td></tr>
                                <tr><th id="txtnombre">Nombre</th><td><input id="nombre" type="text" class="form-control" value="{{$empresa[0]->descripcion}}" name="nombre"></td></tr>
                                <tr><th>Estado</th>
                                    <td>
                                        <select name="estado" id="estado" class="form-control">
                                            <option {{$empresa[0]->activo=='S'?'selected':''}} value="S">Activo</option>
                                            <option {{$empresa[0]->activo=='N'?'selected':''}} value="N">Inactivo</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> <th>Grupo Carvajal</th>
                                    <td>
                                        <select name="grupo" id="grupo" class="form-control">
                                            <option {{$empresa[0]->tipo_empresa=='CARVAJAL'?'selected':''}} value="CARVAJAL">SI</option>
                                            <option {{$empresa[0]->tipo_empresa=='EXTERNA'?'selected':''}} value="EXTERNA">NO</option>
                                        </select>
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>
    <div class="row mt-3">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
        <div class="col-xs-12 col-md-10 col-lg-10">
            <h5 class="text-center">Lista de Sedes Asociadas</h5>
            <table class="table table-light">
                <thead class="thead-light">
                    <tr>
                        <th>Nombre de la Sede</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sedesAsociadas as $sed)
                    <tr>
                        <td>{{$sed->descripcion}}</td>
                        <td><button type="button" class="btn btn-danger" onclick="eliminarSede({{$sed->id_ubicacion}}, {{$codigoEmpresa}})"><i class="fa fa-trash"></i></button></td>
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
     function eliminarSede(sede, empresa)
    {
        var confirma = confirm("¿Está seguro de eliminar esta sede?");
        if(confirma){
            var token = '{{csrf_token()}}';
                $.ajax({
                        type:  'POST',
                        async: true,
                        url: "{{route('elimina.sede')}}", 
                        data: {'sede':sede,'empresa':empresa, _token:token},
                        cache: false,
                        success: function(response){
                            if(response != 1){
                                toastr.success('Sede eliminada.'');
                            }else{
                                alert('No se pudo eliminar la sede');
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
          var codigo = $("#codigo").val();
          var nombre = $("#nombre").val();
            if(codigo.length == 0 || nombre.length == 0){
                alert('Por favor complete los campos');
                return false;
            }else{
                return true;
            }
        }

        function validarCodigo()
        {
            var codigo  = $("#codigo").val();
                if(codigo.length != 0){
                    $("#txtnombre").text("Verificando código...");
                    $("#nombre").attr('disabled', true);
                    var token = '{{csrf_token()}}';
                    $.ajax({
                            type:  'POST',
                            async: true,
                            url: "{{route('consult.empresa')}}", 
                            data: {'codigo':codigo, _token:token},
                            cache: false,
                            success: function(response){
                                if(response != "n"){
                                    alert('El codigo ingresado ya existe.');
                                    var codigoActual = $("#codeemp").val();
                                    $("#codigo").val(codigoActual);
                                }else{
                                    $("#txtnombre").text("Nombre");
                                    $("#nombre").removeAttr('readonly');
                                    $("#nombre").removeAttr('disabled');
                                }
                                $("#txtnombre").text("Nombre");
                                $("#nombre").removeAttr('disabled');
                            },
                            error:function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError);
                                }
                            });
                }
        }
</script>