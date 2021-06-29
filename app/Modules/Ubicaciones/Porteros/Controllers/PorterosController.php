<?php

namespace App\Modules\Ubicaciones\Porteros\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PorterosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
       return view('Ubicaciones::inicioPortero');
    }

    /**
     * Show the form for Requesteating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sedes = DB::table('ohxqc_ubicaciones')->select('id_ubicacion', 'descripcion')->where('id_padre', 1)->get();
        return view('Ubicaciones::crearPortero', compact('sedes'));
    }

    /**
     * Store a newly Requesteated resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = $request->except('_token');

        $insertar = DB::table('ohxqc_porteros')->insert([
            'id' => DB::table('ohxqc_porteros')->max('id') + 1,
            'usuario' => $datos['usuario'],
            'activo' => $datos['estado'],
            'tipo' => $datos['tipo'],
            'id_sede' => $datos['sede'],
        ]);

        if($insertar){
            return redirect('Porteros/create')->with('msj', 'ok');
        }else{
            return redirect('Porteros/create')->with('msj', 'error');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $porteros = DB::table('ohxqc_porteros as por')
        ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_sede')
        ->where('id', $id)
        ->get();

        $porteriasAsociadas = DB::table('ohxqc_porteros_ubicaciones as por')
        ->select('ubi.descripcion')
        ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_ubicacion')
        ->where('id_portero', $id)
        ->get();

        return view('Ubicaciones::verPortero', compact('porteros', 'porteriasAsociadas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $portero = DB::table('ohxqc_porteros as por')
        ->select('por.activo', 'por.tipo', 'por.usuario', 'por.id', 'por.id_sede')
        ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_sede')
        ->where('id', $id)
        ->get();

        $sedes = DB::table('ohxqc_ubicaciones')->select('id_ubicacion','descripcion')->where('id_padre', 1)->get();

        
        $porteriasAsociadas = DB::table('ohxqc_porteros_ubicaciones as por')
        ->select('ubi.descripcion', 'ubi.id_ubicacion')
        ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_ubicacion')
        ->where('id_portero', $id)
        ->get();

        return  view('Ubicaciones::editarPortero', compact('portero', 'sedes', 'porteriasAsociadas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       $datos =  $request->except(['_token','_method']);
        
        $actualizar = DB::table('ohxqc_porteros')->where('id', $datos['id'])->update([
            'usuario' => $datos['usuario'],
            'activo' => $datos['estado'],
            'tipo' => $datos['tipo'],
            'id_sede' => $datos['sede'],
        ]);
        if($actualizar){
            return redirect()->to('Porteros/'.$datos['id'].'/edit')->with('msj', 'ok');
        }else{
            return redirect()->to('Porteros/'.$datos['id'].'/edit')->with('msj', 'error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(DB::table('ohxqc_porteros')->where('id', $id)->delete()){
                return redirect('Porteros')->with('msj', 'ok');
        }else{
                return redirect('Porteros')->with('msj', 'err');
        }
    }

    public function eliminarPortero(Request $request)
    {
        $id = $request->input('id');
        if(DB::table('ohxqc_porteros')->where('id', $id)->delete()){
            echo 1;
        }else{
            echo 2;
        }
    }

    public function actualizarEstado(Request $request)
    {
        $id = $request->input('id');
        $estado = $request->input('estado');
        DB::table('ohxqc_porteros')->where('id', $id)->update([
            'activo' => $estado
        ]);
    }

    public function consultarNombreUsuario(Request $request)
    {
        $usuario = $request->input('usuario');

        $nombre = DB::table('ohxqc_porteros')->select('usuario')->where('usuario', $usuario)->get();
        if(count($nombre) > 0){
           echo 1;
        }else{
            echo "n";
        }
    }


    public function consultarPorteros()
    {
        $porteros = DB::table('ohxqc_porteros as por')
        ->select('por.id', 'por.usuario', 'por.activo as estado', 'por.tipo', 'por.id_sede', 'ubi.descripcion')
        ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_sede')
        ->orderBy('por.activo', 'desc')
        ->get();

        $data = Array();
        $i = 0;     
        foreach($porteros as $por){
           
            if($por->estado == "S"){
                $input = " <div style='cursor: pointer;' class='custom-control custom-switch'>
                <input onchange='cambiarEstado($por->id)'  type='checkbox' checked class='custom-control-input' id='estado".$por->id."' value='s'>
                <label class='custom-control-label' for='estado".$por->id."'></label>

             </div> ";
            }else{
                $input = " <div style='cursor: pointer;' class='custom-control custom-switch'>
                <input onchange='cambiarEstado($por->id)'  type='checkbox' class='custom-control-input' id='estado".$por->id."' value='n'>
                <label class='custom-control-label' for='estado".$por->id."'></label>

             </div> ";
            }
            $urlShow = "/Porteros/$por->id";
            $urlEdit = "/Porteros/$por->id/edit";
            $onclick = "eliminarPortero($por->id)";
            $data[]= array(
                "0"=>$por->usuario,
                "1"=>$por->tipo,
                "2"=>$input,
                "3"=>$por->descripcion,
                "4"=>" <a class='show-user' href='".$urlShow."' title='Info Portero'><button class='btn btn-info btn-sm'><i class='fa fa-eye'></i></button></a>
                <a class='edit-user' href='".$urlEdit."' title='Editar Portero'><button class='btn btn-warning btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>
                <button onclick='".$onclick."' class='btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i></button>" 
            );
            $i++;
        } 
        $results = array(
            "eEcho"=>1, //Informarcion para el datatable
            "iTotalRecors"=>count($data),//enviamos el total de registros  al datatable
            "iTotalDisplayRescors"=>count($data),//enviamos el total de registros a vizualizar
            "aaData"=>$data
        );
        echo json_encode($results);
    }

    public function asociarPorterias()
    {
        $porteros = DB::table('ohxqc_porteros')->select('id', 'usuario')->where('activo', 'S')->where('id_sede', '>', 0)->get(); 

        return view('Ubicaciones::asociarPorterias', compact('porteros'));
    }

    public function porteriasDisponibles(Request $request)
    {
        $id = $request->input('id');
        $sedePertenece = DB::table('ohxqc_porteros')->select('id_sede')->where('id', $id)->get();

        $porteriasActuales = DB::table('ohxqc_porteros_ubicaciones')->select('id_ubicacion')->where('id_portero', $id)->get();
        
        $nombreSede = DB::table('ohxqc_ubicaciones')->select('descripcion')->where('id_ubicacion', $sedePertenece[0]->id_sede)->get();

        //saber si tiene porterias asociadas
        if(count($porteriasActuales) > 0){
            
            $arrayActuales = array();
            foreach($porteriasActuales as $actual){
                array_push($arrayActuales, $actual->id_ubicacion);
            }

            if($sedePertenece[0]->id_sede == 1){
                $porteriasNuevas = DB::table('ohxqc_ubicaciones')
                ->select('id_ubicacion', 'descripcion')
                ->where('activo', 'S')
                ->whereIn('id_padre', [6,15,11,96,35,43,87,91,2] )
                ->orderBy('descripcion')
                ->get();
            }else{
                $porteriasNuevas = DB::table('ohxqc_ubicaciones')
                ->select('id_ubicacion', 'descripcion')
                ->where('activo', 'S')
                ->where('id_padre', $sedePertenece[0]->id_sede)
                ->whereNotIn('id_ubicacion', $arrayActuales)
                ->orderBy('descripcion')
                ->get();
            }

           

        }else{
            if($sedePertenece[0]->id_sede == 1){
                $porteriasNuevas = DB::table('ohxqc_ubicaciones')
                ->select('id_ubicacion', 'descripcion')
                ->where('activo', 'S')
                ->whereIn('id_padre', [6,15,11,96,35,43,87,91,2] )
                ->orderBy('descripcion')
                ->get();
            }else{
                //si no tiene porterias asociadas se muestran todas de acuerdo a la sede a la que pertenece
                $porteriasNuevas = DB::table('ohxqc_ubicaciones')
                ->select('id_ubicacion', 'descripcion')
                ->where('activo', 'S')
                ->where('id_padre', $sedePertenece[0]->id_sede)
                ->orderBy('descripcion')
                ->get();
            }
        
        }   

        foreach($porteriasNuevas as $nuevas){
            echo 
            " <option value='".$nuevas->id_ubicacion."'>".$nuevas->descripcion." - (".$nombreSede[0]->descripcion.")</option>
            ";
        }
    }

    public function guardarAsociacion(Request $request)
    {
        $datos = $request->except(['_token']);

        $insertar = DB::table('ohxqc_porteros_ubicaciones')->insert([
         'id' => DB::table('ohxqc_porteros_ubicaciones')->max('id')+1,
         'id_portero' => $datos['usuario'],
         'id_ubicacion' => $datos['porteria']   
        ]);
        if($insertar){
            return redirect('asociarporterias')->with('msj', 'ok');
        }else{
            return redirect('asociarporterias')->with('msj', 'error');
        }

    }

    public function eliminarPorteria(Request $request)
    {
        $id = $request->input('id');
        $idUsu = $request->input('usu');
        $elimina = DB::table('ohxqc_porteros_ubicaciones')->where('id_ubicacion', $id)->delete();
        if($elimina){
            $porteriasAsociadas = DB::table('ohxqc_porteros_ubicaciones as por')
            ->select('ubi.descripcion', 'ubi.id_ubicacion')
            ->join('ohxqc_ubicaciones as ubi', 'ubi.id_ubicacion', 'por.id_ubicacion')
            ->where('id_portero', $idUsu)
            ->get();
            foreach($porteriasAsociadas as $aso){
                    echo "
                    <tr>    
                        <td>".$aso->descripcion."</td>
                        <td><button class='btn btn-danger' type='button' onclick='eliminarPorteria(".$aso->id_ubicacion.")'> <i class='fa fa-trash'></i></button></td>
                    </tr>
                    ";
            }
        }else{
            echo 2;
        }
    }
}
