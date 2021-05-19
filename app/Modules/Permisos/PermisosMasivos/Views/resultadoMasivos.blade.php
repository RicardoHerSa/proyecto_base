@include('layouts.app', ['modulo' => 'horarios'])
 <!--CSS y JS PARA EL MÓDULO DE PERMISOS UNITARIOS-->
 <script src="{{ asset('reporteParqueadero/js/jquery.min.js')}}"></script> 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/formoid-flat-blue.js')}}"></script>
 <link rel="stylesheet" href="{{asset('reporteParqueadero/js/styles/jqx.base.css')}}" type="text/css" />
    
 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxcore.js')}}"></script>
 <script src="{{ asset('reporteParqueadero/js/jqxTree.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxbuttons.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxscrollbar.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxpanel.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxcheckbox.js')}}"></script>

 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxdata.js')}}"></script> 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxmenu.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.sort.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.filter.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.selection.js')}}"></script> 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxlistbox.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxcombobox.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxdropdownlist.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.pager.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.columnsresize.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.aggregates.js')}}"></script>
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxgrid.edit.js')}}"></script>

 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxchart.core.js')}}"></script> 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxdraw.js')}}"></script> 
 <script type="text/javascript" src="{{asset('reporteParqueadero/js/jqxdata.js')}}"></script> 

 <div class="float-left mt-2">
     <a href="{{url('permisos-masivos')}}" class="btn btn-primary">Volver</a>
 </div>
<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-xs-12 col-md-4 col-lg-4">
        </div>
        <div class="col-xs-12 col-md-4 col-lg-4">
            <div id='jqxTree'></div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-xs-12 col-md-1 col-lg-1">
        </div>
        <div class="col-xs-12 col-md-11 col-lg-11">
            <div id='jqxgrid' ></div>
            <br>
            <label>Horarios:</label>
            <div id="jqxCombo"></div>
            <br>
            <input type="button" value="Guardar" id='jqxButtonG' class="btn btn-primary" />
        </div>
        
    </div>

    
   <input type="hidden" value="{{auth()->user()->name}}" id="username">
</div>
   
