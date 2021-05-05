@extends('layouts.app')
@section('content')

<div class="container2">
    <br>
    

    <div class="row justify-content-center">

        <div class="col-md-12">
            @include('layouts.message')
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            <div class='container'>
                <h5><i class="fa fa-user-circle-o" aria-hidden="true"></i> {{ Auth::user()->name }}</h5>
                <span class="badge badge-info">{{ auth()->user()->profile_externalid}}</span>

                <hr>

                <div class="row">
                    <div class="col"><i class="fa fa-cog" aria-hidden="true"></i> Gestor de Permisos
                        <br><br>
                        <div class="btn-group-vertical">
                            <a href="users" class="btn btn-outline-info" role="button" aria-pressed="true"><i class="fa fa-users" aria-hidden="true"></i> Gestor de Usuarios </a>
                            <a href="usergroup" class="btn btn-outline-info" role="button" aria-pressed="true"><i class="fa fa-cubes" aria-hidden="true"></i> Gestor de Grupos </a>
                            <a href="viewlevels" class="btn btn-outline-info" role="button" aria-pressed="true"><i class="fa fa-tasks" aria-hidden="true"></i> Gestor Niveles de Accesos</a>
                        </div>

                    </div>

                    <div class="col" style="  border-left: 1px solid #D3D3D3;"><i class="fa fa-cog" aria-hidden="true"></i> Menú
                        <br><br>
                        <div class="btn-group-vertical">

                            <a href="menu" type="button" class="btn btn-outline-info " role="button" aria-pressed="true"><i class="fa fa-bars" aria-hidden="true"></i> Gestor de Menús</a>

                        </div>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col">

                        <i class="fa fa-bar-chart" aria-hidden="true"></i> Resumen
                        <br><br>
                        <ul>
                            <li>Total de Usuarios Activos : <span class="badge badge-success"> {{$usuariosAct}}</span> </li>
                            <li>Total de Usuarios lda : <span class="badge badge-success"> {{$usuariosActlda}}</span></li>
                            <li>Total de Usuarios no lda : <span class="badge badge-success"> {{$usuariosActNolda}}</span></li>
                        </ul>

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col"><i class="fa fa-bar-chart" aria-hidden="true"></i> Total de Usuarios Activos por Grupo
                        <br><br>
                        <div class="" aling='center'>
                            <div class="table-responsive">
                                <table class=" table table-striped table-bordered table-sm table-responsive table-hover " style="font-size:13px;">

                                    <thead>
                                        <tr>
                                            <th>Grupo</th>
                                            <th>Total Usuarios</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($UsuariosxGEmpresa as $tg)
                                        <tr>
                                            <td>{{ $tg->grupo }}</td>
                                            <td><span class="badge badge-success">{{ $tg->total }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>

        </div>
    </div>
</div>
@endsection