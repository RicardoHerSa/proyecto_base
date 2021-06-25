@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if (Session::has('msj') && Session::get('msj') == "ok")
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    <strong>Información!</strong> Portero eliminado con éxito.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(Session::has('msj') && Session::get('msj') == "err")
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <strong>Información!</strong> Error al eliminar portero.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
             @endif
            <div class="card">
                <div class="card-header">
                   <strong> Gestor de Porteros </strong>
                </div>
                <div class="card-body">
                    <div class="float-left">
                        <a href="{{url('/porteros').'/create'}}" class="btn btn-success my-3" title="Crear nuevo portero"> <i class="fa fa-plus" aria-hidden="true"></i> Crear Portero</a>
                        <a href="{{route('asociar.porterias')}}" class="btn btn-primary my-3" title="Asociar Porterías"> <i class="fa fa-plus" aria-hidden="true"></i> Asociar Porterías</a>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table id="tblistado" class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Sede Asociada</th>
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
                        url: "{{route('consultar.porteros')}}",
                        type: "get",
                        dataType: "json",
                        data: {_token:'{{csrf_token()}}'},
                        error: function(e){
                            console.log(e.responseText);
                        }
                    },
               "bDestroy": true,
               "iDisplayLength": 10,//paginacion
               "order": [[0, "desc"]] //ordenar (columna , orden) 
        }).dataTable();
    }
    function cambiarEstado(idPortero)
    {
       
      var estado =  $("#estado"+idPortero).val();;
      if(estado == "s"){
        estado = "N";
        $("#estado"+idPortero).val("n");

      }else{
        estado = "S";
        $("#estado"+idPortero).val("s")
      }
      var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('actual.portero')}}", 
                    data: {'id':idPortero, 'estado':estado, _token:token},
                    cache: false,
                    success: function(response){
                        toastr.success('Estado Cambiado');
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
    }

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
</script>

@endsection