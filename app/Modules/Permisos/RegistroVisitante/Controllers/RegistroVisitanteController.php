<?php

namespace App\Modules\Permisos\RegistroVisitante\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\URL;


class RegistroVisitanteController extends Controller
{
    public $solicitudID = "";
    public $infoDeEmpresa  = "";
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

        //Recibe informacion de fechas de ingreso y final
        $fechaInicio = $request->input('fechaIngreso');
        $fechaFin = $request->input('fechaFin');
        $horario = $request->input('horario');
        $hora = $request->input('hora');
        $empVisi = $request->input('empVisi');
        $ciudad = $request->input('ciudad');

        //Recibe labor a realizar
        $labor = $request->input('labor');


        //Guardar en la tabla ohxqc_solicitud_ingreso
        $idSolicitud =  DB::table('ohxqc_solicitud_ingreso')->max('id_solicitud')+1;
        $guardaSolicitud = DB::table('ohxqc_solicitud_ingreso')->insert([
            'id_solicitud' => $idSolicitud,
            'empresa_id' =>  $empVisi,
            'fecha_ingreso' =>  $fechaInicio,
            'fecha_fin' =>  $fechaFin,
            'horario_id' => $horario,
            'ciudad_id' => $ciudad,
            'solicitante' => $solicitante,
            'tipo_ingreso' => $tipoIngreso,
            'tipo_identidad' => $tipoId,
            'empresa_contratista' => $empresaContratista,
            'labor_realizar' => $labor
        ]);

