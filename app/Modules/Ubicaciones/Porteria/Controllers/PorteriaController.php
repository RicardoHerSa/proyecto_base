<?php

namespace App\Modules\Ubicaciones\Porteria\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PorteriaController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            
        $consultaUbicaciones = DB::table('ohxqc_ubicaciones')
        ->select('id_ubicacion', 'id_padre', 'descripcion', 'activo')
        ->get();
       // var_dump($consultaUbicaciones);
        foreach($consultaUbicaciones as $cons){
            $treearr[]=array($cons->id_ubicacion,$cons->id_padre,$cons->descripcion,$cons->activo);
        }

        for($i=0;$i<count($treearr);$i++){
            $dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][3]);
            }

        $dataTree=json_encode($dataT);

       // echo $dataTree;
     
		
        return view('Ubicaciones::porteria', compact('dataTree'));
    }

    public function actualizarNodo(Request $request)
    {
        $nombre = $request->input("nombre_nodo");
		$ids = $request->input("id_nodos");
		$activo = $request->input("ub_activo");
		$respuesta = PorteriaController::updateNode($ids,$nombre,$activo);
        $res=json_encode($respuesta);
        if($res == 1){
            $notiActualizacion = true;
        }else{
            $notiActualizacion = false;
        }
         //echo "NOMBRE: ".$nombre."<br>IDS: ".$ids."<br>Activo: ".$activo;

        $consultaUbicaciones = DB::table('ohxqc_ubicaciones')
        ->select('id_ubicacion', 'id_padre', 'descripcion', 'activo')
        ->get();
       // var_dump($consultaUbicaciones);
        foreach($consultaUbicaciones as $cons){
            $treearr[]=array($cons->id_ubicacion,$cons->id_padre,$cons->descripcion,$cons->activo);
        }

        for($i=0;$i<count($treearr);$i++){
            $dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][3]);
            }

        $dataTree=json_encode($dataT);

        return view('Ubicaciones::porteria', compact('res', 'dataTree', 'notiActualizacion'));
    }

    public function updateNode($ids,$nombre,$activo)
    {
		$array=explode("-",$ids);
		if(sizeof($array)>1){
            $n=sizeof($array)-2;
            $id=$array[$n];
            if($activo !="" && $nombre!=""){
                $actualiza = DB::table('ohxqc_ubicaciones')->where('id_ubicacion', '=', $id)->update([
                    'descripcion' => $nombre,
                    'activo' => $activo
                ]);
                
            }else if($activo !="" && $nombre==""){
                $actualiza = DB::table('ohxqc_ubicaciones')->where('id_ubicacion', '=', $id)->update([
                    'activo' => $activo
                ]);
            }else if($activo=="" && $nombre!=""){
                $actualiza = DB::table('ohxqc_ubicaciones')->where('id_ubicacion', '=', $id)->update([
                    'descripcion' => $nombre,
                    'activo' => 'N'
                ]);
            }else if ($activo=="" && $nombre==""){
                $actualiza = DB::table('ohxqc_ubicaciones')->where('id_ubicacion', '=', $id)->update([
                    'activo' => 'N'
                ]);
            }

            if($actualiza){

                return 1;
            }else{
                 return null;
            }
		//return $result;
		}else{
            return null;
        }
	}

    public function registrarNodo(Request $request)
    {
        $nombre = $request->input("nuevo_nodo");
		$ids = $request->input("id_nodos2");
        $campo = [
            'id_nodos2' => 'required'
        ];
        $mensaje = ['required' => 'Por favor seleccione un nodo padre'];
        $this->validate($request,$campo,$mensaje);
        //echo $nombre." ".$ids;
		$respuesta= PorteriaController::addNode($ids,$nombre);
        $res=json_encode($respuesta);
        if($res == 1){
            $notiRegistro = true;
        }else{
            $notiRegistro = false;
        }

        $consultaUbicaciones = DB::table('ohxqc_ubicaciones')
        ->select('id_ubicacion', 'id_padre', 'descripcion', 'activo')
        ->get();
       // var_dump($consultaUbicaciones);
        foreach($consultaUbicaciones as $cons){
            $treearr[]=array($cons->id_ubicacion,$cons->id_padre,$cons->descripcion,$cons->activo);
        }

        for($i=0;$i<count($treearr);$i++){
            $dataT[]=array('id'=>$treearr[$i][0],'parentid'=>$treearr[$i][1],'text'=>$treearr[$i][2],'value'=>$treearr[$i][3]);
            }

        $dataTree=json_encode($dataT);

        return view('Ubicaciones::porteria', compact('res', 'dataTree', 'notiRegistro'));
    }

    public static function addNode($ids,$nombre)
    {
        $array=explode("-",$ids);
        if(sizeof($array)>1){
            $n=sizeof($array)-2;
            $id_padre=$array[$n];

            $maxid = DB::table('ohxqc_ubicaciones')->max('id_ubicacion') + 1;
            
            $inserta = DB::table('ohxqc_ubicaciones')->insert([
                'id_ubicacion' => $maxid,
                'id_padre' => $id_padre,
                'descripcion' => $nombre,
                'activo' => 'S',
                'usuario_creacion' => 'admin',
                'fecha_creacion' => now(),
                'usuario_actualizacion' => 'admin',
                'fecha_actualizacion' => now()
            ]);
           
            if($inserta){
                return 1;
            }else{
                 return null;
            }
            
        }else{
            return null;
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
