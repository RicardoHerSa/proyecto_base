@include('layouts.app', ['modulo' => 'unitario'])
<style>
    label{
        font-size: 15px;
    }
</style>
<div class="container">
    <br>
    
    @if (Session::has('mensaje'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Información!</strong> {{Session::get('mensaje')}}.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
    @endif
    <div class="row justify-content-center">

        <div class="col-xs-12 col-md-3 col-lg-3">
                <!-- Formulario de consulta-->
                <div class="card" >
                    <div class="card-body">
                      <form method="post" action="{{route('consultarCedula')}}">
                        @csrf
                        <h4 style="color:#666666">Consulta de Visitante</h4>
                        <hr>
                        <div class="form-group">
                            <label class="title" for="cc_visitante"><b>Cedula:</b></label>
                            <input id="cc_visitante" name="cc_visitante" class="form-control" type="text" required />
                        </div>
                        <div class="form-group" style="display:none">
                            <label class="title" for="nombre_v">Nombre: </label>
                            <input id="nombre_v" name="nombre_v" class="form-control" type="text" />
                        </div>
                        <div class="form-group" style="display:none">
                            <label class="title" for="apellido_v">Apellido: </label>
                            <input id="apellido_v" name="apellido_v" class="form-control" type="text" />
                        </div>
                        <div class="submit">
                            <input type="submit" id="btn_consulta" name="btn_consulta" value="Consultar" class="btn btn-primary"/>
                        </div>
                      </form>
                        @if(isset($nombre))
                            <hr>
                           

                            <img onerror="this.src='{{asset('../storage/app/public/fotos/person.png')}}'"  title="{{$nombre}}" class='img-thumbnail' src='{{asset('../storage/app/public/fotos').'/'.$cc.'.jpg'}}' WIDTH='100%' >


            
                        @endif
                       
                    </div>
                </div>
        </div>

        <div class="col-xs-12 col-md-4 col-lg-4">
            <!--Formulario de resultados-->
            <div class="card">
                <div class="card-body">
                    <h4 style="color:#666666">Datos del Visitante</h4>
                    <hr>
                    @if(isset($nombre))
                    <form  method="post" id="form_update">
                        @csrf
                        <input id="cc_visitante2" style="display: none"   name="cc_visitante2"  type="text" value="{{isset($cc)?$cc:''}}"/>
                          
                            <label><B>Nombre:</B> {{isset($nombre)?$nombre:''}}</label>
                        
                            <label id='cc' name='cc'><B>Cedula:</B>{{isset($cc)?$cc:''}}</label>
                            <label><B>Cargo:</B> {{isset($cargo)?$cargo:''}}</label>
                            <label><B>Empresa:</B> {{isset($empresa)?$empresa:''}}</label>
                            <label><B>Tipo:</B> {{isset($tipo)?$tipo:''}}</label>
                            <label><B>Jefe:</B> {{isset($jefe)?$jefe:''}}</label>
                            <label><B>Ciudad:</B> {{isset($ciudad)?$ciudad:''}}</label>
                            <label><B>Tipo de Contrato:</B> {{isset($contrato)?$contrato:''}}</label>
                            <label><B>Fecha Inicio:</B> {{isset($fechaIni)?$fechaIni:''}}</label>
                            <label><B>Fecha Fin:</B> {{isset($fechaFin)?$fechaFin:''}}</label>
                        
                            <div class="element-checkbox">
                                
                                <div class="column column1">
                                    <label><input type="checkbox" name="check_estado" id="check_estado" value="inactivo" {{isset($estado) && !empty($estado) && $estado != 'N' ?'checked':''}} / ><span> Activo</span></label>
                                </div>
                                <span class="clearfix"></span>
                            </div>
                            <div class="element-checkbox">
                               		
                                <div class="column column1">
                                    <label><input type="checkbox" name="check_parqueadero" id="check_parqueadero" value="0" {{isset($parqueadero) && !empty($parqueadero) && $parqueadero == '1' ?'checked':''}} / ><span> Parqueadero</span></label>
                                </div>
                                <span class="clearfix"></span>
                            </div>
                    
                     <label class="title"><B>Horario</B></label>
                      
                        <select class="form-control" id="horario_sel" name="horario_sel" onchange="getHorario();">
                            @foreach ($listaHorarios as $lista)
                                    <option {{isset($idhorario)&&$idhorario==$lista->id?'selected':''}} value="{{$lista->id}}">{{$lista->descripcion}}</option>
                            @endforeach
                        </select>
                    
                        <div class="submit">
                                @isset($btn)
                                    <hr>
                                     <input type="submit" id="btn_actualizar" name="btn_actualizar" value="Guardar" class="btn btn-primary"/>
                                @endisset
                        </div>
                    </form>
                    @else 
                        <p><i>Los resultados de búsqueda cargarán aquí.</i><p>
                    @endif                            
                   
                </div>
              </div>
           

        </div>

        <div class="col-xs-12 col-md-5 col-lg-5">
            <!--Lista de checkbox-->
            <div class="card">
                
                <div class="card-body">
                  <h4 class="card-title" style="color:#666666">Permisos</h4>
                  <hr>
                  @if (isset($nombre))
                    <div id="jqxTree"></div>
                    <div id="aprobado" style="visibility: hidden;display:none" class="mt-5">
                     <img src="{{asset('permisosUnitarios/img/aprobado.png')}}" border="0" style="width: 40px">Los cambios fueron guardados
                    </div>
                  @else 
                    <p><i>Los permisos asociados cargarán aquí.</i></p>    
                  @endif
                </div>
            </div>
        </div>
           

        <input type="hidden" id="json" value="{{$dataTree}}">
        <input type="hidden" id="jsonUser" value="{{isset($dataTUser)?$dataTUser:''}}">
       
    </div>
</div>
 <!--CSS y JS PARA EL MÓDULO DE PERMISOS UNITARIOS-->
 <script src="{{ asset('permisosUnitarios/js/jquery.min.js')}}"></script> 
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/formoid-flat-blue.js')}}"></script>
 <link rel="stylesheet" href="{{asset('permisosUnitarios/styles/jqx.base.css')}}" type="text/css" />
    
 <script src="{{ asset('permisosUnitarios/scripts/demos.js')}}"></script>   
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxcore.js')}}"></script>
 <script src="{{ asset('permisosUnitarios/js/jqxTree.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxbuttons.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxscrollbar.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxpanel.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxcheckbox.js')}}"></script>
 
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxdata.js')}}"></script> 
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxmenu.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.sort.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.filter.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.selection.js')}}"></script> 
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxlistbox.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxcombobox.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxdropdownlist.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.pager.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.columnsresize.js')}}"></script>
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.aggregates.js')}}"></script>
 
<script  type="text/javascript">
    var dataT= $("#json").val();
    var validaData = <?php echo isset($dataTUser)?$dataTUser:0;?>;
    var userDataT;
    if(validaData.length > 0){
        userDataT = validaData;
    }else{
        userDataT = "";
    }
    
    var id_tree = "";
	var id_horario="";
	var check_estado="";
    var check_parqueadero ="";

    $(document).ready(function (){
    var source2 =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'id',type: 'string' },
                        { name: 'parentid',type: 'string' },
                        { name: 'text',type: 'string' },
                        { name: 'value',type: 'string' }
                    ],
                    id: 'id',
                    localdata: dataT,
					cache: false
                };
				// create data adapter.
                var dataAdapter2 = new $.jqx.dataAdapter(source2);
                // perform Data Binding.
                dataAdapter2.dataBind();
                var records = dataAdapter2.getRecordsHierarchy('id', 'parentid', 'items', [{ name: 'text', map: 'label'}]);
                $('#jqxTree').jqxTree({ source: records, height: '470px', hasThreeStates: true, checkboxes: true, width: '100%'});

                //Captura los permisos del arbol
                $('#jqxTree').on('click', function (event) {
                    
                    var items = $('#jqxTree').jqxTree('getCheckedItems');
                    if(id_tree!=""){
                        id_tree="";
                    }
                    for (var i = 0; i < items.length; i++) {
                            var item = items[i];
                            id_tree += item.value + ",";
                        }
                });

            });

    function getEstado(){
	    check_estado=document.getElementById("check_estado").checked;
	}
	function getHorario(){
	    id_horario=document.getElementById("horario_sel").value;
	}
    function getParqueadero(){
	    check_parqueadero=document.getElementById("check_parqueadero").checked;
	}
    
    $( "#form_update" ).submit(function( event ) {
        // Stop form from submitting normally
            event.preventDefault();
        // Get some values from elements on the page:
            getHorario();
            getEstado();
            getParqueadero();
		
			var items = $('#jqxTree').jqxTree('getCheckedItems');
            console.log(items);
				if(id_tree!=""){
					id_tree="";
				}
            for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    id_tree += item.value + ",";
                }
                console.log(id_tree);

				var cedula_u=$("#cc").text().split(":"); 
				var cc_id=cedula_u[1];
				if(cc_id != ''){
                    var token = '{{csrf_token()}}';
				    var request=$.ajax({
                            type:  'POST',
                            url: "actualizarVisitante",
                            data: {'cc':cc_id, 'id_t':id_tree, 'id_horario':id_horario, 'activo':check_estado , 'parqueadero':check_parqueadero, _token:token},
                            cache: false,
                            success: function(response){
                                console.log(response);
                                if(response == 1 ){
                                                $('#aprobado').css('visibility', 'visible');
                                                $('#aprobado').show('slow').delay(1000).hide('slow');
                                            }else{
                                                alert("no llego");
                                            }
                                    
                                        },
                            error:function(xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                            }
                        
                        });
                     }
			
				});

 $(window).load(function(){ 
     
     console.log(userDataT);
	if(userDataT !="0"){
        console.log("cantidad: "+userDataT.length);
		//Esta parte llena el arbol con permisos
		for(var i=0; i < userDataT.length; i++){
            console.log("user "+[i]+": "+userDataT[i]);
                var permisos=(userDataT[i]+"").split(",");
            
                var items = $('#jqxTree').jqxTree('getUncheckedItems');
            //console.log("ITEMS: "+items[0].value);
                for (var a = 0; a < items.length; a++) {
                    //console.log("ITEM: "+items[a].value + " --  PERMISOS: "+ permisos[0]);	
                                var item = items[a];
                                if(item.value == permisos[0]){
                                    $('#jqxTree').jqxTree('checkItem', item, true);
                                }
                     }
				}
	    }
		$('#jqxTree').jqxTree('expandAll');
        $("#jqxTree").css({'width':'100%'});
	});
	

   
</script>
@include('layouts.footer', ['modulo' => 'unitario'])