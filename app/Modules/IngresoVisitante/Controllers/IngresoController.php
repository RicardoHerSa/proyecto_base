<?php

namespace App\Modules\IngresoVisitante\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public $cedula = "";
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
        $id_cod = $request->input("id_cod");
        $tipo_ingreso = $request->input("tipo_ingreso");

        $c=substr($cedula,0,1);
        if($c=='A' && $id_cod!=''){
            // $tabla = IngresoController::registraActivo($id_cod,$cedula,$user_log,$user,$cnx);
        }else{
            // $tabla = IngresoController::consultaVisitante($user,$user_log,$cedula,$opcion,$tipo_ingreso,$cnx);	
        }

        echo $cedula." ".$id_cod." ".$tipo_ingreso;
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
