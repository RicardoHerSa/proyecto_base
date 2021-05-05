@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-1">
            </div>

            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><strong>Usuario #{{ $user->id }}</strong></div>
                    <div class="card-body">

                        <a href="{{ url('/users') }}" title="Back"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                        <a href="{{ url('/users/' . $user->id . '/edit') }}" title="Edit user"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>

                        <form method="POST" action="{{ url('users' . '/' . $user->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger" title="Delete user" onclick="return confirm(&quot;Estas seguro de eliminar el usuario {{ $user->name }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Id</th><td>{{ $user->id }}</td>
                                    </tr>
                                    <tr><th> Nombre </th><td> {{ $user->name }} </td></tr>
                                    <tr><th> Correo electrónico </th><td> {{ $user->email }} </td></tr>
                                    @if(isset($consulta[0]))
                                        <tr>
                                            <th>Grupos</th>
                                            <td>
                                            @foreach($consulta as $item1 )
                                                {{ $item1->getNombre($item1->usergroup_id) }}, 
                                            @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                    <tr><th> Id Organizacion </th><td> {{ $user->profile_orgcountry }} </td></tr>
                                    <tr><th> Id Empleado </th><td> {{ $user->profile_externalid }} </td></tr>
                                    <tr><th> Ordinal Empelado </th><td> {{ $user->profile_ordinal }} </td></tr>
                                    <tr>
                                        <th> Gestor externo </th>
                                            @if($user->gestor_externo == 1)
                                                <td>Si</td>
                                            @else
                                                <td>No</td>
                                            @endif
                                    </tr>
                                    <tr>
                                        <th>Última fecha de ingreso </th>
                                        @if($user->lastvisitdate != null)
                                        <td> {{ $user->lastvisitdate }} </td>
                                        @else
                                        <td>Sin registro</td>
                                        @endif
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
