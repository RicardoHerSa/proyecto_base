@include('layouts.app', ['modulo' => 'horarios'])
<div class="container">
    @if (Session::has('msj'))
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('msj')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <br>
    @endif
    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                   <h2> Reporte de Parqueaderos </h2>
                </div>
                <div class="card-body">
                    <form action="{{route('consultarReporteParqueadero')}}" method="POST">
                        <div class="form-group">
                            @csrf
                            <label for="cedula">Empresa: </label>
                            <select name="" id="" class="form-control">
                                <option value="">TODAS LAS EMPRESAS</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}">{{$emp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @csrf
                            <label for="cedula">Sede: </label>
                            <select name="" id="" class="form-control">
                                @foreach ($sedes as $sed)
                                    <option value="{{$sed->id}}">{{$sed->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @csrf
                            <label for="cedula">Vehículo: </label>
                            <select name="" id="" class="form-control">
                                <option value="CARROS">CARROS</option>
                                <option value="">MOTOS</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <input type="submit" id="btn_consulta" name="btn_consulta" value="Consultar" class="btn btn-primary"/>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer', ['modulo' => 'horarios'])