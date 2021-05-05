@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card cardOverflow">
                <div class="card-header"><strong>Gestor de grupos</strong></div>
                <div class="card-body">
                    <a href="{{ url('/usergroup/create') }}" class="btn btn-success" title="Add New Usergroup">
                        <i class="fa fa-plus" aria-hidden="true"></i> Agregar nuevo grupo
                    </a>
                    <form method="GET" action="{{ url('/usergroup') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Buscar..." value="{{ request('search') }}">
                            <span class="input-group-append">
                                <button class="btn btn-secondary" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    @include('layouts.message')
                    <br /><br />
                    <table id="treeTable" class="table">
                        <thead>
                            <tr>
                                <th>Titulo del grupo</th>
                                <th>Grupo principal</th>
                                <th>Empresa Id</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usergroup as $item)
                            <tr data-id="{{ $item->id }}" data-parent-id="{{ $item->parent_id }}" class="group-style item-{{$item->subParent($item->id)}}">
                                <td data-column="name">- {{ $item->title }}</td>
                                <td id="grupo">{{ $item->getQueryUsergroupTitle($item->parent_id, $item->id) }}</td>
                                <td>{{ $item->companycessid }}</td>
                                <td>
                                    <a href="{{ url('/usergroup/' . $item->id) }}" title="View Usergroup"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                    <a href="{{ url('/usergroup/' . $item->id . '/edit') }}" title="Edit Usergroup"><button class="btn btn-warning btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                                    <form method="POST" action="{{ url('/usergroup' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Usergroup" onclick="return confirm(&quot;Estas seguro de eliminar el grupo {{ $item->title }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-wrapper"> {!! $usergroup->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection