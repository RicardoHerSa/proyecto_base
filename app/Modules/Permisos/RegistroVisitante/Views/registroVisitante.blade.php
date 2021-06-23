@include('layouts.app', ['modulo' => 'unitario'])

<div class="container">
    @if (Session::has('msj'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <strong>Información!</strong> {{Session::get('msj')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (Session::has('errCorreo'))
    <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errCorreo')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errSoliApro'))
    <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errSoliApro')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('aproYa'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('aproYa')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errSedes'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errSedes')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errDocu'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errDocu')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errSoli'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errSoli')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errExcel'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errExcel')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (Session::has('errFechas'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        <strong>Información!</strong> {{Session::get('errFechas')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Formulario Solicitud de Ingreso  </h5>
                </div>
                <div class="card-body">
                    <p style="color:red" class="card-text">SEÑOR SOLICITANTE:  Recuerde que los trabajos en alturas debe tener:</p>
                   <ul>
                       <li>Certificado medico de aptitud (Este no debe exceder un año).</li>
                       <li>Certificado de entrenamiento en alturas (Este no debe exceder un año).</li>
                       <li>Certificado del coordinador de trabajo en alturas.</li>
                       <li>Formato del permiso de trabajo en alturas.</li>
                       <li>Paso a paso de la actividad que van a realizar.</li>
                       <li>Para trabajos con: Redes Electricas, Espacios Confinados, Trabajo en Caliente y de mas de alto riesgo, comunicarse con el área de SST de su empresa.</li>
                   </ul>
                </div>
            </div>

        </div>
    </div> 

    <form id="form" onsubmit="return validaFormulario();" action="{{route('registraranexos')}}" method="POST" enctype="multipart/form-data">

        <!--Solicitante-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="solicitante">Solicitante, Correo, Ext: <span style="color:red">*</span></label>
                                    <input id="solicitante" class="form-control" type="text" name="solicitante"  value="{{auth()->user()->name}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="tipoIngreso">Tipo de Ingreso: <span style="color:red">*</span></label>
                                    <select  onchange="Consultaempresavisitar()" name="tipoIngreso" id="tipoIngreso" class="form-control" >
                                        <option id = 'selingreso' value="0"> Seleccione tipo</option>
                                        @foreach ($tiposVisitante as $tipos)
                                            <option value="{{$tipos->id_tipo_visitante}}">{{$tipos->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group" id="contenedorContratista">
                                    <label for="empresaContratista">Empresa Contratista: </label>
                                    <input onchange="ponerNombreMasivo()" id="empresaContratista" class="form-control" type="text" name="empresaContratista">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <strong style="color:red">NOTA: </strong><span style="color:red"> En el campo de identificación por favor ingresar el número sin puntos, comas o separación decimal.</span>
                    </div>
                </div>
            </div>
        </div> 

        <!--Anexos-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                    <div class="card">
                       <div class="float-left ml-3 mt-2">
                           <button type="button" id="btnAñadirRegistro" onclick="nuevo();" class="btn btn-primary">Añadir</button>
                           <div class="form-check form-check-inline">
                               <input type="hidden" value="1" id="tipReg">
                               <input onchange="tipoRegistroV(this.value)" class="form-check-input" checked type="radio" name="tipoRegistroVisi" id="inlineRadio1" value="RI">
                               <label class="form-check-label"  for="inlineRadio1">Registro individual</label>
                           </div>
                           <div class="form-check form-check-inline">
                               <input onchange="tipoRegistroV(this.value)" class="form-check-input" type="radio" name="tipoRegistroVisi" id="inlineRadio2" value="RM">
                               <label class="form-check-label" for="inlineRadio2">Registro masivo</label>
                           </div>
                           <input type="hidden" id="primerEliminado" value="n">
                       </div>
                       <div class="card-body" id="anexos">
                           @csrf
                           <input type="hidden" id="cantRegis" value="0" name="cantR">
                           <div class="row">
                               <div class="col-xs-12 col-md-2 col-lg-2" id="contenedorTipoId">
                                   <div class="form-group">
                                       <label for="tipoId">Tipo Identidad: <span style="color:red">*</span></label>
                                       <select name="tipoId" id="tipoId" class="form-control" >
                                           <option value="CEDULA">Cédula</option>
                                           <option value="PASAPORTE">Pasaporte</option>
                                       </select>
                                   </div>
                               </div>
                               <div class="col-xs-12 col-md-2 col-lg-2" id="contenedorNit">
                                   <div class="form-group">
                                       <label for="cedula" id="lblIde">Identificación: </label>
                                       <input  id="cedula" class="form-control" type="number" name="cedula">
                                   </div>
                               </div>
                               <div class="col-xs-12 col-md-3 col-lg-3" id="contenedorNomEmpresa">
                                   <div class="form-group">
                                       <label for=nombre" id="lblnom">Nombre Completo: </label>
                                       <input  id="nombre" class="form-control" type="text" name="nombre">
                                   </div>
                               </div>
                           
                               <div class="col-xs-12 col-md-2 col-lg-2" id="contenedorFechaIni">
                                   <div class="form-group">
                                       <label for="fechaIngreso">Fecha Ingreso: </label>
                                       <input type="date" min="<?php echo date('Y-m-d')?>" class="form-control" id="fechaIngreso" name="fechaIngreso" >
                                   </div>
                               </div>
                               <div class="col-xs-12 col-md-2 col-lg-2" id="contenedorFechaFin">
                                   <div class="form-group">
                                       <label for="fechaFinal">Fecha Final: </label>
                                       <input  type="date" min="<?php echo date('Y-m-d')?>" class="form-control" id="fechaFinal" name="fechaFinal" >
                                   </div>
                               </div>
                               <div class="col-xs-12 col-md-6 col-lg-6" id="comprimido" style="display: none">
                                   <div class="form-group">
                                       <label for="comprimidoCola">Subir Comprimido de Documentación: </label>
                                       <input id="comprimidoCola" class="form-control" type="file" accept=".pdf,.zip,.rar" name="comprimidoCola">
                                       <small  class="ml-2">Comprimido con la documentación de cada colaborador.</small>
                                   </div>
                               </div>
                               <div class="col-xs-12 col-md-1 col-lg-1" id="contenedorColaboradores">
                                   <label for="anexo" id="lblAnex">Anexo: </label>
                                   <div class="row" style="margin-left: -40px" id="contenedorAnexos">
                                       <div class="col-md-6 col-lg-6" id="contenedorDocumento">
                                           <button type="button" id="btnDocu" class="btn btn-primary"><i class="fa fa-folder"></i></button>
                                           <!--<i id="check" style="color:green" class="fa fa-check"></i>
                                           <i id="close" style="color:red" class="fa fa-times-circle"></i>-->
                                           <input  onchange="seleccionArchivo($(this))" data-toggle="tooltip" data-placement="bottom" title="Sólo archivos pdf, word, png, jpg, zip, rar" style="position: absolute;
                                           bottom: 2px;
                                           left: 10px;
                                           cursor: pointer;
                                           opacity: 0;" id="anexo" class="form-control" type="file" accept="image/png,image/jpg,.pdf,.doc,.docx,application/msword" name="anexo">
                                           <small style="display: none; position: relative;
                                           top: 33px;" id="txtAnexo"></small>
                                       </div>
                                       <div class="col-md-6 col-lg-6" id="contenedorBorrar">
                                           <button style="height: 35px;" type="button" class="btn btn-danger" onclick="borrarAnexos()"><i class="fa fa-trash"></i></button>
                                       </div>
                                   </div>
                               </div>
                           </div>

                           <div class="row mt-2" id="descargaPlantilla" style="display: none">
                               <div class="col-xs-12 col-md-5 col-lg-5"></div>
                               <div class="col-xs-12 col-md-4 col-lg-4">
                                   <a class="btn btn-primary" href="{{asset('plantillaCargueMasivo/formatoCargueMasivo.xlsx')}}" download >Descargar Plantilla.</a>
                               </div>
                               <div class="col-xs-12 col-md-3 col-lg-3"></div>
                           </div>
                              
                           <div id="inputs" style="display: none">
                               <div class="row" id="clonado">
                                   <hr style="border: 1px solid; width: -webkit-fill-available;">
                                   <div class="col-xs-12 col-md-2 col-lg-2">
                                       <div class="form-group">
                                           <label for="tipoId1">Tipo Identidad: <span style="color:red">*</span></label>
                                           <select name="tipoId1" id="tipoId1" class="form-control">
                                               <option value="CEDULA">Cédula</option>
                                               <option value="PASAPORTE">Pasaporte</option>
                                           </select>
                                       </div>
                                   </div>
                                   <div class="col-xs-12 col-md-2 col-lg-2">
                                       <div class="form-group">
                                           <label for="cedula1">Indetificación: </label>
                                           <input id="cedula1" class="form-control" type="text" name="cedula1">
                                       </div>
                                   </div>
                                   <div class="col-xs-12 col-md-3 col-lg-3">
                                       <div class="form-group">
                                           <label for="nombre1">Nombre Completo: </label>
                                           <input id="nombre1" class="form-control" type="text" name="nombre1">
                                       </div>
                                   </div> 
                                   <div class="col-xs-12 col-md-2 col-lg-2">
                                       <div class="form-group">
                                           <label for="">Fecha Ingreso: </label>
                                           <input  min="<?php echo date('Y-m-d')?>" type="date" class="form-control" id="fechaIngreso1" name="fechaIngreso1">
                                       </div>
                                   </div>
                                   <div class="col-xs-12 col-md-2 col-lg-2">
                                       <div class="form-group">
                                           <label for="">Fecha Final: </label>
                                           <input  min="<?php echo date('Y-m-d')?>"  type="date" class="form-control" id="fechaFinal1" name="fechaFinal1">
                                       </div>
                                   </div>
                                   <div class="col-xs-12 col-md-1 col-lg-1">
                                       <label for="anexo1">Anexo: </label>
                                           <div class="row" style="margin-left: -40px">
                                               <div class="col-md-6 col-lg-6">
                                                   <a class="btn btn-primary"><i class="fa fa-folder"></i></a>
                                                   <!--<i id="check1" style="color:green" class="fa fa-check"></i>
                                                   <i id="close1" style="color:red" class="fa fa-times-circle"></i>-->
                                                   <input onchange="seleccionArchivo($(this))" data-toggle="tooltip" data-placement="bottom" title="Sólo archivos pdf, word, png, jpg, zip, rar" style="position: absolute;
                                                   bottom: 2px;
                                                   left: 10px;
                                                   cursor: pointer;
                                                   opacity: 0;"  id="anexo1" class="form-control" type="file" accept="image/png,image/jpg,.pdf,.doc,.docx,application/msword" name="anexo1">
                                                   
                                                   
                                               </div>
                                               <div class="col-md-6 col-lg-6">
                                                   <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                               </div>
                                           </div>

                                   </div>
                               </div>
                           </div>
                       </div>
                           <p id="txtPermite"  class="ml-3"><i>Sólo se permite el registro de hasta 4 visitantes de manera individual.</i></p>  
                           <div class="row">
                               <div class="col-xs-12 col-md-8 col-lg-8"></div>
                               <div class="col-xs-12 col-md-4 col-lg-4">
                                <input type="hidden" id="adjSelecc" value="1">
                                <div style="display: none; border: 1px solid darkblue;  height: 23px;
                                background: aliceblue;
                                cursor:pointer;" onclick="verAdjuntos()" id="archivosSubidos">

                                    <h6 id="txtArchiSub" style="border: 1px solid darkblue;
                                    background: steelblue;
                                    color: white;" class="text-center">Archivos Subidos<h6>
                                        <ul id="listadoAdj">

                                        </ul>
                                </div>
                               </div>
                           </div>
                   </div>
           </div>
   </div>

        <!--Sedes a Visitar-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <!--<form action="{{route('registraranexos')}}" method="POST">-->
                    
                <div class="card" id="anexarSedes">
                    <div class="card-body">
                        <h5 class="card-title">Sedes a Visitar: </h5>
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4"></div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label  id ='lblempresavisitar' for="empVisi">Empresa a Visitar: <span style="color:red">*</span></label>
                                   
                                    <select  onchange="cargaSedes()" name="selEmpresa" id="selEmpresa" class="form-control" >
                                        <option  value="0">Seleccione Empresa</option>
                                        {{-- @foreach ($empresas as $emp)
                                        <option value="{{$emp->codigo_empresa}}">{{$emp->descripcion}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <input type="hidden" class="form-control" readonly value="Horario Especial Lunes a Domingo 00:00 - 23:59">
                            <input id="hora" class="form-control" type="hidden" name="hora" readonly value="00:00 - 23:59">
                           
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="float-left  ml-3 mt-2">
                                    <button style="display: none" id="btnAddSede" type="button" onclick="nuevaSede();" class="btn btn-primary">Añadir</button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                            
                            <div class="form-group">
                                <label for="sede" id="lblSede">Seleccione Sede:</label>
                                <div class="spinner-border text-primary float-right mb-1" style="display: none" role="status" id="bolaCarga">
                                    <span class="sr-only">Cargando Sedes...</span>
                                </div>
                                <select name="sede" id="sede" class="form-control">
                                    <option value="0">Sin registros</option>
                                </select>
                                    <input type="hidden" id="primerEliminadoSelect" value="n">
                                    <input type="hidden" id="cantRegisSelect" value="0" name="cantRSelects">
                            </div>
                            
                        </div>
                    </div>

                        <div id="selects" style="display: none">
                            <div class="row" id="clonadoSelects">
                                <div class="col-xs-12 col-md-4 col-lg-4"></div>
                                <div class="col-xs-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="sede1">Seleccione Sede:</label>
                                        <select name="sede1" id="sede1" class="form-control">
                                            <option value="0">Sin registros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Borrar</label><br>
                                        <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="sedeSelcc">
            <!-- <input type="submit">
                </form>-->
            </div>
        </div>

        <!--Labor a realizar-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="align-items-center" style="display: none" id="loading">
                    <strong>Enviando Solicitud, por favor espere...</strong>
                    <div class="spinner-border ml-auto " role="status" aria-hidden="true"></div>
                  </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="labor">Labor a Realizar: <span style="color:red">*</span></label>
                            <textarea name="labor" id="labor" cols="15" rows="5" class="form-control" ></textarea>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-lg-6"></div>
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <input type="hidden" value="{{$grupo}}" name="grupo" id="grupo">
                                    <input id="btnEnviar" type="submit" class="btn btn-primary" value="Enviar" onclick="return validaFormulario();">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3 col-lg-3"></div>
                        </div>
                    
                </div>
            </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        ocultaInput();
    
    });
    //consultas en real time - Hs
    function consultarHora()
    {
        var idHorario = $("#horario").val();
        var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: false,
                    url: "consultarHora", 
                    data: {'idhorario':idHorario, _token:token},
                    cache: false,
                    success: function(response){
                            $("#hora").val(response);
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                                }
                    });

    }

    function ocultaInput()
    {
        var texto = $('select[name="tipoIngreso"] option:selected').text();
        var txt = texto.substr(0,3);
        console.log(txt);
        if(txt != "CON" && txt != "Se"){
                $("#contenedorContratista").hide();
                $("#empresaContratista").val('')
            }else{
                $("#contenedorContratista").fadeIn();
            }

        
    }

    //agregar mas inputs
    let nuevo = function() 
    {
        var cant = parseInt(1);
        var cantidadRegistroActual = parseInt($("#cantRegis").val());
        var cantidadRegistro = parseInt($("#cantRegis").val());
        cantidadRegistro+=cant;
        if(cantidadRegistro > 3){
            alert('No puedes añadir mas de 4 registros.');
        }else{
            if(cantidadRegistro == 1){
                $('#inputs').show();
                $("#clonado").find("button").attr("onclick", "eliminar(this,1)");
                $("#clonado").find("button").attr('name', 'btnEliminar'+cantidadRegistro);
                $("#clonado").attr('id', 'clonado'+cantidadRegistro);
                //para los input cc, nombre, anexo
                $('#cedula'+cantidadRegistro).attr('name', 'cedula'+cantidadRegistro);

              
                $('#primerEliminado').val('n');
            }else{
                $("#anexos")                                    // crea una nueva sección
                //.insertBefore("[name='borrar']")    // insértala antes del botón de enviar (para que se vayan añadiendo en orden)
                 .append($("#inputs").html()
                 );        // añádele el código con los campos de .inputs

                    $("#inputs").find($("#clonado"+cantidadRegistroActual)).attr('id', 'clonado'+cantidadRegistro);
                   // $("#clonado").attr('id', 'clonado'+cantidadRegistro);
                   // $("#clonado").removeAttr('id', 'clonado');
                    $("#clonado"+cantidadRegistro).find("button").attr('name', 'btnEliminar'+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find("button").attr("onclick", "eliminar(this,"+cantidadRegistro+")");
                    //para los input tipoid, cc, nombre, fechaIni ,fechaFin,anexo
                    $("#clonado"+cantidadRegistro).find($('#tipoId'+cantidadRegistroActual)).attr("name", "tipoId"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#tipoId'+cantidadRegistroActual)).attr("id", "tipoId"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#cedula'+cantidadRegistroActual)).attr("name", "cedula"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#cedula'+cantidadRegistroActual)).attr("id", "cedula"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#nombre'+cantidadRegistroActual)).attr("name", "nombre"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#nombre'+cantidadRegistroActual)).attr("id", "nombre"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#fechaIngreso'+cantidadRegistroActual)).attr("name", "fechaIngreso"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#fechaIngreso'+cantidadRegistroActual)).attr("id", "fechaIngreso"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#fechaFinal'+cantidadRegistroActual)).attr("name", "fechaFinal"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#fechaFinal'+cantidadRegistroActual)).attr("id", "fechaFinal"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#anexo'+cantidadRegistroActual)).attr("name", "anexo"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#anexo'+cantidadRegistroActual)).attr("id", "anexo"+cantidadRegistro);
                   $("#clonado"+cantidadRegistro).find($('input #anexo'+cantidadRegistro)).removeAttr("onchange");
                                                    
            }
        
                 $("#cantRegis").val(cantidadRegistro);
        }
              
      
    }
    let eliminar = function(obj, posicion)
    {
        var cant = parseInt(1);
        var primerEliminado =  $('#primerEliminado').val();
        var cantidadRegistroActual = parseInt($("#cantRegis").val());
        var cantidadRegistro = parseInt($("#cantRegis").val());
        cantidadRegistro-=cant;
        if(cantidadRegistro < 0){

        }else{
            if(posicion == cantidadRegistroActual && primerEliminado == 'n'){
                $('#inputs').hide();
                $('#primerEliminado').val('s');
                //$("#clonado").find("button").removeAttr("onclick");
            }else if(posicion != cantidadRegistroActual && cantidadRegistroActual == 1  && primerEliminado == 'n'){
                $('#inputs').hide();
                $('#primerEliminado').val('s');
            }else{
                $(obj).closest("#clonado"+posicion).remove();

            }
            $("#cantRegis").val(cantidadRegistro);
        }
    }

    //agregar mas select sedes
    let nuevaSede = function() 
    {
       
       
        var cant = parseInt(1);
        var cantidadRegistroActual = parseInt($("#cantRegisSelect").val());
        var sedeAc = $("#sedeSelcc").val();
        var otrasede = "";
        if(cantidadRegistroActual == 0){
            var sedeUno = $("#sede").val();
            $("#sedeSelcc").val(sedeUno);
        }else if(cantidadRegistroActual == 1){
            otrasede = $("#sede"+cantidadRegistroActual).val();
        }else{
            console.log("entra else: "+cantidadRegistroActual-1);
            otrasede = $("#sede"+cantidadRegistroActual-1).val();
        }
        var acumula = sedeAc+=","+otrasede;
        var cantidadRegistro = parseInt($("#cantRegisSelect").val());
        cantidadRegistro+=cant;
        if(cantidadRegistro > 9){
            alert('No puedes añadir mas de 10 registros.');
        }else{
            if(cantidadRegistro == 1){
                $('#selects').show();
                $("#clonadoSelects").find("button").attr("onclick", "eliminarSelect(this,1)");
                $("#clonadoSelects").find("button").attr('name', 'btnEliminarSelect'+cantidadRegistro);
                
                //para los input cc, nombre, anexo
                $('#cedula'+cantidadRegistro).attr('name', 'cedula'+cantidadRegistro);

              
                $('#primerEliminado').val('n');
            }else{
               
                $("#sedeSelcc").val(acumula);
                $("#anexarSedes")                                    // crea una nueva sección
                //.insertBefore("[name='borrar']")    // insértala antes del botón de enviar (para que se vayan añadiendo en orden)
                 .append($("#selects").html()
                 );        // añádele el código con los campos de .inputs
                    
                    $("#clonadoSelects").find("button").attr("onclick", "eliminarSelect(this,"+cantidadRegistro+")");
                    $("#clonadoSelects").find("button").attr('name', 'btnEliminarSelect'+cantidadRegistro);
                    //para los input cc, nombre, anexo
                    $("#clonadoSelects").find($('#sede'+cantidadRegistroActual)).attr("name", "sede"+cantidadRegistro);
                    $("#clonadoSelects").find($('#sede'+cantidadRegistroActual)).attr("id", "sede"+cantidadRegistro);
                    $("#clonadoSelects").find($('#sede'+cantidadRegistroActual)).attr("chahol");
              
                  
            }
        
                 $("#cantRegisSelect").val(cantidadRegistro);
        }

        //Ir guardando las sedes seleccionadas para que no las vuelva a mostrar
        //guardaSedes();
              
      
    }

    let eliminarSelect = function(obj, posicion)
    {
        var cant = parseInt(1);
        var primerEliminado =  $('#primerEliminadoSelect').val();
        var cantidadRegistroActual = parseInt($("#cantRegisSelect").val());
        var cantidadRegistro = parseInt($("#cantRegisSelect").val());
        cantidadRegistro-=cant;
        if(cantidadRegistro < 0){

        }else{
            if(posicion == cantidadRegistroActual && primerEliminado == 'n'){
                $('#selects').hide();
                $('#primerEliminadoSelect').val('s');
                //$("#clonado").find("button").removeAttr("onclick");
            }else if(posicion != cantidadRegistroActual && cantidadRegistroActual == 1  && primerEliminado == 'n'){
                $('#selects').hide();
                $('#primerEliminadoSelect').val('s');
            }else{
                $(obj).closest(".row").remove();
            }
            $("#cantRegisSelect").val(cantidadRegistro);
        }
    }

    function borrarAnexos()
    {
        $("#cedula").val('');
        $("#nombre").val('');
        $("#fechaIngreso").val('');
        $("#fechaFinal").val('');
        $("#anexo").val('');

    }

    function tipoRegistroV($event)
    {
        if($event == "RM"){
            if($("#empresaContratista").val().length != 0 && $("#tipoIngreso").val() == 2){
                $("#nombre").val($("#empresaContratista").val());
                $("#nombre").attr('readonly', true);
            }else if($("#empresaContratista").val().length == 0 && $("#tipoIngreso").val() != 2){
                $("#nombre").val('');
                $("#nombre").removeAttr('readonly');
            }else{
                $("#nombre").val($("#empresaContratista").val());
                $("#nombre").attr('readonly', true);
            }
            $("#tipReg").val(2);
            $("#btnAñadirRegistro").hide();

            //Descarga de plantilla
            $("#descargaPlantilla").show();

            //oculta el tipo de identificacion y las fechas 
            $("#txtPermite").hide();
            $("#contenedorTipoId").hide();
            $("#contenedorFechaIni").hide();
            $("#contenedorFechaFin").hide();

            //cambia medidas de los contenedores
            $("#btnDocu").hide();
            $("#contenedorAnexos").css({"margin-left": "initial"});
            $("#contenedorDocumento").removeClass("col-md-6");
            $("#contenedorDocumento").removeClass("col-lg-6");
            $("#contenedorDocumento").addClass("col-md-11");
            $("#contenedorDocumento").addClass("col-lg-11");
            $("#contenedorBorrar").removeClass("col-md-6");
            $("#contenedorBorrar").removeClass("col-lg-6");
            $("#contenedorBorrar").addClass("col-md-1");
            $("#contenedorBorrar").addClass("col-lg-1");
            $("#contenedorDocumento input").css({"position":"absolute","opacity":"initial"});

            $("#contenedorNit").removeClass('col-md-4');
            $("#contenedorNit").removeClass('col-lg-4');
            $("#contenedorNit").addClass('col-md-6');
            $("#contenedorNit").addClass('col-lg-6');
            $("#contenedorNomEmpresa").removeClass('col-md-5');
            $("#contenedorNomEmpresa").removeClass('col-lg-5');
            $("#contenedorNomEmpresa").addClass('col-md-6');
            $("#contenedorNomEmpresa").addClass('col-lg-6');
            $("#contenedorColaboradores").removeClass('col-md-1');
            $("#contenedorColaboradores").removeClass('col-lg-1');
            $("#contenedorColaboradores").addClass('col-md-5');
            $("#contenedorColaboradores").addClass('col-lg-5');

            //cambia nombre  labels
            $("#lblIde").text('Nit Empresa:');
            $("#lblnom").text('Nombre Empresa:');
            $("#lblAnex").text('Listado de Colaboradores:');

            //Muestra el comprimido
            $("#comprimido").show();
            $("#comprimidoCola").attr('required', true);

            //quita los  de cedula1,nombre1,anexo1
            $('#tipoId1').removeAttr('required');
            $('#cedula1').removeAttr('required');
            $('#nombre1').removeAttr('required');
            $('#fechaIngreso1').removeAttr('required');
            $('#fechaFinal1').removeAttr('required');
            $('#anexo1').removeAttr('required');
            
            var hasta = parseInt($("#cantRegis").val());
            console.log(hasta);
            if(hasta > 1){
                for (let i = 1; i < hasta; i++) {
                    console.log(i);
                    $("#clonado"+i).remove();
                    
                }
                $("#inputs").hide();
                $("#clonado"+hasta).find($('#cedula'+hasta)).attr("name", "cedula1");
                $("#clonado"+hasta).find($('#cedula'+hasta)).attr("id", "cedula1");
                $("#clonado"+hasta).find($('#nombre'+hasta)).attr("name", "nombre1");
                $("#clonado"+hasta).find($('#nombre'+hasta)).attr("id", "nombre1");
                $("#clonado"+hasta).find($('#anexo'+hasta)).attr("name", "anexo1");
                $("#clonado"+hasta).find($('#anexo'+hasta)).attr("id", "anexo1");
                $("#clonado"+hasta).find($('#fechaIngreso'+hasta)).attr("name", "fechaIngreso1");
                $("#clonado"+hasta).find($('#fechaIngreso'+hasta)).attr("id", "fechaIngreso1");
                $("#clonado"+hasta).find($('#fechaFinal'+hasta)).attr("name", "fechaFinal1");
                $("#clonado"+hasta).find($('#fechaFinal'+hasta)).attr("id", "fechaFinal1");
                $("#clonado"+hasta).attr('id', 'clonado');
                $("#cantRegis").val(0);

            }else if(hasta == 1){
                $("#inputs").hide();
            }
            //$("#clonado").css({'display':'none'});
            $("#txtAnexo").text('Sólo archivo .Xlsx');
            $("#txtAnexo").fadeIn();
            $("#anexo").removeAttr('accept');
            $("#anexo").attr('accept', '.xlsx');
        }else{
            $("#nombre").val('');
            $("#nombre").removeAttr('readonly');
             $("#tipReg").val(1);
             //Descarga de plantilla
             $("#descargaPlantilla").hide();

            //muestra el tipo de identificacion y las fechas 
            $("#txtPermite").show();
            $("#contenedorTipoId").show();
            $("#contenedorFechaIni").show();
            $("#contenedorFechaFin").show();
            //cambia medidas de los contenedores
            $("#btnDocu").show();
            $("#contenedorAnexos").css({"margin-left": "-40px"});
            $("#contenedorDocumento").removeClass("col-md-11");
            $("#contenedorDocumento").removeClass("col-lg-11");
            $("#contenedorDocumento").addClass("col-md-6");
            $("#contenedorDocumento").addClass("col-lg-6");
            $("#contenedorBorrar").removeClass("col-md-1");
            $("#contenedorBorrar").removeClass("col-lg-1");
            $("#contenedorBorrar").addClass("col-md-6");
            $("#contenedorBorrar").addClass("col-lg-6");
            $("#contenedorDocumento input").css({"position":"absolute","opacity":"0"});

            $("#contenedorNit").removeClass('col-md-6');
            $("#contenedorNit").removeClass('col-lg-6');
            $("#contenedorNit").addClass('col-md-2');
            $("#contenedorNit").addClass('col-lg-2');
            $("#contenedorNomEmpresa").removeClass('col-md-6');
            $("#contenedorNomEmpresa").removeClass('col-lg-6');
            $("#contenedorNomEmpresa").addClass('col-md-3');
            $("#contenedorNomEmpresa").addClass('col-lg-3');
            $("#contenedorColaboradores").removeClass('col-md-5');
            $("#contenedorColaboradores").removeClass('col-lg-5');
            $("#contenedorColaboradores").addClass('col-md-1');
            $("#contenedorColaboradores").addClass('col-lg-1');

            //cambia nombre  labels
            $("#lblIde").text('Identificación:');
            $("#lblnom").text('Nombre Completo:');
            $("#lblAnex").text('Anexo:');

            //oculta comprimido
            $("#comprimido").hide();
            $("#comprimidoCola").removeAttr('');

          
            $("#btnAñadirRegistro").show();
            $("#clonado").css({'display':'flex'});
            $("#txtAnexo").fadeOut();
            $("#anexo").removeAttr('accept');
            $("#anexo").attr('accept', 'image/png,image/jpg,.pdf,.doc,.docx,application/msword,.zip,.rar');
        }
    }

    function guardaSedes()
    {
        var sedesSeleccionadas = ""; 
        var cant = $("#cantRegisSelect").val();
        for(var i=0; i < cant; i++){
            if(i == 0){
                var idSede = $("#sede").val();
            }else{
                var idSede = $("#sede"+i).val();
            }
            sedesSeleccionadas += idSede+",";
        }
       
        var token = '{{csrf_token()}}';
            var request=$.ajax({
                    type:  'GET',
                    url: "actualizarSedes/"+sedesSeleccionadas,
                  // data: {'sedes':sedesSeleccionadas, _token:token},
                    cache: false,
                    success: function(response){
                        if(cant > 1){
                            var items = "";
                            var actual = $("#sede"+cant).val();
                            console.log("SELECT ACTUAL: SEDE:"+actual);
                        $("#sede1 option").each(function() { 
                            //console.log($(this).attr('value'));
                            var it = $(this).attr('value');
                            var nom = $(this).text();
                            if(actual == it){
                                items += "<option selected value='"+it+"'>"+nom+"</option>";
                            }else{
                                items += "<option value='"+it+"'>"+nom+"</option>";
                            }
                        });

                        
                        console.log("ITEMS DE LA SEDE: "+actual+" ->> "+items);
                      
                            document.getElementById("sede"+cant).innerHTML=items;
                            var sedeNueva = cant-1;
                            document.getElementById("sede"+sedeNueva).innerHTML=response;
                        }else{

                            document.getElementById("sede1").innerHTML=response;
                        }
                    },
                      
                    error:function(xhr, ajaxOptions, thrownError) {
                                    alert(xhr.status);
                    }
                        
        });
    }
 //lblempresavisitar
    

 function Consultaempresavisitar()
    {
        ocultaInput();
       $("#lblempresavisitar").removeClass('text-danger');
       //$("#btnAddSede").hide();
       
       var tipo = $("#tipoIngreso").val();
       var grupo = $("#grupo").val();
        if(tipo != 0){
            $("#bolaCarga").fadeIn();
            $("#lblempresavisitar").text('Buscando Empresa...');
                var token = '{{csrf_token()}}';
               
                var request=$.ajax({
                        type:  'POST',
                        url: "empresavisitar",
                        data: {'tipoIngreso':tipo,'grupo':grupo, _token:token},
                        cache: false,
                        success: function(response){
                           
                            if(response != 0){
                                $("#lblempresavisitar").text('Seleccione Empresa:');
                                document.getElementById('selEmpresa').innerHTML = response;
                                
                                $("#btnAddSede").show();
                            }else{
                                
                              //  $("#lblempresavisitar").text('No se encontraron sedes asociadas.');
                               // $("#lblempresavisitar").addClass('text-danger');
                                document.getElementById('selEmpresa').innerHTML = "<option value='0'>Sin registros emp</option>";
                               
                            }
                            $("#bolaCarga").hide();
                            
                        },
                        error:function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            $("#bolaCarga").hide();
                            $("#selEmpresa").text('Ninguna empresa configurada.');
                        }
                    
                    });


        }else{
           // $("#lblSede").addClass('text-danger');
           // $("#lblSede").text('Por favor seleccione empresa');
            document.getElementById('selEmpresa').innerHTML = "<option value='0'>Sin registros</option>";
           
        }
    }

    function cargaSedes()
    {
       $("#lblSede").removeClass('text-danger');
       $("#btnAddSede").hide();
       var idempresa = $("#selEmpresa").val();
        if(idempresa != 0){
            $("#bolaCarga").fadeIn();
            $("#lblSede").text('Buscando Sedes...');
            var token = '{{csrf_token()}}';
                var request=$.ajax({
                        type:  'POST',
                        url: "consultasedes",
                        data: {'idempresa':idempresa,  _token:token},
                        cache: false,
                        success: function(response){
                            if(response != 0){
                                $("#lblSede").text('Seleccione Sede:');
                                document.getElementById('sede').innerHTML = response;
                                document.getElementById('sede1').innerHTML = response;
                                $("#btnAddSede").show();
                            }else{
                                console.log(response);
                                $("#lblSede").text('No se encontraron sedes asociadas.');
                                $("#lblSede").addClass('text-danger');
                                document.getElementById('sede').innerHTML = "<option value='0'>Sin registros</option>";
                                document.getElementById('sede1').innerHTML = "<option value='0'>Sin registros</option>";
                            }
                            $("#bolaCarga").hide();
                            
                        },
                        error:function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status+ " "+ajaxOptions );
                            $("#bolaCarga").hide();
                            $("#lblSede").text('No se encontraron sedes.');
                        }
                    
                    });

        }else{
            $("#lblSede").addClass('text-danger');
            $("#lblSede").text('Por favor seleccione empresa');
            document.getElementById('sede').innerHTML = "<option value='0'>Sin registros</option>";
            document.getElementById('sede1').innerHTML = "<option value='0'>Sin registros</option>";
            $("#btnAddSede").hide();
        }
    }

    function validarFechas()
    {

    }

    function validaFormulario()
    {
        var idsede = $("#sede").val();
        if(idsede == 0){
            alert('Debe seleccionar una sede.');
            return false;
        }else{
            //recibir los campos principales para saber si estan completos
            var solicitante = $("#solicitante").val();
            var tipoIngreso = $("#tipoIngreso").val();
            var empresaC = $("#empresaContratista").val();

            var tipReg = $("#tipReg").val();
            var cedula = $("#cedula").val();
            var nombre = $("#nombre").val();
            var fechaIngreso = $("#fechaIngreso").val();
            var fechaFinal = $("#fechaFinal").val();
            var anexo = $("#anexo").val();
            var comprimidoCola = $("#comprimidoCola").val();

            var labor = $("#labor").val();

            if(solicitante.length == 0 ||  tipoIngreso == 0 || tipoIngreso == 2 && empresaC.length == 0 ){
                alert('Campos Incompletos - Información Solicitante');
                return false;
            }else if(tipReg == 1 && cedula.length == 0 || tipReg == 1 && nombre.length == 0 || tipReg == 1 && fechaIngreso == '' || tipReg == 1 && fechaFinal == '' || tipReg == 1 && anexo == ''){
                alert('Campos Incompletos - Ingreso Colaboradores');
                return false;
            }else if(tipReg == 2 && cedula.length == 0 || tipReg == 2 && nombre.length == 0 || tipReg == 2 && anexo == '' || tipReg == 2 && comprimidoCola == ''){
                alert('Campos Incompletos - Ingreso Masivo');
                return false;
            }else if(labor.length == 0){
                alert('Campo Incompleto - Labor a Realizar.');
                return false;
            }else{
                $("#btnEnviar").hide();
                $("#loading").show();
                return true;
            }
        }
    }

    function ponerNombreMasivo()
    {
        //$("#inlineRadio1").removeAttr('checked');
        //$("#inlineRadio2").removeAttr('checked');
        var nombreeContratista = $("#empresaContratista").val();
        if(nombreeContratista.length != 0){
            $("#nombre").val(nombreeContratista);
            //$("#nombre").attr('readonly', true);
            //$("#inlineRadio2").attr('checked', true);
            //tipoRegistroV("RM");
        }else{
            $("#nombre").val('');
           // $("#nombre").removeAttr('readonly');
            //$("#inlineRadio1").attr('checked', true);
            //tipoRegistroV("RI");
        }
    }

    function seleccionArchivo(valor)
    {
        console.log("datos input: "+valor);
        console.log(valor[0].files[0].name);
        if(valor[0].files[0].name.length > 0){
            $("#archivosSubidos").fadeIn();
            var cuentaLi = $("#listadoAdj li").length;
            
            console.log("cuenta: "+cuentaLi);
            var suma = parseInt(cuentaLi) + parseInt(1); 
            $("#txtArchiSub").text('Archivos Subidos ('+suma+')');
        }else{
            $("#archivosSubidos").fadeOut();
        }
        $("#listadoAdj").append("<li>"+valor[0].files[0].name+"</li>").html();  
    }

    function verAdjuntos(){
        if($("#adjSelecc").val() == 1){
            $("#archivosSubidos").css({'height':'auto'});
            $("#adjSelecc").val(2);
        }else{
            $("#archivosSubidos").css({'height':'23px'});
            $("#adjSelecc").val(1);
        }
    }


</script>
@include('layouts.footer', ['modulo' => 'unitario'])