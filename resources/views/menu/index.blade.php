@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                <div class="card-header"><strong> Gestor de menus </strong></div>
                    <div class="card-body">
                        <a href="{{ url('/menu/create') }}" class="btn btn-success" title="Add New Menu">
                            <i class="fa fa-plus" aria-hidden="true"></i> Agregar nuevo menu
                        </a>
                        
                        @include('layouts.message')
                        <br/><br/>
                        <div class="table-responsive">
                            <table id="menuTable" class="table">
                                <thead>
                                    <tr>
                                        <th class="classId">Id Menu</th>
                                        <th class="classCheck">Estado</th>
                                        <th class="classMenu">Tipo menu</th>
                                        <th class="classTitle">Nombre Menu</th>
                                        <th class="classLink">URL Menu</th>
                                        <th class="classParent">Id padre</th>
                                        <th class="classOptions">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($menu as $item)
                                    <tr>
                                        <td class="classId col-xs-1"  align="center">{{ $item->id }}</td>
                                        <td class="classCheck col-xs-1">
                                            <input type="checkbox" data-id="{{ $item->id }}" name="status" class="js-switch" {{ $item->published ? 'checked' : '' }}>
                                        </td>
                                        <td class="classMenu col-xs-1">{{ $item->menutype }}</td>
                                        <td class="classTitle col-xs-2">{{ $item->title }}</td>
                                        <td class="classLink col-xs-3">{{ $item->link }}</td>
                                        <td class="classParent col-xs-2" align="center">{{ $item->parent_id }}</td>
                                        <td class="classOptions col-xs-2" style="width:120px"> 
                                            <a class="col-xs-3" href="{{ url('/menu/' . $item->id) }}" title="View menu"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                            <a class="col-xs-3"  href="{{ url('/menu/' . $item->id . '/edit') }}" title="Edit menu"><button class="btn btn-warning btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                                            <form  class="col-xs-3"  method="POST" action="{{ url('/menu' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete menu" onclick="return confirm(&quot;Estas seguro de eliminar el menu {{ $item->title }}&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
                let switchery = new Switchery(html,  { size: 'small' });
            });

        $(document).ready(function(){
             //Datatable menu
            var table_s = $('#menuTable').DataTable({
                language: {
                    processing:     "Procesando...",
                    search:         "Buscar&nbsp;:",
                    lengthMenu:     "Páginas _MENU_ ",
                    info:           "Página _START_ de _END_ ",
                    loadingRecords: "Cargando...",
                    infoFiltered:   "",
                    zeroRecords:    "No se encontraron registros.",
                    emptyTable:     "No hay información",
                    infoEmpty:      "",
                    paginate: {
                        first:      "Primero",
                        previous:   "Anterior",
                        next:       "Siguiente",
                        last:       "Ultimo"
                    }
                }
            });
            
            changeSwitch();

            table_s.on( 'search.dt', function () {
                changeSwitch();
            });
            table_s.on( 'draw', function () {
                changeSwitch();
            });

        });
        function changeSwitch(){
            
            $('.js-switch').unbind('change');
            $('.js-switch').change(function () {
                let published = $(this).prop('checked') === true ? 1 : 0;
                let menuId = $(this).data('id');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '{{ route('menus.blocks') }}',
                    data: {'published': published, 'menu_id': menuId},
                    success: function (data) {
                        toastr.options.closeButton = true;
                        toastr.options.closeMethod = 'fadeOut';
                        toastr.options.closeDuration = 100;
                        toastr.info(data.message);
                    }
                });
            });
        }
    </script>
@endsection
