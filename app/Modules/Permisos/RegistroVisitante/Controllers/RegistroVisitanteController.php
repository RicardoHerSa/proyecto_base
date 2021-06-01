<?php

namespace App\Modules\Permisos\RegistroVisitante\Controllers;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\URL;
//use Illuminate\Notifications\Notification;
use Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\enviarSolicitud;
use App\Notifications\notificaSolicitud;
use App\Models\User\User;


class RegistroVisitanteController extends Controller
{
    public $solicitudID = "";
    public $infoDeEmpresa  = "";
    public $tipoIngres = "";
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

       $sedes = DB::table('ohxqc_sede_fisica')
       ->get();

       $tiposVisitante = DB::table('ohxqc_tipos_visitante')
       ->select('id_tipo_visitante', 'nombre')
       ->where('estado', '=', 1)
       ->get();

        return view('Permisos::registroVisitante', compact('empresas', 'horarios', 'sedes', 'tiposVisitante'));
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
        $tipoIngreso = $request->input('tipoIngreso'); //recibe es el id
        $nombreTipoIngreso = DB::table('ohxqc_tipos_visitante')->select('nombre')->where('id_tipo_visitante', '=', $tipoIngreso)->get();
        foreach($nombreTipoIngreso as $nombreTipo){
            $nombreIngreso = $nombreTipo->nombre;   //este nombre se ingresa a solicitud_ingreso
        }
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

