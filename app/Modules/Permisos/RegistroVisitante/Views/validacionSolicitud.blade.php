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
                    <h4>Solicitud de Aprobación, Caso #{{$solicitud}} - Sede: {{$nombreSedeTitulo}}</h4>
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
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="solicitante">Solicitante, Correo, Ext: </label>
                                    <input id="solicitante" class="form-control" type="text" name="solicitante" readonly value="{{$arrayInfo[0]['solicitante']}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="tipoIngreso">Tipo de Ingreso: </label>
                                    <input type="text" readonly class="form-control" value="{{$arrayInfo[0]['tipoIngreso']}}">
                                </div>
                            </div>
                        </div>
                        @if ($arrayInfo[0]['empresaC'] != "0")
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
                    <div class="card-body" style="height: 300px;overflow-y: scroll;" id="anexos">
                        <h4 class="text-center mt-2">Personas Que Ingresan</h4>
                        <hr>
                        <br>
                        @if ($tipoR == "RM")
                        <div class="row mb-4">
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                <b>Empresa:</b> {{$arrayDatosEmpresa[0]}}
                            </div>
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                <b>NIT:</b> {{$arrayDatosEmpresa[1]}}
                            </div>
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                Comprimido Colaboradores: <a class="btn btn-primary" href="{{asset('storage').'/'.$arrayDatosEmpresa[2]}}" target="_blank" download>Descargar Comprimido</a>
                            </div>
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                Plantilla Subida: <a class="btn btn-primary" href="{{asset('storage').'/'.$arrayDatosEmpresa[3]}}" target="_blank" download>Descargar Excel</a>
                            </div>
                        </div>
                        @endif
                        <table class="table table-light">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tipo Identificación</th>
                                    <th>Identificación</th>
                                    <th>Nombre</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    @if (!$tipoR == "RM")
                                        <th>Anexo</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentos as $docu)
                                    @if ($docu->tipo_identificacion != "NIT")
                                        <tr>
                                            <td>{{$docu->tipo_identificacion}}</td>
                                            <td>{{$docu->identificacion}}</td>
                                            <td>{{$docu->nombre}}</td>
                                            <td>{{$docu->fecha_inicio}}</td>
                                            <td>{{$docu->fecha_fin}}</td>
                                            <td>{{$docu->estado}}</td>
                                            @if (!$tipoR == "RM")
                                                @if (strlen($docu->url_documento) > 0)
                                                    <td><a class="btn btn-primary" href="{{asset('storage').'/'.$docu->url_documento}}" target="_blank" download>Descargar Documento</a></td>
                                                @else
                                                    <td><span class="badge badge-secondary"></span></td>
                                                @endif
                                            @endif
                                        </tr>
                                        
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
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
                        <div class="row mb-3">
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <h6><b>Empresa a Visitar:</b> {{$empresaVisitar[0]->descripcion}}</h6>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <h6><b>Código de la Empresa:</b> {{$empresaVisitar[0]->codigo_empresa}}</h6>
                            </div>
                        </div>
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
                <div class="align-items-center" id="loading" style="display: none">
                    <strong>Procesando, espere un momento por favor....</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
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
                                            <input type="hidden" name="idtipovisitante" value="{{$ideIngreso}}">
                                            <input type="hidden" name="idsede" value="{{$sedeID}}">
                                            <input type="hidden" name="idempresa" value="{{$arrayInfo[0]['empVisitar']}}">
                                            <label for="">Digite un comentario antes de validar</label>
                                            <textarea required placeholder="Digite aqui..." name="comentario" cols="8" rows="5" class="form-control" id="comentario"></textarea>
                                            <br>
                                            <input id="btnAprobar" onclick="return load(1);" name="aprobar" type="submit" class="btn btn-success" value="Aprobar">
                                            <input id="btnRechazar" onclick="return load(2);" name="rechazar" type="submit" class="btn btn-danger" value="Rechazar">
                                            @if (count($detalles) > 0)
                                                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalDetallesDos" href="#">Ver Flujo</a>  
                                                </div>
                                                
                                            @endif

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
        <div class="modal-dialog modal-lg" role="document">
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
                        <th scope="col">Fecha</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Comentario</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $det)
                            <tr style="background: {{$det->estado=='R'?'#f8d7da':''}};font-weight:{{$det->estado=='R'?'600':''}}; color:{{$det->estado=='R'?'darkred':''}}">
                                <td>{{$det->usuario}}</td>
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

    <!-- Modal DETALLES-->
    @if(!$botonesAccion)
    <div class="modal fade " id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                        <th scope="col">Fecha</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Comentario</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $det)
                            <tr>
                                <td>{{$det->usuario}}</td>
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

     <!-- Modal DETALLES-->
     @if($botonesAccion)
     <div class="modal fade " id="modalDetallesDos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
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
                         <th scope="col">Fecha</th>
                         <th scope="col">Estado</th>
                         <th scope="col">Comentario</th>
                     </tr>
                     </thead>
                     <tbody>
                         @foreach ($detalles as $det)
                             <tr>
                                 <td>{{$det->usuario}}</td>
                                 <td>{{$det->fecha}}</td>
                                 <td><span class="badge badge-{{$det->estado=='A'?'success':'danger'}}">{{$det->estado=='A'?'Aprobado':'Rechazado'}}</span></td>
                                 <td>{{$det->comentario}}  </td>
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

     <script>
        function load(opcion){
            var comentario = $("#comentario").val();
            if(opcion == 1){
                if(confirm('¿Está seguro de aprobar esta solicitud?') && comentario != ""){
                     $("#loading").css({'display':'block'});
                     $("#btnAprobar").hide();
                     $("#btnRechazar").hide();
                }
            }else{
                if(confirm('¿Está seguro de rechazar esta solicitud?') && comentario != ""){
                     $("#loading").css({'display':'block'});
                     $("#btnAprobar").hide();
                     $("#btnRechazar").hide();
                }
            }
        
         }
     </script>

@include('layouts.footer', ['modulo' => 'unitario'])