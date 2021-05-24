@include('layouts.app', ['modulo' => 'unitario'])
<div class="container">
    @if ($msjRechazo)
        <div class="alert alert-danger  fade show mt-2" role="alert">
            <strong>Información!</strong> Esta Solicitud ha sido rechazada.  <b> <a  data-toggle="modal" data-target="#modalRechazo" href="#">Ver Detalles</a></b>.
        
        </div>
    @else
        @if (!$botonesAccion && !Session::has('corrEnv') && !Session::has('msj'))
        <div class="alert alert-warning  fade show mt-2" role="alert">
            <strong>Información!</strong> Esta Solicitud ya ha sido aprobada. <b> <a  data-toggle="modal" data-target="#modalDetalles" href="#">Ver Detalles</a></b>.
        
        </div>
        @endif

        @if (!$botonesAccion && Session::has('corrEnv'))
        <div class="alert alert-success  fade show mt-2" role="alert">
            <strong>Información!</strong> {{Session::get('corrEnv')}}.
        </div>
        @endif

        @if (!$botonesAccion && Session::has('msj'))
        <div class="alert alert-success  fade show mt-2" role="alert">
            <strong>Información!</strong> {{Session::get('msj')}}
        </div>
        @endif

        @if (!$botonesAccion && Session::has('soliRech'))
        <div class="alert alert-info  fade show mt-2" role="alert">
            <strong>Información!</strong> {{Session::get('soliRech')}}
        </div>
        @endif
    @endif
   

    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Solicitud de Aprobación, Caso #{{$solicitud}}</h4>
                </div>
            </div>

        </div>
    </div> 
    
        <!--Solicitante-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="solicitante">Solicitante, Correo, Ext: </label>
                                    <input id="solicitante" class="form-control" type="text" name="solicitante" readonly value="{{$arrayInfo[0]['solicitante']}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tipoIngreso">Tipo de Ingreso: </label>
                                    <input type="text" readonly class="form-control" value="{{$arrayInfo[0]['tipoIngreso'] == 'PROVEEDOR'?'Contratista':'Visitante'}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tipoId">Tipo de Identificación: </label>
                                    <input type="text" readonly class="form-control" value="{{$arrayInfo[0]['tipoId'] == 'CEDULA'?'Cédula':'Pasaporte'}}">
                                </div>
                            </div>
                        </div>
                        @if ($arrayInfo[0]['empresaC'] != 0)
                            <div class="row" id="empContra">
                                <div class="col-xs-12 col-md-6 col-lg-6">
                                    <div class="form-group" id="contenedorContratista">
                                        <label for="empresaContratista">Empresa Contratista: </label>
                                        <input id="empresaContratista" class="form-control" type="text" name="empresaContratista" value="{{$arrayInfo[0]['empresaC']}}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endif
                    
                    </div>
                </div>
            </div>
        </div> 

        <!--Anexos-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body" id="anexos">
                        <h4 class="text-center mt-2">Personas Que Ingresan</h4>
                        <hr>
                        <br>
                        <table class="table table-light">
                            <thead class="thead-light">
                                <tr>
                                    <th>Identificación</th>
                                    <th>Nombre</th>
                                    <th>Anexo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentos as $docu)
                                        <tr>
                                            <td>{{$docu->identificacion}}</td>
                                            <td>{{$docu->nombre}}</td>
                                            <td><a class="btn btn-primary" href="{{asset('storage').'/'.$docu->url_documento}}" target="_blank">Ver Adjunto</a></td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 

        <!--Fechas de ingreso-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="fechaIngreso">Fecha Inicio Ingreso: </label>
                                    <input id="fechaIngreso" class="form-control" type="date" name="fechaIngreso" readonly value="{{$arrayInfo[0]['fechaIni']}}">
                                </div>
                                <div class="form-group">
                                    <label for="horario">Horario: </label>
                                    <select disabled name="horario" id="horario" class="form-control" >
                                        @foreach ($horarios as $horar)
                                            <option {{$arrayInfo[0]['horario']==$horar->id?'selected':''}} value="{{$horar->id}}">{{$horar->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="empVisi">Empresa a Visitar: </label>
                                    <select disabled name="empVisi" id="empVisi" class="form-control" readonly>
                                        
                                        @foreach ($empresas as $emp)
                                        <option {{$arrayInfo[0]['empVisitar']==$emp->codigo_empresa?'selected':''}} value="{{$emp->codigo_empresa}}">{{$emp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="fechaFin">Fecha Fin Ingreso: </label>
                                    <input id="fechaFin" class="form-control" type="date" name="fechaFin" readonly value="{{$arrayInfo[0]['fechaFinal']}}">
                                </div>
                                <div class="form-group">
                                    <label for="ciudad">Ciudad: </label>
                                    <select disabled name="ciudad" id="ciudad" class="form-control" readonly>
                                        
                                        <option {{$arrayInfo[0]['ciudad']==2?'selected':''}} value="2">Bogota</option>
                                        <option {{$arrayInfo[0]['ciudad']==1?'selected':''}} value="1">Cali</option>
                                        <option {{$arrayInfo[0]['ciudad']==4?'selected':''}} value="4">Medellin</option>
                                        <option {{$arrayInfo[0]['ciudad']==3?'selected':''}} value="3">Yumbo</option>
                                        <option {{$arrayInfo[0]['ciudad']==5?'selected':''}} value="5">Montevideo Bgta.</option>
                                        <option {{$arrayInfo[0]['ciudad']==6?'selected':''}} value="6">Palmira</option>
                                        <option {{$arrayInfo[0]['ciudad']==12?'selected':''}} value="12">Tocancipa</option>
                                        <option {{$arrayInfo[0]['ciudad']==13?'selected':''}} value="13">Ginebra</option></es>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <!--Sedes a Visitar-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card" id="anexarSedes">
                    <div class="card-body">
                        <h4 class="card-title text-center">Sedes a Visitar </h4>
                        <hr>
                        <br>
                        <table class="table table-light">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre de la Sede</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sedesVisitar as $sede)
                                        <tr>
                                            <td>{{$sede->descripcion}}</td>
                                          
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    
        <!--Labor a realizar-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="labor">Labor a Realizar: </label>
                            <textarea name="labor" id="labor" cols="15" rows="5" class="form-control" readonly>{{$arrayInfo[0]['labor']}}</textarea>
                        </div>
                      
                </div>
            </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4"></div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                @if ($botonesAccion)
    
                                    <form action="{{route('validarSolicitud')}}" method="POST">
                                        <div class="form-group">
                                            @csrf
                                            <input type="hidden" name="idsolicitud" value="{{$solicitud}}">
                                            <input type="hidden" name="idempresa" value="{{$arrayInfo[0]['empVisitar']}}">
                                            <label for="">Digite un comentario antes de validar</label>
                                            <textarea required placeholder="Digite aqui..." name="comentario" cols="8" rows="5" class="form-control"></textarea>
                                            <br>
                                            <input onclick="return confirm('¿Está seguro de aprobar esta solicitud?')" name="aprobar" type="submit" class="btn btn-success" value="Aprobar">
                                            <input onclick="return confirm('¿Está seguro de rechazar esta solicitud?')" name="rechazar" type="submit" class="btn btn-danger" value="Rechazar">
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</div>

  
  <!-- Modal Rechazo-->
  @if($msjRechazo)
    <div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Detalles del Rechazo, Solicitud # {{$solicitud}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Nivel</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Comentario</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $det)
                            <tr style="background: {{$det->estado=='R'?'#f8d7da':''}};font-weight:{{$det->estado=='R'?'600':''}}; color:{{$det->estado=='R'?'darkred':''}}">
                                <td>{{$det->usuario}}</td>
                                <td>{{$det->nivel}}</td>
                                <td>{{$det->fecha}}</td>
                                <td><span class="badge badge-{{$det->estado=='A'?'success':'danger'}}">{{$det->estado=='A'?'Aprobado':'Rechazado'}}</span></td>
                                <td>{{$det->comentario}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        </div>
    </div>
  @endif

    <!-- Modal Rechazo-->
    @if(!$botonesAccion)
    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Detalles de la Solicitud # {{$solicitud}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Nivel</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Comentario</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $det)
                            <tr>
                                <td>{{$det->usuario}}</td>
                                <td>{{$det->nivel}}</td>
                                <td>{{$det->fecha}}</td>
                                <td><span class="badge badge-{{$det->estado=='A'?'success':'danger'}}">{{$det->estado=='A'?'Aprobado':'Rechazado'}}</span></td>
                                <td>{{$det->comentario}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        </div>
    </div>
  @endif


@include('layouts.footer', ['modulo' => 'unitario'])