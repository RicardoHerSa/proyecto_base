<?php

namespace App\Modules\IngresoVisitante\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class IngresoController extends Controller
{
    public $cedula = "";
    public $id_vis = "";
    public $portero = "";
    public $fechaHoy = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('IngresoVisitante::ingresoVisitante');
    }

    public function consultarRegistroIngreso(Request $request)
    {
        $cedula = $request->input("tx_cedula");
      /* if(!is_numeric($cedula)){
            return redirect('ingreso-visitante')->with('msj', 'Por favor digite un documento válido');
        }*/
        $id_cod = $request->input("id_cod");
        $tipo_ingreso = $request->input("tipo_ingreso");
        $user_log = substr($request->input("username"), 0,25);
        $user = substr($request->input("username"), 0,25);


        $opcion=$request->input("opt_btn");
	    if($opcion == null){$opcion="0";}

        $c=substr($cedula,0,1);
        if($c=='A' && $id_cod!=''){
            $tabla = IngresoController::registraActivo($id_cod,$cedula,$user_log,$user);
          
        }else{
            $tabla = IngresoController::consultaVisitante($user,$user_log,$cedula,$opcion,$tipo_ingreso);	
            
        }

        $Cedula_aux=IngresoController::getCedula($cedula);

       

        //echo $cedula." ".$id_cod." ".$tipo_ingreso;

        return view('IngresoVisitante::ingresoVisitante', compact('tabla', 'Cedula_aux'));
    }

    public function registraActivo($cod_vis,$cod_equipo,$mac,$usuario)
    {
            
           $ini_cod_vis=substr($cod_vis,0,1); //iniciales del codigo de visitante
           
           $genera_reg= IngresoController::generaRegistro($usuario); //Valida si genera registro como portero

           if($ini_cod_vis=='R' || $ini_cod_vis=='V' || $ini_cod_vis=='C'){
               $consulta = DB::table('ohxqc_codigobidimensional')
               ->select('id_equipo', 'id_visitante')
               ->where('codigo', '=', $cod_equipo)
               ->where('activo', '=', 'S')
               ->whereIn('id_visitante', function($query) use ($cod_vis){
                $query->select('v.id_visitante')
                ->from('ohxqc_codigobidimensional as cb')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cb.id_visitante')
                ->where('cb.codigo', '=',$cod_vis)
                ->limit(1);
               })
               ->get();
               
                $id_equipo = "";
                foreach($consulta as $cons){
                    $id_equipo = $cons->id_equipo;
                }
       
                
                $consultaDos = DB::table('ohxqc_codigobidimensional as cb')
                ->select('v.identificacion', 'v.id_visitante', 'cb.codigo')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cb.id_visitante')
                ->where('cb.codigo', '=', $cod_vis)
                ->get();
           }else{

                $consulta = DB::table('ohxqc_codigobidimensional')
                ->select('id_equipo', 'id_visitante')
                ->where('codigo', '=', $cod_equipo)
                ->where('activo', '=', 'S')
                ->whereIn('id_visitante', function($query) use ($cod_vis){
                $query->select('v.id_visitante')
                ->from('ohxqc_codigobidimensional as cb')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cb.id_visitante')
                ->where('v.identificacion', '=', $cod_vis)
                ->limit(1);
                })
                ->get();
                
              
                $id_equipo = "";
                foreach($consulta as $cons){
                    $id_equipo = $cons->id_equipo;
                }

                $consultaDos = DB::table('ohxqc_codigobidimensional as cb')
                ->select('v.identificacion','v.id_visitante', 'cb.codigo')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cb.id_visitante')
                ->where('v.identificacion', '=',$cod_vis)
                ->get();
           }
            $cedula = "";
            $id_visitante = "";
            $activo_usr = "";
            foreach($consultaDos as $cons){
                $cedula = $cons->identificacion;
                $id_visitante = $cons->id_visitante;
                $activo_usr = $cons->codigo;
            }
           
           //*************relación visitante activo, consulta el serial del equipo
           if(trim($id_visitante)!='' && trim($id_equipo)!=''){
               $sql = DB::table('ohxqc_equipos_visitante as ev')
               ->select('ev.id_equipo_visitante', 'e.serial')
               ->join('ohxqc_equipos as e', 'e.id_equipo', '=', 'ev.id_equipo')
               ->where('ev.id_visitante', '=', $id_visitante)
               ->where('ev.id_equipo', '=', $id_equipo)
               ->get();
               $id_relacion = "";
               $serial = "";
               foreach($sql as $sq){
                $id_relacion = $sq->id_equipo_visitante;
                $serial = $sq->serial;
               }
           }else{
               $id_relacion=0;
           }
       
           $id_ub= IngresoController::getIDUbicacionDis($mac);


           if($ini_cod_vis=='R' || $ini_cod_vis=='V' || $ini_cod_vis=='C'){
              
            
           
               $sql = DB::table('ohxqc_visitantes as v')
               ->select(DB::raw("MAX(ins.id_ingreso_salida)"))
               ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
               ->join('ohxqc_permisos as p', 'p.id_empresa_visitante', '=', 'ev.id_empresa_visitante')
               ->join('ohxqc_ingresos_salidas as ins', 'ins.id_permiso', '=', 'p.id_permiso')
               ->whereIn('v.id_visitante', function($query) use ($cod_vis){
                    $query->select('id_visitante')
                    ->from('ohxqc_codigobidimensional')
                    ->where('codigo', '=', $cod_vis);
               })
               ->get();
              
           }else{
              
               
                $sql = DB::table('ohxqc_visitantes as v')
                ->select(DB::raw("MAX(ins.id_ingreso_salida)"))
                ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
                ->join('ohxqc_permisos as p', 'p.id_empresa_visitante', '=', 'ev.id_empresa_visitante')
                ->join('ohxqc_ingresos_salidas as ins', 'ins.id_permiso', '=', 'p.id_permiso')
                ->where('v.identificacion', '=', $cod_vis)
                ->get();

               
           }

         
                 foreach($sql as $sq){
                     $id_ingreso_salida = $sq->max;
                 }

           
       
               //CONSULTA EL ID DEL VISITANTE A PARTIR DEL CODIGO
               if($ini_cod_vis=='R' || $ini_cod_vis=='V' || $ini_cod_vis=='C'){
                   $consulId = DB::table('ohxqc_codigobidimensional')
                   ->select('id_visitante')
                   ->where('codigo', '=', $cod_vis)
                   ->where('activo', '=', 'S')
                   ->get();
               }else{
                   $sql="select id_visitante from ohxqc_visitantes where identificacion='$cod_vis'";
                   $consulId = DB::table('ohxqc_visitantes')
                   ->select('id_visitante')
                   ->where('identificacion', '=', $cod_vis)
                   ->get();
               }
               $id_vis = "";
               foreach($consulId as $id){
                    $id_vis = $id->id_visitante;
               }
               
             
               if(trim($id_vis)==''){
                   $id_vis=0;
               }

               $query = DB::table('ohxqc_visitantes as v')
               ->select('v.nombre', 'v.apellido', 'v.identificacion', 'emp.descripcion as empresa', 'foto')
               ->join('ohxqc_empresas_visitante as emv', 'emv.id_visitante', '=', 'v.id_visitante')
               ->join('ohxqc_empresas as emp', 'emp.id_empresa', '=', 'emv.id_empresa')
               ->where('v.id_visitante', '=', $id_vis)
               ->where('v.activo', '=', 'S')
               ->limit(1)
               ->get();
               
               $nombre_v = "";
               $apellido_v = "";
               $identificacion = "";
               $empresa = "";

               foreach($query as $q){
                $nombre_v = $q->nombre;
                $apellido_v = $q->apellido;
                $identificacion = $q->identificacion;
                $empresa = $q->empresa;
 
               }
               
       
           
               if($id_equipo!='' && $id_ingreso_salida!='' && $id_relacion!='' && ($genera_reg==true)){
                
            
                $inserta = DB::table('ohxqc_i_s_equipos')->insert([
                    'id_ingreso_salida' => $id_ingreso_salida,
                    'id_equipo_visitante' => $id_equipo,
                    'usuario_creacion' => 'admin',
                    'fecha_creacion' =>  now(),
                    'usuario_actualizacion' => 'admin',
                    'fecha_actualizacion' => now()
                   ]);

                    $tabla="<table class='' style='width: 100%; background-color: #00FF1A; border-radius: 10px; border-left:0px;  text-align: center;  font-weight: bold; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr> 
                            <td><label>".$nombre_v." ".$apellido_v." </label></td> 
                        </tr> 
                        <tr>  
                            <td><label>".$identificacion."</label></td> 
                        </tr> 
                        <tr> 
                            <td><label>".$empresa."</label></td> 
                        </tr> 
                        <tr> 
                            <td><label>Serial: ".$serial."</label></td> 
                        </tr> 
                        <tr> 
                            <td><label>Activo permitido</label></td> 
                        </tr> 
                    </table>";
                        //REPRODUCE SONIDO DE ACEPTADO
                       $tabla.="<script> 
                       var audio = new Audio('".asset('/resources/sound')."/beep.mp3');  
                       audio.play(); 
                       </script>";
                       $sonido = "beep.mp3";
               }else{
                   $hora_actual = date("Y-m-d H:i:s", time()-18830); 
                   if($genera_reg==true){
                       $inserta = DB::table('ohxqc_ingresos_rechazados')->insert([
                        'id_porteria' => $id_ub, 
                        'identificacion' => $cedula,
                        'codigo_activo' => $cod_equipo,
                        'usuario_creacion'  => $usuario,
                        'fecha_creacion' =>  now(), 
                        'usuario_actualizacion' => $usuario, 
                        'fecha_actualizacion' =>  now()
                       ]);
                }
                   $tabla="<table class='' style='width: 100%;  text-align: center;   text-align: center;  font-weight: bold; background-color: #FE3333;border-radius: 10px;border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                    </tr> 
                   <tr> 
                       <td><label>".$nombre_v." ".$apellido_v." </label></td> 
                   </tr> 
                   <tr>  
                       <td><label>".$identificacion."</label></td> 
                   </tr> 
                   <tr> 
                       <td><label>".$empresa."</label></td> 
                   </tr> 
                   <tr> 
                       <td><label>Activo No permitido</label></td> 
                   </tr> 
                   </table>";
                   //REPRODUCE SONIDO DE NEGACION
                       $tabla.="<script> 
                       var audio = new Audio('".asset('/resources/sound')."/negado.mp3');  
                       audio.play(); 
                       </script>";
                       $sonido = "negado.mp3";
               }
           return $tabla."/".$sonido;	
    }


    public function generaRegistro($usr)
    {
        $generar= true;
        if($usr=='g-segprueba'){
            $generar=false;
        }
        if($usr=='g-segsur3'){
            $generar=false;
        }
        if($usr=='g-segtransp3'){
            $generar=false;
        }
        if($usr=='g-segrecepcion3'){
            $generar=false;
        }
        return $generar;
    }

    public function getIdUbicacionDis($portero)
    {
        $id=0;
        $sel = DB::table('ohxqc_porteros as p')
        ->select('pu.id_ubicacion')
        ->join('ohxqc_porteros_ubicaciones as pu', 'pu.id_portero', '=', 'p.id')
        ->where('p.activo', '=', 'S')
        ->where('p.usuario', '=', $portero)
        ->get();
        if(count($sel) > 0){
            foreach($sel as $s){
                $id = $s->id_ubicacion;
            }

        }

        return $id;
    }

    public function consultaVisitante($usuario,$mac,$cedula,$opcion,$tipo_ingreso)
    {
	
        $genera_reg= IngresoController::generaRegistro($usuario); //Valida si genera registro como portero

     

        $hora_actual = date("Y-m-d H:i:s", time()-18830);	
        
        if($cedula !=""){
            
            $c=substr($cedula,0,1);
            $id_vis = "";
            
            if($c=='R' || $c=='V' || $c=='C'){
                $sql = DB::table('ohxqc_codigobidimensional')
                ->select('id_visitante')
                ->where('codigo', '=', $cedula)
                ->where('activo', '=', 'S')
                ->get();

                
            }else{
                $sql = DB::table('ohxqc_visitantes')
                ->select('id_visitante')
                ->where('identificacion', '=', $cedula)
                ->get();
            }

            foreach($sql as $sq){
                $id_vis = $sq->id_visitante;
            }
          

            if($id_vis!=''){
               
                $query = DB::table('ohxqc_visitantes as v')
                ->select('v.nombre', 'v.apellido', 'v.identificacion', 'emp.descripcion as empresa', 'foto', 'vt.nombre as tipo_v')
                ->join('ohxqc_empresas_visitante as emv', 'emv.id_visitante', '=', 'v.id_visitante')
                ->join('ohxqc_empresas as emp', 'emp.id_empresa', '=', 'emv.id_empresa')
                ->join('ohxqc_tipos_visitante as vt', 'vt.id_tipo_visitante', '=', 'v.tipo_visitante')
                ->where('v.id_visitante', '=', $id_vis)
                ->where('v.activo', '=', 'S')
                ->where('v.fecha_fin', '>=', now())
                ->limit(1)
                ->get();
               

            }else{
                $query = DB::table('ohxqc_visitantes as v')
                ->select('v.nombre', 'v.apellido', 'v.identificacion', 'emp.descripcion as empresa', 'foto', 'vt.nombre as tipo_v')
                ->join('ohxqc_empresas_visitante as emv', 'emv.id_visitante', '=', 'v.id_visitante')
                ->join('ohxqc_empresas as emp', 'emp.id_empresa', '=', 'emv.id_empresa')
                ->join('ohxqc_tipos_visitante as vt', 'vt.id_tipo_visitante', '=', 'v.tipo_visitante')
                ->where('v.identificacion', '=', $cedula)
                ->where('v.activo', '=', 'S')
                ->where('v.fecha_fin', '>=',  now())
                ->limit(1)
                ->get();	
            }
    
            
            $row = array();
            foreach($query as $q){
                $row[0] = $q->nombre;
                $row[1] = $q->apellido;
                $row[2] = $q->identificacion;
                $row[3] = $q->empresa;
                $row[4] = $q->foto;
                $row[5] = $q->tipo_v;
            }
            

            $permiso = IngresoController::validaPermisos($mac,$cedula);
            $id_permiso = IngresoController::getIdPermiso($mac,$cedula);
            $id_ubi_dis = IngresoController::getIdUbicacionDis($mac);
            $parqueo = IngresoController::consultaParqueo($cedula);
            $id_sede = IngresoController::getSedePortero($usuario);
            $descuento_park = IngresoController::evaluaEmpresa($cedula); //identifica si se le debe descontar parqueadero.
            $contador = ""; //Contador de parqueaderos
            
            

            if(count($query) > 0){


                $cedula= trim($row[2]);
             //Identifica el Vehiculo de entrada
            //Nota: todos los porteros deben tener al final una c para Carro y una M para Moto, de lo contrario son peatonales.		
            //CASO CUANDO NO TIENE FOTO
            
            if(trim($row[0])!='' && $row[4]=='N'){
                
                //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
                
              

                
                if($permiso){
                   
                    if($parqueo == 1){
                        
                        //Descuento de parqueadero fijo
                        if($descuento_park){
                            $contador= IngresoController::contadorPark($tipo_ingreso,$opcion,$cedula,'FIJO',$id_sede,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }else{
                            IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }
                        
                        //NARANJA
                        $tabla="<table class='' style='width: 100%; background-color: #FFBF00;
                        border-radius: 10px;   text-align: center;   text-align: center;  font-weight: bold;
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr>	 
                        <td> 
                        <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/person.png')."'WIDTH='60%' > 
                        </td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[0]." ".$row[1]." </label></td> 
                        </tr> 
                        <tr>  
                        <td><label>".$row[2]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[3]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[5]."</label></td> 
                        </tr>";
                        if(trim($contador != '')){
                        $tabla.="<tr> 
                        <td><label>".$contador."</label></td> 
                        </tr>";
                        } 
                        $tabla.="</table>";
                        
                    }else{
                        
                        //Descuento de parqueadero TEMPORAL
                        if($descuento_park){
                            $contador= IngresoController::contadorPark($tipo_ingreso,$opcion,$cedula,'TEMPORAL',$id_sede,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }else{
                            IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }
                        
                        $tabla="<table class='' style='width: 100%; background-color: #00FF1A;
                        border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold;
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr>	 
                        <td> 
                        <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/person.png')."'WIDTH='60%' > 
                        </td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[0]." ".$row[1]." </label></td> 
                        </tr> 
                        <tr>  
                        <td><label>".$row[2]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[3]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[5]."</label></td> 
                        </tr>";
                        if(trim($contador != '')){
                            $tabla.="<tr> 
                            <td><label>".$contador."</label></td> 
                            </tr>";
                        } 
                        $tabla.="</table>";
                    }
    
                    //REPRODUCE SONIDO DE ACEPTADO
                    if(strpos($contador, 'FULL') !== false){
                        //Parqueadero full
                        $tabla.="<script> 
                        var audio = new Audio('".asset('/resources/sound')."/alarma.mp3');  
                        audio.play(); 
                        </script>";
                        $sonido = "alarma.mp3";
                    }else{
                        $tabla.="<script> 
                        var audio = new Audio('".asset('/resources/sound')."/beep.mp3');  
                        audio.play(); 
                        </script>";
                        $sonido = "beep.mp3";
                    }
    
                    if($opcion == "ENTRADA" && ($genera_reg==true)){
                        //INSERTA INGRESO O SALIDA
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'INGRESO',
                            'estado' => 'APROBADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' =>  now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' =>  now(),
                            'id_visitante' => $id_vis
                        ]);
    
                    }elseif($opcion == "SALIDA" && ($genera_reg==true)){
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'SALIDA',
                            'estado' => 'APROBADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' => now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' =>  now(),
                            'id_visitante' => $id_vis
                        ]);
                    }
                }else{ //SI NO TIENE PERMISOS PINTA LA TABLA DE ROJO
                    

        
                    $tabla="<table class='' style='width: 100%; background-color: #FE3333;
                    border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold;
                    border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                    <tr>	 
                    <td> 
                    <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/person.png')."'WIDTH='60%'  > 
                    </td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[0]." ".$row[1]." </label></td> 
                    </tr> 
                    <tr>  
                    <td><label>".$row[2]."</label></td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[3]."</label></td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[5]."</label></td> 
                    </tr> 
                    </table>";
                    //REPRODUCE SONIDO DE NEGACION
                    $tabla.="<script> 
                    var audio = new Audio('".asset('/resources/sound')."negado.mp3');  
                    audio.play(); 
                    </script>";
                    $sonido = "negado.mp3";
                    
                    //INSERTA CUNDO NO TIENE FOTO Y NO TIENE PERMISOS
                    if($opcion == "ENTRADA" && ($genera_reg==true)){
                        //INSERTA INGRESO O SALIDA
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'INGRESO',
                            'estado' => 'RECHAZADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' => now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' =>  now(),
                            'id_visitante' => $id_vis
                        ]);
                      
    
                    }elseif($opcion == "SALIDA" && ($genera_reg==true)){
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'SALIDA',
                            'estado' => 'RECHAZADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' =>  now(),
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' =>  now(),
                            'id_visitante' => $id_vis
                        ]);
                    }
                    
                    IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'RECHAZADO');
                    
                }
                
                return $tabla;
                
            }elseif (trim($row[0])!='' && $row[4]=='S'){
              
                //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
                if($permiso){
                   
                    
                    if($parqueo == 1){
                        
                        //NARANJA
                        //Descuento de parqueadero fijo
                        if($descuento_park){
                            $contador= IngresoController::contadorPark($tipo_ingreso,$opcion,$cedula,'FIJO',$id_sede,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }else{
                            IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }
    
                        $tabla="<table class='' style='width: 100%; background-color: #FFBF00;
                        border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold; width: 100%;
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr>	 
                        <td> 
                        <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/'.$row[2].'.jpg')."'WIDTH='80%'  > 
                        </td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[0]." ".$row[1]." </label></td> 
                        </tr> 
                        <tr>  
                        <td><label>".$row[2]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[3]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[5]."</label></td> 
                        </tr>"; 
                        if(trim($contador != '')){
                            $tabla.="<tr> 
                            <td><label>".$contador."</label></td> 
                            </tr>";
                        } 
                        $tabla.="</table>";
                    }else{

                        //Descuento de parqueadero temporal
                        if($descuento_park){
                            $contador= IngresoController::contadorPark($tipo_ingreso,$opcion,$cedula,'TEMPORAL',$id_sede,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }else{
                            IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'APROBADO');
                        }
    
                        $tabla="<table class='' style='width: 100%; background-color: #00FF1A;
                        border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold; width: 100%;
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr>	 
                        <td> 
                        <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/'.$row[2].'.jpg')."'WIDTH='80%'  > 
                        </td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[0]." ".$row[1]." </label></td> 
                        </tr> 
                        <tr>  
                        <td><label>".$row[2]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[3]."</label></td> 
                        </tr> 
                        <tr> 
                        <td><label>".$row[5]."</label></td> 
                        </tr>";
                        if(trim($contador != '')){
                            $tabla.="<tr> 
                            <td><label>".$contador."</label></td> 
                            </tr>";
                        } 
                        $tabla.="</table>";
                    }
                    //REPRODUCE SONIDO DE ACEPTADO
                    if(strpos($contador, 'FULL') !== false){
                        //Parqueadero full
                        $tabla.="<script> 
                        var audio = new Audio('".asset('/resources/sound')."/alarma.mp3');  
                        audio.play(); 
                        </script>";
                        $sonido = "alarma.mp3";
                    }else{
                        $tabla.="<script> 
                        var audio = new Audio('".asset('/resources/sound')."/beep.mp3');  
                        audio.play(); 
                        </script>";
                        $sonido = "beep.mp3";
                    }
                    if($opcion == "ENTRADA" && ($genera_reg==true)){
                        //INSERTA INGRESO O SALIDA

                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'INGRESO',
                            'estado' => 'APROBADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' => now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' => now(),
                            'id_visitante' => $id_vis
                        ]);
                       
                    }elseif($opcion == "SALIDA" && ($genera_reg==true)){
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'SALIDA',
                            'estado' => 'APROBADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' =>  now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' => now(),
                            'id_visitante' => $id_vis
                        ]);
                    }
                }else{
                    
                    //SI NO TIENE PERMISOS PINTA DE ROJO
                    $tabla="<table class='' style='width: 100%; background-color: #FE3333;
                    border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold; width: 100%;
                    border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                    <tr>	 
                    <td> 
                    <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/'.$row[2].'.jpg')."'WIDTH='80%'  >
                    </td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[0]." ".$row[1]." </label></td> 
                    </tr> 
                    <tr>  
                    <td><label>".$row[2]."</label></td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[3]."</label></td> 
                    </tr> 
                    <tr> 
                    <td><label>".$row[5]."</label></td> 
                    </tr> 
                    </table>";
                    //REPRODUCE SONIDO DE NEGACION
                    $tabla.="<script> 
                    var audio = new Audio('".asset('/resources/sound')."/negado.mp3');  
                    audio.play(); 
                    </script>";
                    $sonido = "negado.mp3";
                    
                    //INSERTA CUANDO TIENE FOTO PERO NO TIENE PERMISOS
                    if($opcion == "ENTRADA" && ($genera_reg==true)){
                        //INSERTA INGRESO O SALIDA
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'INGRESO',
                            'estado' => 'RECHAZADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' =>  now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' =>  now(),
                            'id_visitante' => $id_vis
                        ]);
    
                    }elseif($opcion == "SALIDA" && ($genera_reg==true)){
                        $inserta = DB::table('ohxqc_ingresos_salidas')->insert([
                            'id_permiso' => $id_permiso,  
                            'id_dispositivo' => $id_ubi_dis,  
                            'tipo' => 'SALIDA',
                            'estado' => 'RECHAZADO',
                            'usuario_creacion' => $usuario,  
                            'fecha_creacion' => now(),  
                            'usuario_actualizacion' => $usuario, 
                            'fecha_actualizacion' => now(),
                            'id_visitante' => $id_vis
                        ]);
                    }
                    
                    IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$opcion,$usuario,$id_vis,$id_permiso,$id_ubi_dis,'RECHAZADO');
                    
                }
                return $tabla;
             }
            }else{

                
                $tabla= IngresoController::validaInactivo($id_vis);

                
                if(trim($tabla) == ''){
                    /* $tabla=" <div class='alert alert-warning alert-dismissible fade show mt-3' role='alert'>
                    <strong>información!</strong> no se encontraron registros.
                    <button type='button' class='close' data-dismiss='alert' aria-label='close'>
                    <span aria-hidden='true'>&times;</span>
                    </button>
                </div>"; */

                $tabla= "<table class='' style='width: 100%; high=50%; background-color: #FE3333;
                        border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold; width: 100%;
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                        <tr>	 
                        <td> 
                        <br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/person.png')."' WIDTH='50%'  >
                        </td> 
                        </tr> 
                        <tr> 
                        <td><label> No se encontraron registros.</label></td> 
                        </tr> 
                        
                        </table>";
                $tabla.="<script> 
                        var audio = new Audio('".asset('/resources/sound')."/negado.mp3');  
                        audio.play(); 
                        </script>";
                        $sonido = "negado.mp3";

                }
                

                $id_ubi_dis=IngresoController::getIdUbicacionDis($mac);
                if($genera_reg==true){
                    $inserta = DB::table('ohxqc_ingresos_rechazados')->insert([
                        'id_porteria' => $id_ubi_dis, 
                        'identificacion' => $cedula,
                        'codigo_activo' => null,
                        'usuario_creacion' => $usuario,
                        'fecha_creacion' => now(),
                        'usuario_actualizacion' => $usuario,
                        'fecha_actualizacion' => now()
                    ]);
                }

               
                return $tabla;
            }
        }
    }

    public function validaPermisos($portero,$cedula)
    {
        $cumple=false;
        $c=substr($cedula,0,1);
        $id_empresa='';
        $id_vis = "";
    

        if($c=='R' || $c=='V' || $c=='C'){
                    $idVisi = DB::table('ohxqc_codigobidimensional')
                    ->select('id_visitante')
                    ->where('codigo', '=', $cedula)
                    ->where('activo', '=', 'S')
                    ->get();
                    foreach($idVisi as $s){
                        $id_vis = $s->id_visitante;
                    }

                 
                 

                    $idEmpresa = DB::table('ohxqc_codigobidimensional as c')
                    ->select('e.id_empresa')
                    ->join('ohxqc_empresas_visitante as e', 'e.id_visitante', 'c.id_visitante')
                    ->where('c.codigo', '=', $cedula)
                    ->where('c.activo', '=', 'S')
                    ->get();
                    foreach($idEmpresa as $id){
                        $id_empresa = $id->id_empresa;
                    }

                   
             
        }else{$id_vis='';}
      
                $this->id_vis = $id_vis;
                $this->portero = $portero;
                $this->cedula = $cedula;
                

              if($id_vis != ''){
            
                    $sql = DB::table('ohxqc_permisos')
                    ->select('id_ubicacion')
                    ->whereIn('id_empresa_visitante', function($query1){
                        $query1->select('id_empresa_visitante')
                        ->from('ohxqc_empresas_visitante')
                        ->whereIn('id_visitante', function($query2){
                            $query2->select('id_visitante')
                            ->from('ohxqc_visitantes')
                            ->where('id_visitante', '=', $this->id_vis)
                            ->where('activo', '=', 'S')
                            ->where('fecha_fin', '>=', DB::raw('current_date'));
                            
                        })->limit(1);
                    })
                    ->whereIn('id_ubicacion', function($query3){
                        $query3->select('id_ubicacion')
                            ->from('ohxqc_ubicaciones')
                            ->whereIn('id_ubicacion', function($query4){
                                $query4->select('pu.id_ubicacion')
                                ->from('ohxqc_porteros_ubicaciones as pu')
                                ->join('ohxqc_porteros as p', 'p.id', '=', 'pu.id_portero')
                                ->where('p.activo', '=', 'S')
                                ->where('p.usuario', '=', $this->portero);
                            });
                        })
                    ->whereBetween(DB::raw('current_date'), [DB::raw('fecha_inicio'), DB::raw('fecha_fin')])
                    ->get();

                   

                          /*$sql="SELECT id_ubicacion 
                          FROM ohxqc_permisos WHERE id_empresa_visitante=(SELECT id_empresa_visitante FROM ohxqc_empresas_visitante WHERE id_visitante=(SELECT id_visitante FROM ohxqc_visitantes WHERE id_visitante=$id_vis AND ACTIVO='S' AND fecha_fin >= current_date) limit 1) 
                          and id_ubicacion=(SELECT id_ubicacion  
                          FROM ohxqc_ubicaciones  
                          WHERE id_ubicacion=(select pu.id_ubicacion 
                                          from ohxqc_porteros p, ohxqc_porteros_ubicaciones pu 
                                          where p.id=pu.id_portero 
                                          and p.activo='S' 
                                          and p.usuario='$portero'))
                          and current_date between fecha_inicio and fecha_fin";		*/	
              }else{
                  $this->fechaHoy = date('Y-m-d');
                
                        $sql = DB::table('ohxqc_permisos')
                        ->select('id_ubicacion')
                        ->whereIn('id_empresa_visitante', function($query1){
                            $query1->select('id_empresa_visitante')
                            ->from('ohxqc_empresas_visitante')
                            ->whereIn('id_visitante', function($query2){
                                $query2->select('id_visitante')
                                ->from('ohxqc_visitantes')
                                ->where('identificacion', '=', $this->cedula)
                                ->where('activo', '=', 'S')
                                ->where('fecha_fin', '>=', $this->fechaHoy );
                                
                            })->limit(1);
                        })
                        ->whereIn('id_ubicacion', function($query3){
                            $query3->select('id_ubicacion')
                            ->from('ohxqc_ubicaciones')
                            ->whereIn('id_ubicacion', function($query4){
                                $query4->select('pu.id_ubicacion')
                                ->from('ohxqc_porteros_ubicaciones as pu')
                                ->join('ohxqc_porteros as p', 'p.id', '=', 'pu.id_portero')
                                ->where('p.activo', '=', 'S')
                                ->where('p.usuario', '=', $this->portero);
                            });
                        })
                        ->where('fecha_inicio', '<=', $this->fechaHoy )
                        ->where('fecha_fin', '>=', $this->fechaHoy )
                        //->whereBetween(now(), ['fecha_inicio', 'fecha_fin'])
                        ->get();

                     

                      
                        /*$sql="SELECT id_ubicacion 
                        FROM ohxqc_permisos WHERE id_empresa_visitante=(SELECT id_empresa_visitante FROM ohxqc_empresas_visitante WHERE id_visitante=(SELECT id_visitante FROM ohxqc_visitantes WHERE identificacion='".$cedula."' AND ACTIVO='S' AND fecha_fin >= current_date) limit 1) 
                        and id_ubicacion=(SELECT id_ubicacion  
                        FROM ohxqc_ubicaciones  
                        WHERE id_ubicacion=(select pu.id_ubicacion 
                                                  from ohxqc_porteros p, ohxqc_porteros_ubicaciones pu 
                                                  where p.id=pu.id_portero 
                                                  and p.activo='S' 
                                                  and p.usuario='$portero'))
                        and current_date between fecha_inicio and fecha_fin";	*/
                   }
          
                    $row = array();
                    $actual_date = date('Y-m-d');
                    $hora_actual = date("H:i:s", time()-18830); 
                   
                    foreach($sql as $ide){
                        $row[0] = $ide->id_ubicacion;
                    }
                    
                    if(count($row)>0){
                    
                        if($id_vis!=''){
                            $consulta = DB::table('ohxqc_visitantes as v')
                            ->select('hr.hora_inicio', 'hr.hora_fin', 'v.fecha_fin', 'v.fecha_ingreso')
                            ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
                            ->join('ohxqc_permisos as p', 'p.id_empresa_visitante', '=', 'ev.id_empresa_visitante')
                            ->join('ohxqc_horarios as h', 'h.id', '=', 'p.id_horario')
                            ->join('ohxqc_horas as hr', 'hr.id_horario', '=', 'h.id')
                            ->where('v.id_visitante', '=', $id_vis)
                            ->where('h.activo', '=', 'S')
                            ->get();
                            /*
                            $sql="SELECT hr.hora_inicio, hr.hora_fin, v.fecha_fin, v.fecha_ingreso 
                                    FROM	ohxqc_visitantes v, 
                                            ohxqc_empresas_visitante ev, 
                                            ohxqc_permisos p, 
                                            ohxqc_horarios h, 
                                            ohxqc_horas	   hr 
                                    WHERE  
                                            v.id_visitante=ev.id_visitante 
                                            and ev.id_empresa_visitante=p.id_empresa_visitante 
                                            and p.id_horario=h.id 
                                            and h.id=hr.id_horario 
                                            and v.id_visitante=$id_vis and h.activo='S'";*/
                        }else{
                            $consulta = DB::table('ohxqc_visitantes as v')
                            ->select('hr.hora_inicio', 'hr.hora_fin', 'v.fecha_fin', 'v.fecha_ingreso')
                            ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
                            ->join('ohxqc_permisos as p', 'p.id_empresa_visitante', '=', 'ev.id_empresa_visitante')
                            ->join('ohxqc_horarios as h', 'h.id', '=', 'p.id_horario')
                            ->join('ohxqc_horas as hr', 'hr.id_horario', '=', 'h.id')
                            ->where('v.identificacion', '=', $cedula)
                            ->where('h.activo', '=', 'S')
                            ->get();
                            /*
                            $sql="SELECT hr.hora_inicio, hr.hora_fin, v.fecha_fin, v.fecha_ingreso 
                                    FROM	ohxqc_visitantes v, 
                                            ohxqc_empresas_visitante ev, 
                                            ohxqc_permisos p, 
                                            ohxqc_horarios h, 
                                            ohxqc_horas	   hr 
                                    WHERE  
                                            v.id_visitante=ev.id_visitante 
                                            and ev.id_empresa_visitante=p.id_empresa_visitante 
                                            and p.id_horario=h.id 
                                            and h.id=hr.id_horario 
                                            and v.identificacion='$cedula' and h.activo='S'";*/	
                        }
                        
                            $fecha_inicio = "";
                            $fecha_fin = "";
                            $hora_inicio = "";
                            $hora_fin = "";
                            foreach($consulta as $cons){
                                $fecha_inicio = $cons->fecha_ingreso;
                                $fecha_fin = $cons->fecha_fin;
                                $hora_inicio = $cons->hora_inicio;
                                $hora_fin = $cons->hora_fin;
                            }
                            //Valida fecha y hora de entrada y salida
                            if(trim($id_empresa)==''){
                               

                                $sql = DB::table('ohxqc_empresas_visitante')
                                ->join('ohxqc_visitantes', 'ohxqc_visitantes.id_visitante', '=', 'ohxqc_empresas_visitante.id_visitante')
                                ->where('ohxqc_visitantes.identificacion', '=',$cedula)
                                ->select('ohxqc_empresas_visitante.id_empresa')
                                ->limit(1)
                                ->get();
                               
                            foreach($sql as $idem){
                                $id_empresa =  $idem->id_empresa;
                            }
                            }
                            if(($fecha_fin>=$actual_date) && ($fecha_inicio<=$actual_date) && ($id_empresa != '750')){ 
                                $cumple=true;
                            }
                    }
      
      return $cumple;
    }

    //Obtiene el id del permiso
    public function getIdPermiso($portero,$cedula)
    {
        $c=substr($cedula,0,1);
        if($c=='R' || $c=='V' || $c=='C'){
            $consultaId = DB::table('ohxqc_codigobidimensional')
            ->select('id_visitante')
            ->where('codigo', '=', $cedula)
            ->where('activo', '=', 'S')
            ->get();
            
            foreach($consultaId as $id){
                $id_vis = $id->id_visitante;
            }

            if(count($consultaId)==0){
                $id_vis ='';
            }

        }else{
            $id_vis='';
        }

        $this->id_vis = $id_vis;
        $this->portero = $portero;
        $this->cedula = $cedula;

        $id=0;
        if($id_vis!=''){
                $sql = DB::table('ohxqc_permisos')
                ->select('id_permiso')
                ->whereIn('id_empresa_visitante', function($query1){
                    $query1->select('id_empresa_visitante')
                    ->from('ohxqc_empresas_visitante')
                    ->whereIn('id_visitante', function($query2){
                        $query2->select('id_visitante')
                        ->from('ohxqc_visitantes')
                        ->where('id_visitante', '=', $this->id_vis)
                        ->where('activo', '=', 'S')
                        ->where('fecha_fin', '>=', DB::raw('current_date'));
                        
                    });
                })
                ->whereIn('id_ubicacion', function($query3){
                    $query3->select('id_ubicacion')
                        ->from('ohxqc_ubicaciones')
                        ->whereIn('id_ubicacion', function($query4){
                            $query4->select('pu.id_ubicacion')
                            ->from('ohxqc_porteros_ubicaciones as pu')
                            ->join('ohxqc_porteros as p', 'p.id', '=', 'pu.id_portero')
                            ->where('p.activo', '=', 'S')
                            ->where('p.usuario', '=', $this->portero);
                        });
                    })
                ->where('fecha_fin', '>=', DB::raw('current_date'))
                ->get();

                    /* $sql="SELECT id_permiso 
                    FROM ohxqc_permisos WHERE id_empresa_visitante=(SELECT id_empresa_visitante FROM ohxqc_empresas_visitante WHERE id_visitante=(SELECT id_visitante FROM ohxqc_visitantes WHERE id_visitante=$id_vis AND ACTIVO='S' AND fecha_fin >= current_date)) 
                    and id_ubicacion=(SELECT id_ubicacion  
                    FROM ohxqc_ubicaciones  
                    WHERE id_ubicacion= (select pu.id_ubicacion 
                                                    from ohxqc_porteros p, ohxqc_porteros_ubicaciones pu 
                                                    where p.id=pu.id_portero 
                                                    and p.activo='S' 
                                                    and p.usuario='$portero'))
                    and fecha_fin >= current_date";*/
            }else{

               
                $sql = DB::table('ohxqc_permisos')
                ->select('id_permiso')
                ->whereIn('id_empresa_visitante', function($query1){
                    $query1->select('id_empresa_visitante')
                    ->from('ohxqc_empresas_visitante')
                    ->whereIn('id_visitante', function($query2){
                        $query2->select('id_visitante')
                        ->from('ohxqc_visitantes')
                        ->where('identificacion', '=', $this->cedula)
                        ->where('activo', '=', 'S')
                        ->where('fecha_fin', '>=', $this->fechaHoy );
                        
                    });
                })
                ->whereIn('id_ubicacion', function($query3){
                    $query3->select('id_ubicacion')
                        ->from('ohxqc_ubicaciones')
                        ->whereIn('id_ubicacion', function($query4){
                            $query4->select('pu.id_ubicacion')
                            ->from('ohxqc_porteros_ubicaciones as pu')
                            ->join('ohxqc_porteros as p', 'p.id', '=', 'pu.id_portero')
                            ->where('p.activo', '=', 'S')
                            ->where('p.usuario', '=', $this->portero);
                        });
                    })
                ->where('fecha_fin', '>=', $this->fechaHoy )
                ->get();

                
            }

            foreach($sql as $s){
                $id = $s->id_permiso;
            }

        return $id;
    }

 /*   public function getIdUbicacionDis($portero)
    {
        $id=0;
        $sql = DB::table('ohxqc_porteros_ubicaciones as pu')
        ->select('pu.id_ubicacion')
        ->join('ohxqc_porteros as p', 'p.id', '=', 'pu.id_portero')
        ->where('p.activo', '=', 'S')
        ->where('p.usuario', '=', $portero)
        ->get();
         if(count($sql) > 0){
            foreach($sql as $ide){
                $id = $ide->id_ubicacion;
            }
         }
       
        return $id;
    }
*/
    //Consulta parqueadero asignado
  public function consultaParqueo($cedula)
    {
        $fecha_actual= date('Y-m-d');
        $c=substr($cedula,0,1);
        if($c=='R'){
            $sql = DB::table('ohxqc_visitantes as v')
            ->select('v.parqueadero')
            ->join('ohxqc_codigobidimensional as cb', 'cb.id_visitante', '=', 'v.id_visitante')
            ->where('cb.codigo', '=', $cedula)
            ->where('cb.activo', '=', 'S')
            ->get();
        
        }elseif($c=='V' || $c=='C'){
            $sql = DB::table('ohxqc_visitantes as v')
            ->select('v.parqueadero')
            ->join('ohxqc_codigobidimensional as cb', 'cb.id_visitante', '=', 'v.id_visitante')
            ->where('cb.codigo', '=', $cedula)
            ->where('cb.activo', '=', 'S')
            ->where('cb.fecha_creacion', '=', $fecha_actual)
            ->get();
        }else{
            $sql = DB::table('ohxqc_visitantes')
            ->select('parqueadero')
            ->where('identificacion', '=', $cedula)
            ->get();
        }
        $parqueo = "";
        foreach($sql as $s){
            $parqueo = $s->parqueadero;
        }
        if(trim($parqueo)==''){
            $parqueo= 0;
        }
        return $parqueo;
    }

   public function getSedePortero($portero)
   {
       $id = "";
        $sql = DB::table('ohxqc_porteros')->select('id_sede')->where('usuario', '=', $portero)->get();
        foreach($sql as $ide){
            $id = $ide->id_sede;
        }
        return $id;
   }

   public function evaluaEmpresa($cedula)
   {
       $this->cedula = $cedula;
            $retorno= true;
            $sql = DB::table('ohxqc_empresas_parqueaderos')->select('id')->whereIn('id_empresa', function($query){
                $query->select('e.id_empresa')
                ->from('ohxqc_empresas_visitante as e')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'e.id_visitante')
                ->where('v.identificacion', '=', $this->cedula);
            })->get();
          
    
        if(count($sql) == 0){
            $retorno = false;
        }
        return $retorno;		
   }

   public function contadorPark($tipo_ingreso,$tipo,$cedula,$tipo_park,$id_sede,$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado)
   {
        $return="";
        
                if($tipo_ingreso == "CARRO"){
                    //Portero de carros
                    //Descuento de parqueadero fijo
                    $id_empresa_v = "";
                    $idEmpresa = DB::table('ohxqc_empresas_visitante as ev')
                    ->select('ev.id_empresa')
                    ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'ev.id_empresa_visitante')
                    ->where('v.identificacion', '=', $cedula)
                    ->get();
                    foreach($idEmpresa as $idem){
                        $id_empresa_v = $idem->id_empresa;
                    }
                        //Consulta estado del parqueadero
                        $asignados = "";
                        $ocupados = "";
                        if($tipo_park=='FIJO'){
                            $ocupacion = DB::table('ohxqc_empresas_parqueaderos')
                            ->select('asignados_fijos', 'ocupados_fijos')
                            ->where('id_empresa', '=', $id_empresa_v)
                            ->where('tipo', '=', 'CARRO')
                            ->where('id_sede', '=', $id_sede)
                            ->get();
                              foreach($ocupacion as $oc){
                                $asignados = $oc->asignados_fijos;
                                $ocupados = $oc->ocupados_fijos;
                            }        
                        }else{
                            $ocupacion = DB::table('ohxqc_empresas_parqueaderos')
                            ->select('asignados_temporales', 'ocupados_temporales')
                            ->where('id_empresa', '=', $id_empresa_v)
                            ->where('tipo', '=', 'CARRO')
                            ->where('id_sede', '=', $id_sede)
                            ->get();
                              foreach($ocupacion as $oc){
                                $asignados = $oc->asignados_temporales;
                                $ocupados =  $oc->ocupados_temporales;
                            }
                        }
                    
                      
                      
                        if(!($ocupados >= $asignados) && ($ocupados >= 0) && ($asignados != 0)){	
                            if($tipo=="ENTRADA"){
                                $ocupados++;
                                //Descuento de parqueadero fijo
                                if($tipo_park=='FIJO'){
                                    $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                    ->where('id_empresa', '=', $id_empresa_v)
                                    ->where('tipo', '=', 'CARRO')
                                    ->where('id_sede', '=', $id_sede)
                                    ->update([
                                        'ocupados_fijos' => $ocupados
                                    ]);
                                }else{
                                    $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                    ->where('id_empresa', '=', $id_empresa_v)
                                    ->where('tipo', '=', 'CARRO')
                                    ->where('id_sede', '=', $id_sede)
                                    ->update([
                                        'ocupados_temporales' => $ocupados
                                    ]);
                                }
                                IngresoController::insertHistoricoIngreso($cedula,'CARRO','ENTRADA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                                
                            }else{
                                if($ocupados > 0){
                                    $ocupados=$ocupados-1;
                                }
                                //Habilita un parqueadero fijo
                                     $ocupados_actual = "";
                                    if($tipo_park=='FIJO'){
                                        $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                        ->where('id_empresa', '=', $id_empresa_v)
                                        ->where('tipo', '=', 'CARRO')
                                        ->where('id_sede', '=', $id_sede)
                                        ->update([
                                            'ocupados_fijos' => $ocupados
                                        ]);
                                        $oct = DB::table('ohxqc_empresas_parqueaderos')
                                        ->select('ocupados_fijos')
                                        ->where('id_empresa', '=', $id_empresa_v)
                                        ->where('tipo', '=', 'CARRO')
                                        ->where('id_sede', '=', $id_sede)
                                        ->get();
                                        foreach($oct as $ocua){
                                            $ocupados_actual = $ocua->ocupados_fijos;
                                        }

                                    }else{
                                        $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                        ->where('id_empresa', '=', $id_empresa_v)
                                        ->where('tipo', '=', 'CARRO')
                                        ->where('id_sede', '=', $id_sede)
                                        ->update([
                                            'ocupados_temporales' => $ocupados
                                        ]);
                                        $oct = DB::table('ohxqc_empresas_parqueaderos')
                                        ->select('ocupados_temporales')
                                        ->where('id_empresa', '=', $id_empresa_v)
                                        ->where('tipo', '=', 'CARRO')
                                        ->where('id_sede', '=', $id_sede)
                                        ->get();
                                        foreach($oct as $ocua){
                                            $ocupados_actual = $ocua->ocupados_temporales;
                                        }

                                    }
                                    IngresoController::insertHistoricoIngreso($cedula,'CARRO','SALIDA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                                
                            }
                            if($tipo_park == 'FIJO'){
                                $return= "CARROS: ".$ocupados_actual."/".$asignados;
                            }else{
                                $return= "CARROS FLOTANTES: ".$ocupados_actual."/".$asignados;
                            }
                        }else{
                            //Caso cuando el parqueadero esta lleno
                            if($tipo=="ENTRADA"){
                                $return="PARQUEADERO FULL";
                            }else{
                                if($asignados != 0){
                                        if($ocupados > 0){
                                            $ocupados=$ocupados-1;
                                            IngresoController::insertHistoricoIngreso($cedula,'CARRO','SALIDA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                                        }
                                        //Habilita un parqueadero fijo
                                        $ocupados_actual = "";
                                        if($tipo_park=='FIJO'){
                                            $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                            ->where('id_empresa', '=', $id_empresa_v)
                                            ->where('tipo', '=', 'CARRO')
                                            ->where('id_sede', '=', $id_sede)
                                            ->update([
                                                'ocupados_fijos' => $ocupados
                                            ]);
                                            $oct = DB::table('ohxqc_empresas_parqueaderos')
                                            ->select('ocupados_fijos')
                                            ->where('id_empresa', '=', $id_empresa_v)
                                            ->where('tipo', '=', 'CARRO')
                                            ->where('id_sede', '=', $id_sede)
                                            ->get();
                                            foreach($oct as $ocua){
                                                $ocupados_actual = $ocua->ocupados_fijos;
                                            }
                                           
                                        }else{
                                            $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                            ->where('id_empresa', '=', $id_empresa_v)
                                            ->where('tipo', '=', 'CARRO')
                                            ->where('id_sede', '=', $id_sede)
                                            ->update([
                                                'ocupados_temporales' => $ocupados
                                            ]);
                                            $oct = DB::table('ohxqc_empresas_parqueaderos')
                                            ->select('ocupados_temporales')
                                            ->where('id_empresa', '=', $id_empresa_v)
                                            ->where('tipo', '=', 'CARRO')
                                            ->where('id_sede', '=', $id_sede)
                                            ->get();
                                            foreach($oct as $ocua){
                                                $ocupados_actual = $ocua->ocupados_temporales;
                                            }
                                        }
                                      
                                        if($tipo_park == 'FIJO'){
                                            $return= "CARROS: ".$ocupados_actual."/".$asignados;
                                        }else{
                                            $return= "CARROS FLOTANTES: ".$ocupados_actual."/".$asignados;
                                        }	
                                }else{$return="PARQUEADERO FULL";}
                            }
                        }
                    
                }elseif($tipo_ingreso == "MOTO"){
                        //portero de motos
                        //Descuento de parqueadero Temporales porque no existen parqueaderos fijos en motos
                        $sql = DB::table('ohxqc_empresas_visitante as ev')
                        ->select('ev.id_empresa')
                        ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'ev.id_empresa_visitante')
                        ->where('v.identificacion', '=', $cedula)
                        ->get();
                        $id_empresa_v = "";
                        foreach($sql as $idem){
                            $id_empresa_v = $idem->id_empresa;
                        }
                        
                        //Consulta estado del parqueadero
                        $consultEsta = DB::table('ohxqc_empresas_parqueaderos')
                        ->select('asignados_temporales', 'ocupados_temporales')
                        ->where('id_empresa', '=', $id_empresa_v)
                        ->where('tipo', '=', 'MOTO')
                        ->where('id_sede', '=', $id_sede)
                        ->get();
                      
                        $asignados = "";
                        $ocupados = "";
                        foreach($consultEsta as $est){
                            $asignados = $est->asignados_temporales;
                            $ocupados = $est->ocupados_temporales;
                        }
                        $ocupados_actual = "";
                        if(!($ocupados >= $asignados) && ($ocupados >= 0) && ($asignados != 0)){
                            if($tipo=="ENTRADA"){
                                $ocupados++;
                                //Descuento de parqueadero fijo
                                $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                ->where('id_empresa', '=', $id_empresa_v)
                                ->where('tipo', '=', 'MOTO')
                                ->where('id_sede', '=', $id_sede)
                                ->update([
                                    'ocupados_temporales' => $ocupados
                                ]); 
                                $oct = DB::table('ohxqc_empresas_parqueaderos')
                                ->select('ocupados_temporales')
                                ->where('id_empresa', '=', $id_empresa_v)
                                ->where('tipo', '=', 'MOTO')
                                ->where('id_sede', '=', $id_sede)
                                ->get();
                                foreach($oct as $ocua){
                                    $ocupados_actual = $ocua->ocupados_temporales;
                                }
                                IngresoController::insertHistoricoIngreso($cedula,'MOTO','ENTRADA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                            }else{
                                if($ocupados > 0){
                                    $ocupados=$ocupados-1;
                                    IngresoController::insertHistoricoIngreso($cedula,'MOTO','SALIDA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                                }
                                //Habilita un parqueadero fijo
                                $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                ->where('id_empresa', '=', $id_empresa_v)
                                ->where('tipo', '=', 'MOTO')
                                ->where('id_sede', '=', $id_sede)
                                ->update([
                                    'ocupados_temporales' => $ocupados
                                ]); 
                                $oct = DB::table('ohxqc_empresas_parqueaderos')
                                ->select('ocupados_temporales')
                                ->where('id_empresa', '=', $id_empresa_v)
                                ->where('tipo', '=', 'MOTO')
                                ->where('id_sede', '=', $id_sede)
                                ->get();
                                foreach($oct as $ocua){
                                    $ocupados_actual = $ocua->ocupados_temporales;
                                }
                            }
                           
                            $return= "MOTOS: ".$ocupados_actual."/".$asignados;
                        }else{
                            if($asignados != 0){
                                    //Caso cuando el parqueadero esta lleno
                                if($tipo=="ENTRADA"){
                                    $return="PARQUEADERO FULL";
                                }else{
                                    if($ocupados > 0){
                                        $ocupados=$ocupados-1;
                                        IngresoController::insertHistoricoIngreso($cedula,'MOTO','SALIDA',$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                                    }
                                    //Habilita un parqueadero fijo
                                    $ocupados_actual = "";
                                    $actualiza = DB::table('ohxqc_empresas_parqueaderos')
                                    ->where('id_empresa', '=', $id_empresa_v)
                                    ->where('tipo', '=', 'MOTO')
                                    ->where('id_sede', '=', $id_sede)
                                    ->update([
                                        'ocupados_temporales' => $ocupados
                                    ]); 
                                    $oct = DB::table('ohxqc_empresas_parqueaderos')
                                    ->select('ocupados_temporales')
                                    ->where('id_empresa', '=', $id_empresa_v)
                                    ->where('tipo', '=', 'MOTO')
                                    ->where('id_sede', '=', $id_sede)
                                    ->get();
                                    foreach($oct as $ocua){
                                        $ocupados_actual = $ocua->ocupados_temporales;
                                    }
                                    $return= "MOTOS: ".$ocupados_actual."/".$asignados;
                                    }
                            }
                        }
                }else{
                
                    IngresoController::insertHistoricoIngreso($cedula,$tipo_ingreso,$tipo,$portero,$id_vis,$id_permiso,$id_ubi_dis,$estado);
                }
        return $return;
    }

    public function insertHistoricoIngreso($cedula,$tipo_vehiculo,$tipo_registro,$usr_creacion,$id_visitante,$id_permiso,$id_dispositivo,$estado)
    {
        $ini_cod_vis=substr($cedula,0,1); //iniciales del codigo de visitante
        if($ini_cod_vis=='R' || $ini_cod_vis=='V' || $ini_cod_vis=='C'){
            $sql = DB::table('ohxqc_empresas_visitante as e')
            ->select('e.id_empresa', 'tipo_visitante')
            ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'e.id_visitante')
            ->join('ohxqc_codigobidimensional as c', 'c.id_visitante', '=', 'v.id_visitante')
            ->where('c.codigo', '=', $cedula)
            ->get();
        }else{
            $sql = DB::table('ohxqc_empresas_visitante as e')
            ->select('e.id_empresa', 'tipo_visitante')
            ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'e.id_visitante')
            ->where('v.identificacion', '=', $cedula)
            ->get();
        }
      
   
        $id_empresa = "";
        $id_tipo_visitante= "";

        foreach($sql as $sq){
            $id_empresa = $sq->id_empresa;;
            $id_tipo_visitante =  $sq->tipo_visitante;
        }
  
        //$sql="INSERT INTO ohxqc_historico_ingresos(
        $inserta = DB::table('ohxqc_trx_ingresos_salidas')->insert([
            'id' => DB::table('ohxqc_trx_ingresos_salidas')->max('id')+1,
            'id_visitante' => $id_visitante,
            'tipo_vehiculo' => $tipo_vehiculo,
            'tipo_registro' => $tipo_registro,
            'usuario_creacion' => $usr_creacion,
            'fecha_registro' => now(),
            'fecha_hora' =>  now(),
            'id_empresa' => $id_empresa,
            'id_tipo_visitante' => $id_tipo_visitante,
            'id_permiso' => $id_permiso,
            'id_dispositivo' => $id_dispositivo,
            'estado' => $estado
        ]);
    }

    public function validaInactivo($id_visitante)
    {
		$tabla='';
		if(trim($id_visitante) != ''){
			$sql = DB::table('ohxqc_visitantes as v')
            ->select('v.nombre', 'v.apellido', 'v.identificacion', 'emp.descripcion as empresa', 'foto', 'vt.nombre as tipo_v')
            ->join('ohxqc_empresas_visitante as emv', 'emv.id_visitante', '=', 'v.id_visitante')
            ->join('ohxqc_empresas as emp', 'emp.id_empresa', '=', 'emv.id_empresa')
            ->join('ohxqc_tipos_visitante as vt', 'vt.id_tipo_visitante', '=', 'v.tipo_visitante')
            ->where('v.id_visitante', '=', $id_visitante )
            ->where( function($q) {
                $q->Where('v.activo', '=', 'N' )
                ->orWhere('v.fecha_fin', '<',now());
            } )->get();

             
			/*$sql="SELECT v.Nombre,
						v.apellido,
						v.identificacion, 
						(select descripcion from ohxqc_empresas where id_empresa=(select id_empresa from ohxqc_empresas_visitante where id_visitante=v.id_visitante) limit 1) empresa, 
						foto, 
						vt.nombre as tipo_v 
					FROM ohxqc_visitantes v, ohxqc_tipos_visitante vt 
					WHERE v.id_visitante=$id_visitante 
					AND (v.activo='N' or v.fecha_fin < now())
					and v.tipo_visitante=vt.id_tipo_visitante";*/

            $row = array();
            foreach($sql as $r){
                $row[0] = $r->nombre;
                $row[1] = $r->apellido;
                $row[2] = $r->identificacion;
                $row[3] = $r->empresa;
                $row[4] = $r->foto;
                $row[5] = $r->tipo_v;
            }
			
			if(trim($row[2]) != ''){
				$tabla="<table class='' style='width: 100%; background-color: #FE3333;
						    border-radius: 10px;  text-align: center;   text-align: center;  font-weight: bold; width: 100%;
						    border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
						<tr><td>";
						
				if($row[4]=='S'){
					$tabla.="<br><img  class='img-thumbnail' src='".asset('../storage/app/public/fotos/'.$row[2].'.jpg')."'WIDTH='80%' >";
				}else{
					$tabla.="<br><img class='img-thumbnail' src='".asset('../storage/app/public/fotos/person.png')."'WIDTH='60%' > ";
				}	
				
				$tabla.="</td> 
				</tr> 
				<tr> 
					<td><label>".$row[0]." ".$row[1]." </label></td> 
				</tr> 
				<tr>  
					<td><label>".$row[2]."</label></td> 
				</tr> 
				<tr> 
					<td><label>".$row[3]."</label></td> 
				</tr> 
				<tr> 
					<td><label>DESACTIVO</label></td> 
				</tr> 
				</table>";
				
				//REPRODUCE SONIDO DE NEGACION
				$tabla.="<script> 
				var audio = new Audio('".asset('/resources/sound')."/negado.mp3');  
				audio.play(); 
				</script>";

                $sonido = "negado.mp3";
			}
		}
		return $tabla;
	}

    public function getCedula($cedula)
    {
        $dtCedula = "";
        if($cedula != ""){
             $dtCedula=$cedula;
        }
        return $dtCedula;
    }
    
        

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\visitante  $visitante
     * @return \Illuminate\Http\Response
     */
    public function show(visitante $visitante)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\visitante  $visitante
     * @return \Illuminate\Http\Response
     */
    public function edit(visitante $visitante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\visitante  $visitante
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, visitante $visitante)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\visitante  $visitante
     * @return \Illuminate\Http\Response
     */
    public function destroy(visitante $visitante)
    {
        //
    }
}
