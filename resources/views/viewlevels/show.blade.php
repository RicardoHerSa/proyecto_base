@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-1">
            </div>


            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><strong> Niveles acceso #{{ $viewlevel->id }}</strong> </div>
                    <div class="card-body">

                        <a href="{{ url('/viewlevels') }}" title="Back"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atrás</button></a>
                        <a href="{{ url('/viewlevels/' . $viewlevel->id . '/edit') }}" title="Edit Viewlevel"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                        <form method="POST" action="{{ url('viewlevels' . '/' . $viewlevel->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger" title="Delete Viewlevel" onclick="return confirm(&quot;Estas seguro de eliminar el nivel de acceso {{ $viewlevel->title }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr> <th>ID</th><td>{{ $viewlevel->id }}</td></tr>
                                    <tr><th>Nombre del nivel</th><td>{{ $viewlevel->title }}</td></tr>
                                    <tr>
                                        <th>Grupos que tiene acceso </th>
                                        <td>
                                            @foreach($consulta as $item1 )
                                                @if($viewlevel->id == $item1->viewlevel_id)
                                                    {{ $item1->getNombre($item1->usergroup_id) }},
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>
@endsection
