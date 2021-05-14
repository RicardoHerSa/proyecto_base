@include('layouts.app', ['modulo' => 'asignacion'])
<div class="container">
    <div class="row mt-5">
        <div class="col-xs-12 col-md-{{!isset($tabla)?'12':'3'}} col-lg-{{!isset($tabla)?'12':'3'}}">
            <div class="card">
                <div class="card-header">
                    <h4>Registro de Visitante</h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="{{route('consultarRegistroIngreso')}}">
                      <div class="form-group">
                          @csrf
                          <label for="tx_cedula">Cédula: </label> 
                          <input type="text" class="form-control" id="tx_cedula" name="tx_cedula">
                          <input  id="id_cod" name="id_cod" type="hidden" value = "{{isset($Cedula_aux)?$Cedula_aux:''}}; ?>" />
                          <input id="opt_btn" name="opt_btn" type="hidden" value = "ENTRADA" />
                        
                           <input id="tipo_ingreso" name="tipo_ingreso" type="hidden" value= "{{isset($tipo_ingreso)?$tipo_ingreso:'PEATON'}}'"/>
                       
                      </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-left">
                            <input type="submit" class="btn btn-primary"id="btn_consulta" name="btn_consulta" value="Registrar">
                        </div>
                 </form>
                </div>
            </div>
            <div class="div">
                <button id="carro"><i class="fa fa-automobile" style="font-size:60px;"></i></button>
                <button id="moto"><i class="fa fa-motorcycle" style="font-size:60px;"></i></button>
                <button id="bicy"><i class="fa fa-bicycle" style="font-size:60px;"></i></button>
                <button type="button" id="peaton"><i class="fa fa-user" style="font-size:60px;width:60px;height: 60px;"></i></button>
            </div>
            <div class="">
                <input type="button" onclick="registraEntrada()" style="font-size: 2.5em;width:100%;text-align:center" id="btn_entrada" value="Entrada"/>
                <input type="button"  onclick="registraSalida()" style="font-size: 2.5em;width:100%;text-align:center" id="btn_salida" value="Salida"/>
            </div>
        </div>
        <div class="col-xs-12 col-md-{{isset($tabla)?'9':''}} col-lg-{{isset($tabla)?'9':''}}">
            <div id="info" name="info" style="visibility: hidden">
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    var opt=<?php echo isset($opcion)?$opcion:"'s'"; ?>;
        if(opt.trim()=='ENTRADA'){
            registraEntrada();
        }else if(opt.trim()=='SALIDA'){
            registraSalida();
        }else{
            registraEntrada();
        }
    var cc = getUrlVars()["tc"];
    var registro=getUrlVars()["reg"];
    
    if(typeof cc !="undefined" && typeof registro !="undefined"){
    document.getElementById("tx_cedula").value= cc;
    document.getElementById("opt_btn").value=registro;
    }
        $('#info').css('visibility', 'visible');
        $('#info').show('slow').delay(10000).hide('slow'); //tiempo en el que muestra la info del usuario a registrar
   });
   
   function escondeBoton(){
   $('#tx_cedula').val("");
   if(!$('#btn_foto').is(':hidden')){
       $('#btn_foto').hide();
       }
   }
   function getUrlVars() {
       var vars = {};
       var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
           vars[key] = value;
       });
       //window.location.href.substring(0, window.location.href.indexOf('?'));
       //document.getElementById("content").innerHTML = response.html;
      // document.title = response.pageTitle;
      // window.history.replaceState({"html":response.html,"pageTitle":response.pageTitle},"", "http://172.16.108.78/control_ingreso");
       return vars;
   }   
   function cortaEntrada(){
        var tipo_script=<?php echo isset($tipo_script)?$tipo_script:"'g'"; ?>;
       if(tipo_script=="0"){
            var entrada=document.getElementById("tx_cedula").value;
            if(entrada.length > 60){
            var id_c=entrada.substring(0, 6);
               if(id_c == "PubDSK"){
                   var texto=entrada.substring(7, entrada.length);
                   var p=0; //posicion de la primera letra encontrada
                   for(var i=0; i<texto.length; i++){
                   if(IsLetter(texto[i])){
                       p=i;
                       break;
                       }
                   }
                   p=p+7;
                   var cedula=entrada.substring((p-10),p);
                   cedula=filtroCedula(cedula);
                   document.getElementById("tx_cedula").value=cedula;
               }else{
                   var p=0; //posicion de la primera letra encontrada
                   for(var i=0; i<entrada.length; i++){
                   if(IsLetter(entrada[i])){
                       p=i;
                       break;
                       }
                   }
                   var cedula=entrada.substring((p-10),p);
                   cedula=filtroCedula(cedula);
                   document.getElementById("tx_cedula").value=cedula;
               }
            }
           }else if(tipo_script=="1"){
               var entrada=document.getElementById("tx_cedula").value;
                if(entrada.length >= 30){
                    var id_c=entrada.substring(10, 16);
                       if(id_c == "PubDSK"){
                           var texto=entrada.substring(17, entrada.length);
                           var p=0; //posicion de la primera letra encontrada
                           for(var i=0; i<texto.length; i++){
                           if(IsLetter(texto[i])){
                               p=i;
                               break;
                               }
                           }
                           p=p+17;
                           var cedula=entrada.substring((p-10),p); 
                           cedula=filtroCedula(cedula);
                           document.getElementById("tx_cedula").value=cedula;
                       }else{
                           entrada=entrada.substring(26,70);
                           var p=0; //posicion de la primera letra encontrada
                           for(var i=0; i<entrada.length; i++){
                           if(IsLetter(entrada[i])){
                               p=i;
                               break;
                               }
                           }
                           var cedula=entrada.substring((p-10),p);
                           cedula=filtroCedula(cedula);
                           document.getElementById("tx_cedula").value=cedula;
                       }
                    }
               }/*else{
                   console.log(entrada);
                   }*/
   }
   //Filtro de 0, Elimina los 0 agregados al número de cedula
   function filtroCedula(numero){
   if(numero!=""){
   var cc="";
   if(numero.charAt(0)=="0"){
       cc=numero.substring(1,numero.length);
       return filtroCedula(cc);
       }else{
           return numero;
           }	
       }
   }
   // Given   : ch is a character
   // Returns : true if ch is a letter
   function IsLetter(ch) 
   { 
       return (isNaN(ch * 1));
   }  
   function FocusOnInput(){	 	
   document.getElementById("tx_cedula").focus();
   }
   function registraEntrada(){
   document.getElementById("opt_btn").value="ENTRADA";
   $("#btn_entrada").css("background","#10D2E5");
   $("#btn_entrada").css("color","#fff");
   $("#btn_salida").css("background","#fff");
   $("#btn_salida").css("color","#666666");
   FocusOnInput();
   }
   function registraSalida(){
   document.getElementById("opt_btn").value="SALIDA";
   $("#btn_salida").css("background","#10D2E5");
   $("#btn_salida").css("color","#fff");
   $("#btn_entrada").css("background","#fff");
   $("#btn_entrada").css("color","#666666");
   FocusOnInput();
   }
   
    //SETEA EL TIPO DE ENTRADA ANTERIOR
    var valor= $('#tipo_ingreso').val();
        if(valor=='PEATON'){
            $('#peaton').css('background-color', '#10D2E5');
        }else if(valor=='CARRO'){
            $('#carro').css('background-color', '#10D2E5');
        }else if(valor=='MOTO'){
            $('#moto').css('background-color', '#10D2E5');
        }else if(valor=='BICY'){
            $('#bicy').css('background-color', '#10D2E5');
        }
    
        $('#peaton').on('click',function(){
            resetButtoms();
            FocusOnInput();
                $(this).css('background-color', '#10D2E5');
                $('#tipo_ingreso').val('PEATON');
        });
            $('#carro').on('click',function(){
            resetButtoms();
            FocusOnInput();
                $(this).css('background-color', '#10D2E5');
                $('#tipo_ingreso').val('CARRO');
            
        });
            $('#moto').on('click',function(){
            resetButtoms();
            FocusOnInput();
                $(this).css('background-color', '#10D2E5');
                $('#tipo_ingreso').val('MOTO');
        });
            $('#bicy').on('click',function(){
            resetButtoms();
            FocusOnInput();
                $(this).css('background-color', '#10D2E5');
                $('#tipo_ingreso').val('BICY');	
        });
        //Resetea el estado y el color de los botones (peaton,carro,moto..)
        function resetButtoms(){
            $('#tipo_ingreso').val('');
            $('#peaton').css('background-color', '');
            $('#carro').css('background-color', '');
            $('#moto').css('background-color', '');
            $('#bicy').css('background-color', '');
        }
    
    </script>
@include('layouts.footer', ['modulo' => 'asignacion'])