<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class companyController extends Controller
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
       return view('company.index', compact('empresas'));
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

        return view('company.create', compact('sedes', 'ciudades'));
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
        ->select('emp.descripcion', 'emp.activo', 'emp.grupo_carvajal')
        ->where('codigo_empresa',$codigoEmpresa)
        ->get();
        foreach($empresa as $emp){
            $nombre = $emp->descripcion;
            $estado = $emp->activo;
            $grupo = $emp->grupo_carvajal;

        }
        if($estado == "S"){ $estado = "Activo";}else{$estado = "Inactivo";}
        if($grupo == 1){$grupo = "SI";}else{$grupo = "NO";}

        $sedesAsociadas = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion', 'ubi.id_ubicacion')
        ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
        ->where('emp.codigo_empresa', $codigoEmpresa)
        ->get();

        return view('company.show', compact('nombre','estado','grupo','codigoEmpresa','sedesAsociadas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $Request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Request $Request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request  $Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $Request)
    {
        //
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
            'grupo_carvajal' =>$grupo,
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
}