        if($guardaSolicitud){
            //Recibe información de anexos
            $guardaDocumento="";
            $cantidadAnexos = $request->input('cantR');
            if($cantidadAnexos > 0){
                //se recibe el primer registro que es obligatorio y se valida el documento
                    if($request->hasFile('anexo')){
                        $urlDocumento = $request->file('anexo')->store('documentosSolicitud', 'public');
                    }else{
                        $urlDocumento = "";
                    }

                    $guardaDocumento = DB::table('ohxqc_documentos_solicitud')->insert([
                        'id_registro' => DB::table('ohxqc_documentos_solicitud')->max('id_registro')+1,
                        'identificacion' =>  $request->input('cedula'),
                        'nombre' => $request->input('nombre'),
                        'url_documento' =>  $urlDocumento,
                        'solicitud_id' => $idSolicitud
                    ]);

                    for($i=1; $i <= $cantidadAnexos; $i++){
                        //Mientras sea diferente de null los registros, los guardo en tabla ohxqc_documentos_solicitud
                        if($request->input('cedula'.$i) != null){

                            //validar si viene un documento para poderlo mover al Storage
                            if($request->hasFile('anexo'.$i)){
                                $urlDocumento = $request->file('anexo'.$i)->store('documentosSolicitud', 'public');
                            }else{
                                $urlDocumento = "";
                            }

                            $guardaDocumento = DB::table('ohxqc_documentos_solicitud')->insert([
                                'id_registro' => DB::table('ohxqc_documentos_solicitud')->max('id_registro')+1,
                                'identificacion' =>  $request->input('cedula'.$i),
                                'nombre' => $request->input('nombre'.$i),
                                'url_documento' =>  $urlDocumento,
                                'solicitud_id' => $idSolicitud
                            ]);
                        }
                    }
            }else{
                if($request->hasFile('anexo')){
                    $urlDocumento = $request->file('anexo')->store('documentosSolicitud', 'public');
                }else{
                    $urlDocumento = "";
                }

                $guardaDocumento = DB::table('ohxqc_documentos_solicitud')->insert([
                    'id_registro' => DB::table('ohxqc_documentos_solicitud')->max('id_registro')+1,
                    'identificacion' =>  $request->input('cedula'),
                    'nombre' => $request->input('nombre'),
                    'url_documento' =>  $urlDocumento,
                    'solicitud_id' => $idSolicitud
                ]);
            }

             //Recibe información de las sedes
             if($guardaDocumento){
                $guardaSedes="";
                $cantidadSedes = $request->input('cantRSelects');
                    if($cantidadSedes > 0){
                    //Se recibe la primer sede que es obligatoria y se guarda en la tabla ohxqc_sedes_solicitud
                        $guardaSedes = DB::table('ohxqc_sedes_solicitud')->insert([
                            'id' => DB::table('ohxqc_sedes_solicitud')->max('id')+1,
                            'id_sede' => $request->input('sede'),
                            'id_solicitud' => $idSolicitud
                        ]);
                        for($i=1; $i <= $cantidadSedes; $i++){
                            $guardaSedes = DB::table('ohxqc_sedes_solicitud')->insert([
                                'id' => DB::table('ohxqc_sedes_solicitud')->max('id')+1,
                                'id_sede' => $request->input('sede'.$i),
                                'id_solicitud' => $idSolicitud
                            ]);
                        }
                    }else{
                        $guardaSedes = DB::table('ohxqc_sedes_solicitud')->insert([
                            'id' => DB::table('ohxqc_sedes_solicitud')->max('id')+1,
                            'id_sede' => $request->input('sede'),
                            'id_solicitud' => $idSolicitud
                        ]);
                    }

                    if($guardaSedes){
                        //teniendo la documentacion correcta, se registraria en la tabla ohxqc_solicitud_por_aprobar

                        //Se consulta en la tabla de configuración ohxqc_config_solicitud_empresas, el max nivel
                        //para conocer el flujo maximo por el cual viajará la solicitud

                        $infoNivel = DB::table('ohxqc_config_solicitud_empresas')
                        ->where('empresa_id', '=', $empVisi)
                        ->max('nivel');

                        if($infoNivel > 0){
                            $niveles = $infoNivel;
                            //al obtener resultados se debe insertar en la tabla ohxqc_solicitud_por_aprobar

                             //se llama a un metodo el cual sea el que genere la URL TOKEN y actualice esta tabla
                             //con el campo que falta (token)
                             $this->solicitudID = $idSolicitud;
                             $token = RegistroVisitanteController::getLinkSubscribe();

                            $guardarSolicitudPorAprobar = DB::table('ohxqc_solicitud_por_aprobar')->insert([
                                'id_apr' =>  DB::table('ohxqc_solicitud_por_aprobar')->max('id_apr')+1,
                                'id_solicitud' => $idSolicitud,
                                'fecha_registro' => now(),
                                'niveles' => $niveles,
                                'nivel_actual' => 1,
                                'estado' => 'Pendiente',
                                'token' => $token
                            ]);
                            
                            if($guardarSolicitudPorAprobar){
                                //Hasta aqui ya tendriamos la info importante, podemos notificar el registro

                                //Enviar el correo con esta solicitud, y la url
                                 $enviar =1;// RegistroVisitanteController::enviarCorreo($token, $empVisi);

                                if($enviar){
                                    return redirect('registro-visitante')->with('msj', 'Solicitud registrada correctamente. Número del caso: '.$idSolicitud);
                                }else{
                                   return redirect('registro-visitante')->with('errCorreo', 'La Solicitud fue registrada correctamente con Número del caso: '.$idSolicitud.', pero el correo no ha sido enviado');
                                }

                            }else{
                                //si no se guarda la solicitud, redireccionamos el error
                                return redirect('registro-visitante')->with('errSoliApro', 'No se pudo guardar la solicitud para aprobar');
                            }

                           


                        }else{
                            //si no se encuentran resultados, hay que configurar la empresa en la maestra
                            return redirect('registro-visitante')->with('errConfig', 'Por favor agregue la empresa visitante en la configuración ohxqc_config_solicitud_empresas');
                        }


                    }else{
                        //si no guardó las sedes se redirige el error
                        return redirect('registro-visitante')->with('errSedes', 'No se pudieron registrar las sedes');
                    }
             }else{
                //si no guardó los documentos se redirige el error
                return redirect('registro-visitante')->with('errDocu', 'No se pudieron guardar los documentos');
             }

        }else{
            //si no guardó la solicitud se redirige el error
            return redirect('registro-visitante')->with('errSoli', 'No se pudo regisrar la solicitud');
         }
    }

 

    //este es el que crear la url temporal, con 3 parametros: nombre_ruta,fecha_Expira,array
    public function getLinkSubscribe()
    {
        //temporarySignedRoute
        return URL::signedRoute(
            'event.subscribe', 
            //now()->addMinutes(5), 
            ['solicitud' => $this->solicitudID]
        );
    }

    //Este metodo se ejecuta cuando el usuario de click en el enlace generado arriba
    public function subscribe(Request $request, $solicitud)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        //Consultar si la solicitud fue rechazada
        $consultaRechazado = DB::table('ohxqc_solicitud_por_aprobar')
        ->where('id_solicitud', '=', $solicitud)
        ->where('estado', '=', 'Rechazado')
        ->get();
        if(count($consultaRechazado) > 0){
            $botonesAccion = false;
            $msjRechazo = true;
            $detalles = DB::table('ohxqc_historico_solicitud as hs')
            ->select('nivel_aprobador as nivel', 'username as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
            ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
            ->where('hs.id_solicitud', '=', $solicitud)
            ->get();
        }else{
            $detalles = "";
            $msjRechazo = false;
            //Consultar si la solicitud ya ha sido aprobada, para no mostrar los botones de accion
            //O tambien se podria abort(403)
            
            $consultaAprobacion = DB::table('ohxqc_solicitud_por_aprobar')
            ->where('id_solicitud', '=', $solicitud)
            ->where('estado', '=', 'Aprobado')
            ->get();
            if(count($consultaAprobacion) > 0){
                $botonesAccion = false;
                 $detalles = DB::table('ohxqc_historico_solicitud as hs')
                ->select('nivel_aprobador as nivel', 'username as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                ->where('hs.id_solicitud', '=', $solicitud)
                ->get();
            }else{
                //Si aún la solicitud está pendiente, entonces se consulta en el histórico para saber si
                //el nivel al que pertenece este usuario ya ha aprobado o no la solicitud.
                $consultaAprobacionHistorico = DB::table('ohxqc_historico_solicitud')
                ->where('id_solicitud', '=', $solicitud)
                ->where('estado', '=', 'A')
                ->whereIn('nivel_aprobador', function($query){
                    $query->select('nivel')
                    ->from('ohxqc_config_solicitud_empresas')
                    ->where('usuario_aprobador_id', '=', auth()->user()->id);
                })
                ->get();
                if(count($consultaAprobacionHistorico) > 0){
                    $botonesAccion = false;
                     $detalles = DB::table('ohxqc_historico_solicitud as hs')
                ->select('nivel_aprobador as nivel', 'username as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                ->where('hs.id_solicitud', '=', $solicitud)
                ->get();
                }else{
                    /***Si la consulta devuelve 0, pueden ocurrir dos situaciones,
                     *  1: que ningun usuario del nivel actual ha validado la solicitud.
                     *  2: que ni siquiera el nivel anterior al del usuario actual la ha validado
                     * Por esta razón al tener el nivel del usuario actual, le restamos 1 para comparar si
                     * el nivel anterior ya ha validado o si definitamente no, así sabremos si halitar los botones***/
                    $consultaNivelActual = DB::table('ohxqc_config_solicitud_empresas')
                    ->select('nivel')
                    ->where('usuario_aprobador_id', '=', auth()->user()->id)
                    ->get();
                    foreach($consultaNivelActual as $nivelA){
                        $nivelUsuario = $nivelA->nivel;
                    }
                    /***Si el nivel actual del usuario es igual a 1, y al saber que no hubieron registros, quiere decir
                    que se cumple el punto (1), el NIVEL 1, no ha validado la solicitud, entonces se habilita botones***/
                    if($nivelUsuario == 1){
                        $botonesAccion = true;
                    }else{
                        //se consulta  si el nivel anterior al actual ya aprobó la solicitud
                        $consAproNivelAnterior = DB::table('ohxqc_historico_solicitud')
                        ->where('id_solicitud', '=', $solicitud)
                        ->where('estado', '=', 'A')
                        ->where('nivel_aprobador', '=', $nivelUsuario-1)
                        ->get();
                        if(count($consAproNivelAnterior) > 0){
                            $botonesAccion = true;
                        }else{
                            //Si nisiquiera el nivel anterior ha validado, entonces a los demas niveles le restringimos acceso
                            abort(403);
                        }

                    }
                }
            }
        }

        
        //consulto toda la información de esta solicitud
        $consultarInfoGeneral= DB::table('ohxqc_solicitud_ingreso')
        ->where('id_solicitud', '=', $solicitud)
        ->get();
        foreach($consultarInfoGeneral as $info){
            $idEmpresa = $info->empresa_id;
            $fechaIngreso = $info->fecha_ingreso;
            $fechaFinal = $info->fecha_fin;
            $idhorario = $info->horario_id;
            $ciudad = $info->ciudad_id;
            $solicitante = $info->solicitante;
            $tipoIngreso = $info->tipo_ingreso;
            $tipoIdentidad = $info->tipo_identidad;
            $empresaContratista = $info->empresa_contratista;
            $labor = $info->labor_realizar;
        }

        $arrayInfo[] = array('solicitante'=>$solicitante, 'tipoIngreso'=>$tipoIngreso, 'tipoId'=>$tipoIdentidad, 'empresaC'=>$empresaContratista,'fechaIni'=>$fechaIngreso,'fechaFinal'=>$fechaFinal,'horario'=>$idhorario,'empVisitar'=>$idEmpresa, 'ciudad'=>$ciudad, 'labor'=>$labor);

        $documentos = DB::table('ohxqc_documentos_solicitud')
        ->where('solicitud_id', '=', $solicitud)
        ->get();

        $sedesVisitar = DB::table('ohxqc_sedes_solicitud as sol')
        ->select('sede.descripcion')
        ->join('ohxqc_sedes as sede', 'sede.id', 'sol.id_sede')
        ->where('sol.id_solicitud', '=', $solicitud)
        ->get();

      //  var_dump($arrayInfo);
        

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

       //valido que el usuario quien intenta ingresar, esté autorizado para validar solicitudes de acuerdo
       //a la empresa, de no ser así, abortamos
       $consultaPermiso = DB::table('ohxqc_config_solicitud_empresas')
       ->select('usuario_aprobador_id')
       ->where('empresa_id', '=', $idEmpresa)
       ->where('usuario_aprobador_id', '=', auth()->user()->id)
       ->get();
        if(count($consultaPermiso) > 0){
            return view('Permisos::validacionSolicitud', compact('solicitud', 'arrayInfo', 'documentos', 'sedesVisitar' ,'empresas', 'horarios', 'sedes', 'botonesAccion', 'msjRechazo', 'detalles'));
        }else{
            abort(403);
        }


       
    }   

    public function enviarCorreo($url, $empVisi)
    {
        //var_dump($this->infoDeEmpresa);
        
        $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
        ->select('nivel','correo_usuario')
        ->where('empresa_id', '=', $empVisi)
        ->get();

        foreach($infoEmpresa as $inf){
            if($inf->nivel == 1){
                try{
                    mail($inf->correo_usuario, 'Solicitud Para Aprobar', $url);
                    $envio = 1;
                }catch(Exception $s){
                    $envio = 0;
                }
            }
        }
        echo $envio;
    }

    public function validarSolicitud(Request $request)
    {
        $idSolicitud = $request->input('idsolicitud');
        $this->solicitudID = $idSolicitud;
        $comentario = $request->input('comentario');
        $empresaId = $request->input('idempresa');

        //consultar nivel de este usuario
        $consultNivel = DB::table('ohxqc_config_solicitud_empresas')
                                    ->select('nivel')
                                    ->where('usuario_aprobador_id', '=', auth()->user()->id)
                                    ->where('empresa_id', '=', $empresaId)
                                    ->get();
        foreach($consultNivel as $consul){
            $nivel = $consul->nivel;
        }

        //Si fue aprobada
        if($request->input('aprobar') != null){
            //Insertamos la información en el histórico
            $guardaHistorico = DB::table('ohxqc_historico_solicitud')->insert([
                'id_his' => DB::table('ohxqc_historico_solicitud')->max('id_his')+1,
                'id_solicitud' => $idSolicitud,
                'nivel_aprobador' => $nivel,
                'usuario_aprobador' =>  auth()->user()->id,
                'fecha_diligenciado' => now(),
                'comentario' => $comentario,
                'estado' => 'A'
            ]);

            if($guardaHistorico){
                //Despues de guardar el histórico, actualizamos la tabla ohxqc_solicitud_por_aprobar
                $siguienteNivel = $nivel + 1;
                //consultamos los niveles registrados actualmente en esa tabla para comparar
                $nivelFinal = DB::table('ohxqc_solicitud_por_aprobar')
                ->select('niveles')
                ->where('id_solicitud', '=', $idSolicitud)
                ->get();
                foreach($nivelFinal as $f){
                    $ultimoNivel = $f->niveles;
                }

                //comparar si el siguiente nivel es mayor al ultimo nivel, para saber si ya termina el flujo
                if($siguienteNivel > $ultimoNivel){
                        //si es mayor, entonces se hace la actualizacion del nivel_actual,estado,comentario y token 
                        DB::table('ohxqc_solicitud_por_aprobar')
                        ->where('id_solicitud', '=', $idSolicitud)
                        ->update([
                                'nivel_actual' => $ultimoNivel,
                                'estado' => 'Aprobado',
                                'comentario' => $comentario,
                                'token'=> null
                        ]);

                        return redirect()->back()->with('msj', 'Solicitud aprobada y Flujo terminado.');
                }else{


                    //si no es mayor, entonces se hace la actualizacion del nivel_actual y token

                     //reasigno un id de solicitud para generar un nuevo token
                     $this->solicitudID = $idSolicitud;
                     $token = RegistroVisitanteController::getLinkSubscribe();

                    DB::table('ohxqc_solicitud_por_aprobar')
                    ->where('id_solicitud', '=', $idSolicitud)
                    ->update([
                            'nivel_actual' => $siguienteNivel,
                            'token' => $token
                    ]);

                    //despues enviaría el nuevo correo a las personas del siguiente nivel
                    $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
                    ->select('correo_usuario')
                    ->where('empresa_id', '=', $empresaId)
                    ->where('nivel', '=', $siguienteNivel)
                    ->get();
            
                   /* foreach($infoEmpresa as $inf){
                        try{
                            mail($inf->correo_usuario, 'Solicitud Para Aprobar', $token);
                            $envio = 1;
                        }catch(Exception $s){
                            $envio = 0;
                        }
                    }*/

                    if($infoEmpresa){
                        return redirect()->back()->with('corrEnv', 'Solicitud aprobada y enviada al nivel: '.$siguienteNivel);
                    }else{
                        return redirect()->back()->with('corrErr', 'NO SE ENVIO EL CORREO  AL NIVEL:'.$siguienteNivel.' Pero se registró la aprobación.');
                    }
                    
                   
                }
            }else{
                //si no registra historico
                
            }
        }else{
            //si no fue aprobada
             //Insertamos la información en el histórico
             $guardaHistorico = DB::table('ohxqc_historico_solicitud')->insert([
                'id_his' => DB::table('ohxqc_historico_solicitud')->max('id_his')+1,
                'id_solicitud' => $idSolicitud,
                'nivel_aprobador' => $nivel,
                'usuario_aprobador' =>  auth()->user()->id,
                'fecha_diligenciado' => now(),
                'comentario' => $comentario,
                'estado' => 'R'
            ]);
            
            //Cambiamos el estado de la solicitud por aprobar, comentario y token
            DB::table('ohxqc_solicitud_por_aprobar')
            ->where('id_solicitud', '=', $idSolicitud)
            ->update([
                    'estado' => 'Rechazado',
                    'comentario' => $comentario,
                    'token'=> null
            ]);

            return redirect()->back()->with('soliRech', 'La solicitud #'.$idSolicitud.', ha sido rechazada.');
            
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
