<?php

namespace App\Modules\Reportes\ReporteIngreso\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteIngresoController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Reportes::reporteIngreso', );
    }

    public function consultarIngresoPersona(Request $request)
    {
        $this->cedula = $request->input('cedula');
        $cedula = $this->cedula;
        $data = ReporteIngresoController::getData($this->cedula);
        echo $data;
        if($data == 0){
            return redirect('reporte-ingreso')->with('msj', 'No se encontraron registros.');
        }else{
            return view('Reportes::resultadoReporte', compact('data', 'cedula'));
        }
        
    }

    public function getData($cedula)
    {
        $consulta = DB::table('ohxqc_visitantes as v')
        ->select('v.nombre',
         'v.identificacion',
         'ing.tipo_registro', DB::raw("(select u.descripcion
         from ohxqc_porteros p,ohxqc_porteros_ubicaciones pu, ohxqc_ubicaciones u
         where p.id = pu.id_portero
         and pu.id_ubicacion = u.id_ubicacion
         and p.usuario = ing.usuario_creacion) as porteria"),
         DB::raw("CAST(ing.fecha_hora as timestamp)"))
        ->join('ohxqc_trx_ingresos_salidas as ing' , 'ing.id_visitante', '=', 'v.id_visitante')
        ->where('v.identificacion', '=', $cedula)
        ->orderBy('ing.fecha_hora', 'DESC')
        ->get();
        if(count($consulta) > 0){
            return json_encode($consulta);
        }else{
            return 0;
        }
        /*select v.nombre, 
        v.identificacion, 
        ing.tipo_registro,
        (select u.descripcion
        from ohxqc_porteros p,ohxqc_porteros_ubicaciones pu, ohxqc_ubicaciones u
        where p.id = pu.id_portero
        and pu.id_ubicacion = u.id_ubicacion
        and p.usuario = ing.usuario_creacion) as porteria,  
        cast (ing.fecha_hora as timestamp(0)), 
        null as equipo, 
        null as serial
                        from ohxqc_visitantes v, 
                        ohxqc_empresas_visitante ev, 
                        ohxqc_trx_ingresos_salidas ing 
                        where v.identificacion = '1130614392' 
                        and v.id_visitante = ev.id_visitante 
                        and v.id_visitante::text = ing.id_visitante
                        order by fecha_hora DESC*/
       
    }

    public function descargarExcelReporteIngreso(Request $request)
    {
        $cedula = $request->input('cedula');

        if($request->input('btn_reporte')  != null){
            $consulta = DB::table('ohxqc_visitantes as v')
            ->select('v.nombre',
             'v.identificacion',
             'ing.tipo_registro', DB::raw("(select u.descripcion
             from ohxqc_porteros p,ohxqc_porteros_ubicaciones pu, ohxqc_ubicaciones u
             where p.id = pu.id_portero
             and pu.id_ubicacion = u.id_ubicacion
             and p.usuario = ing.usuario_creacion) as porteria"),
             DB::raw("CAST(ing.fecha_hora as timestamp)"))
            ->join('ohxqc_trx_ingresos_salidas as ing' , 'ing.id_visitante', '=', 'v.id_visitante')
            ->where('v.identificacion', '=', $cedula)
            ->orderBy('ing.fecha_hora', 'DESC')
            ->get();
            $fp = fopen('php://output','w');
		   fputcsv($fp, array('NOMBRE','CEDULA','TIPO INGRESO','PORTERO','FECHA REGISTRO','EQUIPO','SERIAL'),'|');
           foreach($consulta as $con){
             fputcsv($fp, array(utf8_decode($con->nombre),$con->identificacion,$con->tipo_registro,utf8_decode($con->porteria),'hora',null,null),'|');
           }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte-ingreso.csv');
            exit();	
        }else{
            return redirect('reporte-ingreso');
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
