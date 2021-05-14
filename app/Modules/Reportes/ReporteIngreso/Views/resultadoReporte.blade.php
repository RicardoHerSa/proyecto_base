@include('layouts.app', ['modulo' => 'horarios'])
<div class="container">
    <div class="float-left ">
        <a href="{{url('reporte-ingreso')}}" class="btn btn-primary">Volver</a>
    </div>
    <h3 class="text-center mt-3">Registro de Ingresos y Salidas.</h3>
    <hr>
    <div class="row mt-5">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="mb-5" id="jqx_reporte"></div>
            <form method="POST" style="display: none" action="{{route('descargarExcelReporteIngreso')}}">
                @csrf
                <input type="submit"  id="btn_regresar" name="btn_regresar" />
                <input type="submit"  id="btn_reporte" name="btn_reporte" />
                <input type="hidden"  id="cedula" name="cedula" value="{{$cedula}}" />
            </form>
        </div>
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
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/jqxgrid.edit.js')}}"></script>
<script>
    var data_l = <?php echo $data; ?>;
      $(document).ready(function () {  
                  var source =
    {
        datatype: "json",
        datafields: [
            { name: 'nombre' },
            { name: 'identificacion' },
            { name: 'tipo_registro' },
            { name: 'usuario_creacion' },
            { name: 'fecha_hora' },
            { name: 'equipo' },
            { name: 'serial' }
         
        ],
        localdata: data_l,
        async: false
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    
    $("#jqx_reporte").jqxGrid(
            {
            source: dataAdapter,
            width: '100%',
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
             rendertoolbar: function (toolbar) {
                var me = this;
                var container = $("<div style='margin: 5px;'></div>");
                toolbar.append(container);           
                container.append('<input style="margin-left: 5px;width: 125px;" id="descargar" type="button" value="Descargar" />');
                container.append('<input style="margin-left: 5px;width: 125px;" id="regresar" type="button" value="Regresar" />');
                $("#descargar").jqxButton();
                $("#regresar").jqxButton();
                $("#regresar").on('click',function(){
                    $("#btn_regresar").click();
                });
                $("#descargar").on('click',function(){
                 $("#btn_reporte").click();
                });
             },
            columns: [
               
                { text: 'Nombre', datafield: 'nombre',editable: false, width: 200},
                { text: 'Cedula', datafield: 'identificacion',editable: false, width: 130 },            
                { text: 'Tipo Ingreso', datafield: 'tipo_registro',editable: false, width: 200 },
                { text: 'Portería', datafield: 'usuario_creacion',editable: false, width: 200},
                { text: 'Fecha Registro', datafield: 'fecha_hora',editable: false, width: 180},
                { text: 'Equipo', datafield: 'equipo',editable: false, width: 180},
                { text: 'Serial', datafield: 'serial',editable: false, width: 180}
                                               
                ]
            });

      });
    
    
</script>
@include('layouts.footer', ['modulo' => 'horarios'])