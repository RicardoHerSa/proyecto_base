<?php

namespace App\Modules\Ubicaciones\Empresas\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class EmpresasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = DB::table('ohxqc_empresas')->select(DB::raw("DISTINCT(codigo_empresa)"), 'descripcion', 'activo')->orderBy('descripcion','asc')
        ->get();
       // var_dump($empresas);
       return view('Ubicaciones::inicioEmpresa', compact('empresas'));
    }

    /**
     * Show the form for Requesteating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sedes = DB::table('ohxqc_ubicaciones')->select('id_ubicacion', 'descripcion')->where('id_padre', 1)->get();
        $ciudades = DB::table('ohxqc_sedes')->get();

        return view('Ubicaciones::crearEmpresa', compact('sedes', 'ciudades'));
    }

    /**
     * Store a newly Requesteated resource in storage.
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
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function show($codigoEmpresa)
    {
        $empresa = DB::table('ohxqc_empresas as emp')
        ->select('emp.descripcion', 'emp.activo', 'emp.tipo_empresa')
        ->where('codigo_empresa',$codigoEmpresa)
        ->get();
        foreach($empresa as $emp){
            $nombre = $emp->descripcion;
            $estado = $emp->activo;
            $grupo = $emp->tipo_empresa;

        }
        if($estado == "S"){ $estado = "Activo";}else{$estado = "Inactivo";}
        if($grupo == 'CARVAJAL'){$grupo = "SI";}else{$grupo = "NO";}

        $sedesAsociadas = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion', 'ubi.id_ubicacion')
        ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
        ->where('emp.codigo_empresa', $codigoEmpresa)
        ->get();

        return view('Ubicaciones::verEmpresa', compact('nombre','estado','grupo','codigoEmpresa','sedesAsociadas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function edit($codigoEmpresa)
    {
        $empresa = DB::table('ohxqc_empresas')->where('codigo_empresa',$codigoEmpresa)->get();

        $sedesAsociadas = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion', 'ubi.id_ubicacion')
        ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
        ->where('emp.codigo_empresa', $codigoEmpresa)
        ->get();

        return  view('Ubicaciones::editarEmpresa', compact('empresa', 'codigoEmpresa', 'sedesAsociadas'));
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
        //var_dump($datos);
        //echo $datos['codigo'];
        $actualizar = DB::table('ohxqc_empresas')->where('codigo_empresa', $datos['antiguo'])->update([
            'id_empresa' => $datos['codigo'],
            'codigo_empresa' => $datos['codigo'],
            'descripcion' => $datos['nombre'],
            'activo' => $datos['estado'],
            'tipo_empresa' => $datos['grupo'],
            'usuario_actualizacion' => auth()->user()->username,
            'fecha_actualizacion' => now()
        ]);
        if($actualizar){
            return redirect()->to('Empresas/'.$datos['codigo'].'/edit')->with('msj', 'ok');
        }else{
            return redirect()->to('Empresas/'.$datos['codigo'].'/edit')->with('msj', 'error');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function destroy($codigoEmpresa)
    {
        if(DB::table('ohxqc_empresas')->where('codigo_empresa', $codigoEmpresa)->delete()){
                return redirect('Empresas')->with('msj', 'ok');
        }else{
                return redirect('Empresas')->with('msj', 'err');
        }
    }

    public function eliminarEmpresa(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');
        if(DB::table('ohxqc_empresas')->where('codigo_empresa', $codigoEmpresa)->delete()){
            echo 1;
        }else{
            echo 2;
        }
    }

    public function actualizarEstado(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');
        $estado = $request->input('estado');
        DB::table('ohxqc_empresas')->where('codigo_empresa', $codigoEmpresa)->update([
            'activo' => $estado
        ]);
    }

    public function consultarNombreEmpresa(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');

        $nombre = DB::table('ohxqc_empresas')->select(DB::raw('DISTINCT(descripcion)'))->where('codigo_empresa', $codigoEmpresa)->get();
        if(count($nombre) > 0){
            foreach($nombre as $nom){
                echo $nom->descripcion;
            }
        }else{
            echo "n";
        }
    }

    public function consultarSedesEmpresa(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');
        $listSedes = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion as sed', 'ubi.id_ubicacion')
        ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
        ->where('emp.codigo_empresa', $codigoEmpresa)
        ->get();

        if(count($listSedes) > 0){
            foreach($listSedes as $sedes){
               echo "<tr> 
                <td>".$sedes->sed."</td>
                <td><button onclick='eliminarSede(".$sedes->id_ubicacion.", ".$codigoEmpresa.")' class='btn btn-danger'><i class='fa fa-trash'><i></button></td>
               
               </tr>";
            }
        }else{
            echo "n";
        }
    }

    public function actualizarSedesEmpresa(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');
        $opcion = $request->input('opcion');

        if($opcion == 2){
            $sedes = DB::table('ohxqc_ubicaciones')->select('id_ubicacion', 'descripcion')->where('id_padre', 1)->get();
            foreach($sedes as $todas){
                echo "
                <option value='".$todas->id_ubicacion."'>".$todas->descripcion."</option>
                ";
            }
        }else{
            $listSedes = DB::table('ohxqc_ubicaciones as ubi')
            ->select('ubi.id_ubicacion')
            ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
            ->where('emp.codigo_empresa', $codigoEmpresa)
            ->get();
            $arraySedes = array();
            $i = 0;
            foreach($listSedes as $listado){
                $arraySedes[$i] = $listado->id_ubicacion;
                $i++;
            }

            $actulizaSedes = DB::table('ohxqc_ubicaciones')
            ->select('id_ubicacion', 'descripcion')
            ->whereNotIn('id_ubicacion',$arraySedes)
            ->where('id_padre', 1)
            ->get();
            foreach($actulizaSedes as $actu){
                echo "
                 <option value='".$actu->id_ubicacion."'>".$actu->descripcion."</option>
                ";
            }
        }
      
    }

    public function registrarEmpresa(Request $request)
    {
        $codigoEmpresa = $request->input('codigo');
        $nombre = $request->input('nombre');
        $grupo = $request->input('grupo');
        $sede = $request->input('sede');
        $estado = $request->input('estado');

        $inserta = DB::table('ohxqc_empresas')->insert([
            'id_empresa' => $codigoEmpresa,
            'codigo_empresa' => $codigoEmpresa,
            'descripcion' => $nombre,
            'activo' => $estado,
            'usuario_creacion' => 'admin',
            'fecha_creacion' => now(),
            'usuario_actualizacion' => 'admin',
            'fecha_actualizacion' => now(),
            'id_sede' => 0,
            'id_siso' => 0,
            'tipo_empresa' =>$grupo,
            'sede_especifica_id' => $sede
        ]);

        if($inserta){
            echo true;
        }else{
            echo false;
        }
    }

    public function eliminarSede(Request $request)
    {
        $empresa = $request->input('empresa');
        $sede = $request->input('sede');
        //contar cuantas sedes tiene la empresa, si solo tiene una , ponemos null a la sede_especifica_id
        $cant = DB::table('ohxqc_empresas')->where('codigo_empresa', $empresa)->count();
        if($cant == 1){
           $actualiza = DB::table('ohxqc_empresas')->where('codigo_empresa', $empresa)->where('sede_especifica_id', $sede)->update([
                'sede_especifica_id' => null
            ]);
            if($actualiza){
                echo 1;
            }else{
                echo 2;
            }
        }else{
            //Si hay mas de una se pùede eliminar el registro
            if(DB::table('ohxqc_empresas')->where('codigo_empresa', $empresa)->where('sede_especifica_id', $sede)->delete()){
                echo 1;
            }else{
                echo 2;
            }
        }
    }

    public function consultarEmpresas()
    {
        $empresas = DB::table('ohxqc_empresas')->select(DB::raw("DISTINCT(codigo_empresa) as code"), 'descripcion', 'activo')->orderBy('descripcion','asc')
        ->get();

        $data = Array();
        $i = 0;     
        foreach($empresas as $emp){
            $sedes = DB::table('ohxqc_ubicaciones as ubi')
            ->select('ubi.descripcion')
            ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
            ->where('emp.codigo_empresa', $emp->code)
            ->get();
            $cant = count($sedes);
           
            $asociada = array();
            foreach($sedes as $se){
                array_push($asociada, $se->descripcion);
            }
            $implode = implode(',', $asociada);
            if($emp->activo == "S"){
                $input = " <div style='cursor: pointer;' class='custom-control custom-switch'>
                <input onchange='cambiarEstado($emp->code)'  type='checkbox' checked class='custom-control-input' id='estado".$emp->code."' value='s'>
                <label class='custom-control-label' for='estado".$emp->code."'></label>

             </div> ";
            }else{
                $input = " <div style='cursor: pointer;' class='custom-control custom-switch'>
                <input onchange='cambiarEstado($emp->code)'  type='checkbox' class='custom-control-input' id='estado".$emp->code."' value='n'>
                <label class='custom-control-label' for='estado".$emp->code."'></label>

             </div> ";
            }
            $urlShow = "/Empresas/$emp->code";
            $urlEdit = "/Empresas/$emp->code/edit";
            $onclick = "eliminarEmpresa($emp->code)";
            $data[]= array(
                "0"=>$emp->descripcion,
                "1"=>$emp->code,
                "2"=>$input,
                "3"=>$implode,
                "4"=>" <a class='show-user' href='".$urlShow."' title='Info empresa'><button class='btn btn-info btn-sm'><i class='fa fa-eye'></i></button></a>
                <a class='edit-user' href='".$urlEdit."' title='Editar empresa'><button class='btn btn-warning btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button></a>
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
}
