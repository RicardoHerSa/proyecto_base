<?php

namespace App\Modules\Ubicaciones\Horario\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $consultHorarios = DB::table('ohxqc_horarios')->select('descripcion')->get();
        return view('Ubicaciones::horarios', compact('consultHorarios'));
    }

    public function gestionarHorario(Request $request)
    {
        $display = 0;
        $activo = $request->input("ck_activo");
        $id_horario = $request->input("id_horario");
        $consultHorarios = DB::table('ohxqc_horarios')->select('descripcion')->get();


        if($request->input('btn_guardar') != null){

            $desc=$request->input("descripcion");
            $ids_dias=$request->input("dias");
            $hora_inicio=$request->input("hora_inicio");
            $hora_fin=$request->input("hora_fin");
            
            //echo "DESC: ".$desc."<BR>DIAS: ".$ids_dias."<BR> INI: ".$hora_inicio." ".$hora_fin;

            if($desc!="" && $ids_dias!=""){
                $inserta= HorarioController::registroHorario($desc,$ids_dias,$hora_inicio,$hora_fin,$activo,$id_horario);

                $id_horario="";
                if($inserta == false){
                     $registrado = false;
                }else{
                    $registrado = true;
                    
                    $consultHorarios = DB::table('ohxqc_horarios')->select('descripcion')->get();
                    return view('Ubicaciones::horarios', compact('id_horario', 'registrado','consultHorarios')); 
                }
            }else{
                $incompletos = true;
                return view('Ubicaciones::horarios', compact('id_horario', 'incompletos','consultHorarios')); 
            }
                    
        }else if($request->input('btn_consulta')){
            $display = 1;
            $descripcion = $request->input("descripcion");
            if($descripcion == ""){
                $descripcion = $request->input("descHorario");
            }
            $horas = HorarioController::consultaHorario($descripcion);
            
            $hora_inicio = $horas[0];
            $hora_fin = $horas[1];
            $activo = $horas[2];
            $id_horario = $horas[3];
            
            if($hora_inicio !="" && $hora_fin !=""){
                 $dias = HorarioController::consultaDiasHorario($descripcion);
                
                 return view('Ubicaciones::horarios', compact('dias', 'hora_inicio', 'hora_fin', 'activo', 'id_horario', 'descripcion', 'display','consultHorarios'));
            }else{
                $display = 0;
                $sinResult = true;
                return view('Ubicaciones::horarios', compact('sinResult', 'display','consultHorarios'));
            }
        }else if($request->input('btn_nuevo')){
            $display=1;
	        $id_horario="";
            $nuevo = true;
            return view('Ubicaciones::horarios', compact('id_horario','display','nuevo','consultHorarios'));

        }else if($request->input('btn_cancelar')){
            $id_horario="";
            
            return view('Ubicaciones::horarios', compact('id_horario','consultHorarios'));
        }
        
    }

    public function registroHorario($descripcion,$ids_dias,$hora_inicio,$hora_fin,$activo,$id_horario)
    {
        
        $inserta = false;
        if($descripcion !=""){

            if($id_horario == ""){
                $insertaH = DB::table('ohxqc_horarios')->insert([
                    'descripcion'  => $descripcion,
                    'usuario_creacion' => 'admin',
                    'fecha_creacion' => now(),
                    'usuario_actualizacion' => 'admin',
                    'fecha_actualizacion' =>  now(),
                    'activo' => 'S'
                ]);
                if($insertaH){$inserta = true;}
           
                //consulta el id del horario ya insertado
                $consultaIdHorario = DB::table('ohxqc_horarios')->max('id');
             
                    $id_horario = $consultaIdHorario;
                
            
                //Se borran registros anteriores
                $eliminaRegistros = DB::table('ohxqc_dias')->where('id_horario', '=', $id_horario)->delete();
               
                //se insertan los dias que se afectan
                $array=explode("-",$ids_dias);
                for($i=0;$i<count($array);$i++){
                    if($array[$i] !=""){
                        $insertaD = DB::table('ohxqc_dias')->insert([
                            'id_horario' => $id_horario, 
                            'descripcion' => $array[$i]
                        ]);
                        if($insertaD){$inserta = true;}
                    }
                   
                }
                $consultaIdHora = DB::table('ohxqc_horas')
                ->select('id_hora')
                ->where('id_horario', '=', $id_horario)
                ->get();
                 $id_h = "";
                 foreach($consultaIdHora as $cons){
                    $id_h = $cons->id_hora;
                 }
           
                if($id_h == ""){
                //Se inserta horario
                $insertaHorario = DB::table('ohxqc_horas')->insert([
                    'id_horario' => $id_horario,
                    'hora_inicio' => $hora_inicio, 
                    'hora_fin' => $hora_fin
                ]);
                if($insertaHorario){$inserta = true;}
              
                }else{
                    $actualiza = DB::table('ohxqc_horas')->where('id_hora', '=', $id_h)->update([
                        'hora_inicio' => $hora_inicio,
                        'hora_fin' => $hora_fin
                    ]);
                }	
            }else{
                    if($activo=="1"){
                        //echo "ENTRA";
                        $actuHorario = DB::table('ohxqc_horarios')->where('id', '=', $id_horario)->update([
                            'descripcion' => $descripcion, 
                            'fecha_actualizacion' => now(), 
                            'activo' => 'S'
                        ]);
                        if($actuHorario){$inserta = true;}
                        
                        $actuHora = DB::table('ohxqc_horas')->where('id_horario', '=', $id_horario)->update([
                            'hora_inicio' => $hora_inicio, 
                            'hora_fin' => $hora_fin, 
                        ]);
                        if($actuHora){$inserta = true;}
                        
                        //Se borran registros anteriores
                        $eliminaDias = DB::table('ohxqc_dias')->where('id_horario', '=', $id_horario )->delete();
                       
                        //se insertan los dias que se afectan
                        $array=explode("-",$ids_dias);
                        for($i=0;$i<count($array);$i++){
                            if($array[$i] !=""){
                                $insertaDia = DB::table('ohxqc_dias')->insert([
                                    'id_horario' => $id_horario, 
                                    'descripcion' => $array[$i]
                                ]);
                                if($insertaDia){$inserta = true;}
                                
                            }
                        }
                    
                    }else{
                        $actualizaHorario = DB::table('ohxqc_horarios')->where('id', '=', $id_horario)->update([
                            'descripcion'=> $descripcion, 
                            'fecha_actualizacion' => now(), 
                            'activo' => 'N'
                        ]);
                        if($actualizaHorario){$inserta = true;}

                        $actualizaHora = DB::table('ohxqc_horas')->where('id_horario', '=', $id_horario)->update([
                            'hora_inicio'=> $hora_inicio, 
                            'hora_fin' => $hora_fin, 
                            'activo' => 'N'
                        ]);
                        if($actualizaHora){$inserta = true;}
                        
                            //Se borran registros anteriores
                            $elimina = DB::table('ohxqc_dias')->where('id_horario', '=', $id_horario)->delete();
                        
                            //se insertan los dias que se afectan
                            $array=explode("-",$ids_dias);
                            for($i=0;$i<count($array);$i++){
                                if($array[$i] !=""){
                                    $insertaDia = DB::table('ohxqc_dias')->insert([
                                        'id_horario' => $id_horario, 
                                        'descripcion' => $array[$i]
                                    ]);
                                    if($insertaDia){$inserta = true;}
                                }
                            }
                                    
                    }
                 }
         }
         
         return $inserta;
    }

    public  function consultaHorario($descripcion)
    {
        $consulta = DB::table('ohxqc_horarios as h')
        ->select('h.descripcion', 'hs.hora_inicio', 'hs.hora_fin', 'h.activo', 'h.id')
        ->join('ohxqc_horas as hs', 'hs.id_horario', '=', 'h.id')
        ->where('h.descripcion', '=', $descripcion)
        ->get();
        $row = array();
         foreach($consulta as $c){
            $row[0] = $c->hora_inicio;
            $row[1] = $c->hora_fin;
            $row[2] = $c->activo;
            $row[3] = $c->id;
         }
         
         return $row;
    }

    public function consultaDiasHorario($descripcion)
    {
        $datos="";
        $consulta = DB::table('ohxqc_horarios as h')
        ->select('d.descripcion as id_dia')
        ->join('ohxqc_dias as d', 'd.id_horario', '=', 'h.id')
        ->where('h.descripcion', '=', $descripcion)
        ->orderBy('id_dia')
        ->get();

        foreach($consulta as $c){
            $datos.=$c->id_dia."-";
        }
        
        return $datos;
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
