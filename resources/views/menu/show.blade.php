@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><strong> Menu # {{ $menu->id }} </strong></div>
                    <div class="card-body">

                        <a href="{{ url('/menu') }}" title="Back"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atrás</button></a>
                        <a href="{{ url('/menu/' . $menu->id . '/edit') }}" title="Edit menu"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                        <form method="POST" action="{{ url('menu' . '/' . $menu->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger" title="Delete menu" onclick="return confirm(&quot;Estas seguro de eliminar el menu: {{ $menu->title }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Id menu</th><td>{{ $menu->id }}</td>
                                    </tr>
                                    <tr><th> Tipo menu </th><td> {{ $menu->menutype }} </td></tr>
                                    <tr><th> Nombre menu </th><td> {{ $menu->title }} </td></tr>
                                    <tr><th> URL menu </th><td> {{ $menu->link }} </td></tr>
                                    <tr><th> Id padre </th><td> {{ ($menu->parent_id != '0')?$menu->parent_id." - ".$menu->nameParent($menu->parent_id):" Principal" }} </td></tr>
                                    <tr><th> Estado menu </th><td> {{ ($menu->published == "1")?"Activo":"Inactivo" }} </td></tr>
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