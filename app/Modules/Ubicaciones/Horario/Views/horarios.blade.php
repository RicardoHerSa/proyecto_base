@include('layouts.app', ['modulo' => 'horarios'])
<div class="container">
    @if (isset($registrado) && $registrado == true)
        <div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
            <strong>Información!</strong> Horario registrado satisfactoriamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
    @elseif(isset($registrado) && $registrado == false) 
        <div class="alert alert-danger alert-dismissible fade show mt-5" role="alert">
            <strong>Información!</strong> Error al registrar horario.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
    @endif
    @if (isset($incompletos) && $incompletos == true)
    <div class="alert alert-danger alert-dismissible fade show mt-5" role="alert">
        <strong>Información!</strong> Por favor complete todos los campos.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <br>
    @endif
    @if (isset($sinResult) && $sinResult == true)
    <div class="alert alert-warning alert-dismissible fade show mt-5" role="alert">
        <strong>Información!</strong> No se encontró información para este horarío.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <br>
    @endif
    <div class="row justify-content-center mt-5">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <label for="">Seleccione el horario:</label>
        <form action="{{route('gestionHorario')}}" method="POST">
            <select id="descHorario" name="descHorario" onclick="setDescripcion()" class="form-control">
                @foreach ($consultHorarios as $hora)
                    <option value="{{$hora->descripcion}}">{{$hora->descripcion}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if (isset($nuevo) && $nuevo == true)
            <div class="alert alert-info alert-dismissible fade show mt-5" role="alert">
                <strong>Información!</strong> Para registrar un nuevo horario, complete la siguiente información.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <br>
            @endif
            <div class="card">
                <div class="card-header">
                    <h4>Gestión de Horario</h4>
                </div>
                <div class="card-body">
                
                    <div class="form-group">
                        <label for="">Descripción</label>
                        <input type="text" class="form-control"  name='descripcion' id='descripcion' value="{{isset($descripcion)&&$descripcion!=''?$descripcion:''}}">
                    </div>
                    <div class="form-group">
                        <label for="">Días: </label>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="1" id="ck_lunes">
                                    <label class="form-check-label" for="ck_lunes">
                                    Lunes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="2" id="ck_martes">
                                    <label class="form-check-label" for="ck_martes">
                                    Martes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="3" id="ck_miercoles">
                                    <label class="form-check-label" for="ck_miercoles">
                                    Miercoles
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="4" id="ck_jueves">
                                    <label class="form-check-label" for="ck_jueves">
                                    Jueves
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6">
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="5" id="ck_viernes">
                                    <label class="form-check-label" for="ck_viernes">
                                      Viernes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="6" id="ck_sabado">
                                    <label class="form-check-label" for="ck_sabado">
                                      Sabado
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="ev_check()" name="check_dia" class="form-check-input" type="checkbox" value="7" id="ck_domingo">
                                    <label class="form-check-label" for="ck_domingo">
                                      Domingo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <label for="">Hora Inicio: </label>
                                <input type="time" id='hora_inicio' name='hora_inicio' class="form-control" value="{{isset($hora_inicio)&&$hora_inicio!=''?$hora_inicio:''}}"> 
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <label for="">Hora Fin: </label>
                                <input type="time" id='hora_fin' name='hora_fin' class="form-control" value="{{isset($hora_fin)&&$hora_fin!=''?$hora_fin:''}}"> 
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-check">
                                    <input {{isset($activo)&&$activo=='S'?'checked':''}} class="form-check-input" type="checkbox" value="1" name='ck_activo' id='ck_activo'>
                                    <label class="form-check-label" for="flexCheckDefault">
                                    Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <input class="large" type="text" style="display:none;" name="dias" id="dias" />
                        @if (isset($id_horario) && $id_horario != '')
                            <input class='large' type='text' style='display:none'  name='id_horario' id='id_horario' value='{{$id_horario}}'/>
                        @else 
                            <input class='large' type='text' style='display:none'  name='id_horario' id='id_horario' />
                        @endif

                        @if (isset($display) && $display == 1)
                            <input class="btn btn-primary" type='submit' onclick='ev_check()' name='btn_guardar' id='btn_guardar'  value='{{isset($descripcion)?'Actualizar':'Guardar'}}'/>
                            <input class="btn btn-primary" type='submit' name='btn_cancelar' id='btn_cancelar'  value='Cancelar'/>
                        @else
                            <input class="btn btn-primary" type='submit' name='btn_consulta' id='btn_consulta'  value='Consultar'/>
                            <input class="btn btn-primary" type='submit' name='btn_nuevo' id='btn_nuevo'  value='Nuevo Horario'/>
                        @endif
                        
                        @csrf

                </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@php
    if(isset($inserta) && $inserta!=false){
	$ejecutado=json_encode("1");
	}else{
	$ejecutado=json_encode("0");
	}
	if(isset($dias) && $dias!=""){
	$datos_dias=json_encode($dias);
	}else{
	$datos_dias=json_encode("0");
	}
@endphp

<script type="text/javascript">
	var dias="";
	var eje=<?php echo $ejecutado; ?>;
	var dias=<?php echo $datos_dias; ?>;
	function ev_check(){
		var group = document.getElementsByName("check_dia");
		dias="";
		 for (var i = 0; i < group.length; i++) {
                if (group[i].checked == true) {
                    dias+=group[i].value+"-";
                }
            }
			document.getElementById("dias").value=dias;
	}
	function setDescripcion(){
	var sel=document.getElementById("descHorario").value;
	$('#descripcion').val(sel);
	}
	function evaluaHorario(){
	var h_ini=document.getElementById("hora_inicio").value;
	var h_fin=document.getElementById("hora_fin").value;
	if(h_ini !="" && h_fin!=""){
		/*if(h_ini>h_fin){
			window.alert("La hora de inicio no puede ser mayor a la hora fin");
			}*/
		}
	}
	$(document).ready(function (){
	 
		if(eje == "1" ){
						$('#aprobado').css('visibility', 'visible');
					    $('#aprobado').show('slow').delay(1000).hide('fast');
					}
		var datos_d=dias.split("-");
		for(var i=0;i<datos_d.length;i++){
		if(datos_d[i]=="1"){
			$( "#ck_lunes" ).prop( "checked", true );
			}else if(datos_d[i]=="2"){
			$( "#ck_martes" ).prop( "checked", true );
			}else if(datos_d[i]=="3"){
			$( "#ck_miercoles" ).prop( "checked", true );
			}else if(datos_d[i]=="4"){
			$( "#ck_jueves" ).prop( "checked", true );
			}else if(datos_d[i]=="5"){
			$( "#ck_viernes" ).prop( "checked", true );
			}else if(datos_d[i]=="6"){
			$( "#ck_sabado" ).prop( "checked", true );
			}else if(datos_d[i]=="7"){
			$( "#ck_domingo" ).prop( "checked", true );
			}
		}
	});
	
	</script>
@include('layouts.footer', ['modulo' => 'horarios'])