        $correoSolicitante = DB::table('jess_users')->select('email')->where('id', auth()->user()->id)->get();
        foreach($correoSolicitante as $corr){
            $correoSolicitante = $corr->email;
        }
        $guardaSolicitud = DB::table('ohxqc_solicitud_ingreso')->insert([
            'id_solicitud' => $idSolicitud,
            'empresa_id' =>  $empVisi,
            'fecha_ingreso' =>  $fechaInicio,
            'fecha_fin' =>  $fechaFin,
            'horario_id' => $horario,
            'ciudad_id' => $ciudad,
            'solicitante' => $solicitante,
            'tipo_ingreso' => $nombreIngreso,
            'tipo_identidad' => $tipoId,
            'empresa_contratista' => $empresaContratista,
            'labor_realizar' => $labor,
            'correo_solicitante' =>  $correoSolicitante
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

                        /**Se consulta en la tabla de configuración ohxqc_config_solicitud_empresas, el max nivel
                        para conocer el flujo maximo por el cual viajará la solicitud. Si hay mas de una sede, se insertará en la tabla ohxqc_solicitud_por_aprobar, la cantidad de solicitudes  para c/u de las sedes
                        **/
                        if($cantidadSedes > 0){
                            $j = ""; //la primer sede tiene como name="sede", la segunda name="sede1"
                            $entraSede = 0;
                            for($i = 0; $i <= $cantidadSedes; $i++){
                                $infoNivel = DB::table('ohxqc_config_solicitud_empresas')
                                ->where('empresa_id', '=', $empVisi)
                                ->where('tipo_visitante', '=', $tipoIngreso)
                                ->where('sede_id', '=', $request->input('sede'.$j))
                                ->max('nivel');
                               // echo $infoNivel." ---> ".$request->input('sede'.$j)."->fin<br> ";
                                if($infoNivel > 0){
                                    $niveles = $infoNivel;
                                    //al obtener resultados se debe insertar en la tabla ohxqc_solicitud_por_aprobar
        
                                     //se llama a un metodo el cual sea el que genere la URL TOKEN
                                     $this->solicitudID = $idSolicitud;
                                     $this->tipoIngres = $tipoIngreso;
                                     $token = RegistroVisitanteController::getLinkSubscribe();
        
                                    $guardarSolicitudPorAprobar = DB::table('ohxqc_solicitud_por_aprobar')->insert([
                                        'id_apr' =>  DB::table('ohxqc_solicitud_por_aprobar')->max('id_apr')+1,
                                        'id_solicitud' => $idSolicitud,
                                        'fecha_registro' => now(),
                                        'niveles' => $niveles,
                                        'nivel_actual' => 1,
                                        'estado' => 'Pendiente',
                                        'token' => $token,
                                        'tipo_visitante'=> $tipoIngreso,
                                        'sede_id'=> $request->input('sede'.$j),
        
                                    ]);
                                        //Enviar el correo con esta solicitud, y la url
                                         $enviar = RegistroVisitanteController::enviarCorreo($token, $empVisi,$tipoIngreso, $request->input('sede'.$j),$idSolicitud, $solicitante, $labor);
                                       
                                    
                                }else{
                                    $entraSede++;
                                    //si no hay info de esta sede, se debe aprobar  inmediatamente
                                        $this->solicitudID = $idSolicitud;
                                        $this->tipoIngres = $tipoIngreso;
                                        $token = RegistroVisitanteController::getLinkSubscribe();

                                        $guardarSolicitudPorAprobar = DB::table('ohxqc_solicitud_por_aprobar')->insert([
                                            'id_apr' =>  DB::table('ohxqc_solicitud_por_aprobar')->max('id_apr')+1,
                                            'id_solicitud' => $idSolicitud,
                                            'fecha_registro' => now(),
                                            'niveles' => 1,
                                            'nivel_actual' => 1,
                                            'estado' => 'Aprobado',
                                            'comentario' => 'Aprobado inmediatamente porque no hay configuración de flujos posteriores.',
                                            'token' => $token,
                                            'tipo_visitante'=> $tipoIngreso,
                                            'sede_id'=> $request->input('sede'.$j)

                                        ]);
                           
                       
                                        //se actualiza de una vez la aprobacion
                                        DB::table('ohxqc_historico_solicitud')->insert([
                                            'id_his' =>  DB::table('ohxqc_historico_solicitud')->max('id_his')+1,
                                            'id_solicitud' => $idSolicitud,
                                            'nivel_aprobador' => 1,
                                            'usuario_aprobador' => auth()->user()->id,
                                            'fecha_diligenciado' => now(),
                                            'comentario' => 'Aprobado inmediatamente porque no hay configuración de flujos posteriores.',
                                            'estado' => 'A'
                                        ]);
                                        //Enviar el correo avisando unicamente al solicitante, porque no hay flujo

                                    $correo = User::where('id',auth()->user()->id)->get();
                                    Notification::send($correo, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", ""));
                                }
                                if($i == 0 ){$j = 1;}else{$j = $i+1;}
                            }

                                //se valida si todas las sedes fueron aprobadas inmediatamente porque no tenian config en la maestra, y de ser asi, retornamos avisando
                                 if($entraSede == $cantidadSedes+1){
                                    // echo "<br> AMBAS ENTRARON SIN CONFIG";
                                    return redirect('registro-visitante')->with('msj', 'Solicitud registrada y aprobada correctamente. Número del caso: '.$idSolicitud);
                                 }else{
                                    return redirect('registro-visitante')->with('msj', 'Solicitud registrada correctamente. Número del caso: '.$idSolicitud);
                                 }
                            
                        }else{
                            //si solo tenemos una sede pues entonces vemos el max nivel del flujo de acuerdo a la empresa
                            $infoNivel = DB::table('ohxqc_config_solicitud_empresas')
                            ->where('empresa_id', '=', $empVisi)
                            ->where('tipo_visitante', '=', $tipoIngreso)
                            ->max('nivel');
    
                            if($infoNivel > 0){
                                $niveles = $infoNivel;
                                //al obtener resultados se debe insertar en la tabla ohxqc_solicitud_por_aprobar
    
                                 //se llama a un metodo el cual sea el que genere la URL TOKEN
                                 $this->solicitudID = $idSolicitud;
                                 $this->tipoIngres = $tipoIngreso;
                                 $token = RegistroVisitanteController::getLinkSubscribe();
    
                                $guardarSolicitudPorAprobar = DB::table('ohxqc_solicitud_por_aprobar')->insert([
                                    'id_apr' =>  DB::table('ohxqc_solicitud_por_aprobar')->max('id_apr')+1,
                                    'id_solicitud' => $idSolicitud,
                                    'fecha_registro' => now(),
                                    'niveles' => $niveles,
                                    'nivel_actual' => 1,
                                    'estado' => 'Pendiente',
                                    'token' => $token,
                                    'tipo_visitante'=> $tipoIngreso,
                                    'sede_id' => $request->input('sede')
    
                                ]);
                                
                                if($guardarSolicitudPorAprobar){
                                    //Hasta aqui ya tendriamos la info importante, podemos notificar el registro
    
                                    //Enviar el correo con esta solicitud, y la url
                                     $enviar = RegistroVisitanteController::enviarCorreo($token, $empVisi,$tipoIngreso,$request->input('sede'), $idSolicitud, $solicitante, $labor);
                                    return redirect('registro-visitante')->with('msj', 'Solicitud registrada correctamente. Número del caso: '.$idSolicitud);
                                    
                                }else{
                                    //si no se guarda la solicitud, redireccionamos el error
                                    return redirect('registro-visitante')->with('errSoliApro', 'No se pudo guardar la solicitud para aprobar');
                                }
    
                               
    
    
                            }else{
                                //si no se encuentran resultados, hay que configurar la empresa en la maestra o
                                //se aprueba de una vez
                                $this->solicitudID = $idSolicitud;
                                $this->tipoIngres = $tipoIngreso;
                                $token = RegistroVisitanteController::getLinkSubscribe();
    
                               $guardarSolicitudPorAprobar = DB::table('ohxqc_solicitud_por_aprobar')->insert([
                                   'id_apr' =>  DB::table('ohxqc_solicitud_por_aprobar')->max('id_apr')+1,
                                   'id_solicitud' => $idSolicitud,
                                   'fecha_registro' => now(),
                                   'niveles' => 1,
                                   'nivel_actual' => 1,
                                   'estado' => 'Aprobado',
                                   'comentario' => 'Aprobado inmediatamente porque no hay configuración de flujos posteriores.',
                                   'token' => $token,
                                   'tipo_visitante'=> $tipoIngreso,
                                   'sede_id' => $request->input('sede')
    
                               ]);
                               
                               if($guardarSolicitudPorAprobar){
                                   //se actualiza de una vez la aprobacion
                                   DB::table('ohxqc_historico_solicitud')->insert([
                                    'id_his' =>  DB::table('ohxqc_historico_solicitud')->max('id_his')+1,
                                    'id_solicitud' => $idSolicitud,
                                    'nivel_aprobador' => 1,
                                    'usuario_aprobador' => auth()->user()->id,
                                    'fecha_diligenciado' => now(),
                                    'comentario' => 'Aprobado inmediatamente porque no hay configuración de flujos posteriores.',
                                    'estado' => 'A'
                                ]);
                                    //Enviar el correo avisando unicamente al solicitante, porque no hay flujo
    
                                   $correo = User::where('id',auth()->user()->id)->get();
                                   Notification::send($correo, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", ""));
                                       return redirect('registro-visitante')->with('msj', 'Solicitud registrada y aprobada correctamente. Número del caso: '.$idSolicitud);
    
                               }else{
                                   //si no se guarda la solicitud, redireccionamos el error
                                   return redirect('registro-visitante')->with('errSoliApro', 'No se pudo guardar la solicitud para aprobar');
                               }
                            }
    
    
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
            ['solicitud' => $this->solicitudID, 'ingreso'=> $this->tipoIngres]
        );
    }

    //Este metodo se ejecuta cuando el usuario de click en el enlace generado arriba
    public function subscribe(Request $request, $solicitud, $ideIngreso)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
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
            //valido que el usuario quien intenta ingresar, esté autorizado para validar solicitudes de acuerdo
            //a la empresa y al tipo de visitante que debe validar, de no ser así, abortamos
            $consultaPermiso = DB::table('ohxqc_config_solicitud_empresas')
        ->select('usuario_aprobador_id')
        ->where('empresa_id', '=', $idEmpresa)
        ->where('tipo_visitante', '=', $ideIngreso)
        ->where('usuario_aprobador_id', '=', auth()->user()->id)
        ->get();
        if(count($consultaPermiso) > 0){
            $accede = true;
        }else{
            //Si existe la empresa, entonces bortamos porque quiere decir que no existe el usuario o el tipo
            $consultaExistenciaEmpresa = DB::table('ohxqc_config_solicitud_empresas')
            ->select('usuario_aprobador_id')
            ->where('empresa_id', '=', $idEmpresa)
            ->get();
            if(count($consultaExistenciaEmpresa)>0){
                abort(403);
            }else{
                //si no existe la empresa, es porque fue una solicitud aprobada inmediatamente sin flujo
                $accede = true;
            }
        
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
            ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
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
                ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
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
                    ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
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
                    ->where('tipo_visitante', '=' , $ideIngreso)
                    ->where('empresa_id', '=' , $idEmpresa)
                    ->where('usuario_aprobador_id', '=', auth()->user()->id)
                    ->get();
                    foreach($consultaNivelActual as $nivelA){
                        $nivelUsuario = $nivelA->nivel;
                    }
                    /***Si el nivel actual del usuario es igual a 1, y al saber que no hubieron registros, quiere decir
                    que se cumple el punto (1), el NIVEL 1, no ha validado la solicitud, entonces se habilita botones***/
                    if($nivelUsuario == 1){
                        $botonesAccion = true;
                          $detalles = DB::table('ohxqc_historico_solicitud as hs')
                        ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                        ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                        ->where('hs.id_solicitud', '=', $solicitud)
                        ->get();
                    }else{
                        //se consulta  si el nivel anterior al actual ya aprobó la solicitud
                        $consAproNivelAnterior = DB::table('ohxqc_historico_solicitud')
                        ->where('id_solicitud', '=', $solicitud)
                        ->where('estado', '=', 'A')
                        ->where('nivel_aprobador', '=', $nivelUsuario-1)
                        ->get();
                        if(count($consAproNivelAnterior) > 0){
                            $botonesAccion = true;
                              $detalles = DB::table('ohxqc_historico_solicitud as hs')
                        ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                        ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                        ->where('hs.id_solicitud', '=', $solicitud)
                        ->get();
                        }else{
                            //Si nisiquiera el nivel anterior ha validado, entonces a los demas niveles le restringimos acceso
                            //abort(403);
                            return view('Permisos::sinValidar');
                        }

                    }
                }
            }
        }

        
      
        $documentos = DB::table('ohxqc_documentos_solicitud')
        ->where('solicitud_id', '=', $solicitud)
        ->get();

        $sedesVisitar = DB::table('ohxqc_sedes_solicitud as sol')
        ->select('sede.nombre')
        ->join('ohxqc_sede_fisica as sede', 'sede.id_sedef', 'sol.id_sede')
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

       $tiposVisitante = DB::table('ohxqc_tipos_visitante')
       ->select('id_tipo_visitante', 'nombre')
       ->where('estado', '=', 1)
       ->get();


     
        if($accede){
            return view('Permisos::validacionSolicitud', compact('solicitud','ideIngreso', 'arrayInfo', 'documentos', 'sedesVisitar' ,'empresas', 'horarios', 'sedes', 'tiposVisitante', 'botonesAccion', 'msjRechazo', 'detalles'));
        }


       
    }   

