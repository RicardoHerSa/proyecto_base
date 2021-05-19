@include('layouts.app', ['modulo' => 'unitario'])
<div class="container">
    <br>
    @php
        if(isset($data_v) && $data_v[0] != ''){
            //echo "uno";
            $nombre=$data_v[0];
            $cedula=$data_v[1];
            $vehiculo=$data_v[4];
            $placa=$data_v[5];
            $estado=$data_v[6];
            $codigo=$data_v[7];
            $responsable=$data_v[8];
        }elseif (isset($data_b)&& $data_b[0] != ''){
            //echo "dos";
            $nombre=$data_b[0];
            $cedula=$data_b[1];
            $nombreCiudad = $data_b[2];
            $responsable = $data_b[3];
            $codigo = $data_b[4];
            $vehiculo= $data_b[5];
            $placa= $data_b[6];
            $idEmpresa = $data_b[7];
            $estado="";
        }else{
            //echo "tres";
            $nombre="";
            $vehiculo="";
            $placa="";
            $estado="";
            $codigo="";
        }
    @endphp
    @if (Session::has('alerta'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Información!</strong> {{Session::get('alerta')}}.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
     @endif
    <div class="row justify-content-center">
        <!--Formulario de consulta-->
        <div class="col-xs-12 col-md-{{isset($tabla)?'3':'12'}} col-lg-{{isset($tabla)?'3':'12'}} ">
                <!-- Formulario de consulta-->
                @if (isset($tabla))
                    <a href="{{url('registro-visitante-temporal')}}" class="btn btn-primary mb-3">Volver</a>
                @endif
                <div class="card" >
                    <div class="card-body">
                        <h3 class="card-title" style="color:#666666">Consulta</h3>
                        <hr>
                        <form method="POST" action="{{route('consultaVisitanteTemporal')}}">
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
                                <a style="left: 2px;position: relative;" href="{{url('tomarfototemporal/'.$cedula)}}" class="btn btn-primary">Tomar Foto</a>
                                @endif
                            </div>
                           
                        </form>
                    </div>
                </div>
        </div>

        <!--Resultado encabezado info personal-->
        <div class="col-xs-12 col-md-{{isset($tabla)?'9':''}} col-lg-{{isset($tabla)?'9':''}}" style="margin-top: 5%">
            @if (isset($tabla) && $tabla != '0')
                <div class="row" style="background-color: #00BFFF;
                border-radius: 10px; 
                border-left:0px; font-size:20px;padding:10px">
                    <div class="col-xs-12 col-md-6 col-lg-6">
                        <img style="width: 80%" class="img-thumbnail" src="{{asset('storage').'/fotos'.'/'.$cedula.'.png'}}" alt="">
                    </div>
                    <div class="col-xs-12 col-md-6 col-lg-6">
                        <ul style="list-style: none;margin-top:30px">
                            <li><b>{{$row[0]." ".$row[1]}}</b></li>
                            <li id='cc'><b>{{$row[2]}}</b></li>
                            <li><b>{{$row[3]}}</b></li>
                            @if(trim($row[5])!='' && trim($row[6])!='')
                            <li><b>{{$row[5]}}</b></li>
                            <li><b>{{$row[6]}}</b></li>
                            @endif
                            <li><b>Autorizado Por: {{$row[7]}}</b></li>
                        </ul>
                    </div>
                </div>
                @php
                    //echo $tabla;
                @endphp
            @elseif(isset($tabla) && $tabla == '0')
                 <table class='table' style='background-color: #00BFFF;
                    border-radius: 10px; height: 200px; width=500 px;
                    border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                    <tr>	 
                        <td width='200' height='200'> 
                        <img src='http://172.19.92.223/ingresocarvajal/images/person.png' height='130' width='190'> 
                        </td> 
                        <td width='500' height='100'>
                        <table>
                            <tr> <td><label>NOMBRE </label></td></tr> 
                            <tr><td><label id='cc'>IDENTIFICACIÓN</label></td></tr> 
                            <tr> <td><label>EMPRESA A VISITAR</label></td></tr> 
                            </table>
                        </td> 
                    </tr> 
                 </table>
                <br>
            @endif
        </div>
       
    </div>
    
    @if (isset($tabla))
        <hr>
    @endif
    <!--Formulario de registro de codigo-->
    <div class="row justify-content-center">
        @if (isset($tabla))
        <div class="col-xs-12 col-md-12 col-lg-12">
            <!-- Letrero de cambio realizado -->
            @if (isset($operacion))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Información!</strong>  Operación registrada satisfactoriamente.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card" >
                <div class="card-body">
                    <h3 class="card-title" style="color:#666666">Registro Visitante Temporal</h3>
                    <hr>
                    <form  id="formCod" name="formCod" method="POST" action="{{route('registrarVisitante')}}">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="nombre">Nombre: </label>
                                    <input id="nombre" name="nombre" required value="{{isset($nombre)?$nombre:''}}" type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="cedula">Cedula: </label>
                                    <input id="cedula" name="cedulaR" required value="{{isset($cedula)?$cedula:''}}" type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="empresa">Empresa Destino: </label>
                                    <select name="empresa" id="empresa" class="form-control">
                                            <option value="0">SELECCIONE</option>
                                            @foreach ($listaEmpresas as $lista)
                                            <option {{isset($idEmpresa) && $idEmpresa != null && $idEmpresa == $lista->codigo_empresa?'selected':''}} value="{{$lista->codigo_empresa}}">{{$lista->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="ciudad">Ciudad: </label>
                                    <select name="ciudad" id="ciudad" class="form-control">
                                        <option value="0">SELECCIONE</option>
                                        @foreach ($listaCiudades as $lista)
                                            <option {{isset($nombreCiudad) && $nombreCiudad != null && $nombreCiudad == $lista->ciudad?'selected':''}} value="{{$lista->ciudad}}">{{$lista->ciudad}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="vehiculo">Vehículo: </label>
                                    <input id="vehiculo" name="vehiculo"  type="text" class="form-control" value="{{isset($vehiculo)?$vehiculo:'Sin vehiculo'}}">
                                </div>
                                <div class="form-group">
                                    <label for="placa">Placa: </label>
                                    <input id="placa" name="placa"  type="text" class="form-control" value="{{isset($placa)?$placa:''}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="responsable">Autorizado Por: </label>
                                    <input id="responsable" name="responsable" required type="text" class="form-control" value="{{isset($responsable)?$responsable:''}}">
                                </div>
                                  <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="radio" name="puerta" id="ck_entrada" value="ENTRADA"  {{isset($estado)&&$estado=="ENTRADA"||$estado==""?'checked':''}}>
                                    <label class="form-check-label" for="ck_entrada">
                                      ENTRADA
                                    </label>
                                  </div>
                                  <br>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="puerta" id="ck_salida" value="SALIDA" {{isset($estado)&&$estado!="ENTRADA"&&$estado != ""?'checked':''}}>
                                    <label class="form-check-label" for="exampleRadios2">
                                      SALIDA
                                    </label>
                                  </div>
                                  <br>
                                  <div class="form-group mt-4">
                                    <label for="codigo">Código: </label>
                                    <input required id="codigo" name="codigo" type="text" class="form-control" value="{{isset($codigo)?$codigo:''}}">
                                </div>
                            </div>
                        </div>
                      
                        <hr>
                        <div class="form-group">
                            <div class="float-right">
                                <input type="hidden" name="cedula" value="{{isset($cedulVi)?$cedulVi:''}}">
                                <input type="hidden" name="username" value="{{Auth::user()->name}}">
                                <input type="submit" id="btn_registrar" name="btn_registrar" value="Registrar" class="btn btn-primary"/>
                            </div>
                        </div>
                    
                    </form>
                </div>
            </div>
        </div>
        @endif
       
    </div>

  
</div>
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
                                     console.log(response);
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
@include('layouts.footer', ['modulo' => 'unitario'])