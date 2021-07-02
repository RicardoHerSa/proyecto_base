<?php

namespace App\Modules\Permisos\PermisosUnitarios\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PermisosUnitariosController extends Controller
{
    public $cedula = "";
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function permisosUnitarios()
    {
       $query = DB::table('ohxqc_ubicaciones')->where('activo', '=', 'S')->get();
       
        foreach($query as $q){
            $treearr[]=array($q->id_ubicacion,$q->id_padre,$q->descripcion);
        }
		
		for($i=0;$i<count($treearr);$i++){
		$dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][0]);
		}
		
		$dataTree=json_encode($dataT);
        $listaHorarios = DB::table('ohxqc_horarios')->get();
        return view('Permisos::permisosUnitarios', compact('dataTree', 'listaHorarios'));
    }

    public function consultarCedulaVisitante(Request $request)
    {
        $this->cedula = $request->input('cc_visitante');
      
       // echo $cedula;
        $query = DB::table('ohxqc_visitantes as v')
        ->select('v.nombre', 
        'v.id_visitante',
        'v.identificacion',
        'v.cargo',
        'e.descripcion as empresa',
        'tv.nombre as tipo',
        'v.ciudad',
        'v.tipo_contrato',
        'v.fecha_ingreso',
        'v.fecha_fin',
        'v.activo',
        'per.id_horario',
        'v.nombre as jefe'
        )
        ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
        ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ev.id_empresa')
        ->join('ohxqc_tipos_visitante as tv', 'tv.id_tipo_visitante', '=', 'v.tipo_visitante')
        ->join('ohxqc_permisos as per', 'per.id_empresa_visitante', '=', 'v.id_visitante')
       // ->join('ohxqc_visitantes as jefe', 'jefe.identificacion', '=', 'v.identificacion_jefe')
                                                                    //ev.id_empresa_visitante
                                                                    //DB::raw("cast(ev.id_empresa as numeric)")
        ->where('v.identificacion', '=', $this->cedula)
        ->limit(1)
        ->orderBy('v.fecha_ingreso', 'desc')
        ->get();
        //var_dump($query);
        
        if(count($query) > 0 ){
            $resultado = 1;
            foreach($query as $q){
                $nombre = $q->nombre;
                $idvisitante = $q->id_visitante;
                $cc = $q->identificacion;
                $cargo = $q->cargo;
                $empresa = $q->empresa;
                $tipo = $q->tipo;
                $jefe = $q->jefe;  //falta consultar el jefe
                $ciudad = $q->ciudad;
                $contrato = $q->tipo_contrato;
                $fechaIni = $q->fecha_ingreso;
                $fechaFin = $q->fecha_fin;
                $estado = $q->activo;
                $idhorario = $q->id_horario;
                
            }
          
                
            $consulta = DB::table('ohxqc_ubicaciones')->whereIn('id_ubicacion', function($query){
                    $query->select('id_ubicacion')
                    ->from('ohxqc_permisos') 
                    //->where('identificacion_responsable',$this->cedula) //modificado de acuerdo a registro-visitante
                    ->whereIn('id_empresa_visitante', function($queryDos){ //id empresa de este visitante 128
                        $queryDos->select('id_visitante')
                        ->from('ohxqc_visitantes') 
                        ->where('identificacion', '=', $this->cedula);
                    });
                    
                })
                ->get();

                // $consulta = DB::table('ohxqc_ubicaciones')->whereIn('id_ubicacion', function($query){
                //     $query->select('id_ubicacion')
                //     ->from('ohxqc_permisos') 
                //     ->where('identificacion_responsable',$this->cedula) //modificado de acuerdo a registro-visitante
                //     ->whereIn('id_empresa_visitante', function($queryDos){ //id empresa de este visitante 128
                //         $queryDos->select('id_empresa_visitante') //id_empresa_visitante //DB::raw("cast(id_empresa as numeric)")
                //         ->from('ohxqc_empresas_visitante')
                //         ->whereIn('id_visitante',function($queryTres){
                //             $queryTres->select('id_visitante')
                //             ->from('ohxqc_visitantes') 
                //             ->where('identificacion', '=', $this->cedula);  //id visitante = 352961
                //         });
                //     });
                    
                // })
                // ->get();
            
             
                // $consulta = DB::table('ohxqc_ubicaciones')->whereIn('id_ubicacion', function($query){
                //     $query->select('id_ubicacion')
                //     ->from('ohxqc_permisos') 
                //     ->where('identificacion_responsable',$this->cedula) //modificado de acuerdo a registro-visitante
                //     ->whereIn('id_empresa_visitante', function($queryDos){ //id empresa de este visitante 128
                //         $queryDos->select(DB::raw("cast(id_empresa as numeric)")) //id_empresa_visitante
                //         ->from('ohxqc_empresas_visitante')
                //         ->whereIn('id_visitante',function($queryTres){
                //             $queryTres->select('id_visitante')
                //             ->from('ohxqc_visitantes') 
                //             ->where('identificacion', '=', $this->cedula);  //id visitante = 352961
                //         });
                //     });
                    
                // })
                // ->get();

                
                if(count($consulta) > 0){
                   // echo "<pre>";
                   // print_r($consulta);
                    foreach($consulta as $c){
                        $dataTU[]=array($c->id_ubicacion,$c->id_padre,$c->descripcion);
                    }
                    $dataTUser = json_encode($dataTU);
                }else{
                    
                    $dataTUser = 0;
                    
                }
              
        }else{
            $resultado = 0;
        }

        $btn = 1;

        $query = DB::table('ohxqc_ubicaciones')->where('activo', '=', 'S')->get();
       
        foreach($query as $q){
            $treearr[]=array($q->id_ubicacion,$q->id_padre,$q->descripcion);
        }
		
		for($i=0;$i<count($treearr);$i++){
		    $dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][0]);
		}
		
		$dataTree=json_encode($dataT);
        $listaHorarios = DB::table('ohxqc_horarios')->get();

        if($resultado > 0){
            return view('Permisos::permisosUnitarios', compact('dataTUser', 'nombre', 'cc', 'cargo', 'empresa', 'tipo', 'jefe', 'ciudad', 'contrato', 'fechaIni', 'fechaFin', 'estado', 'idhorario', 'btn', 'dataTree', 'listaHorarios'));
        }else{
            return redirect('permisos-unitarios')->with(['mensaje' => 'No se encontraron registros']);
        }
        
    }

    public function actualizarCliente(Request $request)
    {
        $this->cedula =$request->input('cc');
        $permi = $request->input('id_t');
        $id_horario = $request->input('id_horario');
        $ac= $request->input('activo');
        $activo="";
        //echo "CC: ".$this->cedula."<BR> PERMISOS: ".$permi." <BR>IDHORARIO: ".$id_horario."<BR>ESTADO: ".$ac."<br>";

        if($ac=='true'){$activo='S';}else{$activo='N';}
        //echo "NUEVO ESTADO: ".$activo;
        
        if($this->cedula != ""){
            //$this->cedula=substr ($c,4,strlen($c));
           // echo "cedula: ".$this->cedula;
            $permisos=explode(",",$permi);  //convierte el estring de permisos a un array
           
                if($activo== 'N'){
                            $sql = DB::table('ohxqc_visitantes')->where('identificacion', '=', $this->cedula)->update(['activo'=>'N']);
                }else{
                            $sql = DB::table('ohxqc_visitantes')->where('identificacion', '=', $this->cedula)->update(['activo' => 'S']);
                }
         
            if($permisos[0]!="" && $activo=='S'){

                    $delete = DB::table('ohxqc_permisos')//->where('id_empresa_visitante',$this->cedula)
                    ->whereIn('id_empresa_visitante', function($query){
                        $query->select('id_visitante')
                            ->from('ohxqc_visitantes')
                            ->where('identificacion', '=', $this->cedula);
                    })->delete();




                //Agrega los nuevos permisos
                 //consulta id empresa visitante
                
                    $idEmpresaVisitante = DB::table('ohxqc_empresas_visitante')
                    ->select('id_empresa_visitante')
                    ->whereIn('id_visitante', function($cons){
                        $cons->select('id_visitante')
                        ->from('ohxqc_visitantes')
                        ->where('identificacion', '=', $this->cedula);
                    })->limit(1)
                    ->get();

                    
                    $idEmpresaVisitante = $idEmpresaVisitante[0]->id_empresa_visitante;
                    

                    $informacion = DB::table('ohxqc_visitantes')
                    ->select('identificacion', 'fecha_ingreso', 'fecha_fin')
                    ->where('identificacion', '=', $this->cedula)
                    ->limit(1)
                    ->get();


                    $arrayInfo = array();
                    foreach($informacion as $info){
                        $arrayInfo[0] = $info->identificacion;
                        $arrayInfo[1] = $info->fecha_ingreso;
                        $arrayInfo[2] = $info->fecha_fin;
                    }
                    //var_dump($informacion);
                   // echo "<br>IDENTI: ".$arrayInfo[0]."<br>Fecha Ingreso: ".$arrayInfo[1]."<br>Fecha Fin: ".$arrayInfo[2];
                   // echo count($permisos)."<br>";
                   
                 
                 
                    for($i=0;$i<(count($permisos)-1);$i++){
                        $idmaxi =  DB::select("select nextval('ohxqc_permisos_seq'::regclass)");
                        $inserta = DB::table('ohxqc_permisos')->insert([
                            'id_permiso' =>  $idmaxi[0]->nextval,
                            'id_empresa_visitante' => $idEmpresaVisitante,
                            'id_ubicacion' =>  $permisos[$i],
                            'id_horario' => $id_horario,
                            'identificacion_responsable' => $arrayInfo[0],
                            'fecha_inicio' => $arrayInfo[1],
                            'fecha_fin' => $arrayInfo[2],
                            'activo' => $activo,
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacion' => now()
                        ]);
                       // echo "<br>RESULTADO DEL INSERT: ";var_dump($inserta);
                    }

                    $actualiza = DB::table('ohxqc_visitantes')
                    ->where('identificacion', '=', $this->cedula)
                    ->update([
                        'activo' => 'S'
                    ]);
                        
            }else if($permisos[0]=="" && ($activo== 'S' || $activo=='N')){
                //Elimina primero los permisos
             
                $elimina = DB::table('ohxqc_permisos')->whereIn('id_empresa_visitante', function($conse){
                    $conse->select('id_empresa_visitante')
                    ->from('ohxqc_empresas_visitante')
                    ->whereIn('id_visitante', function($conseDos){
                        $conseDos->select('id_visitante')
                        ->from('ohxqc_visitantes')
                        ->where('identificacion', '=', $this->cedula)
                        ->limit(1);
                    });
                })->delete();
                //echo "NO HAY NINGUN PERMISO Y SE ELIMINARIA: <br>";
                //var_dump($elimina);
            }
      
                echo 1;
        }else{  //cierra el primer condicional de if( c != '')
            echo 0;
        }
    }

  
}
