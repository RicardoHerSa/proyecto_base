<?php

namespace App\Modules\CargueMasivo\Controllers;

use App\Http\Controllers\Controller;
use App\Services\adobe;
use App\Services\validacionCampos ;
use Illuminate\Support\Facades\Storage;
use App\Services\FlujoApr;
use DB;
use App\Notifications\Correcion;
use Illuminate\Support\Facades\Notification;
use Swift_SwiftException;
use Illuminate\Http\Request;


class cargueMasivoController extends Controller
{
    
    public function index()
    {   
        return view('CargueMasivo::cargueMasivo');
    }

    public function cargarColaboradores(Request $request)
    {
        if($request->hasFile('archivo')){
            $cantCadena = strlen($_FILES['archivo']['name']);
            $primerExt = substr($_FILES['archivo']['name'], -3, $cantCadena);
            $segExt = substr($_FILES['archivo']['name'], -4, $cantCadena);
            $archivo = $_FILES['archivo'];
            if($primerExt == "lsx" || $segExt == "xlsx"){
                $ejecutado = cargueMasivoController::cargueCSV($archivo);
                if($ejecutado == false){
                    return redirect('cargue-masivo')->with('errEstr', 'El archivo de excel no cumple con la estructura requerida.');
                }else{
                    return redirect('cargue-masivo')->with('ok', 'El cargue fue ejecutado con éxito.');
                }
            }else{
                return redirect('cargue-masivo')->with('errArchi', 'Por favor sube un archivo de excel: .lsx , .xlsx');
            }
        }else{
            return redirect('cargue-masivo')->with('errEmpty', 'Por favor suba un archivo de excel válido.');
	    }
        
    }

