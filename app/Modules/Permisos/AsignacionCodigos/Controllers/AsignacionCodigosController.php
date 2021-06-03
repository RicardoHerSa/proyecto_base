<?php

namespace App\Modules\Permisos\AsignacionCodigos\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AsignacionCodigosController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Permisos::asignacionCodigos');
    }

    public function consultaVisitante(Request $request)
    {
        $cedulVi = $request->input('tx_cedula');
        //echo $this->cedula;
        $hora_actual = date("Y-m-d H:i:s", time()-18840); 
        $tabla="";
        
         //obtiene info del visitante
         $info = DB::table('ohxqc_visitantes as v')
         ->select('v.nombre', 'v.apellido', 'v.identificacion', 'e.descripcion', 'v.foto')
         ->join('ohxqc_empresas_visitante as emp', 'emp.id_visitante', '=', 'v.id_visitante')
         ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'emp.id_empresa')
         ->where('v.identificacion', '=', $cedulVi)
         ->where('v.activo', '=', 'S')
         ->get();
         
         if(count($info)>0){
           $existeVisitante = true;
             $row = array();
             foreach($info as $i){
               $row[0] = $i->nombre;
               $row[1] = $i->apellido;
               $row[2] = $i->identificacion;
               $row[3] = $i->descripcion;
               $row[4] = $i->foto;
             }
   
             if($row[4]=='N'){
                $tabla = 1;
               //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
              /* $tabla="<table class='table' style='background-color: #00FF1A;
                   border-radius: 10px; 
                   border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
               <tr>	 
                   <td> 
                    <img src=".$rutaFoto."height='130' width='190'> 
                    </td> 
                    <td>
                    <table>
                       <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                       <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                       <tr> <td><label>".$row[3]."</label></td></tr> 
                       </table>
                    </td> 
               </tr> 
               </table>";*/
                           
                       
           }else if($row[4]=='S'){
               
               //pinta tabla con foto
                $tabla = 1;
               /*$tabla="<table class='table' style='background-color: #00FF1A;
                   border-radius: 10px; 
                   border-left:0px; font-size:20px;font-family:'Lato', 'sans-serif'> 
               <tr>	 
                   <td> 
                        <img src='".$rutaFoto."' height='200' width='245'> 
                   </td> 
                   <td>
                       <table>
                           <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                           <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                           <tr> <td><label>".$row[3]."</label></td></tr> 
                       </table>
                   </td> 
               </tr> 
               </table>";*/
           }else{
               $tabla="0";
           }
   
               //Consulta todos los códigos del visitante
           $codigos = DB::table('ohxqc_codigobidimensional as cdb')
           ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
           ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
           ->where('v.identificacion', '=', $cedulVi)
           ->get();
           if(count($codigos) > 0){
              // echo "<br><br>";var_dump($codigos);
               $datosV=array();
               foreach($codigos as $code){
                   $datosV[]=array('cod_vis'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
               }
               $dataV = json_encode($datosV);
               //echo "<br><br>".$dataV;
           }else{
                $dataV = "";
           }
         
           //Consulta todos los activos del visitante
           $activos = DB::table('ohxqc_codigobidimensional as cdb')
           ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
           ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
           ->where('v.identificacion', '=', $cedulVi)
           ->get();
           if(count($activos) > 0){
              // echo "<br><br>";var_dump($activos);
               $datosV=array();
               foreach($activos as $code){
                   $datosV[]=array('cod_act'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
               }
               $dataA = json_encode($datosV);
              // echo "<br><br>".$dataA;
           }else{
            $datosV = "";
            $dataA = "";
           }
           return view('Permisos::asignacionCodigos', compact('tabla', 'dataV', 'dataA', 'cedulVi', 'row'));
        }else{
            return redirect('asigna-codigos')->with('mensaje', 'No se encontraron registros.');
        }
      
     
      
    }

    public function consultarClickTabla(Request $request)
    {
        //Funcion cundo se da click a un registro de la tabla
        $retorno=0;
        $codigo = $request->input('cod');
        $this->cedula = $request->input('cc');
        $fecha = $request->input('fecha');
        $act = $request->input('act');

        if($act == 1){
            $activo= 'S';
        }else{$activo= 'N';}
        
        $consulta = DB::table('ohxqc_codigobidimensional as cb')
        ->select('e.serial', 'e.descripcion', 'e.modelo', 'cb.codigo', 'cb.activo')
        ->join('ohxqc_equipos as e', 'e.id_equipo', '=', 'cb.id_equipo')
        ->whereIn('cb.id_visitante', function($query){
            $query->select('id_visitante')
            ->from('ohxqc_visitantes')
            ->where('identificacion', '=', $this->cedula);
        })
        ->where('cb.codigo', '=', $codigo)
        ->where('cb.tipo', '=', 'ACTIVO')
        ->get();
        if(count($consulta) > 0){
            $row = array();
            foreach($consulta as $cons){
                $row[0] = $cons->serial;
                $row[1] = $cons->descripcion;
                $row[2] = $cons->modelo;
                $row[3] = $cons->codigo;
                $row[4] = $cons->activo;
            }
            $retorno = $row[0]."|".$row[1]."|".$row[2]."|".$row[3]."|".$row[4];
    
            if(trim($row[0])=='' && trim($row[1])=='' && trim($row[2])==''){
                $consultaNula = DB::table('ohxqc_codigobidimensional as cb')
                ->select('null as serial', 'null as descripcion', 'null as modelo', 'cb.codigo', 'cb.activo', ' v.parqueadero')
                ->join('ohxqc_visitantes as v')
                ->where('cb.id_visitante', '=', 'v.id_visitante')
                ->where('v.identificacion', '=', $cedula)
                ->where('cb.tipo', '=', 'VISITANTE')
                ->where('cb.fecha_creacion', '=', $fecha)
                ->where('cb.activo', '=', $activo)
                ->get();
    
                $row2 = array();
                foreach($consulta as $cons){
                    $row2[0] = $cons->serial;
                    $row2[1] = $cons->descripcion;
                    $row2[2] = $cons->modelo;
                    $row2[3] = $cons->codigo;
                    $row2[4] = $cons->activo;
                    $row2[5] = $cons->parqueadero;
                }
            $retorno=$row2[0]."|".$row2[1]."|".$row2[2]."|".$row2[3]."|".$row2[4]."|".$row2[5];
            }
        }
        
        echo $retorno;
        
           
    }

    public function registrarCodigo(Request $request)
    {
        $cedula = $request->input('cedula');
        $cedulVi = $cedula;
        $codigo = $request->input("codigo"); 
        $activo = $request->input("activo");
        $articulo = $request->input("articulo");
        $modelo = $request->input("modelo");
        $serial = $request->input("serial");
        $username = substr($request->input("username"), 0,25);
        $full_parqueo = $request->input("full_parqueo");

        //echo "CEDULA: ".$cedula."<br>CODIGO: ".$codigo."<br>ACTIVO: ".$activo."<br>ARTICULO: ".$articulo."<BR>MODELO:  ".$modelo."<BR>SERIAL: ".$serial."<BR>USERNAME: ".$username."<BR>PRQUEO: ".$full_parqueo;

        $operacion = false;

        $idUsuario = DB::table('ohxqc_visitantes')
        ->select('id_visitante')
        ->where('identificacion', '=', $cedula)
        ->get();
        //var_dump($idUsuario);

        if(count($idUsuario) > 0){
            foreach($idUsuario as $id){
                $id_usuario = $id->id_visitante;
            }
        }

        //Actualiza flag parqueadero
        if($id_usuario != ''){
            if($full_parqueo == "1"){
                $actualiza = DB::table('ohxqc_visitantes')
                ->where('id_visitante', '=', $id_usuario )
                ->update([
                    'parqueadero' => 1,
                    'usuario_actualizacion' => $username,
                    'fecha_actualizacion' => now()
                ]);
                //echo "<BR>RESULTADO DE ACTULIZAR PARQUEO en 1: ";
                //var_dump($actualiza);
            }else{
                $actualiza = DB::table('ohxqc_visitantes')
                ->where('id_visitante', '=', $id_usuario )
                ->update([
                    'parqueadero' => 0,
                    'usuario_actualizacion' => $username,
                    'fecha_actualizacion' => now()
                ]);
                //echo "<BR>RESULTADO DE ACTULIZAR PARQUEO en 0: ";
                //var_dump($actualiza);
            }
        }
        
        $indicador=substr($codigo,0,1);
       //echo "<br>INDICADOR: ".$indicador;
        
        if($indicador=='R'){ //REGISTRO R.H
            $consulta = DB::table('ohxqc_codigobidimensional')
            ->select('id')
            ->where('codigo', '=', $codigo)
            ->get();
            $id = "";
            foreach($consulta as $c){
                $id = $c->id;
            }
                
            if(trim($id) == ''){ //CUANDO EL CODIGO ES NUEVO
                $inserta = DB::table('ohxqc_codigobidimensional')->insert([
                    'id_equipo' => NULL,
                    'id_visitante' => $id_usuario,
                    'codigo' => $codigo,
                    'tipo' => 'VISITANTE',
                    'activo' => 'S',
                    'fecha_creacion' => now(),
                    'usuario_creacion' => 'admin',
                    'fecha_actualizacion' => now(),
                    'usuario_actualizacion' => 'admin'
                ]);
                $operacion = true;
                
            }else{ //CUANDO ES ACTUALIZACIÓN DEL CODIGO
                if($activo == "1"){
                            $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                'activo' => 'S'
                            ]);
                            if($actualiza){
                                $operacion = true;
                            }
                        }else{
                            $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                'activo' => 'N'
                            ]);
                            if($actualiza){
                                $operacion = true;
                            }
                        }
                }		
        }elseif($indicador=='A'){//CUANDO REGISTRA UN ACTIVO
            $consultaIdE = DB::table('ohxqc_equipos')
            ->select('id_equipo')
            ->where('serial', '=', $serial)
            ->get();
            $id_equipo = "";
            foreach($consultaIdE as $c){
                $id_equipo = $c->id_equipo;
            }

            //echo "<br>ID_EQUIPO: ".$id_equipo;

            $consultaIdC = DB::table('ohxqc_codigobidimensional')
            ->select('id')
            ->where('codigo', '=', $codigo)
            ->get();
            $id = "";
            foreach($consultaIdC as $c){
                $id = $c->id;
            }

            //echo "<br>ID_coodigo: ".$id;
            //echo "<br> ARTICULO: ".$articulo." MODELO: ".$modelo." ID_EQUIPO: ".$id_equipo;
			if($articulo!='' && $modelo!='' && $id_equipo ==''){
               // echo "<br> TRIM ID_coodigo: ".trim($id);
                    if(trim($id) == ''){ //CUANDO EL CODIGO ES NUEVO
                        //INSERTA EL EQUIPO CON SU RELACION
                        $insertaEquipos = DB::table('ohxqc_equipos')->insert([
                            'serial' => $serial,
                            'descripcion' => $articulo,
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacion' => now(),
                            'modelo' => $modelo,
                        ]);
                        
                        //Despues de insertar, se debe retornar el id_equipo con el que quedó
                        
                        $idEqu = DB::table('ohxqc_equipos')->max('id_equipo');
                        $id_equipo = "";
                        foreach($idEqu as $ide){
                            $id_equipo = $ide->id_equipo;
                        }

                        $insertaEquiposVisitante = DB::table('ohxqc_equipos_visitante')->insert([
                            'id_visitante' => $id_usuario,
                            'id_equipo' => $id_equipo,
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacio' =>  now()
                        ]);

                        $insertaCodeBidi = DB::table('ohxqc_codigobidimensional')->insert([
                            'id_equipo' => $id_equipo,
                            'id_visitante' => $id_usuario,
                            'codigo' => $codigo,
                            'tipo' => 'ACTIVO',
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacio' =>  now()
                        ]);
                        $operacion = true;
	
                    }else{
						//ACTUALIZA EL CODIGO
                            if($activo == "1"){
                                $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                    'activo' => 'S'
                                ]);
                                if($actualiza){
                                    $operacion = true;
                                }   
                            
                            }else{
                                $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                    'activo' => 'N'
                                ]);
                                if($actualiza){
                                    $operacion = true;
                                }   
                            }
					    }
                }elseif ($articulo!='' && $modelo!='' && $id_equipo !=''){ //UN EQUIPO PERTENECE A MAS DE UN COLABORADOR
                    
                    if(trim($id) == ''){ //CUANDO EL CODIGO ES NUEVO
                        $insertaEquiposVisitante = DB::table('ohxqc_equipos_visitante')->insert([
                            'id_visitante' => $id_usuario,
                            'id_equipo' => $id_equipo,
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacio' =>  now()
                        ]);
        
                        $insertaCodeBidi = DB::table('ohxqc_codigobidimensional')->insert([
                            'id_equipo' => $id_equipo,
                            'id_visitante' => $id_usuario,
                            'codigo' => $codigo,
                            'tipo' => 'ACTIVO',
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacio' =>  now()
                        ]);
                        $operacion = true;
                    }else{
                        //ACTUALIZA EL CODIGO
                        if($activo == "1"){
                            $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                'activo' => 'S'
                            ]);
                            if($actualiza){
                                $operacion = true;
                            }
                        
                        }else{
                            $actualiza = DB::table('ohxqc_codigobidimensional')->where('id', '=', $id)->update([
                                'activo' => 'N'
                            ]);
                            if($actualiza){
                                $operacion = true;
                            }
                            
                        }
                    }
                        
                }
		
					
		}

        //Validar la operacion 
            //obtiene info del visitante
      $info = DB::table('ohxqc_visitantes as v')
      ->select('v.nombre', 'v.apellido', 'v.identificacion', 'e.descripcion', 'v.foto')
      ->join('ohxqc_empresas_visitante as emp', 'emp.id_visitante', '=', 'v.id_visitante')
      ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'emp.id_empresa')
      ->where('v.identificacion', '=', $cedula)
      ->where('v.activo', '=', 'S')
      ->get();
      
      if(count($info)>0){
        $existeVisitante = true;
          $row = array();
          foreach($info as $i){
            $row[0] = $i->nombre;
            $row[1] = $i->apellido;
            $row[2] = $i->identificacion;
            $row[3] = $i->descripcion;
            $row[4] = $i->foto;
          }
         // var_dump($info);

          if($row[4]=='N'){

            //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
            $tabla="<table class='table' style='background-color: #00FF1A;
                border-radius: 10px; 
                border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
            <tr>	 
                <td> 
                 <img src='http://172.19.92.223/ingresocarvajal/images/person.png' height='130' width='190'> 
                 </td> 
                 <td>
                 <table>
                    <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                    <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                    <tr> <td><label>".$row[3]."</label></td></tr> 
                    </table>
                 </td> 
            </tr> 
            </table>";
                        
                    
        }else if($row[4]=='S'){
            //pinta tabla con foto
            $tabla="<table class='table' style='background-color: #00FF1A;
                border-radius: 10px; 
                border-left:0px; font-size:20px;font-family:'Lato', 'sans-serif'> 
            <tr>	 
                <td> 
                     <img src='http://172.19.92.223/ingresocarvajal/modules/mod_visitantes/fotos/".$row[2].".jpg' height='200' width='245'> 
                </td> 
                <td>
                    <table>
                        <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                        <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                        <tr> <td><label>".$row[3]."</label></td></tr> 
                    </table>
                </td> 
            </tr> 
            </table>";
        }else{
            $tabla="0";
        }

            //Consulta todos los códigos del visitante
        $codigos = DB::table('ohxqc_codigobidimensional as cdb')
        ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
        ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
        ->where('v.identificacion', '=', $cedula)
        ->get();
        if(count($codigos) > 0){
           // echo "<br><br>";var_dump($codigos);
            $datosV=array();
            foreach($codigos as $code){
                $datosV[]=array('cod_vis'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
            }
            $dataV = json_encode($datosV);
            //echo "<br><br>".$dataV;
        }else{

        }
      
        //Consulta todos los activos del visitante
        $activos = DB::table('ohxqc_codigobidimensional as cdb')
        ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
        ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
        ->where('v.identificacion', '=', $cedula)
        ->get();
        if(count($activos) > 0){
           // echo "<br><br>";var_dump($activos);
            $datosV=array();
            foreach($activos as $code){
                $datosV[]=array('cod_act'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
            }
            $dataA = json_encode($datosV);
           // echo "<br><br>".$dataA;
        }else{

        }
      
      }
        return view('Permisos::asignacionCodigos', compact('tabla', 'dataV', 'dataA', 'cedulVi','operacion', 'row'));
        
   
    }

    public function consultaInfoAsignacionCodigos($cedula){
        //obtiene info del visitante
      $info = DB::table('ohxqc_visitantes as v')
      ->select('v.nombre', 'v.apellido', 'v.identificacion', 'e.descripcion', 'v.foto')
      ->join('ohxqc_empresas_visitante as emp', 'emp.id_visitante', '=', 'v.id_visitante')
      ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'emp.id_empresa')
      ->where('v.identificacion', '=', $cedula)
      ->where('v.activo', '=', 'S')
      ->get();
      
      if(count($info)>0){
        $existeVisitante = true;
          $row = array();
          foreach($info as $i){
            $row[0] = $i->nombre;
            $row[1] = $i->apellido;
            $row[2] = $i->identificacion;
            $row[3] = $i->descripcion;
            $row[4] = $i->foto;
          }
         // var_dump($info);

          if($row[4]=='N'){

            //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
            $tabla="<table class='table' style='background-color: #00FF1A;
                border-radius: 10px; 
                border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
            <tr>	 
                <td> 
                 <img src='http://172.19.92.223/ingresocarvajal/images/person.png' height='130' width='190'> 
                 </td> 
                 <td>
                 <table>
                    <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                    <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                    <tr> <td><label>".$row[3]."</label></td></tr> 
                    </table>
                 </td> 
            </tr> 
            </table>";
                        
                    
        }else if($row[4]=='S'){
            //pinta tabla con foto
            $tabla="<table class='table' style='background-color: #00FF1A;
                border-radius: 10px; 
                border-left:0px; font-size:20px;font-family:'Lato', 'sans-serif'> 
            <tr>	 
                <td> 
                     <img src='http://172.19.92.223/ingresocarvajal/modules/mod_visitantes/fotos/".$row[2].".jpg' height='200' width='245'> 
                </td> 
                <td>
                    <table>
                        <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                        <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                        <tr> <td><label>".$row[3]."</label></td></tr> 
                    </table>
                </td> 
            </tr> 
            </table>";
        }else{
            $tabla="0";
        }

            //Consulta todos los códigos del visitante
        $codigos = DB::table('ohxqc_codigobidimensional as cdb')
        ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
        ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
        ->where('v.identificacion', '=', $cedula)
        ->get();
        if(count($codigos) > 0){
           // echo "<br><br>";var_dump($codigos);
            $datosV=array();
            foreach($codigos as $code){
                $datosV[]=array('cod_vis'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
            }
            $dataV = json_encode($datosV);
            //echo "<br><br>".$dataV;
        }else{

        }
      
        //Consulta todos los activos del visitante
        $activos = DB::table('ohxqc_codigobidimensional as cdb')
        ->select('cdb.codigo as cod_vis', 'cdb.fecha_creacion', DB::raw("(CASE WHEN cdb.activo='S' THEN 1 ELSE 0 END) as cod_activo"))
        ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'cdb.id_visitante')
        ->where('v.identificacion', '=', $cedula)
        ->get();
        if(count($activos) > 0){
           // echo "<br><br>";var_dump($activos);
            $datosV=array();
            foreach($activos as $code){
                $datosV[]=array('cod_act'=>$code->cod_vis,'fecha_creacion'=>$code->fecha_creacion,'activo'=>$code->cod_activo);
            }
            $dataA = json_encode($datosV);
           // echo "<br><br>".$dataA;
        }else{

        }
        return $tabla."/".$dataV."/".$dataA;
      }else{
        return 0; 
      }

    }

    public function retornarVistaFoto($cedula){

        return view('Permisos::tomarFoto', compact('cedula'));
    }

    public function guardarFoto(Request $request){
        $cedula = $request->input('cedula');
        $url= $request->input('urlfoto');
        $image = explode('base64,',$url); 
        $newname = $cedula.".png";

        
        if(Storage::disk('Permisos')->put($newname,base64_decode($image[1]))){
                return back()->with('msj', 'ok');
            
        }else{
            return back()->with('msj', 'error');
        }
        //echo $newname;
        // $newname = "C:/xampp/htdocs/sica/storage/app/fotos/".$cedula.".png";
        //file_put_contents($newname, base64_decode($image[1])); 

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
