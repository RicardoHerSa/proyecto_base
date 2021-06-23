<?php

namespace App\Modules\Permisos\RegistroVisitante\Controllers;
//require '/app/Portal_Sica/vendor/autoload.php';
require '../vendor\autoload.php';

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
use App\Notifications\porterias;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;
use App\Jobs\registrarPermisos;


class RegistroVisitanteController extends Controller
{
    public $solicitudID = "";
    public $infoDeEmpresa  = "";
    public $tipoIngres = "";
    public $sedeId = "";
    public $nuevaEmpresa = "";    
    
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = DB::table('ohxqc_empresas')
        ->select('codigo_empresa', 'descripcion')
        ->distinct('descripcion')
        /*->whereIn('codigo_empresa', ['42681','119','24305','27027','130','21679','115','128','32506','701','702','703','705','706','707','708','709','710','711','713','714','716','718','720','721','722','723','724','725','726','727','728','729','730','732','733','734','735','736','737','738','739','740','742','743','744',
        '745','129','132'])*/
        ->where('grupo_carvajal',-1)
        ->where('activo', 'S')
        ->orderBy('descripcion')
        ->get();

       $horarios = DB::table('ohxqc_horarios')
       ->select('id', 'descripcion')
       ->get(); 

       $sedes = DB::table('ohxqc_sede_fisica')
       ->orderBy('nombre')
       ->get();

       //Los tipos de visitante se condicionan de acuerdo a los permisos del usuario basado en si es gestor externo y en el id de la orgnizción a la que pertenece
       /** Condiciones:
        * 1.SI EL ID DE ORGANIZACION ES UNA EMPRESA DEL GRUPO CARVAJAL Y ES GESTOR EXTERNO, puede ver todos los tipos de ingreso.
        2.SI EL ID DE ORGANIZACION ES UNA EMPRESA DEL GRUPO CARVAJAL Y  NO  ES GESTOR EXTERNO , se habilitan solo los tipos de ingreso:  Visitante, Contratista.
        3.SI EL ID DE ORGANIZACION NO ES UNA EMPRESA DEL GRUPO CARVAJAL Y  ES GESTOR EXTERNO, se habilitn solo los tipos de ingreso: USUARIO EMPRESA EXTERNA  
        ***/
       $consultaGrupo = DB::table('ohxqc_empresas')
       ->select('grupo_carvajal')
       ->where('codigo_empresa', auth()->user()->profile_orgcountry)
       ->get();
       foreach($consultaGrupo as $gr){
            $grupo = $gr->grupo_carvajal;
       }
       //Grupo Carvajal y es gestor externo
       if($grupo == 1 && auth()->user()->gestor_externo == 1){
            $tiposVisitante = DB::table('ohxqc_tipos_visitante')
            ->select('id_tipo_visitante', 'nombre')
            ->where('estado', '=', 1)
            ->get();
       //Grupo Carvajal y no es gestor externo
       }else if($grupo == 1 && auth()->user()->gestor_externo == 0){
            $tiposVisitante = DB::table('ohxqc_tipos_visitante')
            ->select('id_tipo_visitante', 'nombre')
            ->where('estado', '=', 1)
            ->whereIn('id_tipo_visitante', [2, 3])
            ->get();
        //Usuario empresa externa
       }else{
             $tiposVisitante = DB::table('ohxqc_tipos_visitante')
            ->select('id_tipo_visitante', 'nombre')
            ->where('estado', '=', 1)
            ->where('id_tipo_visitante', '=', 4)
            ->get();
       }
     

        return view('Permisos::registroVisitante', compact('empresas', 'horarios', 'sedes', 'tiposVisitante', 'grupo'));
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
        set_time_limit(0);
        // Recibe información del solicitante
        $solicitante = $request->input('solicitante');
        $tipoIngreso = $request->input('tipoIngreso'); //recibe es el id
        $nombreTipoIngreso = DB::table('ohxqc_tipos_visitante')->select('nombre')->where('id_tipo_visitante', '=', $tipoIngreso)->get();
        foreach($nombreTipoIngreso as $nombreTipo){
            $nombreIngreso = $nombreTipo->nombre;   //este nombre se ingresa a solicitud_ingreso
        }
        if($tipoIngreso == 4){
            $nombreIngreso = "EMPRESA EXTERNA";
        }
        $tipoId = $request->input('tipoId');
        if($request->input('empresaContratista') != null){
         $empresaContratista = $request->input('empresaContratista');
        }else{
            $empresaContratista = 0;
        }

        $horario = 8; //ID Horario especial
        $empVisi = $request->input('selEmpresa');

        //Recibe labor a realizar
        $labor = $request->input('labor');

        //Recibe el tipo de registro de visitantes para saber si fueron escritos o masivos(excel)
        $tipoRegistroV = $request->input('tipoRegistroVisi');

        
        //id de la solicitud para guardar en la tabla ohxqc_solicitud_ingreso
        $idSolicitud =  DB::table('ohxqc_solicitud_ingreso')->max('id_solicitud')+1;
        //var_dump($request->input('anexo'));
        //validar si el registro es masivo, para comprobar que el excel sea correcto y si no, devolver
        $urlComprimido = null;
        if($tipoRegistroV == "RM"){
            $cantidadAnexos = $request->input('cantR');
            if($cantidadAnexos == 0){ //Solo debemos tener un documento para valdar como masivo
                $urlDocumento = $request->file('anexo')->store('documentosSolicitud', 'public'); //se sube para poder leerlo
                $urlComprimido = $request->file('comprimidoCola')->store('documentosSolicitud', 'public'); //contendrá el comprimido de los colaboradores.
                $msj = RegistroVisitanteController::validarExcel($urlDocumento, $idSolicitud);
                if(substr($msj,0,5) == "error"){
                   return redirect()->back()->with('errExcel',$msj);
                }
            }

        }

        
        //validar que las fechas de ingreso no sean mayor a las de fin o que no sean menor a la fecha actual
        $errorFechas = false;
        //Fecha Hoy
        $fechaHoy = date('Y-m-d');
        
