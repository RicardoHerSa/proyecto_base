@include('layouts.app', ['modulo' => 'unitario'])
<div class="container">
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

    <!--Solicitante-->
    <div class="row mt-2">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="my-input">Solicitante, Correo, Ext: <span style="color:red">*</span></label>
                                <input id="my-input" class="form-control" type="text" name="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="my-input">Tipo de Ingreso: <span style="color:red">*</span></label>
                                <select name="" id="tipoIngreso" class="form-control" onchange="ocultaInput()">
                                    <option value="PROVEEDOR">Contratista</option>
                                    <option value="VISITANTE">Visitante</option>
                                </select>
                                <input type="hidden" id="muestraOculta" value="1">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="my-input">Tipo de Identificación: <span style="color:red">*</span></label>
                                <select name="" id="" class="form-control">
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
                                <input id="empresaContratista" class="form-control" type="text" name="">
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
                    <button onclick="nuevo();" class="btn btn-primary">Añadir</button>
                </div>
                <div class="card-body" id="anexos">
                    <div id="inputs">
                        <div class="row">
                            <div class="col-xs-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="my-input">Indetificación: </label>
                                    <input id="my-input" class="form-control" type="text" name="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="my-input">Nombre Completo: </label>
                                    <input id="my-input" class="form-control" type="text" name="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="my-input">Anexo: </label>
                                    <input id="my-input" class="form-control" type="file" name="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3 col-lg-1">
                                <div class="form-group">
                                    <label for="">Borrar</label>
                                    <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>
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
                                <label for="my-input">Fecha Inicio Ingreso: <span style="color:red">*</span></label>
                                <input id="my-input" class="form-control" type="date" name="">
                            </div>
                            <div class="form-group">
                                <label for="horario">Horario: <span style="color:red">*</span></label>
                                <select name="horario" id="horario" class="form-control" onchange="consultarHora()">
                                    @foreach ($horarios as $horar)
                                        <option value="{{$horar->id}}">{{$horar->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="my-input">Empresa a Visitar: <span style="color:red">*</span></label>
                                 <select name="" id="" class="form-control">
                                     <option value="">--SELECCIONE--</option>
                                     @foreach ($empresas as $emp)
                                     <option value="{{$emp->codigo_empresa}}">{{$emp->descripcion}}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="my-input">Fecha Fin Ingreso: <span style="color:red">*</span></label>
                                <input id="my-input" class="form-control" type="date" name="">
                            </div>
                            <div class="form-group">
                                <label for="hora">Horario: <span style="color:red">*</span></label>
                                <input id="hora" class="form-control" type="text" name="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="my-input">Ciudad: <span style="color:red">*</span></label>
                                 <select name="" id="" class="form-control">
                                    <option value="SELECCIONE">--SELECCIONE--</option>
                                    <option value="2">Bogota</option>
                                    <option value="1">Cali</option>
                                    <option value="4">Medellin</option>
                                    <option value="3">Yumbo</option>
                                    <option value="5">Montevideo Bgta.</option>
                                    <option value="6">Palmira</option>
                                    <option value="12">Tocancipa</option>
                                    <option value="13">Ginebra</option></es>
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
            <div class="card">
                <div class="float-left  ml-3 mt-2">
                    <button class="btn btn-primary">Añadir</button>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Sedes a Visitar: </h5>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-lg-4"></div>
                        <div class="col-xs-12 col-md-4 col-lg-4">
                          
                         
                           <div class="form-group">
                               <label for="my-input">Seleccione Sede</label>
                               <select name="" id="" class="form-control">
                                   <option value="0">--SELECCIONE--</option>
                                   @foreach ($sedes as $sede)
                                   <option value="{{$sede->id}}">{{$sede->descripcion}}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                   </div>
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
                        <label for="my-input">Labor a Realizar: <span style="color:red">*</span></label>
                        <textarea name="" id="" cols="15" rows="5" class="form-control"></textarea>
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

</div>

<input type="text" id="cantRegis" value="0">
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
        var opcion = $("#muestraOculta").val();
        if(opcion == 1){
            $("#contenedorContratista").hide();
            $("#muestraOculta").val(0);
        }else{
            $("#contenedorContratista").fadeIn();
            $("#muestraOculta").val(1);
        }
    }

    //agregar mas inputs
    let nuevo = function() 
    {
        var cant = parseInt(1);
        var cantidadRegistro = parseInt($("#cantRegis").val());
        cantidadRegistro+=cant;
        if(cantidadRegistro > 9){
            alert('No puedes añadir mas de 10 registros.');
        }else{
            $("#anexos")                                    // crea una nueva sección
                // .insertBefore("[name='borrar']")    // insértala antes del botón de enviar (para que se vayan añadiendo en orden)
                 .append($("#inputs").html())        // añádele el código con los campos de .inputs
                 .find("button")                     // selecciona el botón de añadir
                 .attr("onclick", "eliminar(this)");  // cambia su acción a eliminar
                 //.text("Eliminar");   // y su texto también
                 $("#cantRegis").val(cantidadRegistro);
        }
              
      
    }
     let eliminar = function(obj)
    {
        var cant = parseInt(1);
        var cantidadRegistro = parseInt($("#cantRegis").val());
        cantidadRegistro-=cant;
        if(cantidadRegistro < 0){

        }else{
            $(obj).closest(".row").remove();
            $("#cantRegis").val(cantidadRegistro);
        }
    }
</script>
@include('layouts.footer', ['modulo' => 'unitario'])