    public function cargueCSV($archivo)
    {
        try{
			$sql="";
			$empresa_visitante="";
			$inserta=false;
			
			$file = $archivo['tmp_name'];

            $operador = fopen($file,"r");
            $longi = 1000000;
            $cab = cargueMasivoController::customfgetcsv($operador, $longi); 

				if(count($cab) == 16){

					$hashCab= trim($cab[0])."-".trim($cab[1])."-".trim($cab[2])."-".trim($cab[3])."-".trim($cab[4])."-".trim($cab[5])."-".trim($cab[6])."-".trim($cab[7])."-".trim($cab[8])."-".trim($cab[9])."-".trim($cab[10])."-".trim($cab[11])."-".trim($cab[12])."-".trim($cab[13])."-".trim($cab[14]);

					//echo $hashCab;
					//if($hashCab == "IDENTIFICACION_JEFE-TIPO_IDENTIFICACION-IDENTIFICACION-NOMBRE-FECHA_INGRESO-FECHA_FIN-TIPO_CONTRATO-EMAIL-TELEFONO1-TELEFONO2-TELEFONO3-TIPO_VISITANTE-CARGO-CIUDAD-ACTIVO"){	
					$iterador=0;
					while (($data = cargueMasivoController::customfgetcsv($operador, $longi))!=false){
						
						$iterador++;
						
                        $sql = DB::table('ohxqc_visitantes')->select('id_visitante')->where('identificacion', $data[2]);
                        $row = array();
                        foreach($sql as $s){
                            $row[0] = $s->id_visitante;
                        }
						if($row[0]!=''){
							
							//**ACTUALIZA AL VISITANTE
							$id_jefe=$data[0];
							if(trim($id_jefe) ==""){
								$id_jefe='null';
							}else{$id_jefe="'".$id_jefe."'";}
							$tipo_identificacion=$data[1];
							$identificacion=trim($data[2]);
							$nombre=$data[3];
							/*$apellido=$data[4];
							if(trim($apellido) == ""){
							$apellido='null';
							}else{$apellido="'".$data[4]."'";}*/
							$fecha_ingreso=$data[4];
							if(trim($fecha_ingreso) == ""){
							$fecha_ingreso='null';
							}else{$fecha_ingreso="'".$data[4]."'";}
							$fecha_fin=$data[5];
							if(trim($fecha_fin) == ""){
							$fecha_fin='null';
							}else{$fecha_fin="'".$data[5]."'";}
							$tipo_contrato=$data[6];
							$email=$data[7];
							if(trim($email) == ""){
							$email='null';
							}else{$email="'".$email."'";}
							$telefono1=$data[8];
							if(trim($telefono1)!=""){
							$telefono1="'".$telefono1."'";
							}else{$telefono1="null";}
							$telefono2=$data[9];
							if(trim($telefono2) !=""){
							$telefono2="'".$telefono2."'";
							}else{$telefono2="null";}
							$telefono3=$data[10];
							if(trim($telefono3) !=""){
							$telefono3="'".$telefono3."'";
							}else{$telefono3="null";}
							$tipo_visitante=$data[11];
							$cargo=$data[12];
							$ciudad=$data[13];
							$activo= trim($data[14]) == 's' || trim($data[14])=='S' ? 'S' : 'N'  ;              
							$empresa_visitante=trim($data[15]);
							//******Tener en cuanta este filtro
							$empresa_visitante= str_replace(chr(10),"",$empresa_visitante);
							$empresa_visitante= str_replace(chr(13),"",$empresa_visitante);
							//******
							//$person_id=$data[16];
							
							if($identificacion!='' && $nombre!='' && $fecha_ingreso!='' && $fecha_fin!='' && trim($empresa_visitante)!=''){
								$actualiza = table('ohxqc_visitantes')->where('id_visitante',$row[0])->update([
                                    'identificacion_jefe' =>$id_jefe, 			
                                    'nombre' => $nombre, 
                                    'fecha_ingreso' => $fecha_ingreso, 
                                    'fecha_fin' => $fecha_fin, 
                                    'tipo_contrato' => $tipo_contrato, 
                                    'telefono1' => $telefono1, 
                                    'telefono2' => $telefono2, 
                                    'telefono3' => $telefono3, 
                                    'tipo_visitante' => $tipo_visitante, 
                                    'cargo' => $cargo, 
                                    'ciudad' => $ciudad,
                                    'activo' => $activo,
                                    'usuario_actualizacion' => 'usr_plantilla',  
                                    'fecha_actualizacion' =>now()
                                ]);
								if($actualiza){$inserta=true;}else{$inserta=false;}
								//OBTIENE ID DE VISITANTE INSERTADO
								$id_visitante=$row[0];
								//ACTUALIZA EL VISITANTE EN UNA EMPRESA
                                $actualiza2 = DB::table('ohxqc_empresas_visitante')
                                ->where('id_visitante', $id_visitante)
                                ->update([
                                    'id_empresa' => $empresa_visitante,
                                    'usuario_actualizacion' =>  'usr_plantilla'
                                ]);

                                if($actualiza2){$inserta=true;}else{$inserta=false;}
								
								
								//Validamos si es empresa del grupo o externo
								//1 empresas del grupo Carvajal
								//2 empresas que no son del grupo pero pueden acceder a las instalaciones del grupo
                                $sql_ve = DB::table('ohxqc_empresas')
                                ->select('id_empresa')
                                ->where('id_empresa',$empresa_visitante)
                                ->whereIn('grupo_carvajal', ['1','2'])
                                ->limit(1);
								
                                $row_ve = array();
                                foreach($sql_ve as $s){
                                    $row_ve[0] = $s->id_empresa;
                                }
								
								if ($row_ve[0]!='') {
									$empresas_permiso = '2,96';
								} else {
									$empresas_permiso = '2';
								}

								//Actualizamos las porterias existentes
                                $actualiza = DB::table('ohxqc_permisos')
                                ->whereIn('id_permiso', function($query){
                                    $query->select('p.id_permiso')
                                    ->from('ohxqc_permisos as p')
                                    ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'p.id_empresa_visitante')
                                    ->join('ohxqc_ubicaciones as u', 'u.id_ubicacion', '=', 'p.id_ubicacion')
                                    ->where('v.id_visitante', $id_visitante)
                                    ->where('u.activo', 'S')
                                    ->whereIn('u.id_padre', $empresas_permiso)
                                    ->whereIn('p.id_permiso', function($query1){
                                        $query1->select(DB::raw('MAX(p2.id_permiso)'))
                                        ->from('ohxqc_permisos as p2')
                                        ->get();
                                    });
                                })
                                ->update([
                                    'activo' => 'S',
                                    'fecha_inicio' => $fecha_ingreso,
                                    'fecha_fin' => $fecha_fin,
                                    'usuario_actualizacion' => 'usr_plantilla',
                                    'fecha_actualizacion' => now()
                                ]);
                                /*
								$sql = "update ohxqc_permisos
										   set activo = 'S', fecha_inicio = $fecha_ingreso, fecha_fin = $fecha_fin,
										       usuario_actualizacion = 'usr_plantilla', fecha_actualizacion = now()
										 where id_permiso in (
											select p.id_permiso
											from ohxqc_permisos p,
												 ohxqc_visitantes v,
												 ohxqc_ubicaciones u
											where p.id_empresa_visitante = v.id_visitante
											  and v.id_visitante = $id_visitante
											  and p.id_ubicacion = u.id_ubicacion
											  and u.id_padre in ($empresas_permiso)
											  and u.activo = 'S'
											  and p.id_permiso = (select max(p2.id_permiso) from ohxqc_permisos p2 where p2.id_ubicacion = p.id_ubicacion and p2.id_empresa_visitante = p.id_empresa_visitante))";*/
                                if($actualiza){$inserta=true;}else{$inserta=false;}

								//Insertamos las nuevas porterias
                                $sql = DB::table('ohxqc_ubicaciones as u2')
                                ->whereNotIn('u2.id_ubicacion', function($query){
                                    $query->select('u.id_ubicacion')
                                    ->from('ohxqc_permisos as p')
                                    ->join('ohxqc_visitantes as v','p.id_empresa_visitante', '=', 'v.id_visitante')
                                    ->join('ohxqc_ubicaciones as u', 'u.id_ubicacion', '=', ' p.id_ubicacion')
                                    ->where('v.id_visitante',$id_visitante)
                                    ->whereIn('u.id_padre',$empresas_permiso)
                                    ->where('u.activo','S')
                                    ->whereIn('p.id_permiso', function($query2){
                                        $query2->select(DB::raw('MAX(p2.id_permiso)'))
                                        ->from('ohxqc_permisos as p2')
                                        ->join('ohxqc_permisos as p', 'p.id_ubicacion', '=', 'p2.id_ubicacion')
                                        ->where('p2.id_empresa_visitante', 'p.id_empresa_visitante')
                                        ->get();
                                    });
                                    
                                })->whereIn('u2.id_padre', $empresas_permiso)
                                ->where('u2.activo', 'S')
                                ->get();
								/*$sql="select *
									  from ohxqc_ubicaciones u2
									 where u2.id_ubicacion not in (
										select u.id_ubicacion
										from ohxqc_permisos p,
											 ohxqc_visitantes v,
											 ohxqc_ubicaciones u
										where p.id_empresa_visitante = v.id_visitante
										  and v.id_visitante = $id_visitante
										  and p.id_ubicacion = u.id_ubicacion
										  and u.id_padre in ($empresas_permiso)
										  and u.activo = 'S'
										  and p.id_permiso = (select max(p2.id_permiso) from ohxqc_permisos p2 where p2.id_ubicacion = p.id_ubicacion and p2.id_empresa_visitante = p.id_empresa_visitante))
									   and u2.id_padre in ($empresas_permiso)
									   and u2.activo = 'S'";
								
								$result = pg_query($conector,$sql);*/

                                foreach($sql as $s){
                                    $id_ubicacion = $s->id_ubicacion;
                                    $insertado = DB::table('ohxqc_permisos')->insert([
                                        'id_empresa_visitante' => $id_visitante,
                                        'id_ubicacion' => $id_ubicacion,
                                        'id_horario' => 5,
                                        'identificacion_responsable' => null,
										'fecha_inicio' => $fecha_ingreso,
                                        'fecha_fin' => $fecha_fin,
                                        'activo' => 'S',
                                        'usuario_creacion' => 'usr_plantilla',
                                        'fecha_creacion' => now(),
										'usuario_actualizacion' => 'usr_plantilla',
                                        'fecha_actualizacion' => now()
                                    ]);
                                }
								
								/*while ($row = pg_fetch_assoc($result)) {
									
									$id_ubicacion = $row["id_ubicacion"];
									
									$sql="INSERT INTO ohxqc_permisos(
										id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
										fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
										usuario_actualizacion, fecha_actualizacion)
										VALUES ($id_visitante, $id_ubicacion, 5, null, 
										$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
										'usr_plantilla', now())";
									
									pg_query($conector,$sql);
									
								}*/
								
							}

						} else {
							
							//INSERTA AL VISITANTE
							$id_jefe=$data[0];
							if(trim($id_jefe) ==""){
							$id_jefe='null';
							}else{$id_jefe="'".$id_jefe."'";}
							$tipo_identificacion=$data[1];
							$identificacion=trim($data[2]);
							$nombre=$data[3];
							/*$apellido=$data[4];
							if(trim($apellido) == ""){
							$apellido='null';
							}else{$apellido="'".$data[4]."'";}*/
							$fecha_ingreso=$data[4];
							if(trim($fecha_ingreso) == ""){
							$fecha_ingreso='null';
							}else{$fecha_ingreso="'".$data[4]."'";}
							$fecha_fin=$data[5];
							if(trim($fecha_fin) == ""){
							$fecha_fin='null';
							}else{$fecha_fin="'".$data[5]."'";}
							$tipo_contrato=$data[6];
							$email=$data[7];
							if(trim($email) == ""){
							$email='null';
							}else{$email="'".$email."'";}
							$telefono1=$data[8];
							if(trim($telefono1)!=""){
							$telefono1="'".$telefono1."'";
							}else{$telefono1="null";}
							$telefono2=$data[9];
							if(trim($telefono2) !=""){
							$telefono2="'".$telefono2."'";
							}else{$telefono2="null";}
							$telefono3=$data[10];
							if(trim($telefono3) !=""){
							$telefono3="'".$telefono3."'";
							}else{$telefono3="null";}
							$tipo_visitante=$data[11];
							$cargo=$data[12];
							$ciudad=$data[13];
							$activo=trim($data[14]) == 's' || trim($data[14])=='S' ? 'S' : 'N'  ;    
							$empresa_visitante=$data[15];
							//$person_id=$data[16];
							//	echo "<br> identifica: ".$identificacion." nombre:".$nombre." fecha_ingreso:".$fecha_ingreso." fecha_fin:".$fecha_fin." empresa:".$empresa_visitante;
							if($identificacion!='' && $nombre!='' && $fecha_ingreso!='' && $fecha_fin!='' && $empresa_visitante!='') {
								$sql = DB::table('ohxqc_visitantes')->insert([
                                'identificacion_jefe' => $id_jefe,
                                'tipo_identificacion' => $tipo_identificacion,
                                'identificacion' => $identificacion,
                                'nombre' => $nombre,
                                'apellido' => null,
                                'fecha_ingreso' => $fecha_ingreso,
                                'fecha_fin' => $fecha_fin,
                                'tipo_contrato' => $tipo_contrato,
                                'foto' => 'N', 
								'email' => $email,
                                'telefono1' => $telefono1, 
                                'telefono2' => $telefono2, 
                                'telefono3' => $telefono3, 
                                'tipo_visitante' => $tipo_visitante, 
                                'cargo' => $cargo, 
								'ciudad' => $ciudad, 
                                'activo' => 'S', 
                                'usuario_creacion' => 'usr_plantilla',
                                'fecha_creacion' => now(), 
                                'usuario_actualizacion' => 'usr_plantilla', 
								'fecha_actualizacion' =>  now()
                                ]);
                                if($sql){$inserta=true;}else{$inserta=false;}
                                /*
								$sql="INSERT INTO ohxqc_visitantes(
								identificacion_jefe, tipo_identificacion, identificacion, 
								nombre, apellido, fecha_ingreso, fecha_fin, tipo_contrato, foto, 
								email, telefono1, telefono2, telefono3, tipo_visitante, cargo, 
								ciudad, activo, usuario_creacion, fecha_creacion, usuario_actualizacion, 
								fecha_actualizacion)
								VALUES ($id_jefe, '$tipo_identificacion', '$identificacion', 
								'$nombre', null, $fecha_ingreso, $fecha_fin, '$tipo_contrato', 'N', 
								$email, $telefono1, $telefono2, $telefono3, '$tipo_visitante', '$cargo', 
								'$ciudad', 'S', 'usr_plantilla', now(), 'usr_plantilla', 
								now())";
								
								//echo "<br>".$sql."<br>";
								//if($identificacion=='41779658'){echo $sql;}			
								//INSERTA EN EL VISITANTE
								$inserta = pg_query($conector,$sql);*/

								//OBTIENE ID DE VISITANTE INSERTADO
								$sql="SELECT id_visitante FROM ohxqc_visitantes WHERE identificacion='$identificacion'";
                                $consultaIdV = DB::table('ohxqc_visitantes')->select('id_visitante')->where('identificacion',$identificacion)->get();
                                foreach($consultaIdV as $id){
                                    $id_visitante = $id->id_visitante;
                                }
								//CONSULTA EL ID DE LA EMPRESA DEL VISITANTE 
                                $consultaIdEm = DB::table('ohxqc_empresas')->select('id_empresa')->where('codigo_empresa', $empresa_visitante)->limit(1)->get();
                                foreach($consultaIdEm as $ide){
                                    $id_em_visitante = $ide->id_empresa;
                                }
								
								//REGISTRA EL VISITANTE EN UNA EMPRESA		
                                $insertado = DB::table('ohxqc_empresas_visitante')->insert([
                                    'id_empresa_visitante' =>  $id_visitante,
                                    'id_visitante' => $id_visitante,
                                    'id_empresa' => $id_em_visitante,
                                    'activo' => $activo,
                                    'usuario_creacion' => 'usr_plantilla',
                                    'fecha_creacion' => now(),
                                    'usuario_actualizacion' => 'usr_plantilla',
                                    'fecha_actualizacion' => now(),
                                ]);						
								/*$sql=" 
								INSERT INTO ohxqc_empresas_visitante ( 
								ID_EMPRESA_VISITANTE,ID_VISITANTE, ID_EMPRESA, ACTIVO, USUARIO_CREACION, 
								FECHA_CREACION, USUARIO_ACTUALIZACION, FECHA_ACTUALIZACION)  
								VALUES ($id_visitante,$id_visitante,'$id_em_visitante','$activo','usr_plantilla',now(),'usr_plantilla',now())";
								pg_query($conector,$sql);
																
								////////////////////////////////////////////////
								//Validamos si es empresa del grupo o externo
								//1 empresas del grupo Carvajal
								//2 empresas que no son del grupo pero pueden acceder a las instalaciones del grupo
								$sql_ve = "select id_empresa
											 from ohxqc_empresas
											where id_empresa = '".trim($empresa_visitante)."'
											  and grupo_carvajal = ('1','2')
											limit 1";
								
								$result_ve = pg_query($conector,$sql_ve);	
								$row_ve = pg_fetch_array($result_ve);
								
								if ($row_ve[0]!='') {
									$empresas_permiso = '2,96';
								} else {
									$empresas_permiso = '2';
								}
								
								//Insertamos las porterias
								$sql="select *
									  from ohxqc_ubicaciones u2
									 where u2.id_ubicacion not in (
										select u.id_ubicacion
										from ohxqc_permisos p,
											 ohxqc_visitantes v,
											 ohxqc_ubicaciones u
										where p.id_empresa_visitante = v.id_visitante
										  and v.id_visitante = $id_visitante
										  and p.id_ubicacion = u.id_ubicacion
										  and u.id_padre in ($empresas_permiso)
										  and u.activo = 'S'
										  and p.id_permiso = (select max(p2.id_permiso) from ohxqc_permisos p2 where p2.id_ubicacion = p.id_ubicacion and p2.id_empresa_visitante = p.id_empresa_visitante))
									   and u2.id_padre in ($empresas_permiso)
									   and u2.activo = 'S'";
								
								$result = pg_query($conector,$sql);
								
								while ($row = pg_fetch_assoc($result)) {
									
									$id_ubicacion = $row["id_ubicacion"];
									
									$sql="INSERT INTO ohxqc_permisos(
										id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
										fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
										usuario_actualizacion, fecha_actualizacion)
										VALUES ($id_visitante, $id_ubicacion, 5, null, 
										$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
										'usr_plantilla', now())";
									
									pg_query($conector,$sql);
									
								}				
								
								/*
								//INSERTA LOS PERMISOS PARA SANTA MONICA (SEDE PRINCIPAL)
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 3, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 4, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 5, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 17, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 18, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 21, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 22, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 23, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 24, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 26, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 27, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 28, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);

								//22-dic-2020. Nuevas puertas
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 64, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 65, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 66, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 67, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 68, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 69, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 74, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 77, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 78, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 81, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 82, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 83, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 84, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 85, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 86, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 97, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 98, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								$sql="	INSERT INTO ohxqc_permisos(
								id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
								fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
								usuario_actualizacion, fecha_actualizacion)
								VALUES ($id_visitante, 99, 5, null, 
								$fecha_ingreso, $fecha_fin, 'S', 'usr_plantilla', now(), 
								'usr_plantilla', now())";
								pg_query($conector,$sql);
								*/

							}

						}
					}
					//}
				}
				
				fclose($operador);
				/*$dia_actual=date('Y-m-d');
				$sql="select count(*) as cuenta from ohxqc_visitantes v, ohxqc_empresas_visitante ev  
				where v.id_visitante=ev.id_visitante
				and ev.id_empresa='$empresa_visitante'
				and v.fecha_actualizacion='$dia_actual'";
				$result= pg_query($conector,$sql);
				$row=pg_fetch_array($result);
				$insertados=$row[0];
				if($iterador==$insertados){
				//echo "inserto completo!";
				$sql="INSERT INTO ohxqc_log_cargue(cantidad, estado, fecha_cargue) VALUES ($insertados,'ACEPTADO',now())";
				pg_query($conector,$sql);
				unlink($file); //Se elimina el archivo
				}else{
				$sql="INSERT INTO ohxqc_log_cargue(cantidad, estado, fecha_cargue) VALUES ($insertados,'RECHAZADO',now())";
				pg_query($conector,$sql);
				}*/
				//echo $sql;
			
			
			if($inserta==false){
				echo "NO SE INSERTARON DATOS!!";
			}

			return $inserta;
		}catch(Exception $e){
			echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
		}
    }

    public static function customfgetcsv(&$handle, $length, $separator = ';'){
		if (($buffer = fgets($handle, $length)) !== false) {
			return explode($separator, iconv("ISO-8859-1", "UTF-8", $buffer));
		}
		return false;
	}

   
    
   
}





