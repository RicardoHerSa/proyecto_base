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
                    <div class="row">
                        <div class="col-xs-12 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="">Código de Empresa: *</label>
                                <input id="codigo" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="">Nombre: *</label>
                                <input id="nombre" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Ciudad: *</label>
                                <select class="form-control" name="" id="ciudad">
                                    @foreach ($ciudades as $ciudad)
                                        <option value="{{$ciudad->id}}">{{$ciudad->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <label for="">Seleccione SISO: *</label>
                            <input type="text" value="0" class="form-control" id="siso">
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <label for="">¿Pertenece al gurpo Carvajal?: *</label>
                            <select class="form-control" name="" id="grupo">
                              <option value="1">SI</option>
                              <option value="0">NO</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <label for="">Seleccione la Sede: *</label>
                                <select id="selectSedes" class="form-control" name="">
                                    @foreach ($sedes as $sed)
                                        <option value="{{$sed->id_ubicacion}}">{{$sed->descripcion}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-5 col-lg-5">
                            <label for="">Estado</label>
                            <select class="form-control" name="" id="estado">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success" onclick="registrarEmpresa()">Registrar</button>
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
                <div class="card-body">
                    <table class="table table-light">
                        <thead class="thead-light">
                            <tr>
                                <th>Ciudad</th>
                                <th>Nombre de la Sede</th>
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

                            //consulto el listado de sedes asociadas
                            listaSedes(codigo);

                        }else{
                            $("#nombre").removeAttr('readonly');
                            $("#nombre").val("");
                            $("#contenedorSedes").fadeOut();
                            actualizaSedes(2,codigo)
                        }

                        
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
        var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('actualiza.sedes')}}", 
                    data: {'codigo':codigo,'opcion':opcion, _token:token},
                    cache: false,
                    success: function(response){
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
        var ciudad = $("#ciudad").val();
        var siso = $("#siso").val();
        var grupo = $("#grupo").val();
        var sede = $("#selectSedes").val();
        var estado = $("#estado").val();
        if(codigo.length == 0 || nombre.length == 0){
            alert('Campos Incompletos');
        }else{
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('registra.empresa')}}", 
                    data: {'codigo':codigo,'nombre':nombre,'ciudad':ciudad,'siso':siso,'grupo':grupo,'sede':sede,'estado':estado, _token:token},
                    cache: false,
                    success: function(response){
                        if(response){
                            listaSedes(codigo);
                            alert('Registro Exitoso');
                        }else{
                            alert('error')
                        }
                    },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
        }
    }
</script>

@endsection