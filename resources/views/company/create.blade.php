@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                   <strong> Registrar Nueva Empresa </strong>
                </div>
                <div class="card-body">
                    <a href="{{ url('/company') }}" title="Volver"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <div class="row mt-3">
                        <div class="col-xs-12 col-md-3 col-lg-3" id="contenedorCodigo">
                            <div class="form-group">
                                <label for="">Código de Empresa: *</label>
                                <input id="codigo" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-5 col-lg-5" id="contenedorNombre">
                            <div class="form-group">
                                <label for="" id="lblNombre">Nombre: *</label>
                                <div class="spinner-grow text-primary" role="status" style="display: none" id="carga">
                                    <span class="sr-only">Buscando empresa...</span>
                                </div>
                                <input id="nombre" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorPertenece">
                            <label for="">¿Pertenece al grupo Carvajal?: *</label>
                            <select class="form-control" name="" id="grupo">
                              <option value="CARVAJAL">SI</option>
                              <option value="EXTERNA">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorSede">
                            <label for="selectSedes" id="lblSede">Seleccione la Sede: *</label>
                                <select id="selectSedes" class="form-control" name="">
                                    @foreach ($sedes as $sed)
                                        <option value="{{$sed->id_ubicacion}}">{{$sed->descripcion}}</option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4" id="contenedorEstado">
                            <label for="">Estado</label>
                            <select class="form-control" name="" id="estado">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" id="encontrada">
                    <button class="btn btn-success" id="btnRegistrar" onclick="registrarEmpresa()">
                        <span id="loadBtn" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
  
                        <span id="txtBtnRegistrar">Registrar Empresa</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3" id="contenedorSedes" style="display: none">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                   Sedes Asociadas
                </div>
                <div class="mt-3" id="loadSedes" style="margin-left:35%;display: none">
                      <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                      </div>
                      <div class="spinner-grow text-danger" role="status">
                      </div>
                      <div class="spinner-grow text-warning" role="status">
                      </div>
                      <div class="spinner-grow text-info" role="status">
                      </div>
                </div>
                <div class="ml-4 mt-3" id="loadElimina" style="display: none">
                    <div class="align-items-center">
                        <strong>Eliminando Sede...</strong>
                        <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                      </div>
                </div>
                <div class="card-body" style="overflow-y: auto;height: 300px;">
                    <table class="table table-light">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre de la Sede</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="cargaDatos">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#codigo").blur(function(){
        var codigo = $("#codigo").val();
        if(codigo.length != 0){
            $("#lblNombre").text("Verificando código...");
            $("#nombre").attr('disabled', true);
            $("#carga").fadeIn();
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('consult.empresa')}}", 
                    data: {'codigo':codigo, _token:token},
                    cache: false,
                    success: function(response){
                        if(response != "n"){
                            $("#nombre").val(response);
                            $("#nombre").attr('readonly', true);
                            $("#txtBtnRegistrar").text('Asociar Sede');
                            $("#lblNombre").text("Empresa Encontrada:");
                            $("#encontrada").val(1);
                            //acomodo medidas de campos
                            $("#contenedorPertenece").hide();
                            $("#contenedorEstado").hide();
                            var clasesOld = ['col-md-3', 'col-lg-3', 'col-md-5', 'col-lg-5'];
                            var clasesnew = ['col-md-5', 'col-lg-5', 'col-md-6', 'col-lg-6'];
                            for(var i = 0; i < clasesOld.length; i++){
                                if(i == 0 || i == 1){
                                    $("#contenedorCodigo").removeClass(clasesOld[i]).addClass(clasesnew[i]);
                                }else{
                                    $("#contenedorNombre").removeClass(clasesOld[i]).addClass(clasesnew[i]);
                                }
                            }
                            $("#contenedorSede").removeClass('col-md-4').addClass('col-md-5');
                            $("#contenedorSede").removeClass('col-lg-4').addClass('col-lg-5');
                            //consulto el listado de sedes asociadas
                            listaSedes(codigo);

                        }else{
                            $("#lblNombre").text("Digite Nombre:");
                            $("#nombre").removeAttr('readonly');
                            $("#nombre").val("");
                            $("#contenedorSedes").fadeOut();
                            $("#encontrada").val(2);
                             //acomodo medidas de campos
                            $("#contenedorPertenece").show();
                            $("#contenedorEstado").show();
                            var clasesOld = ['col-md-5', 'col-lg-5', 'col-md-6', 'col-lg-6'];
                            var clasesNew = ['col-md-3', 'col-lg-3', 'col-md-5', 'col-lg-5'];
                            for(var i = 0; i < clasesNew.length; i++){
                                if(i == 0 || i == 1){
                                    $("#contenedorCodigo").removeClass(clasesOld[i]).addClass(clasesNew[i]);
                                }else{
                                    $("#contenedorNombre").removeClass(clasesOld[i]).addClass(clasesNew[i]);
                                }
                            }
                            $("#contenedorSede").removeClass('col-md-5').addClass('col-md-4');
                            $("#contenedorSede").removeClass('col-lg-5').addClass('col-lg-4');
                            actualizaSedes(2,codigo);
                        }
                        $("#carga").fadeOut();
                        $("#nombre").removeAttr('disabled');

                        
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
        }else{
            $("#contenedorSedes").fadeOut();
            $("#nombre").val("");
        }
    });

    function listaSedes(codigo)
    {
        var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('consult.sedes')}}", 
                    data: {'codigo':codigo, _token:token},
                    cache: false,
                    success: function(response){
                        if(response != "n"){
                            $("#contenedorSedes").fadeIn();
                            document.getElementById('cargaDatos').innerHTML = response;
                            actualizaSedes(1,codigo);
                        }else{
                            $("#contenedorSedes").fadeOut();
                            document.getElementById('cargaDatos').innerHTML = "";
                            actualizaSedes(2,codigo);
                        }
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
        
    }

    function actualizaSedes(opcion,codigo)
    {
        $("#lblSede").text('Buscando Sedes...');
        var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('actualiza.sedes')}}", 
                    data: {'codigo':codigo,'opcion':opcion, _token:token},
                    cache: false,
                    success: function(response){
                        if($("#encontrada").val() == 1){
                            $("#lblSede").text('Asociar Sede: ');
                        }else{
                            $("#lblSede").text('Seleccione la Sede: ');
                        }
                        document.getElementById('selectSedes').innerHTML = response;
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
        
    }

    function registrarEmpresa()
    {
        var codigo = $("#codigo").val();
        var nombre = $("#nombre").val();
        var grupo = $("#grupo").val();
        var sede = $("#selectSedes").val();
        var estado = $("#estado").val();
        if(codigo.length == 0 || nombre.length == 0){
            alert('Campos Incompletos');
        }else{
            $("#loadBtn").fadeIn();
            $("#txtBtnRegistrar").text('Procesando...');
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('registra.empresa')}}", 
                    data: {'codigo':codigo,'nombre':nombre,'grupo':grupo,'sede':sede,'estado':estado, _token:token},
                    cache: false,
                    success: function(response){
                        if(response){
                            if($("#encontrada").val() != 1){
                                $("#codigo").val('');
                                $("#nombre").val('');
                                alert('Registro Exitoso');
                            }else{
                                alert('Sede Asociada Con Éxito');
                                $('#loadSedes').fadeIn();
                                listaSedes(codigo);
                                $('#loadSedes').fadeOut();
                            }
                        }else{
                            alert('error')
                        }
                          $("#txtBtnRegistrar").text('Registrar');
                          $("#loadBtn").fadeOut();
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        $("#txtBtnRegistrar").text('Registrar');
                        $("#loadBtn").fadeOut();   
                        }
                    });
        }
    }

    function eliminarSede(sede, empresa)
    {
        var confirma = confirm("¿Está seguro de eliminar esta sede?");
        if(confirma){
            $("#loadElimina").fadeIn();
            var token = '{{csrf_token()}}';
                $.ajax({
                        type:  'POST',
                        async: true,
                        url: "{{route('elimina.sede')}}", 
                        data: {'sede':sede,'empresa':empresa, _token:token},
                        cache: false,
                        success: function(response){
                            $("#loadElimina").fadeOut();
                            if(response != 1){
                                listaSedes(empresa);
                                actualizaSedes(1,codigo);
                            }else{
                                actualizaSedes(2,codigo);
                            }
                        },
                        error:function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError);
                            $("#loadElimina").fadeOut();
                            }
                        });
        }
        
    }

</script>

@endsection