<script type="text/javascript">
	var dataV = <?php echo $dataVisitante;?>;
	var dataT= <?php echo $dataTree;?>;
	var dataH=<?php echo $dataHorario;?>;
	var usuario= $("#username").val();
	var id_tree = "";
	var id_horario="";
	var filtrado=0;
	var dataUser=new Array();
	var arregloDatos=new Array();
	
        $(document).ready(function () {
			//Evento para boton guardar
			$("#jqxButtonG").on('click', function () {
				console.log("click");
				
			 var getFilter = $('#jqxgrid').jqxGrid('getfilterinformation');
			 filtrado= getFilter.length;
			 console.log("filtrado: "+filtrado);
			 if(filtrado==1){
		 			var selectedrows = $("#jqxgrid").jqxGrid('getselectedrowindexes');
		 			var rowsTOT = $('#jqxgrid').jqxGrid('getboundrows');
		 		if(selectedrows.length != rowsTOT.length){
		 			//Peor de los casos
		 			arregloDatos=$("#jqxgrid").jqxGrid('getdisplayrows');
		 			var selectedrowindex = getIndexes(arregloDatos);
		 			selectedrowindex= getData(selectedrowindex);

		 		}else{
		 			arregloDatos=$("#jqxgrid").jqxGrid('getdisplayrows');
		 			var selectedrowindex = getIndexes(arregloDatos);
		 		}
			
            }else{
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindexes');
            }
				var rowscount = selectedrowindex.length;
				console.log("rowscount: "+rowscount);
				if(rowscount>0){
		
					for(var i=0;i<rowscount;i++){
						//var id_tree_idx = $('#jqxgrid').jqxGrid('getrowid', selectedrowindex[i]);
						var rowData = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex[i]);
					
						dataUser[i]= {'user':usuario,'id_h':id_horario, 'id_ev':rowData.ID_EMPRESA_VISITANTE, 'id_jefe':rowData.IDENTIFICACION_JEFE, 'fecha_i':rowData.FECHA_INGRESO, 'fecha_f':rowData.FECHA_FIN};
					 } 
					 
				
					 $('#loading').show();
					 $('#contenedor').hide();
					 var token = '{{csrf_token()}}';
					 var request=$.ajax({
									type:  'POST',
									url: "insertarRegistrosMasivos",
									data: {'data':dataUser,'id_t':id_tree, _token:token},
									cache: false,
									async: true,
									success: function(response){
									console.log(response);
									if(response == 1 ){
										$('#aprobado').css('visibility', 'visible');
										$('#aprobado').show('slow').delay(10000).hide('slow');
										
										}
									console.log(response);
									},
									complete: function(){					
									    $('#loading').hide('slow');
									    $('#contenedor').show('slow');
									      },
									error:function(xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
												}
									
								});
					 
				}
				dataUser.length=0;
         });         
			
			//Evento para el arbol
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
			

	var source =
		{
			datatype: "json",
			datafields: [
			
				{ name: 'ID_VISITANTE', type: 'string'},
				{ name: 'ID_EMPRESA_VISITANTE', type: 'string'},
				{ name: 'IDENTIFICACION_JEFE', type: 'string'},
				{ name: 'EMPRESA', type: 'string'},
				{ name: 'TIPO_VISITANTE', type: 'string'},
				{ name: 'CEDULA', type: 'string'},
				{ name: 'NOMBRE', type: 'string'},
				//{ name: 'APELLIDO', type: 'string'},
				{ name: 'CARGO', type: 'string'},
				//{ name: 'JEFE', type: 'string'},
				{ name: 'CIUDAD', type: 'string'},
				{ name: 'TIPO_CONTRATO', type: 'string'},
				{ name: 'FECHA_INGRESO', type: 'string'},
				{ name: 'FECHA_FIN', type: 'string'}
				
			],           
            localdata: dataV,
			cache: false
		};
	var dataAdapter = new $.jqx.dataAdapter(source);
    	$("#jqxgrid").jqxGrid(
    	    	{
				source: dataAdapter,
                width: 750,
                sortable: true,
                columnsresize: true,
                pageable: true,
				filterable: true,
                autoheight: true,
                showstatusbar: true,
                showaggregates: true,
				selectionmode: 'checkbox',
                altrows: true,
				columns: [
					{ text: 'Empresa', datafield: 'EMPRESA', width: 120},
					{ text: 'Tipo', datafield: 'TIPO_VISITANTE', width: 120},
					{ text: 'Cedula', datafield: 'CEDULA', width: 120},
					{ text: 'Nombre', datafield: 'NOMBRE', width: 120},
					//{ text: 'Apellido', datafield: 'APELLIDO',width: 120},		
					{ text: 'Cargo', datafield: 'CARGO',width: 120},
					//{ text: 'Jefe', datafield: 'JEFE',width: 120},
					{ text: 'Ciudad', datafield: 'CIUDAD',width: 120},
					{ text: 'Tipo contrato', datafield: 'TIPO_CONTRATO',width: 120},	
					{ text: 'Fecha inicio', datafield: 'FECHA_INGRESO',width: 120},	
					{ text: 'Fecha fin', datafield: 'FECHA_FIN',width: 100}
					]
				}); 
		  
		
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
                $('#jqxTree').jqxTree({ source: records, height: '400px', hasThreeStates: true, checkboxes: true, width: '330px'});
				$('#jqxTree').jqxTree('expandAll');
				
		var source3 =
                {
                    datatype: "json",
                    datafields: [
                        { name: 'ID',type: 'string' },
                        { name: 'DESCRIPCION',type: 'string' }
                    ],
                    localdata: dataH,
					cache: false
                };
				var dataAdapter3 = new $.jqx.dataAdapter(source3);
				
				$("#jqxCombo").jqxComboBox({placeHolder:"Seleccione el horario",source: dataAdapter3, displayMember: "DESCRIPCION", valueMember: "ID", width: 200, height: 25});
                // trigger the select event.
                $("#jqxCombo").on('select', function (event) {
                    if (event.args) {
                        var item = event.args.item;
                        if (item) {
                            id_horario=item.value;
                        }
                    }
                });
                //Retorna arreglo con indices
                //recive como parametro un arreglo de tipo row	
               function getIndexes(arregloDatos){
                	var indexes=new Array();
                	var data=0;
                	for(var i=0;i<arregloDatos.length;i++){
                	 data = $('#jqxgrid').jqxGrid('getrowboundindex', i);
                	 indexes[i]=data;
                	}
                	return indexes;
                }
                //Retorna un arreglo de indices para el peor de los casos
                //Recive un arreglo de los indices de las filas que se encuentran mostrando en la grilla
                function getData(arreglo){
                	var seleccionado=new Array();
                	var iterador=0;
                	var selectedrows = $("#jqxgrid").jqxGrid('getselectedrowindexes');
                	for(var i=0; i<selectedrows.length;i++){
                		for(var b=0;b<arreglo.length;b++){
                			if(selectedrows[i]==arreglo[b]){
                				seleccionado[iterador]=arreglo[b];
                				iterador++;
                			}
                		}
                	}
                	return seleccionado;  
                }	
		
        });
    </script>

 
@include('layouts.footer', ['modulo' => 'horarios'])