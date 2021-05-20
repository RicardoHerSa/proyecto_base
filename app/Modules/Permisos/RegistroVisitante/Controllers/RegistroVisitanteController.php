<?php

namespace App\Modules\Permisos\RegistroVisitante\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistroVisitanteController extends Controller
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
        ->select('codigo_empresa', 'descripcion')
        ->whereIn('codigo_empresa', ['42681','119','24305','27027','130','21679','115','128','32506','701','702','703','705','706','707','708','709','710','711','713','714','716','718','720','721','722','723','724','725','726','727','728','729','730','732','733','734','735','736','737','738','739','740','742','743','744',
        '745','129','132'])
        ->orderBy('descripcion')
        ->get();

       $horarios = DB::table('ohxqc_horarios')
       ->select('id', 'descripcion')
       ->get(); 

       $sedes = DB::table('ohxqc_sedes')
       ->get();

        return view('Permisos::registroVisitante', compact('empresas', 'horarios', 'sedes'));
    }

    function consultarHora(Request $request)
    {
        $idHorario = $request->input('idhorario');

        $horas = DB::table('ohxqc_horas')
        ->select(DB::raw("concat(hora_inicio,'-',hora_fin) as hora"))
        ->where('id_horario', '=', $idHorario)
        ->get();
        foreach($horas as $hrs){
            echo $hrs->hora;
        }
    }

    public function registrarVisitante(Request $request)
    {

        // Recibe información del solicitante
        $solicitante = $request->input('solicitante');
        $tipoIngreso = $request->input('tipoIngreso');
        $tipoId = $request->input('tipoId');
        if($request->input('empresaContratista') != null){
         $empresaContratista = $request->input('empresaContratista');
        }else{
            $empresaContratista = 0;
        }
        $arraySolicitantes[] = array('solicitante'=>$solicitante,'tipoIngreso'=>$tipoIngreso,'tipoId'=>$tipoId,'empresaContratista'=>$empresaContratista);
        echo "<br><hr>".json_encode($arraySolicitantes);

        //Recibe información de anexos
        $cantidadAnexos = $request->input('cantR');
        if($cantidadAnexos > 0){
            //se recibe el primer registro que es obligatorio
            $arrayRegistros[] = array('cedula'=>$request->input('cedula'), 'nombre'=>$request->input('nombre'), 'anexo'=> $request->input('anexo'));

            for($i=1; $i <= $cantidadAnexos; $i++){
                //Mientras sea diferente de null los registros, los guardo en el array
                if($request->input('cedula'.$i) != null){
                    $arrayRegistros[] = array('cedula'=> $request->input('cedula'.$i),  'nombre'=>$request->input('nombre'.$i), 'anexo'=> $request->input('anexo'.$i));
                }
            }
            echo "<br><hr>LISTADO DE REGISTROS: <br>".json_encode($arrayRegistros);
        }else{
            echo "<br><hr>Entra por else, por ende hay un solo registro:<br>";
            $arrayRegistros[] = array('cedula'=> $request->input('cedula'), 'nombre'=>$request->input('nombre'), 'anexo'=> $request->input('anexo'));
            echo json_encode($arrayRegistros);
        }

        //Recibe informacion de fechas de ingreso y final
        $fechaInicio = $request->input('fechaIngreso');
        $fechaFin = $request->input('fechaFin');
        $horario = $request->input('horario');
        $hora = $request->input('hora');
        $empVisi = $request->input('empVisi');
        $ciudad = $request->input('ciudad');
        $arrayFechas[] = array('fechaIngreso'=>$fechaInicio,'fechaFin'=>$fechaFin,'horario'=>$horario,'hora'=>$hora,'empVisi'=>$empVisi,'ciudad'=>$ciudad);
        echo "<br><hr>".json_encode($arrayFechas);


        //Recibe información de las sedes
        $cantidadSedes = $request->input('cantRSelects');
        if($cantidadSedes > 0){
           //Se recibe la primer sede que es obligatoria
            $arraySedes[] = array('id_sede'=>$request->input('sede'));
            for($i=1; $i <= $cantidadSedes; $i++){
                $arraySedes[] = array('id_sede'=>$request->input('sede'.$i));
            }
            echo "<br><hr>".json_encode($arraySedes);
        }else{
            echo "<br><hr>Entra por else, por ende hay un solo registro: <br> ";
            $arraySedes[] = array('id_sede'=>$request->input('sede'));
            echo json_encode($arraySedes);
        }

        //Recibe labor a realizar
        $labor = $request->input('labor');
        echo "<br><hr>".json_encode($labor);

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
