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
                   <h2> Reporte de Ingreso </h2>
                </div>
                <div class="card-body">
                    <p class="card-text">Digite la cédula de la persona a consultar</p>
                    <div class="form-group">
                    <form action="{{route('consultarIngresoPersona')}}" method="POST">
                        @csrf
                        <label for="cedula">Cédula: </label>
                        <input type="text" id="cedula" name="cedula" class="form-control">
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