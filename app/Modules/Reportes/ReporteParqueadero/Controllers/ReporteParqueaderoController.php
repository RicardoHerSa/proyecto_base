<?php

namespace App\Modules\Reportes\ReporteParqueadero\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteParqueaderoController extends Controller
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

        $sedes = DB::table('ohxqc_sedes')
        ->select('id', 'descripcion')
        ->get();

        return view('Reportes::reporteParqueadero', compact('empresas', 'sedes'));
    }

    public function consultarReporteParqueadero(Request $request)
    {
        $id_empresa = $request->input("id_empresa");
        $id_sede = $request->input("id_sede");
        $tipo_v = $request->input("tipo_vehiculo");

       // echo "<br>ID_EMPRESA: ".$id_empresa."<br>ID_SEDE: ".$id_sede."<BR>tipo: ".$tipo_v."<br><br>";
        //gerReporte
        if($id_empresa=='TODOS'){
            $consulta = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', 'ep.asignados_fijos', 'ep.ocupados_fijos', 'ep.asignados_temporales', 'ep.ocupados_temporales', 'ep.tipo', DB::raw("(ep.asignados_fijos + ep.asignados_temporales) as total"))
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', $tipo_v)
            ->get();
        }else{
            $consulta = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', 'ep.asignados_fijos', 'ep.ocupados_fijos', 'ep.asignados_temporales', 'ep.ocupados_temporales', 'ep.tipo', DB::raw("(ep.asignados_fijos + ep.asignados_temporales) as total"))
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_empresa', '=', $id_empresa)
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', $tipo_v)
            ->get();
        }
 
        $data= array();
        foreach($consulta as $cons){
            $data[]= array('empresa'=>$cons->descripcion, 'asignados_f'=>$cons->asignados_fijos, 'ocupados_f'=>$cons->ocupados_fijos,'asignados_t'=>$cons->asignados_temporales, 'ocupados_t'=>$cons->ocupados_temporales, 'tipo'=>$cons->tipo, 'total'=>$cons->total);
        }
        $data_rep = json_encode($data);

        //reporteGraficaFijos y  //reporteGraficaTemp
        if($tipo_v == 'CARRO'){
            $consultaReport = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', DB::raw("(ep.asignados_fijos-ep.ocupados_fijos) as disponibles"), 'ep.ocupados_fijos as ocupados')
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', 'CARRO')
            ->get();

            $consultReportTem = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', DB::raw("(ep.asignados_temporales-ep.ocupados_temporales) as disponibles"), 'ep.ocupados_temporales as ocupados')
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', 'CARRO')
            ->get();
        }else{
            $consultaReport = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', DB::raw("(ep.asignados_fijos-ep.ocupados_fijos) as disponibles"), 'ep.ocupados_fijos as ocupados')
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', 'MOTO')
            ->get();

            $consultReportTem = DB::table('ohxqc_empresas_parqueaderos as ep')
            ->select('e.descripcion', DB::raw("(ep.asignados_temporales-ep.ocupados_temporales) as disponibles"), 'ep.ocupados_temporales as ocupados')
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'ep.id_empresa')
            ->where('e.id_sede', '=', 1)
            ->where('ep.id_sede', '=', $id_sede)
            ->where('ep.tipo', '=', 'MOTO')
            ->get();
        }

            $dataRepo= array();
            foreach($consultaReport as $conr){
            $dataRepo[]= array('empresa'=>$conr->descripcion, 'disponibles'=>$conr->disponibles, 'ocupados'=>$conr->ocupados);
            }
            $data_grap = json_encode($dataRepo);

            $dataRepoTemp= array();
            foreach($consultReportTem as $conrt){
            $dataRepoTemp[]= array('empresa'=>$conrt->descripcion, 'disponibles'=>$conrt->disponibles, 'ocupados'=>$conrt->ocupados);
            }
            $data_temp = json_encode($dataRepoTemp);

            if($data_rep != "" && $data_grap != "" && $data_temp != ""){
                return view('Reportes::resultadoReporteParqueadero', compact('data_rep', 'data_grap', 'data_temp', 'tipo_v'));
                
            }else{
                return redirect('reporte-parqueadero')->with('msj', 'No se encontraron reportes');
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
