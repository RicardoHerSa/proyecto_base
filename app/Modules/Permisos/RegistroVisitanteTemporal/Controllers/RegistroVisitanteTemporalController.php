<?php

namespace App\Modules\Permisos\RegistroVisitanteTemporal\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistroVisitanteTemporalController extends Controller
{
    public $cedula = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Permisos::registroTemporal');
    }

    public function consultaVisitanteTemporal(Request $request)
    {
        $id_entrada = $request->input('tx_cedula');
        $id_entrada= trim($id_entrada);

        $ini = substr($id_entrada,0,1);
        if($ini == 'R' || $ini == 'V' || $ini == 'C'){

            $cedula = DB::table('ohxqc_visitantes as v')
            ->select('v.identificacion')
            ->join('ohxqc_codigobidimensional as cb', 'cb.id_visitante', '=', 'v.id_visitante')
            ->where('cb.codigo', '=', $id_entrada)
            ->where('cb.activo', '=', 'S')
            ->limit(1)
            ->get();
            if(count($cedula) > 0){
                foreach($cedula as $ce){
                    $cedula = $ce->identificacion;
                }
            }

        }else{
            $cedula = $id_entrada;
        }

        
        $session = session('cedula', $cedula);
        $cedula_registro= $cedula;

        if($ini != 'R' && $ini != 'V' && $ini != 'C'){
            $continuar= RegistroVisitanteTemporalController::validaVisitante($cedula);	
           
        }else{
            $continuar = 0;
        }

        if($continuar == 0){
            $tabla="";
            $consulta = DB::table('ohxqc_visitantes as v')
            ->select('v.nombre', 'v.apellido', 'v.identificacion', 'e.descripcion as empresa', 'foto', 'cod.vehiculo','cod.placa', 'v.responsable')
            ->join('ohxqc_empresas_visitante as emp', 'emp.id_visitante', '=', 'v.id_visitante')
            ->join('ohxqc_empresas as e', 'e.id_empresa', '=', 'emp.id_empresa')
            ->join('ohxqc_codigobidimensional as cod', 'cod.id_visitante', '=', 'v.id_visitante')
            ->where('v.identificacion', '=', $cedula)
            ->orderBy('cod.id_visitante', 'DESC')
            ->limit(1)
            ->get();
            $row = array();
            if(count($consulta) > 0){
                foreach($consulta as $c){
                    $row [0] = $c->nombre;
                    $row [1] = $c->apellido;
                    $row [2] = $c->identificacion;
                    $row [3] = $c->empresa;
                    $row [4] = $c->foto;
                    $row [5] = $c->vehiculo;
                    $row [6] = $c->placa;
                    $row [7] = $c->responsable;
                }
    
                //CASO CUANDO NO TIENE FOTO
                if($row[4]=='N'){
                    //SI TIENE PERMISOS PINTA LA TABLA DE VERDE
                    $tabla="<table class='table' style='background-color: #00BFFF;
                        border-radius: 10px; 
                        border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                    <tr>	 
                        <td> 
                        <img src='http://172.19.92.223/ingresocarvajal/images/person.png' height='130' width='190'> 
                        </td> 
                        <td>
                        <table>
                            <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                            <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                            <tr> <td><label>".$row[3]."</label></td></tr> 
                            ";
                        if(trim($row[5])!='' && trim($row[6])!=''){
                             $tabla= $tabla."<tr> <td><label>".$row[5]."</label></td></tr> 
                             <tr> <td><label>".$row[6]."</label></td></tr>";
                        }
                            $tabla= $tabla."
                                <tr> <td><label>Autorizado por:</label></td></tr> 
                                <tr> <td><label>".$row[7]."</label></td></tr> 
                                </table>
                            </td> 
                        </tr> 
                        </table>";
                    
                }else if($row[4]=='S'){
                    //pinta tabla con foto
                $tabla="<table class='table' style='background-color: #00BFFF;
                    border-radius: 10px; 
                    border-left:0px; font-size:20px;font-family:'Lato', sans-serif'> 
                <tr>	 
                    <td> 
                    <img src='http://172.19.92.223/ingresocarvajal/modules/mod_visitantes/fotos/".$row[2].".jpg' height='200' width='245'> 
                    </td> 
                    <td>
                    <table>
                        <tr> <td><label>".$row[0]." ".$row[1]." </label></td></tr> 
                        <tr><td><label id='cc'>".$row[2]."</label></td></tr> 
                        <tr> <td><label>".$row[3]."</label></td></tr>";
                        if(trim($row[5])!='' && trim($row[6])!=''){
                            $tabla= $tabla."<tr> <td><label>".$row[5]."</label></td></tr> 
                        <tr> <td><label>".$row[6]."</label></td></tr>";
                        }
                        $tabla= $tabla."<tr> <td><label>Autorizado por:</label></td></tr> 
                        <tr> <td><label>".$row[7]."</label></td></tr>  
                        </table>
                    </td> 
                </tr> 
                </table>";
        
                }else{
                    $tabla="0";
                }
              }else{
                  $tabla = "0";
              }
            $data_v = RegistroVisitanteTemporalController::getDataV($cedula);
            
            if($data_v[0] !=''){
                $id_empresa=$data_v[2];
                $id_ciudad=$data_v[3];
            }else{
                $id_empresa=null;
                $id_ciudad=null;
                $data_b = RegistroVisitanteTemporalController::getDataBasica($cedula);
                
            }
            

            $listaEmpresas=RegistroVisitanteTemporalController::getListaEmpresas();
            $listaCiudades=RegistroVisitanteTemporalController::getListaCiudades();
            
            return view('Permisos::registroTemporal', compact('listaEmpresas', 'listaCiudades', 'id_empresa', 'id_ciudad', 'data_v', 'tabla', 'data_b','cedula'));

        }else {
            if($continuar == 1){
                $mensaje_alerta="No se puede registrar un COLABORADOR ACTIVO como un visitante temporal";
            }else if($continuar == 3){
                $mensaje_alerta="No se puede registrar un CONTRATISTA ACTIVO como un visitante temporal";
            }else {
                $mensaje_alerta="<strong>La persona pertenece al grupo CANT";
            }
            return redirect('registro-visitante-temporal')->with('alerta', $mensaje_alerta);
        }


      
      
    }

    public function validaVisitante($cedula)
    {
        $retorno= 0;
        $sql = DB::table('ohxqc_visitantes')
        ->select('fecha_ingreso', 'fecha_fin', 'tipo_visitante', 'activo')
        ->where('identificacion', '=', $cedula)
        ->get();
        if(count($sql) > 0){
            foreach($sql as $s){
                $fecha_ingreso= $s->fecha_ingreso;
                $fecha_fin = $s->fecha_fin;
                $tipo_v = $s->tipo_visitante;
                $activo = $s->activo;
            }
                $fecha_actual= date('Y-m-d');
                $idEmpresa = DB::table('ohxqc_empresas_visitante as e')
                ->select('e.id_empresa')
                ->join('ohxqc_visitantes as v', 'v.id_visitante', '=', 'e.id_visitante')
                ->where('v.identificacion', '=', $cedula)
                ->where('e.id_empresa', '=', '750')
                ->get();
                if(count($idEmpresa) > 0){
                    foreach($idEmpresa as $id){
                        $id_empresa = $id->id_empresa;
                    }
                }else{
                    $id_empresa = "";
                }

                /* * Si el tipo visitante es 1 es empleado, por tanto se debe validar si se ecnuentra activo
                * */
         
                if($tipo_v == 1 || $tipo_v == 2){
                    if($activo == 'S'){
                        //echo "fecha_ing:".$fecha_ingreso."<=".$fecha_actual." && fecha_fin:".$fecha_fin."fecha_ac:".$fecha_actual." id_empresa:".$id_empresa;
                        if(trim($id_empresa) == '' ){
                            if(($fecha_ingreso <= $fecha_actual) && ($fecha_fin >= $fecha_actual)){

                                if($tipo_v == 2) {
                                    $retorno= 3;
                                } else {
                                    $retorno= 1;
                                }
                            
                            }
                        }else{
                            $retorno= 2;
                        }
                    }
                }
            }
        return $retorno;
    }

    public function getListaEmpresas()
    {
        $listaEmpresas = DB::table('ohxqc_empresas')
        ->select('codigo_empresa','descripcion')
        ->where('activo', '=', 'S')
        ->where('id_sede', '=', 1)
        ->orderBy('descripcion')
        ->get();
        return $listaEmpresas;
    }

    public function getListaCiudades()
    {
        $listaCiudades = DB::table('ohxqc_ciudades')
        ->select('ciudad')
        ->where('activo', '=', 'S')
        ->get();
         return $listaCiudades;
    }

    public function getDataV($cedula)
    {
        $sql = DB::table('ohxqc_visitantes as v')
        ->select('v.nombre',
        'v.identificacion',
        'ev.id_empresa',
        'v.ciudad',
        'c.vehiculo',
        'c.placa',
         DB::raw("(CASE WHEN fecha_salida IS NULL THEN 'ENTRADA' ELSE 'SALIDA' END) as estado"),
        'c.codigo',
        'v.responsable')
        ->join('ohxqc_empresas_visitante as ev', 'ev.id_visitante','=','v.id_visitante')
        ->join('ohxqc_codigobidimensional as c', 'c.id_visitante','=','v.id_visitante')
        ->where('c.activo', '=', 'S')
        ->where('v.identificacion', '=', '1130614392')
        ->get();
        $row = array();
        if(count($sql)>0){
            foreach($sql as $s){
                $row[0] = $s->nombre;
                $row[2] = $s->id_empresa;
                $row[3] = $s->ciudad;
            }   
        }else{
            return $row;
        }
    }

    public function getDataBasica($cedula)
    {
        $data = DB::table('ohxqc_visitantes')
        ->select('nombre', 'identificacion')
        ->where('identificacion', '=', $cedula)
        ->get();
        $row = array();
        if(count($data) > 0){
            foreach($data as $d){
                $row[0] = $d->nombre;
                $row[1] = $d->identificacion;
            }
            return $row;
        }else{
            $row[0] = "";
            return $row;
        }
    }

    public function registrarVisitante(Request $request)
    {   
        $nombre =  $request->input("nombre");
        $cedula =  $request->input("cedula");
        $empresa =  $request->input("empresa");
        $vehiculo =  $request->input("vehiculo");
        $placa =  $request->input("placa");
        $ciudad = $request->input("ciudad"); 
        $responsable = $request->input("responsable"); 
        $puerta = $request->input("puerta"); 
        $codigo = $request->input("codigo");
        $user = substr(Auth::user()->name, 0,25);

        RegistroVisitanteTemporalController::guardarRegistro($cedula,$nombre,$ciudad,$empresa,$codigo,$vehiculo,$placa,$puerta,$user,$responsable);
    }

    public function guardarRegistro($cedula,$nombre,$ciudad,$empresa,$codigo,$vehiculo,$placa,$puerta,$user,$responsable)
    {
        $id_empresa_v="";
        //INSERTA TABLA VISITANTE
        $insertaVisitante = DB::table('')->insert([

        ]);
        $consulta = DB::table('ohxqc_visitantes')
        ->select('id_visitante')
        ->where('identificacion', '=', $cedula)
        ->get();
        if(count($consulta) > 0){
            $id_v = "";
            foreach($consulta as $c){
                $id_v = $c->id_visitante;
            }
            $fecha_ini= date('Y-m-d');
            $fecha_fin = date('Y-m-d', strtotime("+1 day"));

            if(trim($responsable)==''){$responsable="null";}

            if(trim($puerta)=='ENTRADA'){
                //ACTUALIZA VISITANTE
                $actualizaVisitante = DB::table('ohxqc_visitantes')
                ->where('identificacion', '=', $cedula)
                ->where('id_visitante', '=', $id_v)
                ->update([
                    'fecha_ingreso' => $fecha_ini,
                    'fecha_fin' => $fecha_fin,
                    'ciudad' => $ciudad,
                    'usuario_actualizacion' => $user,
                    'tipo_visitante' => 3,
                    'activo' => 'S',
                    'fecha_actualizacion' => now(),
                    'responsable' => $responsable,
                    'parqueadero' => 0,
                ]);
                
                //ACTUALIZA EMPRESA VISITANTE
                $actualizaEmpresaVisitante = DB::table('ohxqc_empresas_visitante')
                ->where('id_visitante', '=', $id_v)
                ->update([
                    'id_empresa' => $empresa,
                    'activo' => 'S',
                    'usuario_actualizacion' => $user,
                    'fecha_actualizacion' => now()
                ]);
            }

                $consultaIdEmpresa = DB::table('ohxqc_empresas_visitante')
                ->select('id_empresa_visitante')
                ->where('id_visitante', '=', $id_v)
                ->get();
                foreach($consultaIdEmpresa as $consid){
                    $id_empresa_v =  $consid->id_empresa_visitante;
                }
        }else{
            $inserta = DB::table('ohxqc_visitantes')->insert([
                 'identificacion_jefe' => null,
                 'tipo_identificacion' => 'CEDULA',
                 'identificacion' => $cedula,
                 'nombre' => $nombre,
                 'apellido' => null,
                 'fecha_ingreso' => $fecha_ini,
                 'fecha_fin' => $fecha_fin,
                 'tipo_contrato' => null,
                 'foto' => 'N',
                 'email' => null,
                 'telefono1' => null,
                 'telefono2' => null,
                 'telefono3' => null,
                 'tipo_visitante' => 3,
                 'cargo' => null,
                 'ciudad' => $ciudad,
                 'activo' => 'S',
                 'usuario_creacion' => $user,
                 'fecha_creacion' => now(),
                 'usuario_actualizacion' => $user,
                 'fecha_actualizacion' => now(),
                 'parqueadero' => 0,
                 'responsable' => $responsable
            ]);
            $consultaId = DB::table('ohxqc_visitantes')
            ->select('id_visitante')
            ->where('identificacion', '=', $cedula)
            ->get();
            foreach($consultaId as $con){
                $id_v = $con->id_visitante;
            }
            //**INSERTA EN EMPRESA
            $insertaEmpresa = DB::table('ohxqc_empresas_visitante')->insert([
                'id_empresa_visitante' => $id_v,
                'id_visitante' => $id_v,
                'id_empresa' => $empresa,
                'activo' => 'S',
                'usuario_creacion' => $user,
                'fecha_creacion' => now(),
                'usuario_actualizacion' => $user,
                'fecha_actualizacion' => now()
            ]);

            $obtenerIdEmpresa = DB::table('ohxqc_empresas_visitante')->max('id_empresa_visitante as id')->get();
            foreach($obtenerIdEmpresa as $idem){
                $id_empresa_v = $idem->id;
            }
            
           
        }
      
      /*
        if($id_v == ''){ //CUANDO NO EXISTE
           
        }else{
           
        }
        //INSERTA CODIGO  DESDE AQUI SE EMPIEZA
        $sql="SELECT c.id
            FROM ohxqc_visitantes v, ohxqc_empresas_visitante ev, ohxqc_codigobidimensional c
            where v.id_visitante=ev.id_visitante
            and ev.id_visitante=c.id_visitante
            and v.id_visitante=c.id_visitante
            and v.identificacion='$cedula'
            and c.activo='S'
            and ev.id_empresa='$empresa'
            and c.codigo='$codigo'";
        $result= pg_query($cnx,$sql);
        $row= pg_fetch_array($result);
        $id_c=$row[0];
        
        //**Obtiene fecha ingreso y fin de la persona para compararla con la fecha actual
        $sql="SELECT fecha_ingreso, fecha_fin, tipo_visitante FROM ohxqc_visitantes WHERE identificacion='$cedula'";
        $res= pg_query($cnx,$sql);
        $row= pg_fetch_array($res);
        $fecha_ingreso= $row[0];
        $fecha_fin= $row[1];
        $tipo_visitante = $row[2];
        $actual_date = date('Y-m-d');
        
        if(trim($ck_entrada)=='ENTRADA' && trim($ck_salida)=='' && $id_c==''){
            
            if(trim($vehiculo) != ''){$vehiculo= $vehiculo;}else{$vehiculo= "Sin vehiculo.";}
            if(trim($placa) != ''){$placa= "'".$placa."'";}else{$placa= "null";}
            //CUANDO SE INSERTA UN NUEVO CODIGO
            $sql="INSERT INTO ohxqc_codigobidimensional(
              id_equipo, id_visitante, codigo, tipo, activo, fecha_creacion, 
              usuario_creacion, fecha_actualizacion, usuario_actualizacion, 
              vehiculo, placa, fecha_ingreso, fecha_salida, fecha_registro)
      VALUES (null, $id_v, '$codigo', 'VISITANTE', 'S', now(), 
              '$user', now(), '$user', 
              '$vehiculo', $placa, now(), null, now())";
        pg_query($cnx,$sql);
  
            
        }elseif(trim($ck_entrada)=='ENTRADA' || trim($ck_salida)=='' && $id_c != ''){
            //CUANDO SE GUARDO UN CODIGO PERO SE ACTUALIZA ALGUN CAMPO
            $sql="UPDATE ohxqc_codigobidimensional
                 SET fecha_actualizacion= now(), 
                     usuario_actualizacion='$user', 
                     vehiculo='$vehiculo',
                     placa='$placa',
                     fecha_ingreso= now()
               WHERE id= $id_c";
            pg_query($cnx,$sql);
        }elseif(trim($ck_entrada)=='' && trim($ck_salida)=='SALIDA' && $id_c != ''){
          if((($fecha_ingreso <= $actual_date) == false) || (($actual_date <= $fecha_fin) == false)){
                  //CUANDO SALE LA PERSONA
                  if($tipo_visitante != 1){
                      $sql2="delete from ohxqc_permisos where id_permiso in (
                          select p.id_permiso 
                          from ohxqc_visitantes v, ohxqc_empresas_visitante ev, ohxqc_permisos p 
                          where v.identificacion='$cedula'
                          and v.id_visitante=ev.id_visitante
                          and p.id_empresa_visitante=ev.id_empresa_visitante)";
                    pg_query($cnx,$sql2);
                  }
          }	
        $startDate = strtotime($actual_date);
        $endDate = strtotime($fecha_fin);
        if($startDate <= $endDate){
        $datediff = $endDate - $startDate;
        $calc_days = floor($datediff / (60 * 60 * 24));
        }
        $sql="UPDATE ohxqc_codigobidimensional
                 SET fecha_actualizacion= now(), 
                     usuario_actualizacion='$user',fecha_salida= now(),activo='N' WHERE id= $id_c";
            pg_query($cnx,$sql);
        if($calc_days<= 2){
            $sql="UPDATE ohxqc_visitantes SET parqueadero=0, activo = 'N', fecha_fin= '$actual_date' WHERE identificacion='$cedula'";
            //$sql="UPDATE ohxqc_visitantes SET parqueadero=0 WHERE identificacion='$cedula'";
        pg_query($cnx,$sql);
        }else{
            $sql="UPDATE ohxqc_visitantes SET parqueadero=0 WHERE identificacion='$cedula'";
            pg_query($cnx,$sql);
        }
            
        }
        
        //echo $sql;
        //AGREGA LOS PERMISOS PARA CALI
        if((($fecha_ingreso <= $actual_date) == false) && (($actual_date <= $fecha_fin) == false)){
            if($tipo_visitante != 1){
                //***ELIMINA REGISTROS ANTERIORES
                $sql="delete from ohxqc_permisos where id_permiso in (
                          select p.id_permiso 
                          from ohxqc_visitantes v, ohxqc_empresas_visitante ev, ohxqc_permisos p 
                          where v.identificacion='$cedula'
                          and v.id_visitante=ev.id_visitante
                          and p.id_empresa_visitante=ev.id_empresa_visitante)";
                //"DELETE FROM ohxqc_permisos where id_empresa_visitante= $id_empresa_v";
                pg_query($cnx,$sql);
            }	
        }
  
          //26-nov-2020. Validamos si en la ciudad de Cali el ingreso es para una de las empresas de Carvajal
          $sql="SELECT grupo_carvajal FROM ohxqc_empresas where id_empresa = '$empresa'";
          $result = pg_query($cnx, $sql);
          while ($row = pg_fetch_assoc($result)) {
              $grupo_carvajal = $row['grupo_carvajal'];
          }		
          
            //INSERTA PERMISOS A NIVEL DE ID_PADRE EN CASO DE ENTRADA
            //***revisar
          
          if($ciudad == 'CALI' && $grupo_carvajal == '1'){
              $sql="SELECT id_ubicacion FROM ohxqc_ubicaciones where id_padre in ('2','96') and activo='S'";
            }elseif($ciudad == 'CALI'){
                $sql="SELECT id_ubicacion FROM ohxqc_ubicaciones where id_padre= 2 and activo='S'";
            }elseif ($ciudad == 'BOGOTÁ'){
                $sql="SELECT id_ubicacion FROM ohxqc_ubicaciones where id_padre= 15 and activo='S'";
            }elseif ($ciudad == 'YUMBO'){
                $sql="SELECT id_ubicacion FROM ohxqc_ubicaciones where id_padre= 6 and activo='S'";
            }elseif ($ciudad == 'MEDELLÍN'){
                $sql="SELECT id_ubicacion FROM ohxqc_ubicaciones where id_padre= 11 and activo='S'";
            } 		
            $result=pg_query($cnx,$sql);
            if(trim($ck_salida)!='SALIDA'){
                while($row=pg_fetch_array($result)){
                    $id_ub=$row[0];
                    $sql="INSERT INTO ohxqc_permisos(
                      id_empresa_visitante, id_ubicacion, id_horario, identificacion_responsable, 
                          fecha_inicio, fecha_fin, activo, usuario_creacion, fecha_creacion, 
                          usuario_actualizacion, fecha_actualizacion)
                  VALUES ($id_empresa_v, $id_ub, 5, null, 
                          '$fecha_ini', '$fecha_fin', 'S', '$user', now(), 
                          '$user', now())";
              pg_query($cnx,$sql);	
                }	
            }	*/
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
