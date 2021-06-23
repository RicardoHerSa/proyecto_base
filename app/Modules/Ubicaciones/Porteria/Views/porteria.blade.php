@include('layouts.app', ['modulo' => 'horarios'])
<div class="container">
    @if (isset($notiActualizacion) && $notiActualizacion == true)
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> Nodo actualizado satisfactoriamente.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(isset($notiActualizacion) && $notiActualizacion == false)
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> Error al actualizar nodo.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (isset($notiRegistro) && $notiRegistro == true)
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> Nodo registrado satisfactoriamente.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(isset($notiRegistro) && $notiRegistro == false)
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>Información!</strong> Error al registrar nodo.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    <div class="row mt-3">
        <div class="col-xs-12 col-md-4 col-lg-4">
            <div id="jqxTree"></div>
        </div>
        <div class="col-xs-12 col-md-8 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Portería</h5>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="home-tab" style="cursor:pointer" onclick="
                            $('#home').addClass('show active in');
                            $('#profile').removeClass('active');
                        " data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">Agregar Nodo</a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Modificar Nodo</a>
                      </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                          <form action="{{route('registrarNodo')}}" method="POST">
                              @csrf
                              <div class="form-group mt-3">
                                  <label for="nuevo_nodo">Nuevo nodo: </label>
                                    <input type="text" required class="form-control"  id="nuevo_nodo" name="nuevo_nodo">
                                    <input id="id_nodos2" name="id_nodos2" class="large" type="hidden" />
                                    {!!$errors->first('id_nodos2', '<div style="display:block" class="invalid-feedback">:message</div>')!!}
                              </div>
                              <div class="form-group mt-3">
                                 <input type="submit" class="btn btn-primary" value="Crear">
                              </div>
                          </form>
                      </div>
                      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <form action="{{route('actualizarNodo')}}" method="POST">
                            @csrf
                            <div class="form-group mt-3">
                                <label for="nombre_nodo">Nuevo nombre: </label>
                                <input id="nombre_nodo" name="nombre_nodo" type="text" required class="form-control" required>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input id="ub_activo" name="ub_activo"  class="form-check-input" type="checkbox" value="S" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                      Activo
                                    </label>
                                  </div>
                            </div>
                            <div class="form-group mt-3">
                                    <input id="id_nodos" name="id_nodos" class="large" type="hidden" />
                               <input type="submit" class="btn btn-primary" value="Actualizar">
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="dataTree" value="{{$dataTree}}">

<!--CSS y JS PARA EL MÓDULO DE PERMISOS UNITARIOS-->
<script src="{{ asset('permisosUnitarios/js/jquery.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('permisosUnitarios/js/formoid-flat-blue.js')}}"></script>
<link rel="stylesheet" href="{{asset('permisosUnitarios/styles/jqx.base.css')}}" type="text/css" />
   
<script src="{{ asset('permisosUnitarios/scripts/demos.js')}}"></script>   
<script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxcore.js')}}"></script>
<script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxbuttons.js')}}"></script>
<script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxscrollbar.js')}}"></script>
<script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxpanel.js')}}"></script>
<script type="text/javascript" src="{{ asset('permisosUnitarios/js/jqxtree.js')}}"></script>
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
<script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxtabs.js')}}"></script>

<script type="text/javascript">
	var dataT= $("#dataTree").val();
    console.log(dataT);

	var id_tree="";
	$(document).ready(function(){
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
                
				$('#jqxTree').jqxTree({ source: records, width: '300px'});
				
		//CREACION DEL TAB
		// Create jqxTabs.
            //$('#jqxTabs').jqxTabs({ width: 500, height: 200, position: 'top'});
			
			//Evento del arbol
			$('#jqxTree').on('click', function (event) {
					var item = $('#jqxTree').jqxTree('getSelectedItem');
                    console.log(item);
					id_tree += item.id + "-";
					if(item.value == 'S'){
					    $("#ub_activo").attr("checked",true);
					}else{
					    $("#ub_activo").attr("checked",false);
					}
                    console.log(id_tree);
					$("#id_nodos").val(id_tree);
					$("#id_nodos2").val(id_tree);
                    //poner el nopmbre en el input de editar
                    if(item.parentId != 1  ){
                        $("#nombre_nodo").val(item.label);
                        $("#home").removeClass('show active');
                        $("#home-tab").attr('aria-selected', false);
                        $("#home-tab").removeClass('active');
                        $("#profile").addClass('show active in');
                        $("#profile-tab").attr('aria-selected', true);
                        $("#profile-tab").addClass('active');
                    }else{
                        $("#nombre_nodo").val("");
                        $("#profile").removeClass('show active in');
                        $("#profile-tab").attr('aria-selected', false);
                        $("#profile-tab").removeClass('active');
                        $("#home").addClass('show active in');
                        $("#home-tab").attr('aria-selected', true);
                        $("#home-tab").addClass('active');
                    }

            });
			
			//OCULTA LOS DE ESTADO N EN LAS RAMAS DEL ARBOL
			$('#jqxTree').on('expand', function (event) {
                var items = $('#jqxTree').jqxTree('getItems');
                for (var i = 0; i < items.length; i++) {
                            var item = items[i];
                            if(item.value == "N"){
                                document.getElementById(item.id).style.display = "none";
                            }
                           console.log(item.label)
                        }
                });
			//$('#jqxTree').jqxTree('expandAll');
		});
	</script>
@include('layouts.footer', ['modulo' => 'horarios'])