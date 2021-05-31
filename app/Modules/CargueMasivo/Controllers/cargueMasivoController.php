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
    public $id_visitante="";
	public $empresas_permiso = "";
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
            if($primerExt == "csv"){
                $ejecutado = cargueMasivoController::cargueCSV($archivo);
                if($ejecutado == false){
                    //return redirect('cargue-masivo')->with('errEstr', 'El archivo de excel no cumple con la estructura requerida.');
                }else{
                    //return redirect('cargue-masivo')->with('ok', 'El cargue fue ejecutado con éxito.');
                }
            }else{
                return redirect('cargue-masivo')->with('errArchi', 'Por favor sube un archivo de excel: .csv , delimitado por (;) punto y coma.');
            }
        }else{
           // return redirect('cargue-masivo')->with('errEmpty', 'Por favor suba un archivo de excel válido.');
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
			//var_dump($cab);
			//echo "<br> cantidad: ".count($cab)."<br><br>.";
				if(count($cab) == 16){

					$hashCab= trim($cab[0])."-".trim($cab[1])."-".trim($cab[2])."-".trim($cab[3])."-".trim($cab[4])."-".trim($cab[5])."-".trim($cab[6])."-".trim($cab[7])."-".trim($cab[8])."-".trim($cab[9])."-".trim($cab[10])."-".trim($cab[11])."-".trim($cab[12])."-".trim($cab[13])."-".trim($cab[14]);

					//echo $hashCab;
					//if($hashCab == "IDENTIFICACION_JEFE-TIPO_IDENTIFICACION-IDENTIFICACION-NOMBRE-FECHA_INGRESO-FECHA_FIN-TIPO_CONTRATO-EMAIL-TELEFONO1-TELEFONO2-TELEFONO3-TIPO_VISITANTE-CARGO-CIUDAD-ACTIVO"){	
					$iterador=0;
					while (($data = cargueMasivoController::customfgetcsv($operador, $longi))!=false){
						
						$iterador++;
						
                        $sql = DB::table('ohxqc_visitantes')->select('id_visitante')->where('identificacion', $data[2])->get();
                        $row = array();

						if(count($sql) > 0){
							foreach($sql as $s){
								$row[0] = $s->id_visitante;
							}
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
								$actualiza = DB::table('ohxqc_visitantes')->where('id_visitante',$row[0])->update([
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
								$this->id_visitante = $id_visitante;
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
								//echo "EMPRESA VISITANTE: ".$empresa_visitante;
                                $sql_ve = DB::table('ohxqc_empresas')
                                ->select('id_empresa')
                                ->where('id_empresa',$empresa_visitante)
                                ->whereIn('grupo_carvajal', ['1','2'])
                                ->limit(1)
								->get();
								
                              
								if (count($sql_ve) > 0) {
									  $row_ve = array();
									  foreach($sql_ve as $s){
										$row_ve[0] = $s->id_empresa;
									 }
								
									$empresas_permiso = '2,96';
								}else{
									$empresas_permiso = '2';
								}
								$this->empresas_permiso = $empresas_permiso;

								//Actualizamos las porterias existentes
                                $actualiza = DB::table('ohxqc_permisos')
                                ->whereIn('id_permiso', function($query){
                                    $query->select('p.id_permiso')
                                    ->from('ohxqc_permisos as p')
                                    ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'p.id_empresa_visitante')
                                    ->join('ohxqc_ubicaciones as u', 'u.id_ubicacion', '=', 'p.id_ubicacion')
                                    ->where('v.id_visitante',$this->id_visitante)
                                    ->where('u.activo', 'S')
                                    ->whereIn('u.id_padre', [$this->empresas_permiso])
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

                                if($actualiza){$inserta=true;}else{$inserta=false;}

								//Insertamos las nuevas porterias
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
								 $sql = DB::select($sql);
                                /*$sql = DB::table('ohxqc_ubicaciones as u2')
                                ->whereNotIn('u2.id_ubicacion', function($query){
                                    $query->select('u.id_ubicacion')
                                    ->from('ohxqc_permisos as p')
                                    ->join('ohxqc_visitantes as v','p.id_empresa_visitante', '=', 'v.id_visitante')
                                    ->join('ohxqc_ubicaciones as u', 'u.id_ubicacion', '=', ' p.id_ubicacion')
                                    ->where('v.id_visitante',$this->id_visitante)
                                    ->whereIn('u.id_padre',[$this->empresas_permiso])
                                    ->where('u.activo','S')
                                    ->whereIn('p.id_permiso', function($query2){
                                        $query2->select(DB::raw('MAX(p2.id_permiso)'))
                                        ->from('ohxqc_permisos as p2')
                                        ->join('ohxqc_permisos as p', 'p.id_ubicacion', '=', 'p2.id_ubicacion')
                                        //->where('p2.id_empresa_visitante', 'p.id_empresa_visitante')
                                        ->get();
                                    });
                                    
                                })->whereIn('u2.id_padre', $empresas_permiso)
                                ->where('u2.activo', 'S')
                                ->get();*/

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

								//OBTIENE ID DE VISITANTE INSERTADO
                                $consultaIdV = DB::table('ohxqc_visitantes')->select('id_visitante')->where('identificacion',$identificacion)->get();
                                foreach($consultaIdV as $id){
                                    $id_visitante = $id->id_visitante;
                                }
								//CONSULTA EL ID DE LA EMPRESA DEL VISITANTE 
								$id_em_visitante = "";
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
                                    'fecha_actualizacion' => now()
                                ]);						

							}

						}
					}
				}else{
					echo "ENTRA AL PRIMAR ELSE.";
				}
				
				fclose($operador);
			
			if($inserta == false){
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