    public function enviarCorreo($url, $empVisi, $tipoIngreso,$sedeId,$idSolicitud, $solicitante, $labor)
    {
        //var_dump($this->infoDeEmpresa);
        
        $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
        ->select('nivel','correo_usuario')
        ->where('empresa_id', '=', $empVisi)
        ->where('tipo_visitante', '=', $tipoIngreso)
        ->where('sede_id', '=', $sedeId)
        ->get();

        $users = Array();
        $i = 0;
         foreach($infoEmpresa as $inf){
            if($inf->nivel == 1){
                $users[$i] = $inf->correo_usuario;
                $i++;
            }
        }
        $correos = User::whereIn('email', $users)->limit(1)->get();
        
        //envía correo a los del primer flujo
        Notification::send($correos, new enviarSolicitud($url,$idSolicitud, $solicitante, $labor, 1));
        //envía correo al solicitante avisando el registro de la solicitud
        $correo = User::where('id', auth()->user()->id)->limit(1)->get();
        Notification::send($correo, new enviarSolicitud($url,$idSolicitud, $solicitante, $labor, 2));
        echo true;
    }

    public function validarSolicitud(Request $request)
    {
        $idSolicitud = $request->input('idsolicitud');
        $tipoVisi = $request->input('idtipovisitante');
        $comentario = $request->input('comentario');
        $empresaId = $request->input('idempresa');

        //consultar nivel de este usuario
        $consultNivel = DB::table('ohxqc_config_solicitud_empresas')
                                    ->select('nivel')
                                    ->where('usuario_aprobador_id', '=', auth()->user()->id)
                                    ->where('empresa_id', '=', $empresaId)
                                    ->where('tipo_visitante', '=', $tipoVisi)
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

                          //Enviar correo a todos los del flujo para informar aprobacion
                            $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
                            ->select('correo_usuario')
                            ->where('empresa_id', '=', $empresaId)
                            ->where('tipo_visitante', '=', $tipoVisi)
                            ->get();
                        
                            $users = Array();
                            $i = 0;
                        foreach($infoEmpresa as $inf){
                                $users[$i] = $inf->correo_usuario;
                                $i++;
                            }
                        
                        $correos = User::whereIn('email',$users)->get();
                        //consulto la info de esta solicitud para enviar por correo al nuevo nivel.
                        $infSolicitud = DB::table('ohxqc_solicitud_ingreso')->select('solicitante','labor_realizar')->where('id_solicitud',$idSolicitud)->get();
                        foreach($infSolicitud as $info){
                            $solicitante = $info->solicitante;
                            $labor = $info->labor_realizar;
                        }
                        
                        Notification::send($correos, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", ""));

                        //Envia correo tambien al solicitante
                          $correo = DB::table('ohxqc_solicitud_ingreso')->select('correo_solicitante')->where('id_solicitud',$idSolicitud)->get();
                          foreach($correo as $corr){
                              $correo = $corr->correo_solicitante;
                          }
                          $user = User::where('email',$correo)->get();
                          Notification::send($user, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", ""));

                        return redirect()->back()->with('msj', 'Solicitud aprobada y Flujo terminado.');
                }else{


                    //si no es mayor, entonces se hace la actualizacion del nivel_actual y token

                     //reasigno un id de solicitud para generar un nuevo token
                     //$this->solicitudID = $idSolicitud;
                     //$token = RegistroVisitanteController::getLinkSubscribe();

                    DB::table('ohxqc_solicitud_por_aprobar')
                    ->where('id_solicitud', '=', $idSolicitud)
                    ->update([
                            'nivel_actual' => $siguienteNivel
                    ]);

                    //despues enviaría el nuevo correo a las personas del siguiente nivel
                    $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
                    ->select('correo_usuario')
                    ->where('empresa_id', '=', $empresaId)
                    ->where('nivel', '=', $siguienteNivel)
                    ->where('tipo_visitante', '=', $tipoVisi)
                    ->get();
                   
                    $users = Array();
                    $i = 0;
                   foreach($infoEmpresa as $inf){
                        $users[$i] = $inf->correo_usuario;
                        $i++;
                    }

                    $correos = User::whereIn('email',$users)->get();
                    //consulto la info de esta solicitud para enviar por correo al nuevo nivel.
                    $infSolicitud = DB::table('ohxqc_solicitud_ingreso')->select('solicitante','labor_realizar')->where('id_solicitud',$idSolicitud)->get();
                    foreach($infSolicitud as $info){
                        $solicitante = $info->solicitante;
                        $labor = $info->labor_realizar;
                    }
                    //Obtengo nuevamente el token de esta solicitud
                    $infoToken = DB::table('ohxqc_solicitud_por_aprobar')->select('token')->where('id_solicitud',$idSolicitud)->get();
                    foreach($infoToken as $tok){
                        $token = $tok->token;
                    }
                    Notification::send($correos, new enviarSolicitud($token,$idSolicitud, $solicitante, $labor, 1));

                    if($infoEmpresa){
                        return redirect()->back()->with('corrEnv', 'Solicitud aprobada y enviada al siguiente aprobador');
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

              //Enviar correo a todos los del flujo para informar rechazo
              $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
              ->select('correo_usuario')
              ->where('empresa_id', '=', $empresaId)
              ->where('tipo_visitante', '=', $tipoVisi)
              ->get();
          
              $users = Array();
              $i = 0;
             foreach($infoEmpresa as $inf){
                  $users[$i] = $inf->correo_usuario;
                  $i++;
              }
          
            $correos = User::whereIn('email',$users)->get();
            //consulto la info de esta solicitud para enviar por correo al nuevo nivel.
            $infSolicitud = DB::table('ohxqc_solicitud_ingreso')->select('solicitante','labor_realizar')->where('id_solicitud',$idSolicitud)->get();
            foreach($infSolicitud as $info){
                $solicitante = $info->solicitante;
                $labor = $info->labor_realizar;
            }
            
            Notification::send($correos, new notificaSolicitud($idSolicitud, $solicitante, $labor, "R", $comentario));

            //Envia correo tambien al solicitante
            $correo = DB::table('ohxqc_solicitud_ingreso')->select('correo_solicitante')->where('id_solicitud',$idSolicitud)->get();
            foreach($correo as $corr){
                $correo = $corr->correo_solicitante;
            }
            $user = User::where('email',$correo)->get();
            Notification::send($user, new notificaSolicitud($idSolicitud, $solicitante, $labor, "R", ""));

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
