@include('layouts.app', ['modulo' => 'asignacion'])
<div class="container">
    @if (Session::has('errArchi'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('errArchi')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if (Session::has('errEstr'))
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('errEstr')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if (Session::has('errEmpty'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('errEmpty')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if (Session::has('ok'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> {{Session::get('ok')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row mt-3">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>Cargue de Colaboradores</h4>
          </div>
          <div class="card-body">
            <p class="card-text">Suba un archivo excel con las características indicadas: .csv delimitado por (;) punto y coma.</p>
          <form method="POST" action="{{route('cargarColborador')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <input type="file" class="form-control" name="archivo" accept=".csv" required>
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