        if($tipoRegistroV == "RI"){
            if($request->input('fechaIngreso') > $request->input('fechaFinal'))
            {
                $errorFechas = true;
            }
            $cantidadAnexos = $request->input('cantR');
            if($cantidadAnexos > 0){
                for($i=1; $i <= $cantidadAnexos; $i++){
                    if($request->input('fechaIngreso'.$i) > $request->input('fechaFinal'.$i))
                    {
                        $errorFechas = true;
                    } 
                }
            }
        }

        //si las fechas estan correctas prosigo, sino, devuelvo
        if(!$errorFechas){

            $correoSolicitante = DB::table('jess_users')->select('email')->where('id', auth()->user()->id)->get();
            foreach($correoSolicitante as $corr){
                $correoSolicitante = $corr->email;
            }
            $guardaSolicitud = DB::table('ohxqc_solicitud_ingreso')->insert([
                'id_solicitud' => $idSolicitud,
                'empresa_id' =>  $empVisi,
                'horario_id' => $horario,
                'solicitante' => $solicitante,
                'tipo_ingreso' => $nombreIngreso,
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
                            'url_comprimido' =>  $urlComprimido,
                            'fecha_inicio' =>  $request->input('fechaIngreso'),
                            'fecha_fin' =>  $request->input('fechaFinal'),
                            'usuario_creacion' => auth()->user()->name,
                            'fecha_creacion' => now(),
                            'estado' => 'A',
                            'tipo_identificacion' =>  $request->input('tipoId'),
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
                                    'fecha_inicio' =>  $request->input('fechaIngreso'.$i),
                                    'fecha_fin' =>  $request->input('fechaFinal'.$i),
                                    'usuario_creacion' => auth()->user()->name,
                                    'fecha_creacion' => now(),
                                    'estado' => 'A',
                                    'tipo_identificacion' =>  $request->input('tipoId'.$i),
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
                        'url_comprimido' =>  $urlComprimido,
                        'fecha_inicio' =>  $request->input('fechaIngreso'),
                        'fecha_fin' =>  $request->input('fechaFinal'),
                        'usuario_creacion' => auth()->user()->name,
                        'fecha_creacion' => now(),
                        'estado' => 'A',
                        'tipo_identificacion' => strlen($urlComprimido)>0?'NIT': $request->input('tipoId'),
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
                                         $this->sedeId = $request->input('sede'.$j);
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
                                            'tipo_registro' => $tipoRegistroV
            
                                        ]);
                                            //Enviar el correo con esta solicitud, y la url
                                             $enviar = RegistroVisitanteController::enviarCorreo($token, $empVisi,$tipoIngreso, $request->input('sede'.$j),$idSolicitud, $solicitante, $labor);
                                           
                                        
                                    }else{
                                        $entraSede++;
                                        //si no hay info de esta sede, se debe aprobar  inmediatamente
                                            $this->solicitudID = $idSolicitud;
                                            $this->tipoIngres = $tipoIngreso;
                                            $this->sedeId = $request->input('sede'.$j);
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
                                                'sede_id'=> $request->input('sede'.$j),
                                                'tipo_registro' => $tipoRegistroV
    
                                            ]);
                               
                           
                                            //se actualiza de una vez la aprobacion
                                            DB::table('ohxqc_historico_solicitud')->insert([
                                                'id_his' =>  DB::table('ohxqc_historico_solicitud')->max('id_his')+1,
                                                'id_solicitud' => $idSolicitud,
                                                'nivel_aprobador' => 1,
                                                'usuario_aprobador' => auth()->user()->id,
                                                'fecha_diligenciado' => now(),
                                                'comentario' => 'Aprobado inmediatamente porque no hay configuración de flujos posteriores.',
                                                'estado' => 'A',
                                                'sede_id' => $request->input('sede'.$j)
                                            ]);
                                            //se agregan a la tabla visitantes y se asigna permisos
    
                                            RegistroVisitanteController::agregarVisitantes($idSolicitud,$empVisi,$request->input('sede'.$j));
    
                                            //Enviar el correo avisando unicamente al solicitante, porque no hay flujo
    
                                        $correo = User::where('id',auth()->user()->id)->get();
                                        Notification::send($correo, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", "", $request->input('sede'.$j) ));
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
                                ->where('sede_id', '=', $request->input('sede'))
                                ->max('nivel');
        
                                if($infoNivel > 0){
                                    $niveles = $infoNivel;
                                    //al obtener resultados se debe insertar en la tabla ohxqc_solicitud_por_aprobar
        
                                     //se llama a un metodo el cual sea el que genere la URL TOKEN
                                     $this->solicitudID = $idSolicitud;
                                     $this->tipoIngres = $tipoIngreso;
                                     $this->sedeId = $request->input('sede');
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
                                        'sede_id' => $request->input('sede'),
                                        'tipo_registro' => $tipoRegistroV
        
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
                                    $this->sedeId = $request->input('sede');
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
                                       'sede_id' => $request->input('sede'),
                                       'tipo_registro' => $tipoRegistroV
        
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
                                        'estado' => 'A',
                                        'sede_id' => $request->input('sede')
                                    ]);
                                        //se agregan a la tabla visitantes y se asignan permisos
                                         RegistroVisitanteController::agregarVisitantes($idSolicitud,$empVisi,$request->input('sede'));
    
                                        //Enviar el correo avisando unicamente al solicitante, porque no hay flujo
        
                                       $correo = User::where('id',auth()->user()->id)->get();
                                       Notification::send($correo, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", "", $request->input('sede')));
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
        }else{
            return redirect('registro-visitante')->with('errFechas', 'Las fechas de Ingreso no deben ser mayor a las fechas de fin, ni deben ser menor a la fecha actual: '.$fechaHoy);
        }
    }

 

    //este es el que crear la url temporal, con 3 parametros: nombre_ruta,fecha_Expira,array
    public function getLinkSubscribe()
    {
        //temporarySignedRoute
        return URL::signedRoute(
            'event.subscribe', 
            //now()->addMinutes(5), 
            ['solicitud' => $this->solicitudID, 'ingreso'=> $this->tipoIngres, 'sede'=> $this->sedeId]
        );
    }

    //Este metodo se ejecuta cuando el usuario de click en el enlace generado arriba
    public function subscribe(Request $request, $solicitud, $ideIngreso, $sedeID)
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
            $idhorario = $info->horario_id;
            $solicitante = $info->solicitante;
            $tipoIngreso = $info->tipo_ingreso;
            $empresaContratista = $info->empresa_contratista;
            $labor = $info->labor_realizar;
        }
        
        $arrayInfo[] = array('solicitante'=>$solicitante, 'tipoIngreso'=>$tipoIngreso, 'empresaC'=>$empresaContratista,'horario'=>$idhorario,'empVisitar'=>$idEmpresa,'labor'=>$labor);
            //valido que el usuario quien intenta ingresar, esté autorizado para validar solicitudes de acuerdo
            //a la empresa y al tipo de visitante que debe validar, de no ser así, abortamos
            $consultaPermiso = DB::table('ohxqc_config_solicitud_empresas')
        ->select('usuario_aprobador_id')
        ->where('empresa_id', '=', $idEmpresa)
        ->where('tipo_visitante', '=', $ideIngreso)
        ->where('sede_id', '=', $sedeID)
        ->where('usuario_aprobador_id', '=', auth()->user()->id)
        ->get();
        if(count($consultaPermiso) > 0){
            $accede = true;
        }else{
            //Si existe la empresa, entonces abortamos porque quiere decir que no existe el usuario o el tipo
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
        ->where('sede_id', '=', $sedeID)
        ->where('estado', '=', 'Rechazado')
        ->get();
        if(count($consultaRechazado) > 0){
            $botonesAccion = false;
            $msjRechazo = true;
            $detalles = DB::table('ohxqc_historico_solicitud as hs')
            ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
            ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
            ->where('hs.id_solicitud', '=', $solicitud)
            ->where('hs.sede_id', '=', $sedeID)
            ->get();
        }else{
            $detalles = "";
            $msjRechazo = false;
            //Consultar si la solicitud ya ha sido aprobada, para no mostrar los botones de accion
            //O tambien se podria abort(403)
            
            $consultaAprobacion = DB::table('ohxqc_solicitud_por_aprobar')
            ->where('id_solicitud', '=', $solicitud)
            ->where('sede_id', '=', $sedeID)
            ->where('estado', '=', 'Aprobado')
            ->get();
            if(count($consultaAprobacion) > 0){
                $botonesAccion = false;
                 $detalles = DB::table('ohxqc_historico_solicitud as hs')
                ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                ->where('hs.id_solicitud', '=', $solicitud)
                ->where('hs.sede_id', '=', $sedeID)
                ->get();
            }else{
                //Si aún la solicitud está pendiente, entonces se consulta en el histórico para saber si
                //el nivel al que pertenece este usuario ya ha aprobado o no la solicitud.
                $consultaAprobacionHistorico = DB::table('ohxqc_historico_solicitud')
                ->where('id_solicitud', '=', $solicitud)
                ->where('sede_id', '=', $sedeID)
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
                    ->where('hs.sede_id', '=', $sedeID)
                    ->get();
                }else{
                    /***Si la consulta devuelve 0, pueden ocurrir dos situaciones,
                     *  1: que ningun usuario del nivel actual ha validado la solicitud.
                     *  2: que ni siquiera el nivel anterior al del usuario actual la ha validado
                     * Por esta razón al tener el nivel del usuario actual, le restamos 1 para comparar si
                     * el nivel anterior ya ha validado o si definitamente no, así sabremos si halitar los botones***/
                    $consultaNivelActual = DB::table('ohxqc_config_solicitud_empresas')
                    ->select('nivel')
                    ->where('sede_id', '=', $sedeID)
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
                        ->where('hs.sede_id', '=', $sedeID)
                        ->get();
                    }else{
                        //se consulta  si el nivel anterior al actual ya aprobó la solicitud
                        $consAproNivelAnterior = DB::table('ohxqc_historico_solicitud')
                        ->where('id_solicitud', '=', $solicitud)
                        ->where('sede_id', '=', $sedeID)
                        ->where('estado', '=', 'A')
                        ->where('nivel_aprobador', '=', $nivelUsuario-1)
                        ->get();
                        if(count($consAproNivelAnterior) > 0){
                            $botonesAccion = true;
                              $detalles = DB::table('ohxqc_historico_solicitud as hs')
                        ->select('nivel_aprobador as nivel', 'name as usuario', 'fecha_diligenciado as fecha','estado' ,'comentario')
                        ->join('jess_users as j', 'j.id', '=', 'hs.usuario_aprobador')
                        ->where('hs.id_solicitud', '=', $solicitud)
                        ->where('hs.sede_id', '=', $sedeID)
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
        ->orderBy('id_registro', 'DESC')
        ->get();

        //validar si esxiste una empresa para mostrar como encabezado
        $arrayDatosEmpresa = array();
        foreach($documentos as $docu){
            if($docu->tipo_identificacion == "NIT"){
                $arrayDatosEmpresa[0] = $docu->nombre;
                $arrayDatosEmpresa[1] = $docu->identificacion;
                $arrayDatosEmpresa[2] = $docu->url_comprimido;
                $arrayDatosEmpresa[3] = $docu->url_documento;
                break;
            }
        }

        $sedesVisitar = DB::table('ohxqc_sedes_solicitud as sol')
        ->select('sede.descripcion')
        ->join('ohxqc_ubicaciones as sede', 'sede.id_ubicacion', 'sol.id_sede')
        ->where('sol.id_solicitud', '=', $solicitud)
        ->get();

        $empresaVisitar = DB::table('ohxqc_empresas')
        ->select('codigo_empresa', 'descripcion')
        ->where('codigo_empresa', $idEmpresa)
        ->orderBy('descripcion')
        ->get();


       $tiposVisitante = DB::table('ohxqc_tipos_visitante')
       ->select('id_tipo_visitante', 'nombre')
       ->where('estado', '=', 1)
       ->get();

       $tipoSolicitud = DB::table('ohxqc_solicitud_por_aprobar')
       ->select('tipo_registro')
       ->where('id_solicitud', $solicitud)
       ->where('sede_id', $sedeID)
       ->where('tipo_visitante', $ideIngreso)
       ->get();
       foreach($tipoSolicitud as $tipo){
           $tipoR = $tipo->tipo_registro;
       }

     
        if($accede){
            return view('Permisos::validacionSolicitud', compact('solicitud','ideIngreso','sedeID','arrayInfo', 'documentos', 'sedesVisitar' ,'empresaVisitar','tiposVisitante', 'botonesAccion', 'msjRechazo', 'detalles', 'tipoR', 'arrayDatosEmpresa'));
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
        set_time_limit(0);
        $idSolicitud = $request->input('idsolicitud');
        $tipoVisi = $request->input('idtipovisitante');
        $sedeID = $request->input('idsede');
        $comentario = $request->input('comentario');
        $empresaId = $request->input('idempresa');
            
        //consultar nivel de este usuario
        $consultNivel = DB::table('ohxqc_config_solicitud_empresas')
                                    ->select('nivel')
                                    ->where('usuario_aprobador_id', '=', auth()->user()->id)
                                    ->where('empresa_id', '=', $empresaId)
                                    ->where('tipo_visitante', '=', $tipoVisi)
                                    ->where('sede_id', '=', $sedeID)
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
                'estado' => 'A',
                'sede_id' => $sedeID
            ]);

            if($guardaHistorico){
                //Despues de guardar el histórico, actualizamos la tabla ohxqc_solicitud_por_aprobar
                $siguienteNivel = $nivel + 1;
                //consultamos los niveles registrados actualmente en esa tabla para comparar
                $nivelFinal = DB::table('ohxqc_solicitud_por_aprobar')
                ->select('niveles')
                ->where('id_solicitud', '=', $idSolicitud)
                ->where('sede_id', '=', $sedeID)
                ->get();
                foreach($nivelFinal as $f){
                    $ultimoNivel = $f->niveles;
                }

                //comparar si el siguiente nivel es mayor al ultimo nivel, para saber si ya termina el flujo
                if($siguienteNivel > $ultimoNivel){
                        //si es mayor, entonces se hace la actualizacion del nivel_actual,estado,comentario y token 
                        DB::table('ohxqc_solicitud_por_aprobar')
                        ->where('id_solicitud', '=', $idSolicitud)
                        ->where('sede_id', '=', $sedeID)
                        ->update([
                                'nivel_actual' => $ultimoNivel,
                                'estado' => 'Aprobado',
                                'comentario' => $comentario,
                                'token'=> null
                        ]);

                        /**Despues de ser aprobada, se deben registrar los visitantes, la empresa visitante, los permisos etc
                        
                         ***/
                        
                        RegistroVisitanteController::agregarVisitantes($idSolicitud,$empresaId,$sedeID);
                     
                          //Enviar correo a todos los del flujo para informar aprobacion
                            $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
                            ->select('correo_usuario')
                            ->where('empresa_id', '=', $empresaId)
                            ->where('tipo_visitante', '=', $tipoVisi)
                            ->where('sede_id', '=', $sedeID)
                            ->get();
                        
                            $users = Array();
                            $i = 0;
                        foreach($infoEmpresa as $inf){
                                $users[$i] = $inf->correo_usuario;
                                $i++;
                            }
                        
                        $correos = User::whereIn('email',$users)->get();
                        //consulto la info de esta solicitud 
                        $infSolicitud = DB::table('ohxqc_solicitud_ingreso')->select('solicitante','labor_realizar')->where('id_solicitud',$idSolicitud)->get();
                        foreach($infSolicitud as $info){
                            $solicitante = $info->solicitante;
                            $labor = $info->labor_realizar;
                        }
                        
                        Notification::send($correos, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", "", $sedeID));

                        //Envia correo tambien al solicitante
                          $correo = DB::table('ohxqc_solicitud_ingreso')->select('correo_solicitante')->where('id_solicitud',$idSolicitud)->get();
                          foreach($correo as $corr){
                              $correo = $corr->correo_solicitante;
                          }
                          $user = User::where('email',$correo)->get();
                          Notification::send($user, new notificaSolicitud($idSolicitud, $solicitante, $labor, "A", "", $sedeID));

                        //Enviar correo a las porterias de la sede: ohxqc_correos_porterias
                        $consultaCorreosPorterias = DB::table('ohxqc_correos_porterias')->select('correo')->where('sede_id', $sedeID)->get();
                        foreach($consultaCorreosPorterias as $porteros){
                            Notification::route('mail', $porteros->correo)
                            ->notify(new porterias($idSolicitud,$solicitante,$sedeID, $labor));
                        }

                        return redirect()->back()->with('msj', 'Solicitud aprobada y Flujo terminado.');
                }else{


                    //si no es mayor, entonces se hace la actualizacion del nivel_actual y token
                    DB::table('ohxqc_solicitud_por_aprobar')
                    ->where('id_solicitud', '=', $idSolicitud)
                    ->where('sede_id', '=', $sedeID)
                    ->update([
                            'nivel_actual' => $siguienteNivel
                    ]);

                    //despues enviaría el nuevo correo a las personas del siguiente nivel
                    $infoEmpresa = DB::table('ohxqc_config_solicitud_empresas')
                    ->select('correo_usuario')
                    ->where('empresa_id', '=', $empresaId)
                    ->where('nivel', '=', $siguienteNivel)
                    ->where('tipo_visitante', '=', $tipoVisi)
                    ->where('sede_id', '=', $sedeID)
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
                    $infoToken = DB::table('ohxqc_solicitud_por_aprobar')->select('token')->where('id_solicitud',$idSolicitud)->where('sede_id', '=', $sedeID)->get();
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
                'estado' => 'R',
                'sede_id' => $sedeID
            ]);
            
            //Cambiamos el estado de la solicitud por aprobar, comentario y token
            DB::table('ohxqc_solicitud_por_aprobar')
            ->where('id_solicitud', '=', $idSolicitud)
            ->where('sede_id', '=', $sedeID)
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
              ->where('sede_id', '=', $sedeID)
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
            
            Notification::send($correos, new notificaSolicitud($idSolicitud, $solicitante, $labor, "R", $comentario, $sedeID));

            //Envia correo tambien al solicitante
            $correo = DB::table('ohxqc_solicitud_ingreso')->select('correo_solicitante')->where('id_solicitud',$idSolicitud)->get();
            foreach($correo as $corr){
                $correo = $corr->correo_solicitante;
            }
            $user = User::where('email',$correo)->get();
            Notification::send($user, new notificaSolicitud($idSolicitud, $solicitante, $labor, "R", "", $sedeID));

            return redirect()->back()->with('soliRech', 'La solicitud #'.$idSolicitud.', ha sido rechazada.');
            
        }
    }
                                        // 4        // 128       //96
    public function agregarVisitantes($idSolicitud,$idempresa,$sedeID)
    {
        $usuarioCreador =  substr(auth()->user()->name , 0,25);

        //obtener todas las porterias de la sede, para asignar los permisos
        $arrayPermisos = array();
        $ubicaciones = DB::table('ohxqc_ubicaciones')->select('id_ubicacion')->where('id_padre', $sedeID)->get();
        $i=0;
        foreach($ubicaciones as $per){
            $arrayPermisos[$i] = $per->id_ubicacion;
            $i++;
        }
        array_push($arrayPermisos, 2); //ppne carvajal centro empresarial
        //consultamos los datos relevantes de la tabla: ohxqc_solicitud_ingreso, para insertar en visitantes
        $solicitudIngreso = DB::table('ohxqc_solicitud_ingreso')
        ->select('tipo_ingreso')
        ->where('id_solicitud', $idSolicitud)
        ->get();
        foreach($solicitudIngreso as $soli){
            $tipoIngr = $soli->tipo_ingreso;
        }
        if($tipoIngr == "EMPRESA EXTERNA"){
            $tipoIngr = "USUARIOS EMPRESA EXTERNA";
        }

        $idTipoIngreso = DB::table('ohxqc_tipos_visitante')->select('id_tipo_visitante')->where('nombre',  $tipoIngr)->get();
        foreach($idTipoIngreso as $tip){
            $idTipoVisi = $tip->id_tipo_visitante;
        }
        //consultamos los visitantes que fueron guardados en la tabla: ohxqc_documentos_solicitud
        $listadoVisitantes = DB::table('ohxqc_documentos_solicitud')
        ->select('identificacion', 'nombre', 'fecha_inicio', 'fecha_fin', 'tipo_identificacion')
        ->where('solicitud_id', $idSolicitud)
        ->get();
        foreach($listadoVisitantes as $v){
            //consulto si el visitante ya estaba registrado para simplemente actualizarlo
            $consultVisitante = DB::table('ohxqc_visitantes')->select('id_visitante')->where('identificacion',$v->identificacion)->get();
            if(count($consultVisitante) > 0){
                foreach($consultVisitante as $id){
                    $idNuevoVisitante = $id->id_visitante;
                }
                //actualiza
                DB::table('ohxqc_visitantes')->where('id_visitante', $idNuevoVisitante)->update([
                    'fecha_ingreso' =>  $v->fecha_inicio,
                    'fecha_fin' => $v->fecha_fin,
                    'tipo_visitante' =>  $idTipoVisi,
                    'cargo' =>  $tipoIngr,
                    'usuario_creacion' =>  $usuarioCreador,
                    'fecha_creacion' => now(),
                    'usuario_actualizacion' =>  $usuarioCreador,
                    'fecha_actualizacion' => now(),
                    'responsable' => $usuarioCreador,
                ]);
                  //Actualiza la empresa visitante
                DB::table('ohxqc_empresas_visitante')->where('id_visitante', $idNuevoVisitante)->update([
                    'id_empresa' => $idempresa,
                    'activo' => 'S',
                    'usuario_creacion' =>  $usuarioCreador,
                    'fecha_creacion' => now(),
                    'usuario_actualizacion' =>  $usuarioCreador,
                    'fecha_actualizacion' => now()
                ]);

                //actualiza permisos
                 //insertamos los permisos para cada visitante en la sede actual
                 for($j = 0; $j < count($arrayPermisos); $j++){
                    DB::table('ohxqc_permisos')->insert([
                    'id_permiso' => DB::table('ohxqc_permisos')->max('id_permiso')+1,
                    'id_empresa_visitante' => $idNuevoVisitante,
                    'id_ubicacion' =>  $arrayPermisos[$j],
                    'id_horario' => 8, //DIA HORARIO ESPECIAL
                    'identificacion_responsable' =>  $v->identificacion,
                    'fecha_inicio' => is_null($v->fecha_inicio)?now():$v->fecha_inicio,
                    'fecha_fin' =>  is_null($v->fecha_fin)?now():$v->fecha_fin,
                    'activo' => 'S',
                    'usuario_creacion' => 'admin',
                    'fecha_creacion' => now(),
                    'usuario_actualizacion' => 'admin',
                    'fecha_actualizacion' => now()
                ]);
               
            }
                
              

            }else{
                //Registro nuevo
                $idNuevoVisitante = DB::table('ohxqc_visitantes')->max('id_visitante')+1;
                DB::table('ohxqc_visitantes')->insert([
                    'id_visitante' => $idNuevoVisitante,
                    'identificacion_jefe' => null,
                    'tipo_identificacion' =>  $v->tipo_identificacion,
                    'identificacion' => $v->identificacion,
                    'nombre' =>  $v->nombre,
                    'apellido' => null,
                    'fecha_ingreso' =>  $v->fecha_inicio,
                    'fecha_fin' => $v->fecha_fin,
                    'tipo_contrato' => null,
                    'foto' => 'N',
                    'email' => null,
                    'telefono1' => null,
                    'telefono2' => null,
                    'telefono3' => null,
                    'tipo_visitante' =>  $idTipoVisi,
                    'cargo' =>  $tipoIngr,
                    'ciudad' => 0,
                    'activo' => 'S',
                    'usuario_creacion' =>  $usuarioCreador,
                    'fecha_creacion' => now(),
                    'usuario_actualizacion' =>  $usuarioCreador,
                    'fecha_actualizacion' => now(),
                    'parqueadero' => 0,
                    'responsable' => $usuarioCreador,
                    'usr_dominio' => null
                ]);
                     //Insertamos en la empresa visitante: ohxqc_empresas_visitante
                    DB::table('ohxqc_empresas_visitante')->insert([
                        'id_empresa_visitante' => $idNuevoVisitante,
                        'id_visitante' => $idNuevoVisitante,
                        'id_empresa' => $idempresa,
                        'activo' => 'S',
                        'usuario_creacion' =>  $usuarioCreador,
                        'fecha_creacion' => now(),
                        'usuario_actualizacion' =>  $usuarioCreador,
                        'fecha_actualizacion' => now()
                    ]);
                    //insertamos los permisos para cada visitante en la sede actual
                    for($j = 0; $j < count($arrayPermisos); $j++){
                            DB::table('ohxqc_permisos')->insert([
                            'id_permiso' => DB::table('ohxqc_permisos')->max('id_permiso')+1,
                            'id_empresa_visitante' => $idNuevoVisitante,
                            'id_ubicacion' =>  $arrayPermisos[$j],
                            'id_horario' => 8, //DIA HORARIO ESPECIAL
                            'identificacion_responsable' =>  $v->identificacion,
                            'fecha_inicio' => is_null($v->fecha_inicio)?now():$v->fecha_inicio,
                            'fecha_fin' =>  is_null($v->fecha_fin)?now():$v->fecha_fin,
                            'activo' => 'S',
                            'usuario_creacion' => 'admin',
                            'fecha_creacion' => now(),
                            'usuario_actualizacion' => 'admin',
                            'fecha_actualizacion' => now()
                        ]);
                       
                    }
            }

        }
    }

    public function validarExcel($urlDocumento, $idSolicitud)
    {
        
        $ruta = storage_path('app\public/'.$urlDocumento);
       // $ruta ='/app/Portal_Sica/storage/app/public/'.$urlDocumento;
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        
        $spreadsheet = $reader->load($ruta);
        $worksheet = $spreadsheet->getActiveSheet();

        $cantFilas = $worksheet->getHighestRow();


        $arrayColumnas = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $arrayColumnasPermitidas = ['A', 'B', 'C', 'D', 'E', 'F'];

        //validar que no estén escritas las otras columnas
        for ($i=0; $i < count($arrayColumnas) ; $i++) { 
            for ($j=1; $j <= $cantFilas ; $j++) { 
                //echo $arrayColumnas[$i].$j."<br>";
                if(strlen($worksheet->getCell($arrayColumnas[$i].$j)) > 0){
                    if($j == 1){
                        return "error, la columna: ".$arrayColumnas[$i].$j.", NO debe contener cabeceras ni registros.";
                         break;
                    }else{
                        return "error, la columna: ".$arrayColumnas[$i].$j.", NO debe contener registros.";
                         break;
                    }
                   
                }
            }
        }

        
       
        $noEsNumero = true;
        $celdasNoEsNumero = array();
        $arrayIdentidades = array();

        $noEsDocumento = true;
        $celdasNoEsDocumento = array();
        $arrayTiposDocu = array();

        $noEsFechaIngr = true;
        $arrayFechaIngreso = array();
        $celdasNoEsFechaIngr = array();

        $noEsFechaFin = true;
        $arrayFechaFinal = array();
        $celdasNoEsFechaFin = array();

        $arrayNombres = array();
        $arrayEstados = array();
        

        if(strlen($worksheet->getCell('A1')) == 0 || strtoupper($worksheet->getCell('A1')->getValue()) != "TIPO IDENTIFICACION"){
            return "error, no se encuentra la cabecera para la columna A: TIPO IDENTIFICACION";
        }else if(strlen($worksheet->getCell('B1')) == 0  || strtoupper($worksheet->getCell('B1')->getValue()) != "IDENTIFICACION"){
            return "error, no se encuentra la cabecera para la columna B: IDENTIFICACION";
        }else if(strlen($worksheet->getCell('C1')) == 0  || strtoupper($worksheet->getCell('C1')->getValue()) != "NOMBRE"){
            return "error, no se encuentra la cabecera para la columna C: NOMBRE";
        }else if(strlen($worksheet->getCell('D1')) == 0  || strtoupper($worksheet->getCell('D1')->getValue()) != "FECHA INGRESO"){
            return "error, no se encuentra la cabecera para la columna D: FECHA INGRESO";
        }else if(strlen($worksheet->getCell('E1')) == 0  || strtoupper($worksheet->getCell('E1')->getValue()) != "FECHA FIN"){
            return "error, no se encuentra la cabecera para la columna E: FECHA FIN";
        }else if(strlen($worksheet->getCell('F1')) == 0  || strtoupper($worksheet->getCell('F1')->getValue()) != "ESTADO"){
            return "error, no se encuentra la cabecera para la columna F: ESTADO";
        }

        $conG = 1;
        $columns = 0;

        $conA = 0;
        $conB = 0;
        $conC = 0;
        $conD = 0;
        $conE = 0;
        $conF = 0;

        //Fecha Hoy
        $fechaHoy = date('Y-m-d');
        
        while (strlen($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)) > 0) {
            //echo $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)."<br>";
            switch ($columns) {
                case 0:
                    if(strtoupper($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue()) != "CC" && strtoupper($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue()) != "PS"){
                        if($conG != 1){
                            $noEsDocumento = false;
                            array_push($celdasNoEsDocumento, "A".$conG);
                        }
                    }else{
                        //voy guardando los tipos de documento
                        $arrayTiposDocu[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                    }   
                    
                    $conA++;
                    break;
                case 1:
                    if(gettype($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue()) != "integer"){
                        if($conG != 1){
                            $noEsNumero = false;   //validar que la identificacion sea numero.
                            array_push($celdasNoEsNumero, "B".$conG);
                        }
                    }else{
                        //voy guardando los documentos de identificación para asociarlos a los nombres.
                        $arrayIdentidades[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                    }
                    $conB++;
                    break;
                case 2:
                    if($conG != 1){
                        $arrayNombres[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                    }
                    $conC++;
                    break;
                case 3:
                    if($conG != 1){
                        if($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue() < $fechaHoy){

                            return "error, Las fechas de ingreso y fecha fin, no deben ser menor a la fecha actual: ".$fechaHoy.", en la celda: D".$conG;
                        }
                        $explode = explode('-',$worksheet->getCell($arrayColumnasPermitidas[$columns].$conG));
                        $explode[0];
                        $explode[1];
                        $explode[2];
                                            //mes        //dia        //AÑO
                        if(checkdate($explode[1], $explode[2], $explode[0] )){
                            $arrayFechaIngreso[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                        }else{
                            $noEsFechaIngr = false;
                            array_push($celdasNoEsFechaIngr, "D".$conG);
                        }

                    }
                    $conD++;
                    break;
                case 4:
                    if($conG != 1){
                        if($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue() < $fechaHoy){

                            return "error, Las fechas de ingreso y fecha fin, no deben ser menor a la fecha actual: ".$fechaHoy.", en la celda: E".$conG;
                        }
                        $explode = explode('-',$worksheet->getCell($arrayColumnasPermitidas[$columns].$conG));
                        $explode[0]."<br>";
                        $explode[1]."<br>";
                        $explode[2]."<br>";
                                            //mes        //dia        //AÑO
                        if(checkdate($explode[1], $explode[2], $explode[0] )){
                            $arrayFechaFinal[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                        }else{
                            $noEsFechaFin = false;
                            array_push($celdasNoEsFechaFin, "E".$conG);
                        }
                    }
                    $conE++;
                    break;
                case 5:
                    if($conG != 1){
                        $arrayEstados[$conG] = $worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)->getValue();
                    }
                    $conF++;
                    break;
            }
            $conG++;
            if(!strlen($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)) > 0 && $columns != 5){
                $conG = 1;
                $columns++;
            }else if(!strlen($worksheet->getCell($arrayColumnasPermitidas[$columns].$conG)) > 0 && $columns == 5){
                $arrayEstados[$conG] = "A";
                $conG++;
            }
        }

        
      
        //Se valida que todas las columnas tengn la misma cantidad de registros, omitiendo la columna de estados
        if($conA == $conB && $conB == $conC && $conC == $conD && $conD == $conE ){
           
            //Se valida que no hayan tipos de identificacion diferentes a CEDULA O PASAPORTE
            if(!$noEsDocumento){
                $listaCeldas = implode(",", $celdasNoEsDocumento);
                return "error, las celdas [".$listaCeldas."] de la columna TIPO IDENTIFICACION son diferentes a CC O PS.";
            }else{
                //Si los tipos de identificación son válidos, entonces seguimos con la identificacion.
                if(!$noEsNumero){
                    $listaCeldas = implode(",", $celdasNoEsNumero);
                    return "error, las celdas [".$listaCeldas."] de la columna IDENTIFICACION, no tienen un número válido.";
                }else{
                    //se valida que las fechas  de ingreso sean correctas
                    if(!$noEsFechaIngr){
                         $listaCeldas = implode(",", $celdasNoEsFechaIngr);
                         return "error, las celdas [".$listaCeldas."] de la columna FECHA INGRESO, no tienen una fecha válida.";
                    }else if(!$noEsFechaFin){
                         $listaCeldas = implode(",", $celdasNoEsFechaFin);
                         return "error, las celdas [".$listaCeldas."] de la columna FECHA FIN, no tienen una fecha válida.";
                    }else{
                        //Si las fechas están bien, entonces  recibimos los datos.
                                
                        $guardado = false;

                        //validar si la fecha final es mayor a la de ingreso, de no ser asi, retornamos
                        $error = false;
                        for ($i=2; $i < count($arrayFechaFinal)+2 ; $i++) { 
                            if($arrayFechaFinal[$i] < $arrayFechaIngreso[$i]){
                                return "error, la fecha de ingreso no debe ser mayor a la fecha final, en las celdas D".$i." y E".$i;            
                            }
                        }
                    
                        for ($i=2; $i < count($arrayIdentidades)+2 ; $i++) { 
                    
                        $inserta = DB::table('ohxqc_documentos_solicitud')->insert([
                                'id_registro' => DB::table('ohxqc_documentos_solicitud')->max('id_registro')+1,
                                'tipo_identificacion' => $arrayTiposDocu[$i],
                                'identificacion' => $arrayIdentidades[$i],
                                'nombre' => $arrayNombres[$i],
                                'fecha_inicio' => $arrayFechaIngreso[$i],
                                'fecha_fin' => $arrayFechaFinal[$i],
                                'url_documento' => null,
                                'solicitud_id' => $idSolicitud,
                                'usuario_creacion' => auth()->user()->name,
                                'fecha_creacion' => now(),
                                'estado' => isset($arrayEstados[$i])?$arrayEstados[$i]:'A'
                            ]);
                            if($inserta){ $guardado = true;}else{ $guardado = false; }
                        }
                        if($guardado){
                            return "ok, los visitantes han sido guardados.";
                        }else{
                            return "error, ha ocurrido un problema al guardar los visitantes.";
                        }
                    }
                  
                }
            }
        }else{
            //Si las columnas NO son iguales, retornamos y eliminamos el excel.
                return "error, Las columnas del excel no tienen la misma cantidad de registros.";
        }

    }

    public function actualizarSedes($sedes/*Request $request*/)
    {
       // $sedes = $request->input('sedes');
       $longiCadena = strlen($sedes);
        
       $sedes = substr($sedes, 0,$longiCadena-1);
       $listaSedes = explode(",", $sedes);
        $cantSedes = count($listaSedes);
      
        $nuevasSedes = DB::table('ohxqc_sede_fisica')->select('id_sedef', 'nombre')->whereNotIn('id_sedef', $listaSedes)->get();
        foreach($nuevasSedes as $nueva){
            echo "
                <option value='".$nueva->id_sedef."'>".$nueva->nombre."</option>
            
            ";
        }
    }

    public function consultaSedes(Request $request)
    {
        $idempresa = $request->input('idempresa');

        $consulta = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.id_ubicacion', 'ubi.descripcion')
        ->join('ohxqc_empresas as emp', 'emp.sede_especifica_id', 'ubi.id_ubicacion')
        ->where('emp.codigo_empresa', $idempresa)
        ->whereNotIn('emp.sede_especifica_id',[2])  // poner la sede centro empresa
        ->orderBy('ubi.descripcion')
        ->get();

        if(count($consulta) > 0){
            foreach($consulta as $emp){
                echo "<option value='".$emp->id_ubicacion."'>".$emp->descripcion."</option>";
            }
        }else{
            echo 0;
        }

    }


    public function empresaVisitar(Request $request)
    {

        $tipo = $request->input('tipoIngreso');
        $grupo = $request->input('grupo');
        //Siendo colaborador y Si es visitante o contratista, deberia ver todas las empresas de grupo
        if($tipo == 2 || $tipo == 3 && $grupo == 1){
            $consulta = DB::table('ohxqc_empresas')
            ->select('codigo_empresa', 'descripcion')
            ->distinct('descripcion')
            ->where('grupo_carvajal',1)
            ->where('activo', 'S')
            ->orderBy('descripcion')
            ->get();

        //Siendo colaborador y escoge empresa externa, debe ver todas las empresas externas
        }else if($tipo == 4 && $grupo == 1){
            $consulta = DB::table('ohxqc_empresas')
            ->select('codigo_empresa', 'descripcion')
            ->distinct('descripcion')
            ->whereNull('grupo_carvajal')
            ->where('activo', 'S')
            ->orderBy('descripcion')
            ->get();

        //Si no es colaborador, solo debe ver la empresa de él mismo
        }else if($tipo == 4 && $grupo == 2){
            
            $consulta = DB::table('ohxqc_empresas')
            ->select('codigo_empresa', 'descripcion')
            ->distinct('descripcion')
            ->where('activo', 'S')
            ->where('codigo_empresa', auth()->user()->profile_orgcountry)
            ->get();

        }

        if(count($consulta) > 0){
               echo "<option value='0'>Seleccione Empresa</option>";
            foreach($consulta as $emp){
                echo "<option id=".count($consulta)." value='".$emp->codigo_empresa."'>".$emp->descripcion."</option>";
            }
        }else{
            echo 0;
        }



    }

   
   
    
}
