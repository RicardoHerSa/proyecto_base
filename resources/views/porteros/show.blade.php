@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
        <div class="col-xs-12 col-md-10 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <strong>Portero: #{{$porteros[0]->id}}</strong>
                </div>
                <div class="card-body">
                    <a href="{{ url('/porteros') }}" title="Volver"><button class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i>Atrás</button></a>
                    <a href="{{ url('/porteros/' . $porteros[0]->id . '/edit') }}" title="Editar Portero"><button class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
        
                    <form method="POST" action="{{ url('porteros' . '/' . $porteros[0]->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger" title="Eliminar Portero" onclick="return confirm(&quot;¿Estás seguro de eliminar el portero {{ $porteros[0]->id }}?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </form>
                    <br/>
                    <br/>
        
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr><th>Usuario</th><td>{{ $porteros[0]->usuario}}</td></tr>
                                <tr><th>Tipo</th><td>{{ $porteros[0]->tipo}}</td></tr>
                                <tr><th>Sede Asociada</th><td>{{ $porteros[0]->descripcion}}</td></tr>
                                <tr> <th>Estado</th><td>{{ $porteros[0]->activo=="S"?'Activo':'Inactivo'}}</td></tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-1 col-lg-1"></div>
    </div>
   
</div>

@endsection