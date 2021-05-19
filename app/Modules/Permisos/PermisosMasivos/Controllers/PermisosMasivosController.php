<?php

namespace App\Modules\Permisos\PermisosMasivos\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermisosMasivosController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = DB::table('ohxqc_empresas')
        ->select('id_empresa', 'descripcion')
        ->whereIn('id_empresa', function($query){
            $query->select('id_empresa')
            ->from('ohxqc_empresas_parqueaderos')
            ->groupBy('id_empresa');
        })
        ->where('activo', '=', 'S')
        ->where('id_sede', '=', 1)
        ->orderBy('descripcion')
        ->get();

        return view('Permisos::permisosMasivos', compact('empresas'));
    }

    public function consultarEmpresas(Request $request)
    {
        $id_sede = $request->input('ids');
        $sql = DB::table('ohxqc_empresas')
        ->select('codigo_empresa', 'descripcion')
        ->where('activo', '=', 'S')
        ->where('id_sede', '=', $id_sede)
        ->orderBy('descripcion')
        ->get();
        $lista='<select name="empresa" id="empresa" class="form-control">';
        $lista.='<option value="0">--SELECCIONE--</option>';
        foreach($sql as $s){
            
                $lista.='<option value="'.$s->codigo_empresa.'">'.$s->descripcion.'</option>';		
        }
        $lista.='</select>';
        echo $lista;
    }

    public function consultarPermisosMasivos(Request $request)
    {
        $id_sede = $request->input("sede");
        $empresa = $request->input("empresa");
        $fecha_reg = $request->input("fecha_reg");

        $dataVisitante = PermisosMasivosController::getColaboradores($id_sede,$empresa,$fecha_reg);
        $dataTree = PermisosMasivosController::getPorterias();
	    $dataHorario = PermisosMasivosController::getHorarios();

        return view('Permisos::resultadoMasivos', compact('dataVisitante', 'dataTree', 'dataHorario'));

       /* echo "<pre>";
        print_r($dataVisitante);
        echo"<hr>";
        print_r($dataTree);
        echo"<hr>";
        print_r($dataHorario);*/
        
    }

    public function getColaboradores($id_sede,$empresa,$fecha_reg)
    {
        if($empresa !='0'){
            if(trim($fecha_reg) ==''){
                $query = DB::table('ohxqc_visitantes as v')->select('v.id_visitante', 'ev.id_empresa_visitante', 'v.identificacion_jefe', 'em.descripcion as empresa', 'tv.nombre as tipo_visitante', 'v.identificacion as cedula', 'v.nombre', 'v.apellido', 'v.cargo', DB::raw('(select nombre from ohxqc_visitantes where identificacion = v.identificacion_jefe limit 1 ) as jefe'), 'v.ciudad', 'v.tipo_contrato', 'v.fecha_ingreso', 'v.fecha_fin')
                ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', 'v.id_visitante')
                ->join('ohxqc_empresas as em', 'em.id_empresa', 'ev.id_empresa')
                ->join('ohxqc_tipos_visitante as tv', 'tv.id_tipo_visitante', 'v.tipo_visitante')
                ->where('em.codigo_empresa', '=', $empresa)
                ->where('em.id_sede', '=', $id_sede)
                ->where('v.activo', '=', 'S')
                ->get();
  
            }else{
                $query = DB::table('ohxqc_visitantes as v')->select('v.id_visitante', 'ev.id_empresa_visitante', 'v.identificacion_jefe', 'em.descripcion as empresa', 'tv.nombre as tipo_visitante', 'v.identificacion as cedula', 'v.nombre', 'v.apellido', 'v.cargo', DB::raw('(select nombre from ohxqc_visitantes where identificacion = v.identificacion_jefe limit 1 ) as jefe'), 'v.ciudad', 'v.tipo_contrato', 'v.fecha_ingreso', 'v.fecha_fin')
                ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante', 'v.id_visitante')
                ->join('ohxqc_empresas as em', 'em.id_empresa', 'ev.id_empresa')
                ->join('ohxqc_tipos_visitante as tv', 'tv.id_tipo_visitante', 'v.tipo_visitante')
                ->where('em.codigo_empresa', '=', $empresa)
                ->where('em.id_sede', '=', $id_sede)
                ->where('v.fecha_actualizacion', '=', $fecha_reg)
                ->where('v.activo', '=', 'S')
                ->get();
               
            }
            $datosV=array();
            foreach($query as $q){
                $datosV[]=array('ID_VISITANTE'=>$q->id_visitante,'ID_EMPRESA_VISITANTE'=>$q->id_empresa_visitante,'IDENTIFICACION_JEFE'=>$q->identificacion_jefe,'EMPRESA'=>$q->empresa,'TIPO_VISITANTE'=>$q->tipo_visitante,'CEDULA'=>$q->cedula,'NOMBRE'=>$q->nombre,'APELLIDO'=>$q->apellido,'CARGO'=>$q->cargo, 'JEFE'=>$q->jefe, 'CIUDAD'=>$q->ciudad, 'TIPO_CONTRATO'=>$q->tipo_contrato, 'FECHA_INGRESO'=>$q->fecha_ingreso, 'FECHA_FIN'=>$q->fecha_fin);
            }
            
            
              return json_encode($datosV);
                           
        }else{return "'0'";}
       
    }

    public function getPorterias()
    {
        //Datos del arbol		
      $query= DB::table('ohxqc_ubicaciones')
      ->select('id_ubicacion', 'id_padre', 'descripcion')
      ->where('activo', '=', 'S')
      ->get();
      
        foreach ($query as $q){
            $treearr[]=array($q->id_ubicacion,$q->id_padre,$q->descripcion);
        }
      
      for($i=0;$i<count($treearr);$i++){
         $dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][0]);
      }
      
      return json_encode($dataT);
    }

    public function getHorarios()
    {
        $query = DB::table('ohxqc_horarios')->select('id', 'descripcion')->get();
       
        
        foreach ($query as $q){
          $dataH[]=array('ID'=>$q->id,'DESCRIPCION'=>$q->descripcion);	
        }
          return json_encode($dataH);
    }

    public function insertarRegistrosMasivos(Request $request)
    {
        $resp = 0;
        if($request->input('data') != null && $request->input('id_t') != null){
            $datos = $request->input('data');
            $n = count($datos);
            $ids_tree = $request->input('id_t');
            $ubicaciones=explode(',',$ids_tree);

            if($n > 0 && count($ubicaciones) > 0){
                for($i=0;$i<count($ubicaciones);$i++){
                    for($a=0;$a<$n;$a++){
                        $usuario=$datos[$a]['user'];
                        $id_horario=$datos[$a]['id_h'];
                        $id_empresa=$datos[$a]['id_ev'];
                        $id_jefe=$datos[$a]['id_jefe'];
                        $fecha_inicio=$datos[$a]['fecha_i'];
                        $fecha_fin=$datos[$a]['fecha_f'];

                        if( $id_horario!="" && $ubicaciones[$i]!=""){
                            // ***HACE UPDATE EN CASO DE REGISTROS ANTERIORES
                            $consulta = DB::table('ohxqc_permisos')
                            ->where('id_empresa_visitante', '=', $id_empresa)
                            ->where('id_ubicacion', '=', $ubicaciones[$i])
                            ->get();
                                if(count($consulta) > 0){ 
                                    //Este caso es cuando ya se tienen registros anteriores
                                    $actualiza = DB::table('ohxqc_permisos')
                                    ->where('id_empresa_visitante', '=', $id_empresa)
                                    ->where('id_ubicacion', '=', $ubicaciones[$i])
                                    ->update([
                                        'id_empresa_visitante' => $id_empresa,
                                        'id_ubicacion' => $ubicaciones[$i],
                                        'id_horario' => $id_horario,
                                        'activo' => 'S',
                                        'usuario_actualizacion' => $usuario,
                                        'fecha_actualizacion' => now()
                                    ]);
                                  
                                    $resp="1";

                                }else{
                                    if($id_jefe!=""){
                                         $inserta = DB::table('ohxqc_permisos')->insert([
                                            'id_empresa_visitante' => $id_empresa,
                                            'id_ubicacion' => $ubicaciones[$i],
                                            'id_horario' => $id_horario,
                                            'identificacion_responsable' => $id_jefe,
                                            'fecha_inicio' => $fecha_inicio,
                                            'fecha_fin' => $fecha_fin,
                                            'activo' => 'S',
                                            'usuario_creacion' => 'admin',
                                            'fecha_creacion' => now(),
                                            'usuario_actualizacion' => 'admin',
                                            'fecha_actualizacion' => now()
                                        ]);
                                            $resp= "1";
                                        }else{
                                            $inserta = DB::table('ohxqc_permisos')->insert([
                                                'id_empresa_visitante' => $id_empresa,
                                                'id_ubicacion' => $ubicaciones[$i],
                                                'id_horario' => $id_horario,
                                                'identificacion_responsable' => 'null',
                                                'fecha_inicio' => $fecha_inicio,
                                                'fecha_fin' => $fecha_fin,
                                                'activo' => 'S',
                                                'usuario_creacion' => 'admin',
                                                'fecha_creacion' => now(),
                                                'usuario_actualizacion' => 'admin',
                                                'fecha_actualizacion' => now()
                                            ]);
                                                $resp= "1";
                                        }
                                    }
                        }
                    }//segundo for
                }//primer for
            }//segundo if
        }//if principal

        echo $resp;
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
