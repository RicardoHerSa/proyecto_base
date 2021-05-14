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

<div class="container">
    <div class="float-left ">
        <a href="{{url('reporte-parqueadero')}}" class="btn btn-primary">Volver</a>
    </div>
    <h3 class="text-center mt-3">Registro de Parqueaderos.</h3>
    <hr>
    <div class="row mt-5">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div id='chartContainer_fijos' style="width:auto; height:400px"></div> 
            </div>
    </div> 
    <div class="row mt-3">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
             <div id='chartContainer_temp' style="width:auto; height:400px"></div>
        </div> 
    </div>

    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
                <div id="jqxgrid"></div>
        </div>
    </div>
</div>
   
 

 <script type="text/javascript">
	var dataR= <?php echo $data_rep; ?>;
	var dataG= <?php echo $data_grap; ?>;
    var dataG_Temp= <?php echo $data_temp; ?>;
    var tipo_v=<?php echo "'".$tipo_v."'"; ?>; //Tipo de vehiculo en grafica
	
	$(document).ready(function(){
	console.log(dataR);
   		var source =
		{
			datatype: "json",
			datafields: [
			
				{ name: 'empresa', type: 'string'},
				{ name: 'asignados_f', type: 'string'},
				{ name: 'ocupados_f', type: 'int'},
                { name: 'asignados_t', type: 'string'},
                { name: 'ocupados_t', type: 'int'},
                { name: 'tipo', type: 'string'},
                { name: 'total', type: 'string'}

				],    
				addrow: function (rowid, rowdata, position, commit) {
                    commit(true);
                },  
                 deleterow: function (rowid, commit) {
                    commit(true);
                },     
            localdata: dataR,
			cache: false
		};
	var dataAdapter = new $.jqx.dataAdapter(source);
		$("#jqxgrid").jqxGrid(
    	    	{
				source: dataAdapter,
                width: 1230,
                showtoolbar: true,
                editable: true,
                sortable: true,
                columnsresize: true,
                pageable: true,
				filterable: true,
                autoheight: true,
                showstatusbar: true,
                showaggregates: true,
                selectionmode:'singlerow',
				columns: [
					{ text: 'Empresa', datafield: 'empresa',editable: true, width: 370},
					{ text: 'Asignados Fijos', datafield: 'asignados_f',editable: false, width: 150},
					{ text: 'Ocupados Fijos', datafield: 'ocupados_f',editable: false, width: 150},
                    { text: 'Asignados Temporales', datafield: 'asignados_t',editable: false, width: 160},
                    { text: 'Ocupados Temporales', datafield: 'ocupados_t',editable: false, width: 160},
                    { text: 'Tipo', datafield: 'tipo',editable: false, width: 100},
                    { text: 'Total Parqueaderos', datafield: 'total',editable: false, width: 140}       	
					]
				});	

		//GRAFICA
				
            // prepare jqxChart settings
            var settings_fijos = {
                title: "Uso de Parqueaderos "+tipo_v+" Fijos por Empresa",
                description: "Muestra cuantos parqueaderos estan ocupados por empresa",
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataG,
                xAxis:
                {
                    dataField: 'empresa',
                    gridLines: {visible: false},
                    tickMarks: {visible: true}
                },
                valueAxis:
                {
                    minValue: 0,
                    maxValue: 300,
                    unitInterval: 10,
                    title: {text: 'Número de Parqueaderos Fijos'}
                },
                colorScheme: 'scheme01',
                seriesGroups:
                    [
                        {
                            type: 'column',
                            columnsGapPercent: 30,
                            seriesGapPercent: 10,
                            series: [
                                    { dataField: 'disponibles', displayText: 'Disponible'},
                                    { dataField: 'ocupados', displayText: 'Ocupado'} 
                                                       
                                ]
                        }
                    ]
            };
            var settings_temp = {
                title: "Uso de Parqueaderos "+tipo_v+" Temporales por Empresa",
                description: "Muestra cuantos parqueaderos estan ocupados por empresa",
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataG_Temp,
                xAxis:
                {
                    dataField: 'empresa',
                    gridLines: {visible: false},
                    tickMarks: {visible: true}
                },
                valueAxis:
                {
                    minValue: 0,
                    maxValue: 300,
                    unitInterval: 10,
                    title: {text: 'Número de Parqueaderos Temporales'}
                },
                colorScheme: 'scheme01',
                seriesGroups:
                    [
                        {
                            type: 'column',
                            columnsGapPercent: 30,
                            seriesGapPercent: 10,
                            series: [
                                    { dataField: 'disponibles', displayText: 'Disponible'},
                                    { dataField: 'ocupados', displayText: 'Ocupado'} 
                                                       
                                ]
                        }
                    ]
            };
            
            // select the chartContainer DIV element and render the chart.
            console.log(settings_fijos);
            $('#chartContainer_fijos').jqxChart(settings_fijos);  
            $('#chartContainer_temp').jqxChart(settings_temp);  
            $("#jqxgrid").css({'width': 'auto'});
   			      
	});

</script>
@include('layouts.footer', ['modulo' => 'horarios'])