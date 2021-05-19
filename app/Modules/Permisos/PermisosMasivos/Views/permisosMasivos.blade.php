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
                   <h4> Permisos Masivos </h4>
                </div>
                <div class="card-body">
                    <form action="{{route('consultarPermisosMasivos')}}" method="POST">
                        <div class="form-group">
                            @csrf
                            <label for="sede">Sede: </label>
                            <select name="sede" id="sede" class="form-control">
                                <option value="0">--SELECCIONE--</option>
                                <option value="1">Cali</option>
                                <option value="2">Bogota</option>
                                <option value="3">Yumbo</option>
                                <option value="4">Medellin</option>
                                <option value="5">Montevideo Bgta</option>
                                <option value="6">Palmira</option>
                            </select>
                        </div>
                        <div class="form-group">
                            @csrf
                            <label for="empresa">Empresa: </label>
                            <select name="id_empresa" id="empresa" class="form-control">
                                <option value="TODOS">TODAS LAS EMPRESAS</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}">{{$emp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="form-group">
                            @csrf
                            <label for="vehi">Fecha Cargue: </label>
                           <input type="date" class="form-control" name="fecha_reg" id="fecha_reg">
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

<script>
    $('#sede').on('change',function(){
        var id_sede= $('#sede').val();
        if(id_sede != 0){
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: false,
                    url: "consultarEmpresas", 
                    data: {'ids':id_sede, _token:token},
                    cache: false,
                    success: function(response){
                        document.getElementById("empresa").innerHTML = response;
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                                }
                    });
                }
            });
</script>
@include('layouts.footer', ['modulo' => 'horarios'])