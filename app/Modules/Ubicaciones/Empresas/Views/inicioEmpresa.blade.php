@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if (Session::has('msj') && Session::get('msj') == "ok")
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    <strong>Información!</strong> Empresa eliminada con éxito.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(Session::has('msj') && Session::get('msj') == "err")
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <strong>Información!</strong> Error al eliminar empresa.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
             @endif
            <div class="card">
                <div class="card-header">
                   <strong> Gestor de Empresas </strong>
                </div>
                <div class="card-body">
                    <div class="float-left">
                        <a href="{{url('/Empresas').'/create'}}" class="btn btn-success my-3" title="Crear nueva empresa"> <i class="fa fa-plus" aria-hidden="true"></i> Crear Empresa / Asociar Sedes</a>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table id="tblistado" class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Sedes Asociadas</th>
                                    <th style="display: flex;width:100%;">Opciones</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function(){
        listar();
      
    });
    function listar()
    {
        tabla= $('#tblistado').dataTable(
        {
            language: {  
                processing: "Procesando...", 
                search: "Buscar&nbsp;:", 
                lengthMenu: "Registros _MENU_ ", 
                info: "Registros del _START_ al _END_ ", 
                loadingRecords: "Cargando...", 
                infoFiltered: "", 
                zeroRecords: "No se encontraron registros.", 
                emptyTable: "No hay información", 
                infoEmpty: "",
                paginate: { 
                    first: "Primero", 
                    previous: "Anterior", 
                    next: "Siguiente", 
                    last: "Ultimo" 
                } 
            }, 
            "aProcessing":true,//Activa el procesamiento del datatable
            "aServerSide":true,//Paginacion y filtado realizados por servidor 
            dom: 'Bfrtip',//Definimos los elemtos del contro de tabla 
            buttons:[
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdf'
            ],
            "ajax":
                    {
                        url: "{{route('consultar.empresas')}}",
                        type: "get",
                        dataType: "json",
                        data: {_token:'{{csrf_token()}}'},
                        error: function(e){
                            console.log(e.responseText);
                        }
                    },
               "bDestroy": true,
               "iDisplayLength": 10,//paginacion
               "ordering": false  //evitar el orden por parte de DataTable y dejar el de PGSQL
               //"order": [[5, "desc"]] //ordenar (columna , orden) 
        }).dataTable();
    }
    function cambiarEstado(codigoEmpresa)
    {
       
      var estado =  $("#estado"+codigoEmpresa).val();;
      if(estado == "s"){
        estado = "N";
        $("#"+codigoEmpresa).val("n");

      }else{
        estado = "S";
        $("#"+codigoEmpresa).val("s")
      }
      var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('actual.estado')}}", 
                    data: {'codigo':codigoEmpresa, 'estado':estado, _token:token},
                    cache: false,
                    success: function(response){
                        toastr.success('Estado Cambiado');
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
    }

    function eliminarEmpresa(codigoEmpresa)
    {
      var confirma = confirm('¿Está seguro de eliminar la empresa ?');
      if(confirma){
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('eliminar.empresa')}}", 
                    data: { _token:token, codigo:codigoEmpresa},
                    cache: false,
                    success: function(response){
                       if(response == 1){
                            toastr.success('Empresa Eliminada');
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
</script>

@endsection