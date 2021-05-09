<?php

namespace App\Modules\Visitante\Controllers;
use DB;
use App\Models\visitante;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitanteController extends Controller
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
       
        //$query="SELECT * FROM ohxqc_ubicaciones WHERE ACTIVO='S'";
		//$result=pg_query($cnx,$query);
		//while ($row=pg_fetch_array($result)){
		//		$treearr[]=array($row[0],$row[1],$row[2]);
		//			}
        foreach($query as $q){
            $treearr[]=array($q->id_ubicacion,$q->id_padre,$q->descripcion);
        }
        //var_dump($treearr);
        
		
		for($i=0;$i<count($treearr);$i++){
		$dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][0]);
		}
		
		$dataTree=json_encode($dataT);
       // echo $dataTree;
        //Consulta listado de horarios
        $listaHorarios = DB::table('ohxqc_horarios')->get();
        //var_dump($listaHorarios);
        return view('Visitante::permisosUnitarios', compact('dataTree', 'listaHorarios'));
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
        'per.id_horario')
        ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', '=', 'v.id_visitante')
        ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ev.id_empresa')
        ->join('ohxqc_tipos_visitante as tv', 'tv.id_tipo_visitante', '=', 'v.tipo_visitante')
        ->join('ohxqc_permisos as per', 'per.id_empresa_visitante', '=', 'ev.id_empresa_visitante')
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
                $jefe = $q->nombre;  //falta consultar el jefe
                $ciudad = $q->ciudad;
                $contrato = $q->tipo_contrato;
                $fechaIni = $q->fecha_ingreso;
                $fechaFin = $q->fecha_fin;
                $estado = $q->activo;
                $idhorario = $q->id_horario;
                
            }
            /*
            //Consulta ubicaciones del visitante
                //id empresa
                $arrayEmpresa = array();
                $idempresa= DB::table('ohxqc_empresas_visitante')->select('id_empresa_visitante')->where('id_visitante', '=', $idvisitante)->get(); 
                $i = 0; 
                foreach($idempresa as $id){
                    $arrayEmpresa[$i] = $id->id_empresa_visitante;
                    $i++;
                }

                //traer id_ubicacion 
                $arrayUbicacion = array();
                $ubicacion= DB::table('ohxqc_permisos')->select('id_ubicacion')->whereIn('id_empresa_visitante',$arrayEmpresa)->get(); 
                
                $i = 0;
                foreach($ubicacion as $idUbi){
                    $arrayUbicacion[$i] = $idUbi->id_ubicacion;
                    $i++;
                }

                 //traer todo de ohxqc_ubicaciones
                 $result= DB::table('ohxqc_ubicaciones')->whereIn('id_ubicacion',$arrayUbicacion)->get(); 
               
                foreach($result as $r){
                    $dataTU[]=array($r->id_ubicacion,$r->id_padre,$r->descripcion);
                }
                if(count($dataTU)>0){
                    $dataTUser = json_encode($dataTU);
                }
                echo"<hr><br>".$dataTUser;*/

                $consulta = DB::table('ohxqc_ubicaciones')->whereIn('id_ubicacion', function($query){
                    $query->select('id_ubicacion')
                    ->from('ohxqc_permisos') 
                    ->whereIn('id_empresa_visitante', function($queryDos){
                        $queryDos->select('id_empresa_visitante')
                        ->from('ohxqc_empresas_visitante')
                        ->whereIn('id_visitante',function($queryTres){
                            $queryTres->select('id_visitante')
                            ->from('ohxqc_visitantes') 
                            ->where('identificacion', '=', $this->cedula);  
                        });
                    });
                    
                })
                ->get();
                if(count($consulta) > 0){
                    foreach($consulta as $c){
                        $dataTU[]=array($c->id_ubicacion,$c->id_padre,$c->descripcion);
                    }
                    $dataTUser = json_encode($dataTU);
                }else{
                    
                    $dataTUser = 0;
                    
                }
                //echo $this->cedula;
                //echo "<pre>";

                //print_r($consulta);

            /*$query="select * from ohxqc_ubicaciones where id_ubicacion in( 
                select id_ubicacion from ohxqc_permisos where id_empresa_visitante in 
                (select id_empresa_visitante from ohxqc_empresas_visitante where id_visitante=(select id_visitante from ohxqc_visitantes where identificacion='".$cedula."')))";*/
              

        }else{
            $resultado = 0;
        }

        

       /* $sql="SELECT 	
        V.NOMBRE, 	
        V.IDENTIFICACION,	
        V.CARGO, 	
        E.DESCRIPCION EMPRESA, 	
        TV.NOMBRE TIPO, 	
        (Select NOMBRE || ' ' ||APELLIDO FROM ohxqc_visitantes WHERE IDENTIFICACION=V.IDENTIFICACION_JEFE limit 1) JEFE,  	
        V.CIUDAD,	 
        V.TIPO_CONTRATO,	 
        V.FECHA_INGRESO, 	
        V.FECHA_FIN, 
        V.ACTIVO	
        FROM ohxqc_visitantes V, ohxqc_empresas E, ohxqc_empresas_visitante EV, ohxqc_tipos_visitante TV 	
        WHERE 	
        V.IDENTIFICACION='$cedula' 	
        AND E.ID_EMPRESA=EV.ID_EMPRESA 	
        AND EV.ID_VISITANTE=V.ID_visitante 	
        AND TV.ID_TIPO_VISITANTE=V.TIPO_VISITANTE LIMIT 1*/
        if($resultado > 0){
            return redirect()->route('permisosUnitarios', ['json'=>$dataTUser,'nombre'=>$nombre,'cc'=>$cc,'cargo'=>$cargo,'empresa'=>$empresa,'tipo'=>$tipo,'jefe'=>$jefe,'ciudad'=>$ciudad,'contrato'=>$contrato,'fechaIni'=>$fechaIni,'fechaFin'=>$fechaFin,'estado'=>$estado,'horario'=>$idhorario, 'btn']);
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
                            /*
                            $sql="UPDATE ohxqc_visitantes SET ACTIVO='N'  WHERE IDENTIFICACION='$cedula'";
                            pg_query($cnx,$sql);*/
                            //echo "<br> IF ACTUALIZAR ESTADO EN N: entro";
                }else{
                            $sql = DB::table('ohxqc_visitantes')->where('identificacion', '=', $this->cedula)->update(['activo' => 'S']);
                            /*$sql="UPDATE ohxqc_visitantes SET ACTIVO='S'  WHERE IDENTIFICACION='$cedula'";
                            pg_query($cnx,$sql);*/
                            //echo "<br> ELSE ACTUALIZAR ESTADO EN S: entro";
                }
         
            if($permisos[0]!="" && $activo=='S'){
                    //Elimina primero los permisos
                    //echo "<br><br>THIS CEDULA:".$this->cedula;
                    $delete = DB::table('ohxqc_permisos')->whereIn('id_empresa_visitante', function($query){
                        $query->select('id_empresa_visitante')
                        ->from('ohxqc_empresas_visitante') 
                        ->whereIn('id_visitante', function($queryDos){
                            $queryDos->select('id_visitante')
                            ->from('ohxqc_visitantes')
                            ->where('identificacion', '=', $this->cedula);
                        })->limit(1);
                        
                    })
                   
                    ->delete();
                    //$delete = DB::select('select * from users where active = ?', [1])
                    //echo "<br><br>RESULTADO DE ELIMINACION: ";
                    //var_dump($delete);
                
                /*
                $delete="DELETE FROM ohxqc_permisos WHERE ID_EMPRESA_VISITANTE in (select id_empresa_visitante from ohxqc_empresas_visitante where id_visitante=(select id_visitante from ohxqc_visitantes where identificacion='".$cedula."' limit 1))";
                pg_query($cnx,$delete);
                */

                //Agrega los nuevos permisos
                 //consulta id empresa visitante
                
                    $idEmpresaVisitante = DB::table('ohxqc_empresas_visitante')->select('id_empresa_visitante')->whereIn('id_visitante', function($cons){
                        $cons->select('id_visitante')
                        ->from('ohxqc_visitantes')
                        ->where('identificacion', '=', $this->cedula);
                    })->limit(1)
                    ->get();
                    foreach($idEmpresaVisitante as $idem){
                        $idEmpresaVisitante = $idem->id_empresa_visitante;
                    }
                    //echo "<br><br>";
                    //echo "IdEmpresaaVisitante ".$idEmpresaVisitante;
                    //consulta identificacion jefe, fecha ingreso, fecha fin
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
                   // var_dump($informacion);
                   // echo "<br>IDENTI: ".$arrayInfo[0]."<br>Fecha Ingreso: ".$arrayInfo[1]."<br>Fecha Fin: ".$arrayInfo[2];
                   // echo count($permisos)."<br>";
                    for($i=0;$i<(count($permisos)-1);$i++){
                        $inserta = DB::table('ohxqc_permisos')->insert([
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
                    /*
                $sql="INSERT INTO ohxqc_permisos( 
                        ID_EMPRESA_VISITANTE,  
                        ID_UBICACION,  
                        ID_HORARIO,  
                        IDENTIFICACION_RESPONSABLE,  
                        FECHA_INICIO,  
                        FECHA_FIN, 
                        ACTIVO,  
                        USUARIO_CREACION,  
                        FECHA_CREACION,  
                        USUARIO_ACTUALIZACION,  
                        FECHA_ACTUALIZACION) VALUES (
                        (SELECT ID_EMPRESA_VISITANTE FROM ohxqc_empresas_visitante WHERE ID_VISITANTE=(select ID_VISITANTE FROM ohxqc_visitantes WHERE IDENTIFICACION='$cedula' limit 1)),
                        '".$permisos[$i]."',
                        '".$id_horario."',
                        (select IDENTIFICACION_JEFE FROM ohxqc_visitantes WHERE IDENTIFICACION='$cedula' limit 1),
                        (select FECHA_INGRESO FROM ohxqc_visitantes WHERE IDENTIFICACION='$cedula' limit 1),
                        (select FECHA_FIN FROM ohxqc_visitantes WHERE IDENTIFICACION='$cedula' limit 1),
                        '".$activo."','admin',now(),'admin',now())";
                
                        pg_query($cnx,$sql);
                        //$inserta=$sql;
                            }
                            //echo $sql;
                        $sql="UPDATE ohxqc_visitantes SET ACTIVO='S'  WHERE IDENTIFICACION='$cedula'";
                        pg_query($cnx,$sql);*/
                        
                        
            }else if($permisos[0]=="" && ($activo== 'S' || $activo=='N')){
                //Elimina primero los permisos
               /* $delete="DELETE FROM ohxqc_permisos WHERE ID_EMPRESA_VISITANTE in (select id_empresa_visitante from ohxqc_empresas_visitante where id_visitante=(select id_visitante from ohxqc_visitantes where identificacion='".$cedula."' limit 1))";
                pg_query($cnx,$delete);*/
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
