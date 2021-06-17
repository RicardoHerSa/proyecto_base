@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
        <div class="col-xs-12 col-md-10 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <strong>Empresa: #{{$codigoEmpresa}}</strong>
                </div>
                <div class="card-body">
                    <a href="{{ url('/company') }}" title="Volver"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <a href="{{ url('/company/' . $codigoEmpresa . '/edit') }}" title="Editar Empresa"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
        
                    <form method="POST" action="{{ url('company' . '/' . $codigoEmpresa) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger" title="Eliminar Empresa" onclick="return confirm(&quot;¿Estás seguro de eliminar la empresa {{ $codigoEmpresa }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </form>
                    <br/>
                    <br/>
        
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr><th>Código</th><td>{{ $codigoEmpresa}}</td></tr>
                                <tr><th>Nombre</th><td>{{ $nombre}}</td></tr>
                                <tr><th>Estado</th><td>{{ $estado}}</td></tr>
                                <tr> <th>Ciudad</th><td>{{ $ciudad}}</td></tr>
                                <tr> <th>Grupo Carvajal</th><td>{{ $grupo}}</td></tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>
    <div class="row mt-3">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
        <div class="col-xs-12 col-md-10 col-lg-10">
            <table class="table table-light">
                <thead class="thead-light">
                    <tr>
                        <th>Lista de Sedes Asociadas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sedesAsociadas as $sed)
                    <tr>
                        <td>{{$sed->descripcion}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>
</div>



@endsection