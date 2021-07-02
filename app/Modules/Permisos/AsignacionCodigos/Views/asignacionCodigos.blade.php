@include('layouts.app', ['modulo' => 'asignacion'])
<div class="container">
    <br>
    @if (Session::has('mensaje'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Información!</strong> {{Session::get('mensaje')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
     @endif
    
    <div class="row justify-content-center">
        <!--Formulario de consulta-->
        @if (!isset($tabla))
            <div class="col-xs-12 col-md-2 col-lg-2"></div>
            
        @endif

        <div class="col-xs-12 col-md-{{isset($tabla)?'3':'8'}} col-lg-{{isset($tabla)?'3':'8'}} ">
                <!-- Formulario de consulta-->
                @if (isset($tabla))
                 <a href="{{url('asigna-codigos')}}" class="btn btn-primary mb-3">Volver</a>
                @endif
                <div class="card" >
                    <div class="card-body">
                        <h3 class="card-title" style="color:#666666">Consulta</h3>
                        <hr>
                        <form method="POST" action="{{route('consultaVisitante')}}">
                            @csrf
                            <div class="form-group">
                                <label for="tx_cedula">Cedula: </label>
                                <input required id="tx_cedula" name="tx_cedula" type="text" class="form-control">
                            </div>
                            <hr>
                            <div class="form-group" style="display: flex">
                                <input id="btn_consulta" name="btn_consulta" type="submit" value="Consultar" class="btn btn-primary">
                                @if (isset($tabla) && $tabla != "")
                                 <br>
                                <a style="position: relative;left:2px"  href="{{url('tomarFoto/'.$cedulVi)}}" class="btn btn-primary">Tomar Foto</a>
                                @endif
                            </div>
                           
                        </form>
                    </div>
                </div>
        </div>

        <!--Resultado encabezado info personal-->
        
        <div class="col-xs-12 col-md-{{isset($tabla)?'9':'2'}} col-lg-{{isset($tabla)?'9':'2'}}" style="margin-top: 2%">
            @if (isset($tabla) && $tabla != '0')
            <div class="row" style="background-color: #00FF1A;
            border-radius: 10px; 
            border-left:0px; font-size:20px;padding:10px;margin-top:5%">
                <div class="col-xs-12 col-md-6 col-lg-6">
                     

                    <img onerror="this.src='{{asset('../storage/app/public/fotos/person.png')}}'"  style="width:80%" src="{{ asset('../storage/app/public/fotos/'.$cedulVi.'.jpg') }}"  width="50%" >
                    
                </div>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <ul style="list-style: none;margin-top:30px">
                        <li><b>{{$row[0]." ".$row[1]}}<b></li>
                        <li id='cc'><b>{{$row[2]}}<b></li>
                        <li><b>{{$row[3]}}</b></li>
                    </ul>
                </div>
            </div>
          
                @php
                    //echo $tabla;
                @endphp
            @elseif(isset($tabla) && $tabla == '0')
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Información!</strong> No se encontraron registros.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <br>
            @endif
        </div>
       
    </div>
    
    @if (isset($tabla))
        <hr>
    @endif
    <!--Formulario de registro de codigo-->
    <div class="row justify-content-center">
        @if (isset($tabla) && $tabla != null)
        <div class="col-xs-12 col-md-12 col-lg-12">
            <!-- Letrero de cambio realizado -->
            @if (isset($operacion) && $operacion == true)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Información!</strong>  Operación registrada satisfactoriamente.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(isset($operacion) && $operacion == false)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Información!</strong>  Ha ocurrido un error al registrar.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card" >
                <div class="card-body">
                    <h3 class="card-title" style="color:#666666">Registro de Código</h3>
                    <hr>
                    <form  method="POST" action="{{route('registrarCodigos')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tx_cedula">Artículo: </label>
                                    <input required id="articulo" name="articulo" type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="tx_cedula">Código Visitante: </label>
                                    <input   id="codigo" name="codigo" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tx_cedula">Modelo: </label>
                                    <input required id="modelo" name="modelo" type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input  id="activo" name="activo" value="1" checked class="form-check-input" type="checkbox">
                                        <label class="form-check-label" for="activo">
                                          Activo
                                        </label>
                                      </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="tx_cedula">Serial: </label>
                                    <input required id="serial" name="serial" type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input id="full_parqueo" name="full_parqueo" value="1" class="form-check-input" type="checkbox">
                                        <label class="form-check-label" for="full_parqueo">
                                          Parquedero
                                        </label>
                                      </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="float-right">
                                <input type="hidden" name="cedula" value="{{isset($cedulVi)?$cedulVi:''}}">
                                <input type="hidden" name="username" value="{{Auth::user()->name}}">
                                <input type="submit" id="btn_guardar" name="btn_guardar" value="Guardar" class="btn btn-primary"/>

                            </div>
                        </div>
                    
                    </form>
                </div>
            </div>
        </div>
        @endif
       
    </div>

    <!--Tabla de codigos -->
    <div class="row justify-content-center">
        @if (isset($tabla) && $tabla != null)
            <div class="col-xs-12 col-md-12 col-lg-12">
                <hr>
                <div id='jqxgrid'></div>
            </div>
        @endif

    </div>
</div>
<br>
 <!--CSS y JS PARA EL MÓDULO DE PERMISOS UNITARIOS-->
 <script src="{{ asset('permisosUnitarios/js/jquery.min.js')}}"></script> 
 <script type="text/javascript" src="{{asset('permisosUnitarios/js/formoid-flat-blue.js')}}"></script>
 <link rel="stylesheet" href="{{asset('permisosUnitarios/styles/jqx.base.css')}}" type="text/css" />
    
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
 var datos = <?php echo isset($dataV)?$dataV:"l"; ?>;
 var dataV;
 if(datos != 'l'){
     dataV = datos;
 }
 var arreglo1=new Array(); //Datos de codigos del visitante
 var arreglo2=new Array(); //Datos de codigos de los activos asociados a un visitante
 var selectedrowindex1=""; //indices del arreglo 1
 var datos=new Array();
 var cedula="";
 var d = new Date();
 var day=d.getDate().toString();
 var mt=(d.getMonth()+1).toString();
 var year=d.getFullYear().toString();
 var insertar=true;

 $(document).ready(function(){
 
     var generaterow = function (i) {
             var row = {};
             row["cod_vis"] = null;
             row["fecha_creacion"] =  year+"-"+(mt[1]?mt:"0"+mt[0])+"-"+(day[1]?day:"0"+day[0]);
             row["activo"] = 1;
             return row;
         }
    if(dataV !="0"){
            var source =
        {
            datatype: "json",
            datafields: [
            
                { name: 'cod_vis', type: 'string'},
                { name: 'fecha_creacion', type: 'string'},
                { name: 'activo', type: 'int'}

                ],    
                addrow: function (rowid, rowdata, position, commit) {
                    commit(true);
                },  
                deleterow: function (rowid, commit) {
                    commit(true);
                },     
            localdata: dataV,
            cache: false
        };
            var dataAdapter = new $.jqx.dataAdapter(source);
     $("#jqxgrid").jqxGrid(
             {
             source: dataAdapter,
             width: 690,
             showtoolbar: true,
             editable: false,
             sortable: true,
             columnsresize: true,
             pageable: true,
             filterable: true,
             autoheight: true,
             showstatusbar: true,
             showaggregates: true,
             selectionmode:'singlerow',
             columns: [
                 { text: 'Código Visitante', datafield: 'cod_vis',editable: true, width: 230},
                 { text: 'Fecha Creación', datafield: 'fecha_creacion',editable: false, width: 230},
                 { text: 'Activo', datafield: 'activo', columntype: 'checkbox', editable: true, width: 230}	
                 ]
             });
             
       $("#jqxgrid").on("cellclick", function (event){    
                   
                   var args = event.args;        
                   var rowBoundIndex = args.rowindex;
                   var rowData = $('#jqxgrid').jqxGrid('getrowdata', rowBoundIndex);
                   var obj=rowData.cod_vis;
                   var ini=obj.substring(0, 1);

             
                 if(ini=='A'){
                     if($("#modelo").val()==''){insertar=false;}
                     if($("#serial").val()==''){insertar=false;}
                     if($("#articulo").val()==''){insertar=false;}			
                     }
               });


            $("#jqxgrid").on('cellvaluechanged', function (event){
                     var args = event.args;
                     var datafield = event.args.datafield;
                     var rowBoundIndex = args.rowindex;
                     $("#jqxgrid").jqxGrid('endcelledit', rowBoundIndex, "cod_vis", false);
             }); 

             $('#jqxgrid').on('rowclick', function (event){
                    
                     var args = event.args;
                     var boundIndex = args.rowindex;
                     var rowData = $('#jqxgrid').jqxGrid('getrowdata', boundIndex);
                     var cod=rowData.cod_vis;
                     var fecha=rowData.fecha_creacion;
                     var activo=rowData.activo;
                     cedula=$("#cc").text();
                     var token = '{{csrf_token()}}';
                                       
                     var request=$.ajax({
                                 type:  'POST',
                                 async: false,
                                 url: "consultaAlClickearTabla", 
                                 data: {'cod':cod, 'cc':cedula, 'fecha':fecha, 'act':activo, _token:token},
                                 cache: false,
                                 success: function(response){
                                     
                                 if(response != 0 ){  //modificado != 1
                         
                                     var arreglo=response.split("|");
                                     $("#serial").val(arreglo[0]);
                                     $("#articulo").val(arreglo[1]);
                                     $("#modelo").val(arreglo[2]);
                                     $("#codigo").val(arreglo[3]);
                                     var activo= arreglo[4];
                                     var parqueadero= arreglo[5];
                                     if(activo == 'S'){
                                         $("#activo").prop('checked', true);
                                         }else{
                                         $("#activo").prop('checked', false);
                                             }
                                     
                                     if(parqueadero == 1){
                                         $("#full_parqueo").prop('checked', true);
                                         }else{
                                         $("#full_parqueo").prop('checked', false);
                                             }
                                     
                                     }else{
                                        $("#serial").val('');
                                        $("#articulo").val('');
                                        $("#modelo").val('');
                                        $("#codigo").val('');
                                     }
                                 },
                                 error:function(xhr, ajaxOptions, thrownError) {
                                     alert(xhr.status);
                                     alert(thrownError);
                                             }
                                 
                             }); 
                                                                       
                     }); 
             
         
            } 
 });
    
 </script>
@include('layouts.footer', ['modulo' => 'asignacion'])