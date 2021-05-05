@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-1">
            </div>

            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><strong> Grupo #{{ $usergroup->id }} </strong></div>
                    <div class="card-body">

                        <a href="{{ url('/usergroup') }}" title="Back"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atrás</button></a>
                        <a href="{{ url('/usergroup/' . $usergroup->id . '/edit') }}" title="Edit Usergroup"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                        <form method="POST" action="{{ url('usergroup' . '/' . $usergroup->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger" title="Delete Usergroup" onclick="return confirm(&quot;Estas seguro de eliminar el grupo: {{ $usergroup->title }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $usergroup->id }}</td>
                                    </tr>
                                    <tr><th> Titulo del grupo </th><td> {{ $usergroup->title }} </td></tr>
                                    <tr><th> Grupo principal </th><td> {{ $usergroup->getNameParent($usergroup->parent_id) }} </td></tr>
                                    <tr><th> Empresa Id </th><td> {{ $usergroup->companycessid }} </td></tr>
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
