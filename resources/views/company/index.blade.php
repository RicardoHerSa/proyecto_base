@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if (Session::has('msj') && Session::get('msj') == "ok")
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    <strong>Información!</strong> Empresa eliminada con éxito.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(Session::has('msj') && Session::get('msj') == "err")
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <strong>Información!</strong> Error al eliminar empresa.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
             @endif
            <div class="card">
                <div class="card-header">
                   <strong> Gestor de Empresas </strong>
                </div>
                <div class="card-body">
                    <div class="float-left">
                        <a href="{{url('/company').'/create'}}" class="btn btn-success my-3" title="Crear nueva empresa"> <i class="fa fa-plus" aria-hidden="true"></i> Crear Empresa / Asociar Sedes</a>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table id="empresas" class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Sedes Asociadas</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($empresas as $emp)

                                    <tr>
                                        <td>{{$emp->descripcion}}</td>
                                        <td>{{$emp->codigo_empresa}}</td>
                                        @if ($emp->activo == "S")
                                          <td>
                                              <div style="cursor: pointer;" class="custom-control custom-switch">
                                                <input onchange="cambiarEstado('estado{{$loop->index}}',{{$emp->codigo_empresa}})"  type="checkbox" checked class="custom-control-input" id="estado{{$loop->index}}" value="s">
                                                <label class="custom-control-label" for="estado{{$loop->index}}"></label>

                                             </div>
                                         </td>
                                        @else 
                                        <td>
                                            <div style="cursor: pointer;" class="custom-control custom-switch">
                                              <input onchange="cambiarEstado('estado{{$loop->index}}',{{$emp->codigo_empresa}})"  type="checkbox" class="custom-control-input" id="estado{{$loop->index}}" value="n">
                                              <label class="custom-control-label" for="estado{{$loop->index}}"></label>

                                           </div>
                                       </td>
                                        @endif
                                        <td>
                                            @php
                                            $codigo = $emp->codigo_empresa;
                                                
                                                $sedes = DB::table('ohxqc_ubicaciones as ubi')
                                                ->select('ubi.descripcion')
                                                ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
                                                ->where('emp.codigo_empresa', $codigo)
                                                ->get();
                                                $cant = count($sedes);
                                                $i = 0;
                                                foreach($sedes as $se){
                                                    echo $se->descripcion;
                                                    $i++;
                                                    if($i != $cant){
                                                        echo ", ";
                                                    }else{
                                                        echo ".";
                                                    }
                                                }
                                            @endphp
                                       </td>
                                       <td style="display: inline-flex;">
                                        <a class='show-user' href='{{url('/company').'/'.$emp->codigo_empresa}}' title='Info empresa'><button class='btn btn-info btn-sm'><i class='fa fa-eye'></i></button></a>
                                        <a class='edit-user' href='{{url('company/')}}' title='Editar empresa'><button class='btn btn-warning btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>
                                        <button onclick="eliminarEmpresa({{$emp->codigo_empresa}},'{{$emp->descripcion}}')" class='btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i></button>
                                       </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    Footer
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function cambiarEstado(campo,codigoEmpresa)
    {
       
      var estado =  $("#"+campo).val();;
      if(estado == "s"){
        estado = "N";
        $("#"+campo).val("n");

      }else{
        estado = "S";
        $("#"+campo).val("s")
      }
      var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('actual.estado')}}", 
                    data: {'codigo':codigoEmpresa, 'estado':estado, _token:token},
                    cache: false,
                    success: function(response){
                       
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
    }

    function eliminarEmpresa(codigoEmpresa, descripcion)
    {
      var confirma = confirm('¿Está seguro de eliminar la empresa '+ descripcion+'?');
      if(confirma){
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "company/"+codigoEmpresa, 
                    data: { _token:token, codigo:codigoEmpresa},
                    cache: false,
                    success: function(response){
                       
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
         }
      
    }
</script>

@endsection