@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong> Gestor de Usuarios </strong></div>
                <div class="card-body">
                    @include('layouts.message')
                    <a href="{{ url('/users/create') }}" class="btn btn-success" title="Agregar nuevo usuario">
                        <i class="fa fa-plus" aria-hidden="true"></i> Agregar Usuario
                    </a>
                   
                    <table accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right">
                        <tr>
                            <td>
                                <select class="form-control" name="search" id='searchByGroups'>
                                    <option value="0">Seleccione un grupo</option>
                                    @foreach($group as $usergroups)
                                    <option value="{{ $usergroups->id }}">{{$usergroups->title}}</option>
                                    @endforeach
                                </select>

                            </td>
                        </tr>
                    </table>
                    <button id="massiveRight" class="btn btn-success form-inline my-1 mr-2 my-lg-0 float-right" title="Desbloquear usuarios"><i class="fa fa-unlock" aria-hidden="true"></i></button>
                    <button id="massive" class="btn btn-dark form-inline  mr-1 my-lg-0 float-right" title="Bloquear usuarios"><i class="fa fa-lock" aria-hidden="true"></i></button>
                    <br /><br />
                    <div class="table-responsive">

                        <table id="users" class="table" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectall"></th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Correo electrónico</th>
                                    <th>Último ingreso</th>
                                    <th>ID</th>
                                    <th style="width:150px">Opciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#searchGroup").select2({
        placeholder: "Seleccione un grupo",
        allowClear: true,
    });

     //Select
     $("#selectall").on("click", function() {
                $(".case").prop("checked", this.checked);
            });

            //marque la casilla de selectAll si todas estan seleccionadas y viceversa
            $(".case").on("click", function() {
                if ($(".case").length == $(".case:checked").length) {
                    $("#selectall").prop("checked", true);
                } else {
                    $("#selectall").prop("checked", false);
                }
            });

    $(document).ready(function() {

        printDatatable(false);
        
        $('#searchByGroups').change(function(){
            printDatatable(true);
        });
    });

    var printDatatable = function(refresh){
        
        if(refresh){
            $('#users').DataTable().clear().destroy();
        }
        $.fn.dataTable.ext.errMode = 'none';
        //Datatable usuario
        $('#users').DataTable({

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
            'lengthMenu': [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            'responsive': true,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'GET',
            'ajax': {
                        'url':'{{ route('users.pagination') }}',
                        'data': {
                            'searchByGroups': $('#searchByGroups').val()
                        }
                    },
            'columns': [
                {
                    render: function (data,type, row){
                        return '<input type="checkbox" class="case" name="case[]" data-id="'+row.id+'">';
                    }
                },
                { data: 'name' },
                { data: 'username' },
                {
                    render: function (data,type, row){
                        if(row.block == "1"){
                            var estado = ""
                        }else{
                            var estado = "checked"
                        }
                        //console.log(row);
                        return "<input type='checkbox' data-id='"+row.id+"' name='status' class='js-switch' "+estado+">";
                    }
                },
                { data: 'email' },
                { data: 'lastvisitdate' },
                { data: 'id' },
                {
                    render: function ( data, type, row ) {
                        return "<a class='show-user' data-url='{{ url('/users/') }}' data-id='"+row.id+"' href='#' title='Info usuario'><button class='btn btn-info btn-sm'><i class='fa fa-eye'></i></button></a>\n\
                                <a class='edit-user' data-url='{{ url('/users/') }}' data-id='"+row.id+"' href='#' title='Editar usuarios'><button class='btn btn-warning btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>\n\
                                <a class='deleted-user' data-url='{{ url('/users/destroy/') }}' data-id='"+row.id+"' data-name='"+row.name+"' href='#' title='eliminar usuarios'>\n\
                                <button class='btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i></button></a>";  
                    }
                }
            ],
            'select': {
                    style:    'os',
                    selector: 'td:first-child'
                },
            
            'order': [[ 1, 'asc' ]]
            
        }).on('draw.dt', function () {

            $(".show-user").on("click", function(event){
                event.preventDefault();
                let enlace = $(this).attr("data-id");
                let url = $(this).attr("data-url");
                window.location.replace(url+"/"+enlace);
            });
            $(".edit-user").on("click", function(event){
                event.preventDefault();
                let id = $(this).attr("data-id");
                let user_url = $(this).attr("data-url");
                window.location.replace(user_url+"/"+id+"/edit");
            });
            $(".deleted-user").on("click", function(event){
                event.preventDefault();
                let name = $(this).attr("data-name");
                if(confirm("Estas seguro de eliminar el usuario"+" "+name)){
                    let id = $(this).attr("data-id");
                    let user_url = $(this).attr("data-url");
                   // console.log(user_url+"/"+id);
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: user_url+"/"+id,
                        data: {'id': id},
                        success: function(data) {
                            toastr.options.closeButton = true;
                            toastr.options.closeMethod = 'fadeOut';
                            toastr.options.closeDuration = 100;
                            toastr.success(data.message);
                            setTimeout(function() {
                                printDatatable(true);
                            }, 400);
                        }
                    });
                };
            });
            $(".switchery-small").remove();
            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
                let switchery = new Switchery(html, {
                    size: 'small'
                });
            });
            //Block usuario
            $('.js-switch').change(function() {
                let block = $(this).prop('checked') === true ? 0 : 1;
                let userId = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '{{ route('users.block') }}',
                    data: {'block': block, 'user_id': userId },
                    success: function(data) {
                        toastr.options.closeButton = true;
                        toastr.options.closeMethod = 'fadeOut';
                        toastr.options.closeDuration = 100;
                        toastr.info(data.message);
                        /*setTimeout(function() {
                            window.location.reload(1);
                        }, 1000);*/
                    }
                });
            });
        });
    }

    //Block usuario masivo
    $('#massive').on("click", function(event) {
        var arrayMasive = [];
        $(".case:checked").each(function(index) {
            let massive = $(this).data('id');
            //console.log(massive);
            arrayMasive.push(massive);
        });
       // console.log(arrayMasive);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{ route('users.blockmassive') }}',
            data: {'massive': arrayMasive},
            success: function(data) {
                toastr.options.closeButton = true;
                toastr.options.closeMethod = 'fadeOut';
                toastr.options.closeDuration = 100;
                if (data.message) {
                    toastr.warning(data.message);
                    setTimeout(function() {
                    printDatatable(true);
                    }, 1000);
                }
                if (data.messageError) {
                    toastr.error(data.messageError);
                }
            }
        });
    });

    //Unblock usuario masivo
    $('#massiveRight').on("click", function(event) {
        var arrayMasive = [];
        $(".case:checked").each(function(index) {
            let massive = $(this).data('id');
            //console.log(massive);
            arrayMasive.push(massive);
        });
        //console.log(arrayMasive);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{ route('users.unblockmassive') }}',
            data: {
                'massiveright': arrayMasive
            },

            success: function(data) {
                toastr.options.closeButton = true;
                toastr.options.closeMethod = 'fadeOut';
                toastr.options.closeDuration = 100;
                if (data.message) {
                    toastr.success(data.message);
                    setTimeout(function() {
                    printDatatable(true);
                    }, 1000);
                }
                if (data.messageError) {
                    toastr.error(data.messageError);
                }
            }
        });

    });
</script>
@endsection