@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><strong> Gestror de niveles acceso </strong></div>
                    <div class="card-body">
                        <a href="{{ url('/viewlevels/create') }}" class="btn btn-success" title="Add New Viewlevel">
                            <i class="fa fa-plus" aria-hidden="true"></i> Agregar nuevo nivel
                        </a>
                        @include('layouts.message')
                        <br/><br/>
                        <div class="table-responsive">
                            <table id="viewlevel" class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre del nivel de acceso</th>
                                        <!--<th>Grupos que tienen acceso</th>-->
                                        <th>ID</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($viewlevels as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        {{-- @if(isset($consulta[0]))
                                            <td style="width:500px">
                                            @foreach($consulta as $item1 )
                                                @if($item->id == $item1->viewlevel_id)
                                                    {{ $item1->getNombre($item1->usergroup_id) }},
                                                @endif
                                            @endforeach
                                            </td>
                                        @endif --}}
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <a href="{{ url('/viewlevels/' . $item->id) }}" title="View Viewlevel"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                            <a href="{{ url('/viewlevels/' . $item->id . '/edit') }}" title="Edit Viewlevel"><button class="btn btn-warning btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                                            <form method="POST" action="{{ url('/viewlevels' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Viewlevel" onclick="return confirm(&quot;Estas seguro de eliminar el nivel de acceso {{ $item->title }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
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
        $(document).ready(function(){
            //Datatable Viewleves
            $('#viewlevel').DataTable({
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
        });
    </script>
@endsection
