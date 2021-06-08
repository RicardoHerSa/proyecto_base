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

    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Registro de Nuevo Visitante</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">SEÑOR SOLICITANTE:  Recuerde que los trabajos en alturas debe tener:</p>
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

    <form action="{{route('registraranexos')}}" method="POST" enctype="multipart/form-data">

        <!--Solicitante-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="solicitante">Solicitante, Correo, Ext: <span style="color:red">*</span></label>
                                    <input id="solicitante" class="form-control" type="text" name="solicitante" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tipoIngreso">Tipo de Ingreso: <span style="color:red">*</span></label>
                                    <select name="tipoIngreso" id="tipoIngreso" class="form-control" onchange="ocultaInput()" required>
                                        @foreach ($tiposVisitante as $tipos)
                                            <option value="{{$tipos->id_tipo_visitante}}">{{$tipos->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tipoId">Tipo de Identificación: <span style="color:red">*</span></label>
                                    <select name="tipoId" id="tipoId" class="form-control" required>
                                        <option value="CEDULA">Cédula</option>
                                        <option value="PASAPORTE">Pasaporte</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="empContra">
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group" id="contenedorContratista">
                                    <label for="empresaContratista">Empresa Contratista: </label>
                                    <input id="empresaContratista" class="form-control" type="text" name="empresaContratista">
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
                                    <div class="col-xs-12 col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="cedula">Identificación: </label>
                                            <input required id="cedula" class="form-control" type="number" name="cedula">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for=nombre">Nombre Completo: </label>
                                            <input required id="nombre" class="form-control" type="text" name="nombre">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="anexo">Anexo: </label>
                                            <input required id="anexo" class="form-control" type="file" accept="image/png,image/jpg,.pdf,.doc,.docx,application/msword,.zip,.rar" name="anexo">
                                            <small id="txtAnexo" class="ml-2">Sólo archivos pdf, word, png, jpg, zip, rar</small>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-3 col-lg-1">
                                        <div class="form-group">
                                            <label for="">Borrar</label>
                                            <button type="button" class="btn btn-danger" onclick="borrarAnexos()"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div id="inputs" style="display: none">
                                    <div class="row" id="clonado">
                                        <div class="col-xs-12 col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="cedula1">Indetificación: </label>
                                                <input id="cedula1" class="form-control" type="text" name="cedula1">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="nombre1">Nombre Completo: </label>
                                                <input id="nombre1" class="form-control" type="text" name="nombre1">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="anexo1">Anexo: </label>
                                                <input id="anexo1" class="form-control" type="file" accept="image/png,image/jpg,.pdf,.doc,.docx,application/msword,.zip,.rar" name="anexo1">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-3 col-lg-1">
                                            <div class="form-group">
                                                <label for="">Borrar</label>
                                                <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p  class="ml-3"><i>Sólo se permite el registro de hasta 10 visitantes de manera individual.</i></p>
                    <!-- <input type="submit" value="enviar">
                    </form>-->
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
                                    <label for="fechaIngreso">Fecha Inicio Ingreso: <span style="color:red">*</span></label>
                                    <input id="fechaIngreso" class="form-control" type="date" name="fechaIngreso" required>
                                </div>
                                <div class="form-group">
                                    <label for="horario">Horario:</label>
                                   <input type="text" class="form-control" readonly value="Horario Especial Lunes a Domingo 00:00 - 23:59">
                                </div>
                                <div class="form-group">
                                    <label for="empVisi">Empresa a Visitar: <span style="color:red">*</span></label>
                                    <select name="empVisi" id="empVisi" class="form-control" required>
                                        
                                        @foreach ($empresas as $emp)
                                        <option value="{{$emp->codigo_empresa}}">{{$emp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="fechaFin">Fecha Fin Ingreso: <span style="color:red">*</span></label>
                                    <input id="fechaFin" class="form-control" type="date" name="fechaFin" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora">Hora: </label>
                                    <input id="hora" class="form-control" type="text" name="hora" readonly value="00:00 - 23:59">
                                </div>
                                <!--
                                <div class="form-group">
                                    <label for="ciudad">Ciudad: <span style="color:red">*</span></label>
                                    <select name="ciudad" id="ciudad" class="form-control" required>
                                        
                                        <option value="2">Bogota</option>
                                        <option value="1">Cali</option>
                                        <option value="4">Medellin</option>
                                        <option value="3">Yumbo</option>
                                        <option value="5">Montevideo Bgta.</option>
                                        <option value="6">Palmira</option>
                                        <option value="12">Tocancipa</option>
                                        <option value="13">Ginebra</option></es>
                                    </select>
                                </div>-->
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
                        <hr>
                        <div class="row">
                        
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="float-left  ml-3 mt-2">
                                    <button type="button" onclick="nuevaSede();" class="btn btn-primary">Añadir</button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                            
                            <div class="form-group">
                                <label for="my-input">Seleccione Sede</label>
                                <select name="sede" id="sede" class="form-control">
                                    
                                    @foreach ($sedes as $sede)
                                    <option value="{{$sede->id_sedef}}">{{$sede->nombre}}</option>
                                    @endforeach
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
                                        <label for="my-input">Seleccione Sede</label>
                                        <select name="sede1" id="sede1" class="form-control">
                                            
                                            @foreach ($sedes as $sede)
                                            <option value="{{$sede->id_sedef}}">{{$sede->nombre}}</option>
                                            @endforeach
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
            <!-- <input type="submit">
                </form>-->
            </div>
        </div>

        <!--Labor a realizar-->
        <div class="row mt-2">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="labor">Labor a Realizar: <span style="color:red">*</span></label>
                            <textarea name="labor" id="labor" cols="15" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-lg-6"></div>
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Enviar">
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
        if(txt != "CON"){
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
        if(cantidadRegistro > 9){
            alert('No puedes añadir mas de 10 registros.');
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
                    //para los input cc, nombre, anexo
                    $("#clonado"+cantidadRegistro).find($('#cedula'+cantidadRegistroActual)).attr("name", "cedula"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#cedula'+cantidadRegistroActual)).attr("id", "cedula"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#nombre'+cantidadRegistroActual)).attr("name", "nombre"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#nombre'+cantidadRegistroActual)).attr("id", "nombre"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#anexo'+cantidadRegistroActual)).attr("name", "anexo"+cantidadRegistro);
                    $("#clonado"+cantidadRegistro).find($('#anexo'+cantidadRegistroActual)).attr("id", "anexo"+cantidadRegistro);
                                                    
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
                $(obj).closest(".row").remove();

            }
            $("#cantRegis").val(cantidadRegistro);
        }
    }

    //agregar mas select sedes
    let nuevaSede = function() 
    {
        var cant = parseInt(1);
        var cantidadRegistroActual = parseInt($("#cantRegisSelect").val());
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
                $("#anexarSedes")                                    // crea una nueva sección
                //.insertBefore("[name='borrar']")    // insértala antes del botón de enviar (para que se vayan añadiendo en orden)
                 .append($("#selects").html()
                 );        // añádele el código con los campos de .inputs
                    
                    $("#clonadoSelects").find("button").attr("onclick", "eliminarSelect(this,"+cantidadRegistro+")");
                    $("#clonadoSelects").find("button").attr('name', 'btnEliminarSelect'+cantidadRegistro);
                    //para los input cc, nombre, anexo
                    $("#clonadoSelects").find($('#sede'+cantidadRegistroActual)).attr("name", "sede"+cantidadRegistro);
                    $("#clonadoSelects").find($('#sede'+cantidadRegistroActual)).attr("id", "sede"+cantidadRegistro);
                  
            }
        
                 $("#cantRegisSelect").val(cantidadRegistro);
        }
              
      
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
        $("#anexo").val('');
    }

    function tipoRegistroV($event)
    {
        if($event == "RM"){
            $("#btnAñadirRegistro").hide();
            
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
                $("#clonado"+hasta).attr('id', 'clonado');
                $("#cantRegis").val(0);

            }else if(hasta == 1){
                $("#inputs").hide();
            }
            //$("#clonado").css({'display':'none'});
            $("#txtAnexo").text('Sólo archivo .Xlsx');
            $("#anexo").removeAttr('accept');
            $("#anexo").attr('accept', '.xlsx');
        }else{
            $("#btnAñadirRegistro").show();
            $("#clonado").css({'display':'flex'});
            $("#txtAnexo").text('Sólo archivos pdf, word, png, jpg, zip, rar');
            $("#anexo").removeAttr('accept');
            $("#anexo").attr('accept', 'image/png,image/jpg,.pdf,.doc,.docx,application/msword,.zip,.rar');
        }
    }


</script>
@include('layouts.footer', ['modulo' => 'unitario'])