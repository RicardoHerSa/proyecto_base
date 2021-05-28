@include('layouts.app', ['modulo' => 'asignacion'])
<div class="container">
    @if (Session::has('msj'))
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('msj')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row mt-5">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>Cargue de Colaboradores</h4>
          </div>
          <div class="card-body">
            <p class="card-text">Suba un archivo excel con las características indicadas.</p>
          <form method="POST" action="{{route('cargarColborador')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <input type="file" class="form-control" name="archivo" accept=".xls,.xlsx">
            </div>
          </div>
          <div class="card-footer">
            <div class="float-left">
              <input type="submit" value="Cargar" class="btn btn-primary">
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
      
</div>

@include('layouts.footer', ['modulo' => 'asignacion'])