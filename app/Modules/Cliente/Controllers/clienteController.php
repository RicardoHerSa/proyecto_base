<?php

namespace App\Modules\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cliente\Models\cl_pais;
use App\Modules\Cliente\Models\cl_tipo_identifica;
use App\Modules\Cliente\Models\cl_identi_por_pais;
use App\Modules\Cliente\Models\cl_tipo_persona;
use App\Modules\Cliente\Models\cl_tipopersona_pais;
use App\Modules\Cliente\Models\cl_departamento;
use App\Modules\Cliente\Models\cl_direccion_por_formulario;
use App\Modules\Cliente\Models\cl_accionista;
use App\Modules\Cliente\Models\cl_representante;
use App\Modules\Cliente\Models\cl_referencia_comerciale;
use App\Modules\Cliente\Models\cl_servicio_etiqueta;
use App\Modules\Cliente\Models\cl_referencia_bancaria;
use App\Modules\Cliente\Models\cl_barrios;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\Utilidad ;
use App\Services\adobe;
use App\Services\validacionCampos ;
use Illuminate\Support\Facades\Storage;
use App\Services\FlujoApr;
use DB;
use App\Notifications\Correcion;
use Illuminate\Support\Facades\Notification;
use Swift_SwiftException;


class clienteController extends Controller
{
    
    public function index()
    {   
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));
       
    
        return view('Cliente::index');
    }

    public function addGeneral()
    {   

        if (session('lang')){
            if(session('lang') == "es"){
                 $idioma = "es";
            }else{
                $idioma = "en";
            }
         }else{
             $idioma = "es";
         }
        
         

        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }


        //consulto si este usuario ya tiene información general
        $formulario = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->limit(1)->get();

        //si aún no tiene información, consultamos el máximo id del formulario
        if(count($formulario) == 0){
            $maxIdFormulario = DB::table('cl_info_general')->max('id_info')+1;
            
        }else{
            //si ya tiene información general, consulto el id del formulario
            $maxIdFormulario = DB::table('cl_info_general')->select('id_info')->where('usuario_id', '=', $idUsuario)->get();
            foreach($maxIdFormulario as $id){
                $maxIdFormulario =  $id->id_info;
            }
        }   
        
        //consultar las direcciones agregadas a este formulario
            $listaDirecciones = DB::table('cl_direccion_por_formulario')
            ->join('cl_tipo_direccion', 'cl_tipo_direccion.id_tipo_dir', '=', 'cl_direccion_por_formulario.tipo_direccion')
            ->join('cl_municipio', 'cl_municipio.id_muni', '=', 'cl_direccion_por_formulario.municipio_id')
            ->join('cl_departamento', 'cl_departamento.id_depart', '=', 'cl_municipio.departamento_id')
            ->join('cl_pais', 'cl_pais.id_pais', '=', 'cl_departamento.pais_id')
            ->where('cl_direccion_por_formulario.usuario_id', '=', $idUsuario)
            ->get();

        $codePais = $dataUser[0]; //trae pais Empresa
        $codePaisCliente = $dataUser[1]; //trae pais cliente
        $codeEmpresa = $dataUser[2]; //trae Codigo Empresa   

        $idePais = cl_pais::select('id_pais')->where('code', '=', $codePais )->get();
        foreach($idePais as $codigo){
            $idePais = $codigo->id_pais; //guarda el id del país
        }

        $idPaisCliente = cl_pais::select('id_pais')->where('code', '=', $codePaisCliente )->get();
        foreach($idPaisCliente as $codigo){
            $idPaisCliente = $codigo->id_pais; //guarda el id del país
        }

        
        //consulta los tipos de identidad del pais
        $tiposId =  DB::table('cl_tipo_identifica')
                    ->join('cl_identi_por_pais', 'cl_identi_por_pais.tipo_ident_id', '=', 'cl_tipo_identifica.id_tipo')
                    ->join('cl_pais', 'cl_pais.id_pais', '=', 'cl_identi_por_pais.pais_id')
                    ->where('cl_pais.id_pais', '=', $idePais)
                    ->get();

        //consulta el nombre del pais y el id
        $datosPais = cl_pais::select('id_pais','nombre')->where('id_pais', '=', $idePais)->get();


        //consulta info tributaria deacuerdo al pais cliente en tabla maestra
        $datosTributario   =  Utilidad::maestraPais($codePaisCliente,'INFO_TRIBUTARIA','INFO_TRIBUTARIA');
        $grupo_empresarial =  Utilidad::maestraPais($codePais,'GRUPO_EMPRESARIAL','GRUPO_EMPRESARIAL');
        //consulta actividades económicas
        $actividadesEconomicas = Utilidad::maestraPais($codePais,'ACTIVIDAD_ECONOMICA','ACTIVIDAD_ECONOMICA');


        //consulta el nombre del pais y el id pais Cliente
        $datosPaisCliente = cl_pais::select('id_pais','nombre')->where('id_pais', '=', $idPaisCliente)->get();

        $pais = array();
        foreach($datosPais as $datos){
            $pais[0] = $datos->id_pais; 
            $pais[1] = $datos->nombre;

        }

        $paisCliente = array();
        foreach($datosPaisCliente as $dato){
            $paisCliente[0] = $dato->id_pais; 
            $paisCliente[1] = $dato->nombre;
        }

         //consulta los tipos de persona de acuerdo al pais
        
         $tiposPersona =  DB::table('cl_maestra')
         ->where('cl_cod_pais', '=', $codePais)
         ->where('cl_id_var_1', '=', 'TIPO_PERSONA')
         ->where('cl_idioma', '=', $idioma) 
         ->get();

         //consulta listado de paises
         $listadoPaises = cl_pais::orderBy('nombre')->get();

         //consulta tipos de direcciones
         $arrayDirecc = array();
         $i = 0;
         $tiposDirecciones = DB::table('cl_tipo_direccion')->get();
         foreach($tiposDirecciones as $tipos){
            $arrayDirecc[$i] = array( 
                                "nombre_tipo"=>$tipos->nombre_direcc,
                                "id_tipo" =>$tipos->id_tipo_dir
                            );    
            $i++;
         }
         //echo "<pre>";
         //print_r($arrayDirecc);
         //echo $arrayDirecc[0]['nombre_tipo'];
         //var_dump($arrayDirecc);
         $noAplicaDireccion = DB::table('cl_config_campo')
         ->select('obsrvacion')
         ->where('cl_org_id', '=', $codeEmpresa)
         ->where('cl_cod_pais', '=', $codePais)
         ->where('campo', '=', 'TIPOS_DIRECCIONES')
         ->get();
         $listaNoAplica = array();
         $nuevoListadoDirecc = array();
         $j = 0;
         $n = 0;
         $cantNoAPlica = count($noAplicaDireccion);
         foreach($noAplicaDireccion as $noAplica){
            $listaNoAplica[$n] = $noAplica->obsrvacion;
            $n++;
         }
   
         for ($k = 0; $k < 5; $k++){
             
             if (!in_array($arrayDirecc[$k]['nombre_tipo'], $listaNoAplica)) {
                $nuevoListadoDirecc[$k] = $arrayDirecc[$k];
            }
               
            }
         
         

         //consulta listado de formas de recibir facturación electrónica
         $recepcionFacturaElect = DB::table('cl_maestra')
         ->where('cl_cod_pais', '=', $codePais)
         ->where('cl_org_id', '=', $codeEmpresa)
         ->where('cl_id_var_1', '=', 'RECIBE_FACTURA')
         ->where('cl_estado','=','ACTIVO')
         ->where( 'cl_idioma'  , '=' , app()->getLocale())
         ->get();

         //indicador
         $indicador = DB::table('cl_direccion_por_formulario')->where('usuario_id', '=', $idUsuario)->count('id_ubicacion');
        
        /*porcentaje
        $porcentaje = clienteController::calcularPorcentaje($codePaisCliente,$codeEmpresa, $idUsuario);
        $porcentajes = explode('/', $porcentaje);*/

        $porcentaje = clienteController::porcentajeModulo($codePaisCliente,$codeEmpresa, $idUsuario);
        
       
      return view('Cliente::infoGeneral', 
                        compact('grupo_empresarial',
                                'formulario',
                                'maxIdFormulario',
                                'codePais', 
                                'codeEmpresa', 
                                'tiposId', 
                                'tiposPersona',
                                'pais', 
                                'listadoPaises',
                                'nuevoListadoDirecc',
                                'actividadesEconomicas', 
                                'listaDirecciones', 
                                'indicador',
                                'paisCliente',
                                'datosTributario', 
                                'idGet', 
                                'recepcionFacturaElect'));
    }

    public function addInfo()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }

        //consulta de datos de acuerdo al usuario
        $datos = DB::table('cl_info_adicional')->where('usuario_id', '=', $idUsuario)->get();

        //porcentaje
        $porcentaje = clienteController::porcentajeModulo($codePaisCliente,$codeEmpresa, $idUsuario);
        
        return view('Cliente::infoadd', compact('codePais', 'codeEmpresa', 'datos', 'idGet'));
    }

    public function addEtiqueta()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }

        //consulta etiquetas
        $listaEtiquetas = cl_servicio_etiqueta::where('usuario_id', '=', $idUsuario)->get();
        //indicador
        $indicador = cl_servicio_etiqueta::where('usuario_id', '=', $idUsuario)->count('id_etiqueta');
        //porcentaje
        $porcentaje = clienteController::porcentajeModulo($codePaisCliente,$codeEmpresa, $idUsuario);
        return view('Cliente::infoEtiquetas', compact('codePais', 'codeEmpresa', 'listaEtiquetas', 'indicador','idGet'));
    }


    public function addCalidad()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
        //si viene un id en el get, este será redireccionado entre las páginas
        $idGet = "";
        if($dataUser[4] == "si"){
            $idGet = $idUsuario;
        }
        //consultar si el usuario ha llenado datos de este módulo
        $datos = DB::table('cl_calidad_tributaria')->where('usuario_id', '=', $idUsuario)->get();
        //consultar los ICA
        $listaIca = DB::table('cl_ica')->where('usuario_id', '=', $idUsuario)->get();


        // Responsabilida Fiscal
        $responsabilidad_fiscal  =  Utilidad::maestraPais($codePais,'RESPONSABILIDAD_FISCAL','RESPONSABILIDAD_FISCAL');
        

        // Tipo de Obligacion
        $tipo_obligacion  =  Utilidad::maestraPais($codePais,'TIPO_OBLIGACION','TIPO_OBLIGACION');
        
        //indicador
        $indicador = DB::table('cl_ica')->where('usuario_id', '=', $idUsuario)->count('id_ica');

         //porcentaje
         $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);
        return view('Cliente::infoCalidad', compact('codePais','listaIca', 'datos', 'codeEmpresa', 'indicador', 'idGet','responsabilidad_fiscal','tipo_obligacion'));
    }

   
    public function addAccion()
    {   
        if(session('lang')){
            if(session('lang') == "es"){
                $idioma = "es";
            }else{
                $idioma = "en";
            }
        }else{
            $idioma = "es";
        }
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3] ; //trae idusuario
         //si viene un id en el get, este será redireccionado entre las páginas
         $idGet = "";
         if($dataUser[4] == "si"){
            $idGet = $idUsuario;
        }

        //listado de paises
        $listadoPaises = cl_pais::get();
        //idepais
        $idePais = cl_pais::select('id_pais')->where('code', '=', $codePais )->get();
        foreach($idePais as $codigo){
            $idePais = $codigo->id_pais; //guarda el id del país
        }
         //consulta los tipos de persona de acuerdo al pais
         $tiposPersona =  DB::table('cl_maestra')
         ->where('cl_cod_pais', '=', $codePais)
         ->where('cl_id_var_1', '=', 'TIPO_PERSONA')
         ->where('cl_idioma', '=', $idioma)
         ->get();

         //consulta accionistas y socios
        $listaAcc = cl_accionista::join('cl_pais', 'cl_pais.id_pais', '=', 'cl_accionistas.pais_origen')
        ->where('usuario_id', '=', $idUsuario)->get();
        //indicador
        $indicador = cl_accionista::where('usuario_id', '=', $idUsuario)->count('id_accion');
        
        //porcentaje
        $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);
        
       return view('Cliente::accionistas', compact('codePais','listadoPaises', 'tiposPersona', 'listaAcc','codeEmpresa', 'indicador', 'idGet'));
    }

    public function addJunta()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3] ; //trae codigo Empresa
        //si viene un id en el get, este será redireccionado entre las páginas
        $idGet = "";
        if($dataUser[4] == "si"){
            $idGet = $idUsuario;
        }


        //$cancelado = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'N')->count();
               

        //lista de junta directiva
        $listaJunta = DB::table('cl_junta_directiva')
        ->join('cl_pais', 'cl_pais.id_pais', '=', 'cl_junta_directiva.pais_origen')
        ->where('usuario_id', '=', $idUsuario)->get();

        //lista de paises
        $paises = cl_pais::get();
    
        //indicador
        $indicador = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'S')->count('id_junta');

         //porcentaje
         $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);
        return view('Cliente::junta', compact('codePais', 'listaJunta', 'paises', 'codeEmpresa', 'indicador', 'idGet'));
    }

    public function addRepresentante()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3] ; //trae codigo Empresa
         //si viene un id en el get, este será redireccionado entre las páginas
         $idGet = "";
         if($dataUser[4] == "si"){
            $idGet = $idUsuario;
        }

        //lista de representantes
        $listaRepre = cl_representante::join('cl_pais', 'cl_pais.id_pais', '=', 'cl_representantes.pais_origen') ->where('usuario_id', '=', $idUsuario)->get();

        //lista de paises
        $paises = cl_pais::get();
        //indicador
        $indicador = cl_representante::where('usuario_id', '=', $idUsuario)->count('id_repre');

          //porcentaje
          $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);
        return view('Cliente::representante', compact('codePais', 'codeEmpresa', 'listaRepre', 'paises', 'indicador', 'idGet'));
    }

    public function addReferenBanc()
    {   
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }

        //consulta referencias bancarías
        $listaReferencias = cl_referencia_bancaria::where('usuario_id', '=', $idUsuario)->get();
        //indicador
        $indicador = cl_referencia_bancaria::where('usuario_id', '=', $idUsuario)->count('id_referencia');
        //porcentaje
        $porcentaje = clienteController::porcentajeModulo($codePaisCliente,$codeEmpresa, $idUsuario);
        return view('Cliente::infoReferenBanc', compact('codePais', 'codeEmpresa', 'listaReferencias','indicador', 'idGet'));
    }

    public function addReferenComer()
    {   
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ; //trae pais Empresa
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; //trae id usuario
         //si viene un id en el get, este será redireccionado entre las páginas
         $idGet = "";
         if($dataUser[4] == "si"){
            $idGet = $idUsuario;
        }


        //consultar las referencias
        $referencias = cl_referencia_comerciale::where('usuario_id', '=', $idUsuario)->get();

        //indicador
        $indicador = cl_referencia_comerciale::where('usuario_id', '=', $idUsuario)->count('id_referen_comer');

         //porcentaje
         $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);
        return view('Cliente::infoReferenComer', compact('codePais', 'codeEmpresa', 'referencias', 'indicador', 'idGet'));
    }
    public function  load()
    {   
            //Trae informacion de usuario jess_users
            $dataUser = clienteController::infoUser();
            $codePais = $dataUser[0] ; //trae pais Empresa
            $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
            $idUsuario = $dataUser[3]; //trae id usuario
            //si viene un id en el get, este será redireccionado entre las páginas
            $idGet = "";
            if($dataUser[4] == "si"){
                $idGet = $idUsuario;
            }
    
            $idioma = app()->getLocale();
          

           //consultar términos y condiciones
            //Tratamiento de datos
            $tratamientoDatos = DB::table('cl_terminos')
            ->select('cl_va_atrr_1')
            ->where('cl_id_var_1', '=', 'TRATAMIENTO_DATOS')
            ->where('cl_org_id', '=', $codeEmpresa)
            ->where('cl_cod_pais', '=', $codePais)
            ->where('cl_idioma', '=', $idioma)
            ->get();
            //Terminos y Condiciones
            $terminosCondiciones = DB::table('cl_terminos')
            ->select('cl_va_atrr_1')
            ->where('cl_id_var_1', '=', 'TERMINOS_CONDICIONES')
            ->where('cl_org_id', '=', $codeEmpresa)
            ->where('cl_cod_pais', '=', $codePais)
            ->where('cl_idioma', '=', $idioma)
            ->get();

            //veracidad de la información
            $terminosVeracidad = DB::table('cl_terminos')
            ->select('cl_va_atrr_1')
            ->where('cl_id_var_1', '=', 'VERACIDAD_INFORMACION')
            ->where('cl_org_id', '=', $codeEmpresa)
            ->where('cl_cod_pais', '=', $codePais)
            ->where('cl_idioma', '=', $idioma)
            ->get();

            //Declaración origen de los recursos
            $terminosOrigen = DB::table('cl_terminos')
            ->select('cl_va_atrr_1')
            ->where('cl_id_var_1', '=', 'ORIGEN_RECURSO')
            ->where('cl_org_id', '=', $codeEmpresa)
            ->where('cl_cod_pais', '=', $codePais)
            ->where('cl_idioma', '=', $idioma)
            ->get();

        //consulta si ya tiene documentos guardados
        $tieneDocumentos = DB::table('cl_documentos')->where('usuario_id', '=', $idUsuario)->count();
        //$AllDocumentos = DB::table('cl_documentos')->where('usuario_id', '=', $idUsuario)->get();

        $condicionalDocumentos = false;
        $fechaSubida = false;
        if($tieneDocumentos > 0){
            $condicionalDocumentos = true;
            $fechaSubida = DB::table('cl_documentos')->select('fecha_hora')->where('usuario_id', '=', $idUsuario)->get();
            foreach($fechaSubida as $fechaS){
                $fechaSubida = $fechaS->fecha_hora;
            }
        }


        //porcentaje
        $porcentaje = clienteController::porcentajeModulo($codePais,$codeEmpresa, $idUsuario);

        //consulta el tipo de persona 
        $paisGuardado = DB::table('jess_users')->select('cl_org_id','cl_cod_pais', 'cl_cod_pais_cliente')->where('id', '=', $idUsuario)->get();
        
        $tipoPersonaElegida = "";
        foreach($paisGuardado as $pais){
            if($pais->cl_cod_pais == $pais->cl_cod_pais_cliente){
                
                $tipoPersonaElegida = DB::table('cl_info_general')
                ->select('tipo_persona')
                ->where('usuario_id','=', $idUsuario)
                ->get();

                if(count($tipoPersonaElegida) > 0){
                    foreach($tipoPersonaElegida as $elegida){
                        $tipoPersonaElegida = $elegida->tipo_persona;
                    }
                }else{
                      $tipoPersonaElegida = "ORGANIZATION";
                }
               
            }else{
                $tipoPersonaElegida = "EXTERNO";
            }
        }
         

        $org_id = $paisGuardado[0]->cl_org_id;
        $cod_pais =  $paisGuardado[0]->cl_cod_pais;
        $tipo_persona = $tipoPersonaElegida ;

        $lista_campo = validacionCampos::ValidacionDocuemtos($org_id, $cod_pais, $tipo_persona);

        $val = DB::table('cl_terminos_aceptados')
        ->where('id_user',$idUsuario)->count();


        return view('plantillas.loadFile.carga_documento_natural', 
        compact('codePais', 
                'codeEmpresa',
                'codeEmpresa', 
                'terminosCondiciones',
                'tratamientoDatos',
                'terminosVeracidad', 
                'terminosOrigen', 
                'idioma', 
                'idGet', 
                'idUsuario', 
                'condicionalDocumentos',
                'fechaSubida', 
                'tipoPersonaElegida',
                'val',
                'lista_campo',
            ));
    }


    public function infoUser()
    {   

        $Rol = Utilidad::UserRol();
        $condicion = "no";
        if ($Rol =='Cliente' ){
            
            $idUsuario = auth()->user()->id; 

        }else{
            if(isset($_GET) && !empty($_GET)){
                if(isset($_GET['id'])){
                    $idUsuario =  $_GET['id'];
                    $condicion = "si";
                }
            }
        }

        $UserData = array();
        $jess_users = DB::table('jess_users')->where('id', '=', $idUsuario )->get();
        foreach($jess_users as $codigo){

            $UserData[0]  = $codigo->cl_cod_pais; //trae pais Empresa
            $UserData[1]  = $codigo->cl_cod_pais_cliente; //trae pais cliente
            $UserData[2]  = $codigo->cl_org_id; //trae Codigo Empresa
            $UserData[3]  = $idUsuario; // id de usuario a consultar
            $UserData[5]  = $codigo->cl_terminos_pago;
            $UserData[6]  = $codigo->cl_cupo_sugerido;
            $UserData[7]  = $codigo->tipo_moneda;
            $UserData[8]  = $codigo->name;
        }
        $UserData[4]  = $condicion; // condicion de get

        return $UserData;
    }


    public function eleccion()
    {   
    
        return view('Cliente::index');
    }

    //Consulta en tiempo real el listado de departamentos

    public function listadoDepartamentos($idpais){
        
        $departamentos = DB::table('cl_departamento')->where('pais_id', '=', $idpais)->get();
        
        echo '<option value=0>Seleccione Departamento</option>';
        foreach($departamentos as $departa){
            echo '<option value=' . $departa->id_depart . '>' . $departa->nombre_departamento.'</option>';
        }


    }

     //Consulta en tiempo real el listado de ciudades
     public function listadoDeCiudades($ideDeparta){
        
        $municipios = DB::table('cl_municipio')
                        ->where('departamento_id', '=', $ideDeparta)
                        ->get();
        
        echo '<option value=0>Seleccione Ciudad</option>';
        foreach($municipios as $ciudad){
            echo '<option value=' . $ciudad->id_muni . '>' . $ciudad->nombre_municipio.'</option>';
        }


    }

     //Consulta en tiempo real el listado de barrios
     public function listadoDeBarrios($idCiudad){
        
        $barrios = cl_barrios::where('municipio_id', '=', $idCiudad)->get();
        if(count($barrios) > 0){
            echo '<option value=0>Seleccione Barrio/Colonia</option>';
            foreach($barrios as $barri){
                echo '<option value=' . $barri->id_barrio . '>' . $barri->nombre_barrio.'</option>';
            }
        }else{
            echo "n";
        }
        
       


    }

    //guardar direcciones de cada formulario
    public function guardarDirecciones($idciudad,$codigo,$direccion,$tipo,$telefono,$condicion,$colo_provi,$idbarrio){
        if($idbarrio == 0){
            $nombreBarrio = null;
        }else{
            $nombreBarrio = DB::table('cl_barrios')->where('id_barrio', '=', $idbarrio)->get();
            foreach($nombreBarrio as $nombreBarr){
                $nombreBarrio = $nombreBarr->nombre_barrio;
            }
        }
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];

        if($condicion == true){
            $colonia_provincia = $colo_provi;
        }else{
            $colonia_provincia = null;
        }
        $seleccionados = explode("-", $tipo);

        $aceptaRegistroMatriz = false;
        $validaMatriz = DB::table('cl_direccion_por_formulario')
        ->select('direccion')
        ->where('usuario_id', '=', $idUsuario)
        ->where('tipo_direccion', '=', 1)
        ->limit(1)
        ->get();
        if(count($validaMatriz) > 0){
            foreach($validaMatriz as $dir){
                if($dir->direccion == $direccion){
                    $aceptaRegistroMatriz = true; 
                }else{
                    for($i = 1; $i < count($seleccionados); $i++){
                        if($seleccionados[$i] != 1){
                            $aceptaRegistroMatriz = true; 
                        }else{
                            echo "errorMatriz";
                            break;
                        }
                    }
                    
                }
            }
        }else{
            $aceptaRegistroMatriz = true; 
        }

       
        //echo $aceptaRegistroMatriz." ".$aceptaRegistroDespacho;
        if($aceptaRegistroMatriz){
            for($i = 1; $i < count($seleccionados); $i++){
                $informacion =  DB::table('cl_direccion_por_formulario')->insert([
                    'codigo_postal' => $codigo,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'tipo_direccion' => $seleccionados[$i],
                    'nombre_colonia_provincia' => $colonia_provincia,
                    'municipio_id' => $idciudad,
                    'nombre_barrio' => $nombreBarrio,
                    'usuario_id' => $idUsuario
                ]);
            }
            if($informacion){
                echo "e";
            }else{
                echo "n";
            }
        }   

    }
    public function consultarDirecciones($codePais){
        //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        //consultar las direcciones agregadas a este formulario
        $listaDirecciones = DB::table('cl_direccion_por_formulario')
        ->join('cl_tipo_direccion', 'cl_tipo_direccion.id_tipo_dir', '=', 'cl_direccion_por_formulario.tipo_direccion')
        ->join('cl_municipio', 'cl_municipio.id_muni', '=', 'cl_direccion_por_formulario.municipio_id')
        ->join('cl_departamento', 'cl_departamento.id_depart', '=', 'cl_municipio.departamento_id')
        ->join('cl_pais', 'cl_pais.id_pais', '=', 'cl_departamento.pais_id')
        ->where('cl_direccion_por_formulario.usuario_id', '=', $idUsuario)
        ->get();

 
          if(count($listaDirecciones) > 0){
            foreach ($listaDirecciones as $lista){
                if($lista->nombre_barrio!=null){
                    $estilo = "";
                    $nombreB = $lista->nombre_barrio ;
                }else{
                    $estilo = "text-decoration: line-through";
                    $nombreB = "No Registra";
                }
            echo "
            <tr>
                <td>".$lista->nombre."</td>
                <td>".$lista->nombre_municipio."</td>
                <td style='".$estilo."'>".$nombreB."</td>";
                if($codePais == "MX" || $codePais == "PE" || $codePais == "EC"){
                    echo "<td>".$lista->nombre_colonia_provincia."</td>";
                }
               echo "
                <td>".$lista->codigo_postal."</td>
                <td>".$lista->direccion."</td>
                <td>".$lista->nombre_direcc."</td>
                <td>".$lista->telefono."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarDireccion(".$lista->id_ubicacion.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>
            </tr>
            ";
            }
          }else{
              echo "<b>No se ha agregado direcciones.</b>";
          }
    }

    public function eliminarDireccion($id){
        $eliminar = DB::table('cl_direccion_por_formulario')->where('id_ubicacion', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
       
  }

    public function contadorDireccion(){
       //Trae informacion de usuario jess_users
       $dataUser = clienteController::infoUser();
       $idUsuario = $dataUser[3];
    $contador = DB::table('cl_direccion_por_formulario')->where('usuario_id', '=',  $idUsuario)->count('id_ubicacion');
    echo "(".$contador.")";
   
}

//guardar direcciones de cada formulario
    public function guardarIca($codigo,$ciudad,$tarifa){
      //Trae informacion de usuario jess_users
      $dataUser = clienteController::infoUser();
      $idUsuario = $dataUser[3];
    $informacion =  DB::table('cl_ica')->insert([
          'codigo_act' => $codigo,
          'ciudad_tributacion' => $ciudad,
          'tarifa' => $tarifa,
          'usuario_id' =>  $idUsuario 
      ]);

      if($informacion){
          echo "e";
      }else{
          echo "n";
      }

  }
 //consultarica 
  public function consultarIca(){
       //Trae informacion de usuario jess_users
       $dataUser = clienteController::infoUser();
       $idUsuario = $dataUser[3];
    //consultar los ica de este formulario
    $listaIca = DB::table('cl_ica')->where('usuario_id', '=', $idUsuario)->get();
    if(count($listaIca) > 0){
      foreach ($listaIca as $lista){
      echo "
      <tr>
        <td>".$lista->codigo_act."</td>
        <td>".$lista->ciudad_tributacion."</td>
        <td>".$lista->tarifa."</td>
        <td><button type='button' class='btn btn-danger' onclick='eliminarIca(".$lista->id_ica.")'><i class='fa fa-trash' aria-hidden='true'></i>
        </button></td>
      </tr>
      ";
      }
    }else{
        echo "<b>No se ha agregado ICA.</b>";
    }
}

public function eliminarIca($id){
    $eliminar = DB::table('cl_ica')->where('id_ica', '=', $id)->delete();
    if($eliminar){
        echo "e";
    }else{
        echo "n";
    }
   
}
    //contador ica
    public function contadorIca(){
         //Trae informacion de usuario jess_users
      $dataUser = clienteController::infoUser();
      $idUsuario = $dataUser[3];
        $contador = DB::table('cl_ica')->where('usuario_id', '=',  $idUsuario)->count('id_ica');
        echo "(".$contador.")";
    
    }

    //consultar Accionista 
    public function consultarAcc(){
            $dataUser = clienteController::infoUser();
            $idUsuario = $dataUser[3];
        //consultar los accionistas de este formulario
        $listaAcc = cl_accionista::join('cl_pais', 'cl_pais.id_pais', '=', 'cl_accionistas.pais_origen')
        ->where('usuario_id', '=',$idUsuario)->get();
        if(count($listaAcc) > 0){
            foreach ($listaAcc as $lista){
            echo "
            <tr>
                <td>".$lista->tipo_persona."</td>
                <td>".$lista->nombre_razon.' '.$lista->apellido."</td>
                <td>".$lista->identificacion."</td>
                <td>".$lista->nombre."</td>
                <td>".$lista->fecha_nacimiento."</td>
                <td>".$lista->pep."</td>
                <td>".$lista->participacion."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarAcc(".$lista->id_accion.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>
            </tr>
            ";
            }
        }else{
            echo "<b>No se ha agregado accionistas.</b>";
        }
    }
    //eliminar Acc
    public function eliminarAcc($id){
        $eliminar = cl_accionista::where('id_accion', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador ica
    public function contadorAcc(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $contador = cl_accionista::where('usuario_id', '=', $idUsuario)->count('id_accion');
        echo "(".$contador.")";
    
    }

    //consultar Junta 
    public function consultarJunta(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        //consultar los accionimiembro de la junta de este formulario
        $listaJunta = DB::table('cl_junta_directiva')
        ->join('cl_pais', 'cl_pais.id_pais', '=', 'cl_junta_directiva.pais_origen')
        ->where('usuario_id', '=', $idUsuario)->get();
        
        if(count($listaJunta) > 0){
            foreach ($listaJunta as $lista){
            echo "
            <tr>
                <td>".$lista->nombre_apellido." ".$lista->apellido."</td>
                <td>".$lista->identificacion."</td>
                <td>".$lista->nombre."</td>
                <td>".$lista->fecha_nacimiento."</td>
                <td>".$lista->pep."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarJunta(".$lista->id_junta.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>
            </tr>
            ";
            }
        }else{
            echo "<b>No se ha agregado miembros de junta.</b>";
        }
    }
    //eliminar miembro Junta
    public function eliminarJunta($id){
        $eliminar = DB::table('cl_junta_directiva')->where('id_junta', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador junta
    public function contadorJunta(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $contador = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'S')->count('id_junta');
        echo "(".$contador.")";
    
    }

    //consultar representante  
    public function consultarRepresentantes(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        //consultar los representantes
        $listaRepre = cl_representante::join('cl_pais', 'cl_pais.id_pais', '=', 'cl_representantes.pais_origen')
        ->where('usuario_id', '=', $idUsuario)->get();
        if(count($listaRepre) > 0){
            foreach ($listaRepre as $lista){
            echo "
            <tr>
                <td>".$lista->nombre_apellido.' '.$lista->apellido."</td>
                <td>".$lista->identificacion."</td>
                <td>".$lista->nombre."</td>
                <td>".$lista->fecha_nacimiento."</td>
                <td>".$lista->pep."</td>
                <td>".$lista->email."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarRepresentante(".$lista->id_repre.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>
            </tr>
            ";
            }
        }else{
            echo "<b>No se ha agregado representantes.</b>";
        }
    }
    //eliminar representante
    public function eliminarRepresentante($id){
        $eliminar = cl_representante::where('id_repre', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador Representante
    public function contadorRepresentante(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $contador = cl_representante::where('usuario_id', '=', $idUsuario)->count('id_repre');
        echo "(".$contador.")";
    
    }

    

    //consultar referencias comerciales
     public function consultarReferenciasComerciales(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        //consultar los representantes
        $referencias = cl_referencia_comerciale::where('usuario_id', '=', $idUsuario)->get();
        if(count($referencias) > 0){
            foreach ($referencias as $lista){
            echo "
            <tr>
                <td>".$lista->nombre_empresa."</td>
                <td>".$lista->nombre_contacto."</td>
                <td>".$lista->cupo_credito."</td>
                <td>".$lista->telefono."</td>
                <td>".$lista->correo."</td>
                <td>".$lista->plazo_venta."</td>
                <td>".$lista->ciudad."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarReferencia(".$lista->id_referen_comer.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>
            </tr>
            ";
            }
        }else{
            echo "<b>No se ha agregado referencias comerciales.</b>";
        }
    }
    //eliminar referencias comerciales
    public function eliminarReferenciasComerciales($id){
        $eliminar = cl_referencia_comerciale::where('id_referen_comer', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador referencias comerciales
    public function contadorReferenciasComerciales(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $contador = cl_referencia_comerciale::where('usuario_id', '=', $idUsuario)->count('id_referen_comer');
        echo "(".$contador.")";
    
    }

    //consultar etiquetas
    public function consultarEtiquetas(){
        
          //consulta etiquetas
          $listaEtiquetas = cl_servicio_etiqueta::where('usuario_id', '=', $idUsuario)->get();
        if(count($listaEtiquetas) > 0){
            foreach ($listaEtiquetas as $lista){
            echo "
            <tr>
                <td>".$lista->dia_horario_revision."</td>
                <td>".$lista->datos_documentos."</td>
                <td>".$lista->dia_horario_pagos."</td>
                <td>".$lista->nombre_socio."</td>
                <td>".$lista->num_proveedor."</td>
                <td>".$lista->etiquetas."</td>
                <td><button type='button' class='btn btn-danger' onclick='eliminarEtiqueta(".$lista->id_etiqueta.")'><i class='fa fa-trash' aria-hidden='true'></i>
                </button></td>           
            </tr>
            ";
            }
        }else{
            echo "<b>No se han agregado etiquetas.</b>";
        }
    }
    //eliminar etiquetas
    public function eliminarEtiquetas($id){
        $eliminar = cl_servicio_etiqueta::where('id_etiqueta', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador etiquetas
    public function contadorEtiquetas(){
        $contador = cl_servicio_etiqueta::where('usuario_id', '=', $idUsuario)->count('id_etiqueta');
        echo "(".$contador.")";
    
    }

    //consultar referencias bancarias
    public function consultarReferenciasBancarias(){
        
        //consulta etiquetas
        $listaReferencias = cl_referencia_bancaria::where('usuario_id', '=', $idUsuario)->get();
      if(count($listaReferencias) > 0){
          foreach ($listaReferencias as $lista){
          echo "
          <tr>
            <td>".$lista->nombre_banco."</td>
            <td>".$lista->sucursal."</td>
            <td>".$lista->numero_cuenta."</td>
            <td>".$lista->correo."</td>
            <td><button type='button' class='btn btn-danger' onclick='eliminarBanco(".$lista->id_referencia.")'><i class='fa fa-trash' aria-hidden='true'></i>
            </button></td>
          </tr>
          ";
          }
      }else{
          echo "<b>No se han agregado referencias bancarias.</b>";
      }
  }
    //eliminar  referencias bancarias
    public function eliminarReferenciasBancarias($id){
        $eliminar = cl_referencia_bancaria::where('id_referencia', '=', $id)->delete();
        if($eliminar){
            echo "e";
        }else{
            echo "n";
        }
    
    }
    //contador  referencias bancarias
    public function contadorReferenciasBancarias(){
        $contador = cl_referencia_bancaria::where('usuario_id', '=', $idUsuario)->count('id_referencia');
        echo "(".$contador.")";
    
    }

    //Recibe formulario de avance general
    public function registroAvanceGeneral($tipoid,$numid,$tipoper,$nombrer,$apellido,$pep,$acta,$fecha,$pais){
        //valido si el usuario ya tenía datos de acuerdo al  id del usuario
          //Trae informacion de usuario jess_users
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $consultaExistencia = DB::table('cl_info_general')->where('usuario_id', '=',$idUsuario)->count('id_info');

        if($apellido == "null"){
            $apellido = null;
        }

          //si el usuario ya tiene un formulario, la información que llega será actualizada(update)
          if($consultaExistencia > 0){
            $query = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->update([
                'fecha_diligencia' => $fecha,
                'num_documento' => $numid,
                'nombre_razon_social' => $nombrer,
                'apellido' => $apellido,
                'pep' => $pep,
                'registro_mercantil' => $acta,
                'tipo_id_tributaria' => $tipoid,
                'tipo_persona' => $tipoper,
                'pais_id' => $pais
            ]);
          }else{
            $query = DB::table('cl_info_general')->insert([
                'fecha_diligencia' => $fecha,
                'num_documento' => $numid,
                'nombre_razon_social' => $nombrer,
                'apellido' => $apellido,
                'pep' => $pep,
                'registro_mercantil' => $acta,
                'tipo_id_tributaria' => $tipoid,
                'tipo_persona' => $tipoper,
                'pais_id' => $pais,
                'usuario_id' => $idUsuario
            ]);
          }

          if($query){
            echo "e";
          }else{
              echo "n";
          }
    }

    //Recibe formulario de registro general

    public function registroGeneral(Request $request){
        
     
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3];
        if($request['idget']){
            $idUsuario = $request['idget'];
            $parametro = "?id=".$idUsuario;
         }else{
            $dataUser = clienteController::infoUser();
            $idUsuario = $dataUser[3];
            $parametro = "";
         }
     
     
     
     
        /*   if($request['idget']){
            $idUsuario = $request['idget'];
            $parametro = "?id=".$idUsuario;
        }else{
            //Trae informacion de usuario jess_users
            $dataUser = clienteController::infoUser();
            $codePais = $dataUser[0] ;
            $idUsuario = $dataUser[3];
            $parametro = "";
        }*/
          
        if($request['pep'] == 0){$request['pep'] = 'N';}

        //if($request['grupo_empresarial_economico'] == 0){$request['grupo_empresarial_economico'] = 'N';}

       // if($request['reealiza_operaciones'] == 0){$request['reealiza_operaciones'] = 'N';}
        
        //valido si el usuario ya tenía datos de acuerdo al  id del usuario
        $consultaExistencia = DB::table('cl_info_general')->where('usuario_id', '=',$idUsuario)->count('id_info');
        

        //si el usuario ya tiene un formulario, la información que llega será actualizada(update)
        if($consultaExistencia > 0){
            
            if ($request['cer_aduana'] == null){
                $request['cer_aduana'] = 'N';
            }
            if ($request['cer_basic'] == null){
                $request['cer_basic'] = 'N';
            }
            if ($request['cer_iso_28000'] == null){
                $request['cer_iso_28000'] = 'N';
            }
            if ($request['cer_iso_9001'] == null){
                $request['cer_iso_9001'] = 'N';
            }
            if ($request['cer_OEA'] == null){
                $request['cer_OEA'] = 'N';
            }
            if ($request['cer_otras'] == null){
                $request['cer_otras'] = 'N';
            }
            if ($request['cer_ninguna'] == null){
                $request['cer_ninguna'] = 'N';
            }

           if (Utilidad::AllUserRol(['Cliente'])){ 
                    $query = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->update([
                        'fecha_diligencia'                  => $request['fecha_diligencia'],
                        'num_documento'                     => $request['num_documento'],
                        'nombre_razon_social'               => $request['nombre_razon_social'],
                        'pep'                               => $request['pep'],
                        'registro_mercantil'                => $request['registro_mercantil'],
                        'actividad_comercial_id'            => $request['actividad_comercial_id'],
                        'grupo_empresarial_economico'       =>  $request['grupo_empresarial_economico'] =='0' ? null :$request['grupo_empresarial_economico'], //$request['grupo_empresarial_economico'],
                        'nombre_grupo'                      => $request['nombre_grupo'],
                        'web'                               => $request['web'],
                        'cer_aduana'                        => $request['cer_aduana'],
                        'cer_basic'                         => $request['cer_basic'],
                        'cer_iso_28000'                     => $request['cer_iso_28000'],
                        'cer_iso_9001'                      => $request['cer_iso_9001'],
                        'cer_OEA'                           => $request['cer_OEA'],
                        'cer_otras'                         => $request['cer_otras'],
                        'cer_ninguna'                       => $request['cer_ninguna'],
                        'email_contacto_compras'            => $request['email_contacto_compras'],
                        'nombre_contacto_compras'           => $request['nombre_contacto_compras'],
                        'apellido_compras'                  => $request['apellido_compras'],
                        'email_contacto_tesoreria'          => $request['email_contacto_tesoreria'],
                        'nombre_contacto_tesoreria'         => $request['nombre_contacto_tesoreria'],
                        'apellido_tesoreria'                => $request['apellido_tesoreria'],
                        'email_recibir_factura'             => $request['email_recibir_factura'],
                        'forma_recibir_factura'             => $request['forma_recibir_factura'],
                        'fecha_limite'                      => $request['fecha_limite'],
                        'realiza_operaciones'               => $request['realiza_operaciones'] =='0' ? null :$request['realiza_operaciones']  ,
                        'tipo_id_tributaria'                => $request['tipo_id_tributaria'],
                        'tipo_persona'                      => $request['tipo_persona'],
                        'pais_id'                           => $request['pais_id'],
                        'id_user_comercial'                 => auth()->user()->id_comercial  ,
                        'factura_oc'                        => isset($request['factura_oc']) && $request['factura_oc'] =='0'?null:$request['factura_oc'],
                        'factura_oc_observacion'            => isset($request['factura_oc_observacion']) && $request['factura_oc_observacion'] =='0'?null:$request['factura_oc_observacion'],
                        'factura_oc_anexo'                  => isset($request['factura_oc_anexo']) && $request['factura_oc_anexo'] =='0'? null:$request['factura_oc_anexo'],
                        
                    ]);

            }
            if (Utilidad::AllUserRol(['Credito'])){ 

                $query = true;
            }

            $transaccion = "u";    

            //si no tenía formulario, entonces será información nueva(insert)
        }else{

            if ($request['cer_aduana'] == null){
                $request['cer_aduana'] = 'N';
            }
            if ($request['cer_basic'] == null){
                $request['cer_basic'] = 'N';
            }
            if ($request['cer_iso_28000'] == null){
                $request['cer_iso_28000'] = 'N';
            }
            if ($request['cer_iso_9001'] == null){
                $request['cer_iso_9001'] = 'N';
            }
            if ($request['cer_OEA'] == null){
                $request['cer_OEA'] = 'N';
            }
            if ($request['cer_otras'] == null){
                $request['cer_otras'] = 'N';
            }
            if ($request['cer_ninguna'] == null){
                $request['cer_ninguna'] = 'N';
            }

           $query =  DB::table('cl_info_general')->insert([
                'fecha_diligencia'              => $request['fecha_diligencia'],
                'num_documento'                 => $request['num_documento'],
                'nombre_razon_social'           => $request['nombre_razon_social'],
                'pep'                           => $request['pep'],
                'registro_mercantil'            => $request['registro_mercantil'],
                'actividad_comercial_id'        => $request['actividad_comercial_id'],
                'grupo_empresarial_economico'   =>  $request['grupo_empresarial_economico'] =='0' ? null :$request['grupo_empresarial_economico'], //$request['grupo_empresarial_economico'],
                'nombre_grupo'                  => $request['nombre_grupo'],
                'web'                           => $request['web'],
                'cer_aduana'                    => $request['cer_aduana'],
                'cer_basic'                     => $request['cer_basic'],
                'cer_iso_28000'                 => $request['cer_iso_28000'],
                'cer_iso_9001'                  => $request['cer_iso_9001'],
                'cer_OEA'                       => $request['cer_OEA'],
                'cer_otras'                     => $request['cer_otras'],
                'cer_ninguna'                   => $request['cer_ninguna'],
                'email_contacto_compras'        => $request['email_contacto_compras'],
                'nombre_contacto_compras'       => $request['nombre_contacto_compras'],
                'apellido_compras'              => $request['apellido_compras'],
                'email_contacto_tesoreria'      => $request['email_contacto_tesoreria'],
                'nombre_contacto_tesoreria'     => $request['nombre_contacto_tesoreria'],
                'apellido_tesoreria'            => $request['apellido_tesoreria'],
                'email_recibir_factura'         => $request['email_recibir_factura'],
                'forma_recibir_factura'         => $request['forma_recibir_factura'],
                'fecha_limite'                  => $request['fecha_limite'],
                'realiza_operaciones'           => $request['realiza_operaciones'] =='0' ? null :$request['realiza_operaciones']  ,
                'tipo_id_tributaria'            => $request['tipo_id_tributaria'],
                'tipo_persona'                  => $request['tipo_persona'],
                'pais_id'                       => $request['pais_id'],
                'estado'                        =>'EN_PROCESO',
                //'id_user_credito' => FlujoApr::setAprobador(auth()->user()->cl_cod_pais,auth()->user()->cl_org_id )->id_userFlujoApr::setAprobador(auth()->user()->cl_cod_pais,auth()->user()->cl_org_id )->id_user,
                'fecha_asignacion'              => date('Y-m-d H:i:s'),
                'usuario_id'                    => $idUsuario,
                'id_user_comercial'=> auth()->user()->id_comercial  ,
                'factura_oc'                        => isset($request['factura_oc']) && $request['factura_oc'] =='0'?null:$request['factura_oc'],
                'factura_oc_observacion'            => isset($request['factura_oc_observacion']) && $request['factura_oc_observacion'] =='0'?null:$request['factura_oc_observacion'],
                'factura_oc_anexo'                  => isset($request['factura_oc_anexo']) && $request['factura_oc_anexo'] =='0'? null:$request['factura_oc_anexo'],
                
            ]);
            $transaccion = "i";    
        }


        //validación del codigo del país según su sesión para saber que vista retornar
        
        switch ($codePais) {
            case 'CO':
                if($query&&$transaccion == "u"){
                    $mensaje = "Información Actualizada y Guardada";
                }else if($query&&$transaccion == "i"){
                    $mensaje = "Información Guardada";
                }else{
                    $mensaje = "Error al intentar code: ".$transaccion;
                }
                return redirect('ifcalidad'.$parametro)->with('mensaje',$mensaje);
                break;

            case 'PE':
                if($query&&$transaccion == "u"){
                    $mensaje = "Información Actualizada y Guardada";
                }else if($query&&$transaccion == "i"){
                    $mensaje = "Información Guardada";
                }else{
                    $mensaje = "Error al intentar code: ".$transaccion;
                }
                return redirect('ifcalidad'.$parametro)->with('mensaje',$mensaje);
                break;

            case 'MX':
                if($query&&$transaccion == "u"){
                    $mensaje = "Información Actualizada y Guardada";
                }else if($query&&$transaccion == "i"){
                    $mensaje = "Información Guardada";
                }else{
                    $mensaje = "Error al intentar code: ".$transaccion;
                }
                return redirect('ifoadd'.$parametro)->with('mensaje',$mensaje);
                break;
            
            default:
                # code...
                break;
        }
        
        //return response()->json($request);
    }   

    //Recibe formulario de registro adicional
    public function registroAdicional(Request $request){
        //Si la empresa es de méxico pero no es ni CT&S, ni Empaques, entonces no hago nada
            //Trae informacion de usuario jess_users
            $dataUser = clienteController::infoUser();
            $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
            $idUsuario = $dataUser[3];
      
        if($codeEmpresa != "CT&S" && $codeEmpresa != "Empaques"){
            //no se reciben los datos y no se opera.
            
        }else{
         
            //se reciben los campos en este método y se insertan, validando si es update o insert
            $cuenta =  DB::table('cl_info_adicional')->where('usuario_id', '=', $idUsuario)->count('id_info_add');

            if($cuenta > 0){
                $query = DB::table('cl_info_adicional')->where('usuario_id', '=', $idUsuario)->update([
                    'forma_pago' => $request['forma_pago'],
                    'metodo_pago' => $request['metodo_pago'],
                    'cfdi' => $request['cfdi'],
                    'contacto_entrega' => $request['contacto_entrega'],
                    'cuenta_pago' => $request['cuenta_pago'],
                    'horario_dia' => $request['horario_dia'],
                    'requiere_oc' => $request['requiere_oc'],
                    'requiere_anexos' => $request['requiere_anexos'],
                    'tipo_anexos' => $request['tipo_anexos'],
                    'factura_portal_cliente' => $request['factura_portal_cliente'],
                    'manual_portal_cliente' => $request['manual_portal_cliente'],
                    'ult_dia_ingreso' => $request['ult_dia_ingreso']
                ]);
                $transaccion = "u";
            }else{
                $query = DB::table('cl_info_adicional')->insert([
                    'forma_pago' => $request['forma_pago'],
                    'metodo_pago' => $request['metodo_pago'],
                    'cfdi' => $request['cfdi'],
                    'contacto_entrega' => $request['contacto_entrega'],
                    'cuenta_pago' => $request['cuenta_pago'],
                    'horario_dia' => $request['horario_dia'],
                    'requiere_oc' => $request['requiere_oc'],
                    'requiere_anexos' => $request['requiere_anexos'],
                    'tipo_anexos' => $request['tipo_anexos'],
                    'factura_portal_cliente' => $request['factura_portal_cliente'],
                    'manual_portal_cliente' => $request['manual_portal_cliente'],
                    'ult_dia_ingreso' => $request['ult_dia_ingreso'],
                    'usuario_id' => $idUsuario
                ]); 
                $transaccion = "i";
            }
        }

        if($query && $transaccion == "u"){
            $mensaje = "Información Actualizada y Guardada";
        }else if($query && $transaccion == "i"){
            $mensaje = "Información Registrada y Guardada";
        }else{
            $mensaje = "Error";
        }
    
        if($codeEmpresa == "CT&S" && $codePais == "MX"){
            return redirect('ifetiqueta')->with('mensaje', $mensaje);
        }else{
            return redirect('ifaccionistas')->with('mensaje', $mensaje);
        }
        
        //return response()->json($request);
    }   
    //Recibe formulario de registro etiquetas
    public function registroEtiqueta($horarior,$documento,$horariop,$nombre,$proveedor,$etiqueta){
        //Si la empresa es de méxico pero no  CT&S,  entonces no hago nada
         //Trae informacion de usuario jess_users
         $dataUser = clienteController::infoUser();
         $codePais = $dataUser[0] ;
         $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
         $idUsuario = $dataUser[3];

      
        if($codeEmpresa != "CT&S"){
            //no se reciben los datos y no se opera.
            
        }else{
            
            //se reciben los campos en este método y se insertan
            $inserta = DB::table('cl_servicio_etiquetas')->insert([
                'dia_horario_revision' => $horarior,
                'datos_documentos' => $documento,
                'dia_horario_pagos' => $horariop,
                'nombre_socio' => $nombre,
                'num_proveedor' => $proveedor,
                'etiquetas' => $etiqueta,
                'usuario_id' => $idUsuario,
            ]);
            if($inserta){
                echo "e";
            }else{
                echo "n";
            }
        }

    }   

     //Recibe formulario de registro calidad tributaria
     public function registroCalidad(Request $request){
       
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3];
        if($request['idget']){
            $idUsuario = $request['idget'];
            $parametro = "?id=".$idUsuario;
         }else{
            $dataUser = clienteController::infoUser();
            $idUsuario = $dataUser[3];
            $parametro = "";
         }
        //Si la empresa es empaques perú y el request es vacío,  entonces no hago nada

        if($codeEmpresa == "Empaques" && $codePais == "PE" && COUNT($requet) < 2){
            //no se reciben los datos y no se opera.
            
        }else{
            if($request->tipo_obligacion == null ||
            $request->respons_fiscal == null ||
            //$request->regimen_tributario == null ||
            $request->clase_empresa == null){
                $mensaje = "Información Actualizada y Guardada";
            }else{
               
                //se valida si es un insert o un update
                $valida = DB::table('cl_calidad_tributaria')->where('usuario_id', '=', $idUsuario )->count();
                //update
                if($valida > 0){
                    $query =  DB::table('cl_calidad_tributaria')->where('usuario_id', '=', $idUsuario )->update([
                        'tipo_obligacion' => $request->tipo_obligacion,
                        'respons_fiscal' => $request->respons_fiscal,
                        'regimen_tributario' => $request->regimen_tributario,
                        'clase_empresa' => $request->clase_empresa,                    
                        'usuario_id' => $idUsuario 
                    ]);
                    $transaccion = "u";
                    $mensaje = "Información Actualizada y Guardada";
                //insert
                }else{
                    //se reciben los campos en este método y se insertan
                $query =  DB::table('cl_calidad_tributaria')->insert([
                        'tipo_obligacion' => $request->tipo_obligacion,
                        'respons_fiscal' => $request->respons_fiscal,
                        'regimen_tributario' => $request->regimen_tributario,
                        'clase_empresa' => $request->clase_empresa,
                        'usuario_id' => $idUsuario
                    ]);
                    $transaccion = "i";
                    $mensaje = "Información Guardada";
                }
                if(!$query){
                    $mensaje = "Error code:  ".$transaccion;
                }
            }
           
        }

        

      
       //como colombia y perú son los que registran esta info. entonces despues los envío a accionistas
       return redirect('ifaccionistas'.$parametro)->with('mensaje', $mensaje);
    }

     //Recibe formulario de registro accionistas
     public function registroAccionista($tipo,$nombre,$identifi,$fecha,$pais,$pep,$participa,$apellido){
        //Si la empresa es empaques perú y el request es vacío,  entonces no hago nada
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3];

        if($codeEmpresa == "Empaques" && $codePais == "PE" && COUNT($requet) < 2){
            //no se reciben los datos y no se opera.
            
        }else{
            //se reciben los campos en este método y se insertan
            if($fecha == "0000-00-00"){
                $fecha = null;
            }
           
            $existe =  DB::table('cl_accionistas')
            ->where('usuario_id',$idUsuario)
            ->where('identificacion' , $identifi)->count();

                if($existe == 0) {

                    $guardar = DB::table('cl_accionistas')->insert([
                        'tipo_persona' => $tipo,
                        'nombre_razon' => Str::upper($nombre),
                        'identificacion' => $identifi,
                        'fecha_nacimiento' => $fecha,
                        'pais_origen' => $pais,
                        'pep' => $pep,
                        'participacion' => $participa,
                        'usuario_id' =>  $idUsuario,
                        'apellido' => Str::upper($apellido) ]);

                    if($guardar){
                            echo "e";
                    }else{
                            echo "n1";
                    }


                }else{
                        echo "n2";
                }

            
                

        }

    }

    //Recibe formulario de registro junta directiva
    public function registroJunta($nombre,$identifi,$fecha,$pais,$pep,$apellido){
        //Si la empresa es empaques perú y el request es vacío,  entonces no hago nada
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        
          $idUsuario = $dataUser[3];
        DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'N')->delete();
        if($codeEmpresa == "Empaques" && $codePais == "PE"){
            //no se reciben los datos y no se opera.
            
        }else{
            if($fecha == "0000-00-00"){$fecha = null;}
                //se reciben los campos en este método y se insertan
                
              
               
                $guarda = DB::table('cl_junta_directiva')->insert([
                    'nombre_apellido' => Str::upper($nombre),
                    'identificacion' => $identifi,
                    'fecha_nacimiento' => $fecha,
                    'pais_origen' => $pais,
                    'pep' => $pep,
                    'aplica' => 'S',
                    'usuario_id' =>  $idUsuario,
                    'apellido' =>Str::upper( $apellido)
               ]);
               if($guarda){
                    echo "e";
               }else{
                   echo "n";
               }
                
        }

    
    }

    //RECIBE EL FORMULARIO DE REGISTRO DE JUNTA PERO INDICANDO QUE NO TIENE JUNTA
    public function registroSinJunta(){
        //Si la empresa es empaques perú y el request es vacío,  entonces no hago nada
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3]; 

        if($codeEmpresa == "Empaques" && $codePais == "PE"){
            //no se reciben los datos y no se opera.
            
        }else{
               
                //consulto si este usuario ya habia cancelado la junta
                $cancelado = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'N')->count();
                DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'S')->delete();
               
                if($cancelado > 0){
                    DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->where('aplica', '=', 'S')->delete();
                    echo "e";
                }else{
                    $guarda = DB::table('cl_junta_directiva')->insert([
                        'nombre_apellido' => 'N/A',
                        'identificacion' => 'N/A',
                        'fecha_nacimiento' => '9999-12-31',
                        'pais_origen' => '52',
                        'pep' => 'N',
                        'aplica' => 'N',
                        'apellido' => 'N/A',
                        'usuario_id' =>  $idUsuario,
                   ]);

                    if($guarda){
                        echo "e";
                        }else{
                        echo "n";
                        }
                }
               
             
                
        }

    
    }

     //Recibe formulario de registro representantes
     public function registroRepresentante($nombre,$identifi,$fecha,$pais,$pep,$apellido,$email){

        if($fecha == "0000-00-00"){$fecha = null;}
        
        //se reciben los campos en este método y se insertan
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        $guarda = DB::table('cl_representantes')->insert([
            'nombre_apellido' => Str::upper($nombre),
            'identificacion' => $identifi,
            'fecha_nacimiento' => $fecha,
            'pais_origen' => $pais,
            'pep' => $pep,
            'email' => $email,
            'usuario_id' => $idUsuario,
            'apellido' => Str::upper($apellido),
        ]);
        if($guarda){
            echo "e";
        }else{
            echo "n";
        }
    }  

       //Recibe formulario de registro referencias bancarias
       public function registroReferenBanc($banco,$sucursal,$cuenta,$correo){
        //se recibe el formulario y se inserta
         $insertar = DB::table('cl_referencia_bancarias')->insert([
             'nombre_banco' => $banco,
             'sucursal' => $sucursal,
             'numero_cuenta' => $cuenta,
             'correo' => $correo,
             'usuario_id' => $idUsuario,
         ]);
         if($insertar){
            echo "e";
         }else{
             echo "n";
         }
     } 

     //Recibe formulario de registro referencias comerciales
     public function registroReferenComer($codeEmpresa,$contacto,$cupo,$plazo,$tel,$ciudad,$correo){
        //se recibe el formulario y se inserta, validando maximo 3 referencias
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
         $cantidad = cl_referencia_comerciale::where('usuario_id', '=',  $idUsuario)->count('id_referen_comer');
         if($cantidad < 3){
            $inserta = DB::table('cl_referencia_comerciales')->insert([
                'nombre_empresa' => $codeEmpresa,
                'nombre_contacto' => $contacto,
                'cupo_credito' => $cupo,
                'telefono' => $tel,
                'correo' => $correo,
                'plazo_venta' => $plazo,
                'ciudad' => $ciudad,
                'usuario_id' =>  $idUsuario,
            ]);
            if($inserta){
                echo "e";
            }
         
         }else{
             echo "m";
         }
        
     }  

     //subida de documentos
     function subidaDocumentos(Request $request){
        //validar los campos que no son obligatorios de acuerdo a la empresa
        //FOTOCOPIA: No es obligatorio para la empresa Espacios
        //INFO FINANCIERA: No es obligatorio para la empresa Espacios
        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3];
        if($request['idget']){
            $idUsuario = $request['idget'];
         }else{
            $dataUser = clienteController::infoUser();
            $idUsuario = $dataUser[3];
         }
          
        

        
        


        
        $datos = request()->all();
        $datos =request()->except("_token");
        $fechaHora = date('Y-m-d H:i:s');
        $formulario = DB::table('cl_info_general')->select('id_info')->where('usuario_id', '=', $idUsuario)->get();

        
        $idFormulario = "";
 
        if ( count( $formulario) <= 0){
            return redirect('load?id='.$idUsuario)->with('danger','Error, Se debe diligenciar la información general antes de cargar los documentos.'  );
        }
        
        foreach($formulario  as $idenfor){
            $idFormulario = $idenfor->id_info;
        }

        DB::table('cl_documentos')->where('usuario_id', $idUsuario)
        ->where('nombre_docu' ,'<>','FORMATO_CREACION'  )
        ->delete();

        
        $input_file = array_keys ($request->file());

        foreach ($input_file as $file) {

            $url = $request->file($file)->storeAs('Soportes_Doc/'.$idUsuario,$file.'.pdf');
          
            DB::table('cl_documentos')->insert([
                'id_docu' => DB::table('cl_documentos')->max('id_docu') + 1,
                'nombre_docu' => $file,
                'ruta' => $url,
                'formulario_id' => $idFormulario,
                'usuario_id' => $idUsuario,
                'fecha_hora' => date('Y-m-d H:i:s')
            ]);

        }
       


        //Guardar los términos y condiciones*/
           $arregloCondiciones = array($request['terminos_condiciones'],$request['tratamiento_datos'],$request['veracidad_informacion'],$request['origen_recurso'] );

           $fechaActual = date('Y-m-d H:i:s');
           if(session('lang')){
                if(session('lang') == "es"){
                    $idioma = "es";
                }else{
                    $idioma = "en";
                }
           }else{
               $idioma = "es";
           }


           for($i = 0; $i<count($arregloCondiciones);$i++){

                if($arregloCondiciones[$i] == "terminos_condiciones"){
                    $clIdVar1 = "TERMINOS_CONDICIONES";
                    $textoAceptado = DB::table('cl_terminos')->select('cl_va_atrr_1')->where('cl_id_var_1', '=', $clIdVar1)->where('cl_cod_pais', '=', $codePais)->where('cl_org_id', '=', auth()->user()->cl_org_id)->where('cl_idioma', '=', $idioma)->get();
                }else if($arregloCondiciones[$i] == "tratamiento_datos"){
                    $clIdVar1 = "TRATAMIENTO_DATOS";
                    $textoAceptado = DB::table('cl_terminos')->select('cl_va_atrr_1')->where('cl_id_var_1', '=', $clIdVar1)->where('cl_cod_pais', '=', $codePais)->where('cl_org_id', '=', auth()->user()->cl_org_id)->where('cl_idioma', '=', $idioma)->get();
                }else if($arregloCondiciones[$i] == "veracidad_informacion"){
                    $clIdVar1 = "VERACIDAD_INFORMACION";
                    $textoAceptado = DB::table('cl_terminos')->select('cl_va_atrr_1')->where('cl_id_var_1', '=', $clIdVar1)->where('cl_cod_pais', '=', $codePais)->where('cl_org_id', '=', auth()->user()->cl_org_id)->where('cl_idioma', '=', $idioma)->get();
                }else{
                    $clIdVar1 = "ORIGEN_RECURSO";
                    $textoAceptado = DB::table('cl_terminos')->select('cl_va_atrr_1')->where('cl_id_var_1', '=', $clIdVar1)->where('cl_cod_pais', '=', $codePais)->where('cl_org_id', '=', auth()->user()->cl_org_id)->where('cl_idioma', '=', $idioma)->get();
                }

                $val = DB::table('cl_terminos_aceptados')
                    ->where('cl_id_var_1',$clIdVar1)
                    ->where('cl_id_var_2',$clIdVar1)
                    ->where('id_user',$idUsuario)->count();
                    $guardado =true;
                   if( $val == 0 ){
                        $guardado =  DB::table('cl_terminos_aceptados')->insert([
                            
                            'ip'            =>  $request->ip(),
                            'cl_id_var_1'   =>  $clIdVar1,
                            'cl_id_var_2'   =>  $clIdVar1,
                            'contrato_1'    =>  $textoAceptado[0]->cl_va_atrr_1,
                            'contrato_2'    =>  'N/A',
                            'contrato_3'    =>  'N/A',
                            'lat'           =>  $request['lat'],
                            'lon'           =>  $request['long'],
                            'fecha_registro'=>  $fechaActual,
                            'id_user'       => $idUsuario
                        ]);
                    }  

            }
              
            if($guardado){
             return redirect('documentoCliente?id='.$idUsuario)->with('success','Información guardada y finalizada'  );
                
               
                
            }else{
                return redirect('load?id='.$idUsuario)->with('danger','Error cargadno los archivos'  );
            }



     }

     function calcularPorcentaje($codePais,$codeEmpresa, $idUsuario){
        //calcular porcentaje de este módulo
        $camposTablaGeneral = 24;
        $camposTablaJunta = 5;
        $camposTablaBanc = 4;
        $camposTablaComer = 7;
        $camposTablaRepre = 5;
        $camposTablaEtiq = 6;
        $camposTablaCalid = 4;
        $camposTablaAcc = 7;
        $camposTablaInfoAdd = 12;


        if($codePais == "CO" || $codePais == "PE" && $codeEmpresa == "EMPAQUES"){
            $camposPorCompletar = $camposTablaGeneral+$camposTablaJunta+$camposTablaComer+$camposTablaRepre+$camposTablaCalid+$camposTablaAcc;
            $porncentajeCadaModulo = 6*100;

            //consulta tabla general 
            //valido si tiene registros
            $tieneRegistrosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosGeneral > 0){
                $camposIncompletosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosGeneral);
              $camposIncompletosGeneral1 = explode('[] =>', $camposIncompletosGeneral);
              $separadosGeneral = implode(":", $camposIncompletosGeneral1);
          
              $cantidadNullGeneral = 0;
              $inicioConteoGeneral = 0;
              for($i = 0; $i < $camposTablaGeneral; $i++){
                  if($posicion_coincidencia = strpos($separadosGeneral, 'null', $inicioConteoGeneral)){
                      $cantidadNullGeneral++;
                      $inicioConteoGeneral = $posicion_coincidencia + 1;
                      //echo "<br> CANTIDAD NULL: ".$cantidadNullGeneral."<br> INICIO CONTEO: ".$inicioConteo;
                  }
              }
            // echo "<br>ANTES DE VALIDAR NULOS: ".$cantidadNullGeneral;
                    //validar los campos que no son obligatorios
                    foreach($camposIncompletosGeneral as $nulos){
                        if($nulos->registro_mercantil == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_grupo == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->web == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->realiza_operaciones == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->estado == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_credito == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_comercial == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->fecha_asignacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->dt_actualizacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_aduana == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_basic == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_28000 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_9001 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_OEA == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_otras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_ninguna == null){
                            $cantidadNullGeneral--;
                        }
                    }
                   //echo "<br>DESPUES DE VALIDAR NULOS: ".$cantidadNullGeneral;
            }else{
                $cantidadNullGeneral = $camposTablaGeneral;
            }
           
            $camposCompletosGeneral = $camposTablaGeneral - $cantidadNullGeneral;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloGeneral = ($camposCompletosGeneral/$camposTablaGeneral)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloGeneral = round($porcentajeActualModuloGeneral);

            $porcentajeGlobalModuloGeneral = round(($porcentajeActualModuloGeneral/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO GENERAL:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloGeneral."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloGeneral."<br><br>";
        
            //consulta tabla calidad
            //valido si tiene registros
            $tieneRegistrosCalidad = $camposIncompletosCalidad = DB::table('cl_calidad_tributaria')->where('usuario_id', '=', $idUsuario)->count();

            if($tieneRegistrosCalidad){
                $camposIncompletosCalidad = DB::table('cl_calidad_tributaria')->where('usuario_id', '=', $idUsuario)->get();
              
                //var_dump($camposIncompletosCalidad);
                $camposIncompletosCalidad1 = explode('[] =>', $camposIncompletosCalidad);
                $separadosCalidad = implode(":", $camposIncompletosCalidad1);
            
                $cantidadNullCalidad = 0;
                $inicioConteoCalidad = 0;
                for($i = 0; $i < $camposTablaCalid; $i++){
                    if($posicion_coincidencia = strpos($separadosCalidad, 'null', $inicioConteoCalidad)){
                        $cantidadNullCalidad++;
                        $inicioConteoCalidad = $posicion_coincidencia + 1;
                        //echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
               // echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullCalidad;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosCalidad as $nulos){
                    if($nulos->tipo_obligacion == null){
                        $cantidadNullCalidad--;
                    }
                    if($nulos->regimen_tributario == null){
                        $cantidadNullCalidad--;
                    }
                    if($nulos->clase_empresa == null){
                        $cantidadNullCalidad--;
                    }
                }
               // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullCalidad;
            }else{
                $cantidadNullCalidad = $camposTablaCalid;
                
            }
           
            $camposCompletosCalidad = $camposTablaCalid - $cantidadNullCalidad;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloCalidad = ($camposCompletosCalidad/$camposTablaCalid)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloCalidad = round($porcentajeActualModuloCalidad);

            $porcentajeGlobalModuloCalidad = round(($porcentajeActualModuloCalidad/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO CALIDAD:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloCalidad."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloCalidad."<br><br>";
        
            //consulta tabla accionistas
            //valido si tiene registros
            $tieneRegistrosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosAcc > 0){
                $camposIncompletosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosAcc);
                $camposIncompletosAcc1 = explode('[] =>', $camposIncompletosAcc);
                $separadosAcc = implode(":", $camposIncompletosAcc1);
            
                $cantidadNullAcc = 0;
                $inicioConteoAcc = 0;
                for($i = 0; $i < $camposTablaAcc; $i++){
                    if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoAcc)){
                        $cantidadNullAcc++;
                        $inicioConteoAcc = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }

                 // echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullAcc;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosAcc as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullAcc--;
                    }
                }
                //echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullAcc;
            
            }else{
                $cantidadNullAcc = $camposTablaAcc;
            }
              $camposCompletosAcc = $camposTablaAcc - $cantidadNullAcc;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloAcc = ($camposCompletosAcc/$camposTablaAcc)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloAcc = round($porcentajeActualModuloAcc);

            $porcentajeGlobalModuloAcc = round(($porcentajeActualModuloAcc/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO ACCIONISTAS:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloAcc."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloAcc."<br><br>";
        
            //consulta tabla junta dir
           //saber si aplica junta
            $NoAplica = DB::table('cl_junta_directiva')
            ->where('usuario_id', '=', $idUsuario)
            ->where('aplica', '=', 'N')
            ->count();
            if($NoAplica > 0){
                $cantidadNullJunta = 0;
            }else{
                //valido que hayan registros
                $tieneRegistrosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->count();
                if($tieneRegistrosJunta > 0){
                    $camposIncompletosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->get();
                    //var_dump($camposIncompletosJunta);
                    $camposIncompletosJunta1 = explode('[] =>', $camposIncompletosJunta);
                    $separadosAcc = implode(":", $camposIncompletosJunta1);
                
                    $cantidadNullJunta = 0;
                    $inicioConteoJunta = 0;
                    for($i = 0; $i < $camposTablaJunta; $i++){
                        if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoJunta)){
                            $cantidadNullJunta++;
                            $inicioConteoJunta = $posicion_coincidencia + 1;
                            ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                        }
                    }
                    //  echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullJunta;
                    //validar los campos que no son obligatorios
                    foreach($camposIncompletosJunta as $nulos){
                        if($nulos->fecha_nacimiento == null){
                            $cantidadNullJunta--;
                        }
                    }
                    // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullJunta;
                }else{
                    $cantidadNullJunta = $camposTablaJunta;
                }

            }
           
            $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
            
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualJunta = round($porcentajeActualJunta);

            $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";
            
            //consulta tabla represen
            //valido si hay registros
            $tieneRegistrosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosRepre > 0){
                $camposIncompletosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosRepre);
                $camposIncompletosRepre1 = explode('[] =>', $camposIncompletosRepre);
                $separadosRepre = implode(":", $camposIncompletosRepre1);
            
                $cantidadNullRepre = 0;
                $inicioConteoRepre = 0;
                for($i = 0; $i < $camposTablaRepre; $i++){
                    if($posicion_coincidencia = strpos($separadosRepre, 'null', $inicioConteoRepre)){
                        $cantidadNullRepre++;
                        $inicioConteoRepre = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
                   //echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullRepre;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosRepre as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullRepre--;
                    }
                }
               // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullRepre;
            }else{
                $cantidadNullRepre = $camposTablaRepre;
            }
            
            $camposCompletosRepre = $camposTablaRepre - $cantidadNullRepre;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualRepre = ($camposCompletosRepre/$camposTablaRepre)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualRepre = round($porcentajeActualRepre);

            $porcentajeGlobalModuRepre = round(($porcentajeActualRepre/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO REPRESENTANTES:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualRepre."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuRepre."<br><br>";
            
             //consulta tabla referen comer
             //valido si hay registros
             $tieneRegistrosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->count();
             if($tieneRegistrosComer > 0){
                $camposIncompletosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosComer);
                $camposIncompletosComer1 = explode('[] =>', $camposIncompletosComer);
                $separadosComer = implode(":", $camposIncompletosComer1);
            
                $cantidadNullComer = 0;
                $inicioConteoComer = 0;
                for($i = 0; $i < $camposTablaComer; $i++){
                    if($posicion_coincidencia = strpos($separadosComer, 'null', $inicioConteoComer)){
                        $cantidadNullComer++;
                        $inicioConteoComer = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
             }else{
                $cantidadNullComer = $camposTablaComer;
             }
           
            $camposCompletosComer = $camposTablaComer - $cantidadNullComer;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualComer = ($camposCompletosComer/$camposTablaComer)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualComer = round($porcentajeActualComer);

            $porcentajeGlobalModuComer = round(($porcentajeActualComer/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO REFEREN COMER:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualComer."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuComer."<br><br>";
            $totalPorcentajeCompletado = $porcentajeGlobalModuloGeneral+$porcentajeGlobalModuloCalidad+$porcentajeGlobalModuloAcc+$porcentajeGlobalModuJunta+$porcentajeGlobalModuRepre+$porcentajeGlobalModuComer;
            
            return $totalPorcentajeCompletado."/".$porcentajeActualModuloGeneral."/".'espacio_adicional'."/".'espacio_etquetas'."/".$porcentajeActualModuloCalidad."/".$porcentajeActualModuloAcc."/".$porcentajeActualJunta."/".$porcentajeActualRepre."/".'espacio_banc'."/".$porcentajeActualComer;
            
            
            //------------------SI EL PAÍS ES MEXICO-----------------------------------------
        }else if($codePais == "MX" && $codeEmpresa != "CT&S" && $codeEmpresa != "EMPAQUES"){
            $porncentajeCadaModulo = 5*100;

             //consulta tabla general
              //valido si tiene registros
            $tieneRegistrosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosGeneral > 0){
                $camposIncompletosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosGeneral);
              $camposIncompletosGeneral1 = explode('[] =>', $camposIncompletosGeneral);
              $separadosGeneral = implode(":", $camposIncompletosGeneral1);
              $cantidadNullGeneral = 0;
              $inicioConteoGeneral = 0;
              for($i = 0; $i < $camposTablaGeneral; $i++){
                  if($posicion_coincidencia = strpos($separadosGeneral, 'null', $inicioConteoGeneral)){
                      $cantidadNullGeneral++;
                      $inicioConteoGeneral = $posicion_coincidencia + 1;
                      //echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                  }
              }

                // echo "<br>ANTES DE VALIDAR NULOS: ".$cantidadNullGeneral;
                    //validar los campos que no son obligatorios
                    foreach($camposIncompletosGeneral as $nulos){
                        if($nulos->registro_mercantil == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_grupo == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->web == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->realiza_operaciones == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->estado == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_credito == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_comercial == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->fecha_asignacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->dt_actualizacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_aduana == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_basic == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_28000 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_9001 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_OEA == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_otras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_ninguna == null){
                            $cantidadNullGeneral--;
                        }
                    }
                   // echo "<br>DESPUES DE VALIDAR NULOS: ".$cantidadNullGeneral;
            }else{
                $cantidadNullGeneral = $camposTablaGeneral;
            }
    
       
         
           $camposCompletosGeneral = $camposTablaGeneral - $cantidadNullGeneral;
           //$camposFaltantes = $camposPorCompletar - $camposCompletos;
           $porcentajeActualModuloGeneral = ($camposCompletosGeneral/$camposTablaGeneral)*100;
           //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
           $porcentajeActualModuloGeneral = round($porcentajeActualModuloGeneral);

           $porcentajeGlobalModuloGeneral = round(($porcentajeActualModuloGeneral/$porncentajeCadaModulo)*100);

           //echo "<br><br>MODULO GENERAL:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloGeneral."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloGeneral."<br><br>";

            //consulta tabla accionistas
            //valido si tiene registros
            $tieneRegistrosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosAcc > 0){
                $camposIncompletosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosAcc);
                $camposIncompletosAcc1 = explode('[] =>', $camposIncompletosAcc);
                $separadosAcc = implode(":", $camposIncompletosAcc1);
            
                $cantidadNullAcc = 0;
                $inicioConteoAcc = 0;
                for($i = 0; $i < $camposTablaAcc; $i++){
                    if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoAcc)){
                        $cantidadNullAcc++;
                        $inicioConteoAcc = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }

                 // echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullAcc;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosAcc as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullAcc--;
                    }
                }
                //echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullAcc;
            
            }else{
                $cantidadNullAcc = $camposTablaAcc;
            }
              $camposCompletosAcc = $camposTablaAcc - $cantidadNullAcc;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloAcc = ($camposCompletosAcc/$camposTablaAcc)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloAcc = round($porcentajeActualModuloAcc);

            $porcentajeGlobalModuloAcc = round(($porcentajeActualModuloAcc/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO ACCIONISTAS:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloAcc."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloAcc."<br><br>";
        
            //consulta tabla junta dir
           //saber si aplica junta
           $NoAplica = DB::table('cl_junta_directiva')
           ->where('usuario_id', '=', $idUsuario)
           ->where('aplica', '=', 'N')
           ->count();
           if($NoAplica > 0){
               $cantidadNullJunta = 0;
           }else{
               //valido que hayan registros
               $tieneRegistrosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->count();
               if($tieneRegistrosJunta > 0){
                   $camposIncompletosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->get();
                   //var_dump($camposIncompletosJunta);
                   $camposIncompletosJunta1 = explode('[] =>', $camposIncompletosJunta);
                   $separadosAcc = implode(":", $camposIncompletosJunta1);
               
                   $cantidadNullJunta = 0;
                   $inicioConteoJunta = 0;
                   for($i = 0; $i < $camposTablaJunta; $i++){
                       if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoJunta)){
                           $cantidadNullJunta++;
                           $inicioConteoJunta = $posicion_coincidencia + 1;
                           ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                       }
                   }
                   //  echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullJunta;
                   //validar los campos que no son obligatorios
                   foreach($camposIncompletosJunta as $nulos){
                       if($nulos->fecha_nacimiento == null){
                           $cantidadNullJunta--;
                       }
                   }
                   // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullJunta;
               }else{
                   $cantidadNullJunta = $camposTablaJunta;
               }

           }
          
           $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
           
           //$camposFaltantes = $camposPorCompletar - $camposCompletos;
           $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
           //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
           $porcentajeActualJunta = round($porcentajeActualJunta);

           $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

          // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";

          
            $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
            
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualJunta = round($porcentajeActualJunta);

            $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";
            
            //consulta tabla represen
            //valido si hay registros
            $tieneRegistrosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosRepre > 0){
                $camposIncompletosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosRepre);
                $camposIncompletosRepre1 = explode('[] =>', $camposIncompletosRepre);
                $separadosRepre = implode(":", $camposIncompletosRepre1);
            
                $cantidadNullRepre = 0;
                $inicioConteoRepre = 0;
                for($i = 0; $i < $camposTablaRepre; $i++){
                    if($posicion_coincidencia = strpos($separadosRepre, 'null', $inicioConteoRepre)){
                        $cantidadNullRepre++;
                        $inicioConteoRepre = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
                   //echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullRepre;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosRepre as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullRepre--;
                    }
                }
               // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullRepre;
            }else{
                $cantidadNullRepre = $camposTablaRepre;
            }
            
            $camposCompletosRepre = $camposTablaRepre - $cantidadNullRepre;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualRepre = ($camposCompletosRepre/$camposTablaRepre)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualRepre = round($porcentajeActualRepre);

            $porcentajeGlobalModuRepre = round(($porcentajeActualRepre/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO REPRESENTANTES:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualRepre."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuRepre."<br><br>";
            
             //consulta tabla referen comer
             //valido si hay registros
             $tieneRegistrosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->count();
             if($tieneRegistrosComer > 0){
                $camposIncompletosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosComer);
                $camposIncompletosComer1 = explode('[] =>', $camposIncompletosComer);
                $separadosComer = implode(":", $camposIncompletosComer1);
            
                $cantidadNullComer = 0;
                $inicioConteoComer = 0;
                for($i = 0; $i < $camposTablaComer; $i++){
                    if($posicion_coincidencia = strpos($separadosComer, 'null', $inicioConteoComer)){
                        $cantidadNullComer++;
                        $inicioConteoComer = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
             }else{
                $cantidadNullComer = $camposTablaComer;
             }
           
            $camposCompletosComer = $camposTablaComer - $cantidadNullComer;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualComer = ($camposCompletosComer/$camposTablaComer)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualComer = round($porcentajeActualComer);

            $porcentajeGlobalModuComer = round(($porcentajeActualComer/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO REFEREN COMER:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualComer."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuComer."<br><br>";
            $totalPorcentajeCompletado = $porcentajeGlobalModuloGeneral+$porcentajeGlobalModuloAcc+$porcentajeGlobalModuJunta+$porcentajeGlobalModuRepre+$porcentajeGlobalModuComer;
             
            return $totalPorcentajeCompletado."/".$porcentajeActualModuloGeneral."/".'espacio_adicional'."/".'espacio_etquetas'."/".'espacio_calidad'."/".$porcentajeActualModuloAcc."/".$porcentajeActualJunta."/".$porcentajeActualRepre."/".'espacio_banc'."/".$porcentajeActualComer;

            
            //-----------SI EL PAÍS ES MEXICO Y LA EMPRESA ES CT&S------------
        }
        else if($codePais == "MX" && $codeEmpresa == "CT&S"){
            $porncentajeCadaModulo = 7*100;

              //consulta tabla general 
            //valido si tiene registros
            $tieneRegistrosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosGeneral > 0){
                $camposIncompletosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosGeneral);
              $camposIncompletosGeneral1 = explode('[] =>', $camposIncompletosGeneral);
              $separadosGeneral = implode(":", $camposIncompletosGeneral1);
          
              $cantidadNullGeneral = 0;
              $inicioConteoGeneral = 0;
              for($i = 0; $i < $camposTablaGeneral; $i++){
                  if($posicion_coincidencia = strpos($separadosGeneral, 'null', $inicioConteoGeneral)){
                      $cantidadNullGeneral++;
                      $inicioConteoGeneral = $posicion_coincidencia + 1;
                      //echo "<br> CANTIDAD NULL: ".$cantidadNullGeneral."<br> INICIO CONTEO: ".$inicioConteo;
                  }
              }
             // echo "<br>ANTES DE VALIDAR NULOS: ".$cantidadNullGeneral;
                    //validar los campos que no son obligatorios
                    foreach($camposIncompletosGeneral as $nulos){
                        if($nulos->registro_mercantil == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_grupo == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->web == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_compras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->email_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->nombre_contacto_tesoreria == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->realiza_operaciones == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->estado == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_credito == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->id_user_comercial == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->fecha_asignacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->dt_actualizacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_aduana == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_basic == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_28000 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_9001 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_OEA == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_otras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_ninguna == null){
                            $cantidadNullGeneral--;
                        }
                    }
                   // echo "<br>DESPUES DE VALIDAR NULOS: ".$cantidadNullGeneral;
            }else{
                $cantidadNullGeneral = $camposTablaGeneral;
            }
           
            $camposCompletosGeneral = $camposTablaGeneral - $cantidadNullGeneral;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloGeneral = ($camposCompletosGeneral/$camposTablaGeneral)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloGeneral = round($porcentajeActualModuloGeneral);

            $porcentajeGlobalModuloGeneral = round(($porcentajeActualModuloGeneral/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO GENERAL:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloGeneral."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloGeneral."<br><br>";

            //consulta tabla adicional
            //Valido que hayan registros
            $tieneRegistrosAdicional = DB::table('cl_info_adicional')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosAdicional > 0){
                $camposIncompletosAdicional = DB::table('cl_info_adicional')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosAdicional);
                $camposIncompletosAdicional1 = explode('[] =>', $camposIncompletosAdicional);
                $separadosAdicional = implode(":", $camposIncompletosAdicional1);
            
                $cantidadNullAdicional = 0;
                $inicioConteoAdicional = 0;
                for($i = 0; $i < $camposTablaInfoAdd; $i++){
                    if($posicion_coincidencia = strpos($separadosAdicional, 'null', $inicioConteoAdicional)){
                        $cantidadNullAdicional++;
                        $inicioConteoAdicional = $posicion_coincidencia + 1;
                        //echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
               
            }else{
                $cantidadNullAdicional = $camposTablaInfoAdd;
            }
            
            $camposCompletosAdicional = $camposTablaInfoAdd - $cantidadNullAdicional;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloAdicional = ($camposCompletosAdicional/$camposTablaInfoAdd)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloAdicional = round($porcentajeActualModuloAdicional);

            $porcentajeGlobalModuloAdicional = round(($porcentajeActualModuloAdicional/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO GENERAL:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloAdicional."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloAdicional."<br><br>";

            //consulta tabla etiquetas
            //valido si tiene registros
            $tieneRegistrosEtiquetas = DB::table('cl_servicio_etiquetas')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosEtiquetas > 0){
                $camposIncompletosEtiquetas = DB::table('cl_servicio_etiquetas')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosEtiquetas);
                $camposIncompletosEtiquetas1 = explode('[] =>', $camposIncompletosEtiquetas);
                $separadosEtiquetas = implode(":", $camposIncompletosEtiquetas1);
            
                $cantidadNullEtiquetas = 0;
                $inicioConteoEtiquetas = 0;
                for($i = 0; $i < $camposTablaEtiq; $i++){
                    if($posicion_coincidencia = strpos($separadosEtiquetas, 'null', $inicioConteoEtiquetas)){
                        $cantidadNullEtiquetas++;
                        $inicioConteoEtiquetas = $posicion_coincidencia + 1;
                        //echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
            }else{
                $cantidadNullEtiquetas = $camposTablaEtiq;
            }
           
            $camposCompletosEtiquetas = $camposTablaEtiq - $cantidadNullEtiquetas;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloEtiquetas = ($camposCompletosEtiquetas/$camposTablaEtiq)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloEtiquetas = round($porcentajeActualModuloEtiquetas);

            $porcentajeGlobalModuloEtiquetas = round(($porcentajeActualModuloEtiquetas/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO ETIQUETAS:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloEtiquetas."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloEtiquetas."<br><br>";
 
            //consulta tabla accionistas
            //valido si tiene registros
            $tieneRegistrosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosAcc > 0){
                $camposIncompletosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosAcc);
                $camposIncompletosAcc1 = explode('[] =>', $camposIncompletosAcc);
                $separadosAcc = implode(":", $camposIncompletosAcc1);
            
                $cantidadNullAcc = 0;
                $inicioConteoAcc = 0;
                for($i = 0; $i < $camposTablaAcc; $i++){
                    if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoAcc)){
                        $cantidadNullAcc++;
                        $inicioConteoAcc = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }

                 // echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullAcc;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosAcc as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullAcc--;
                    }
                }
                //echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullAcc;
            
            }else{
                $cantidadNullAcc = $camposTablaAcc;
            }
              $camposCompletosAcc = $camposTablaAcc - $cantidadNullAcc;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloAcc = ($camposCompletosAcc/$camposTablaAcc)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloAcc = round($porcentajeActualModuloAcc);

            $porcentajeGlobalModuloAcc = round(($porcentajeActualModuloAcc/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO ACCIONISTAS:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloAcc."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloAcc."<br><br>";
        
             //consulta tabla junta dir
           //saber si aplica junta
           $NoAplica = DB::table('cl_junta_directiva')
           ->where('usuario_id', '=', $idUsuario)
           ->where('aplica', '=', 'N')
           ->count();
           if($NoAplica > 0){
               $cantidadNullJunta = 0;
           }else{
               //valido que hayan registros
               $tieneRegistrosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->count();
               if($tieneRegistrosJunta > 0){
                   $camposIncompletosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->get();
                   //var_dump($camposIncompletosJunta);
                   $camposIncompletosJunta1 = explode('[] =>', $camposIncompletosJunta);
                   $separadosAcc = implode(":", $camposIncompletosJunta1);
               
                   $cantidadNullJunta = 0;
                   $inicioConteoJunta = 0;
                   for($i = 0; $i < $camposTablaJunta; $i++){
                       if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoJunta)){
                           $cantidadNullJunta++;
                           $inicioConteoJunta = $posicion_coincidencia + 1;
                           ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                       }
                   }
                   //  echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullJunta;
                   //validar los campos que no son obligatorios
                   foreach($camposIncompletosJunta as $nulos){
                       if($nulos->fecha_nacimiento == null){
                           $cantidadNullJunta--;
                       }
                   }
                   // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullJunta;
               }else{
                   $cantidadNullJunta = $camposTablaJunta;
               }

           }
          
           $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
           
           //$camposFaltantes = $camposPorCompletar - $camposCompletos;
           $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
           //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
           $porcentajeActualJunta = round($porcentajeActualJunta);

           $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

          // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";

          
            $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
            
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualJunta = round($porcentajeActualJunta);

            $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";
            
            //consulta tabla represen
            //valido si hay registros
            $tieneRegistrosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosRepre > 0){
                $camposIncompletosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosRepre);
                $camposIncompletosRepre1 = explode('[] =>', $camposIncompletosRepre);
                $separadosRepre = implode(":", $camposIncompletosRepre1);
            
                $cantidadNullRepre = 0;
                $inicioConteoRepre = 0;
                for($i = 0; $i < $camposTablaRepre; $i++){
                    if($posicion_coincidencia = strpos($separadosRepre, 'null', $inicioConteoRepre)){
                        $cantidadNullRepre++;
                        $inicioConteoRepre = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
                   //echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullRepre;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosRepre as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullRepre--;
                    }
                }
               // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullRepre;
            }else{
                $cantidadNullRepre = $camposTablaRepre;
            }
            
            $camposCompletosRepre = $camposTablaRepre - $cantidadNullRepre;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualRepre = ($camposCompletosRepre/$camposTablaRepre)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualRepre = round($porcentajeActualRepre);

            $porcentajeGlobalModuRepre = round(($porcentajeActualRepre/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO REPRESENTANTES:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualRepre."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuRepre."<br><br>";
            
             //consulta tabla referen comer
             //valido si hay registros
             $tieneRegistrosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->count();
             if($tieneRegistrosComer > 0){
                $camposIncompletosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosComer);
                $camposIncompletosComer1 = explode('[] =>', $camposIncompletosComer);
                $separadosComer = implode(":", $camposIncompletosComer1);
            
                $cantidadNullComer = 0;
                $inicioConteoComer = 0;
                for($i = 0; $i < $camposTablaComer; $i++){
                    if($posicion_coincidencia = strpos($separadosComer, 'null', $inicioConteoComer)){
                        $cantidadNullComer++;
                        $inicioConteoComer = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
             }else{
                $cantidadNullComer = $camposTablaComer;
             }
           
            $camposCompletosComer = $camposTablaComer - $cantidadNullComer;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualComer = ($camposCompletosComer/$camposTablaComer)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualComer = round($porcentajeActualComer);

            $porcentajeGlobalModuComer = round(($porcentajeActualComer/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO REFEREN COMER:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualComer."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuComer."<br><br>";
            $totalPorcentajeCompletado = $porcentajeGlobalModuloGeneral+$porcentajeActualModuloAdicional+$porcentajeActualModuloEtiquetas+$porcentajeGlobalModuloAcc+$porcentajeGlobalModuJunta+$porcentajeGlobalModuRepre+$porcentajeGlobalModuComer;
            
            return $totalPorcentajeCompletado."/".$porcentajeActualModuloGeneral."/".$porcentajeActualModuloAdicional."/".$porcentajeActualModuloEtiquetas."/".'espacio_calidad'."/".$porcentajeActualModuloAcc."/".$porcentajeActualJunta."/".$porcentajeActualRepre."/".'espacio_banc'."/".$porcentajeActualComer;
            
            //SI EL PAÍS ES MEXICO Y LA EMPRESA ES EMPAQUES
        }else if($codePais == "MX" && $codeEmpresa == "EMPAQUES"){
            $porncentajeCadaModulo = 6*100;

             //valido si tiene registros
             $tieneRegistrosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->count();
             if($tieneRegistrosGeneral > 0){
                 $camposIncompletosGeneral = DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->get();
                 //var_dump($camposIncompletosGeneral);
               $camposIncompletosGeneral1 = explode('[] =>', $camposIncompletosGeneral);
               $separadosGeneral = implode(":", $camposIncompletosGeneral1);
           
               $cantidadNullGeneral = 0;
               $inicioConteoGeneral = 0;
               for($i = 0; $i < $camposTablaGeneral; $i++){
                   if($posicion_coincidencia = strpos($separadosGeneral, 'null', $inicioConteoGeneral)){
                       $cantidadNullGeneral++;
                       $inicioConteoGeneral = $posicion_coincidencia + 1;
                       //echo "<br> CANTIDAD NULL: ".$cantidadNullGeneral."<br> INICIO CONTEO: ".$inicioConteo;
                   }
               }
              // echo "<br>ANTES DE VALIDAR NULOS: ".$cantidadNullGeneral;
                     //validar los campos que no son obligatorios
                     foreach($camposIncompletosGeneral as $nulos){
                         if($nulos->registro_mercantil == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->nombre_grupo == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->web == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->email_contacto_compras == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->nombre_contacto_compras == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->email_contacto_tesoreria == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->nombre_contacto_tesoreria == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->realiza_operaciones == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->estado == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->id_user_credito == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->id_user_comercial == null){
                             $cantidadNullGeneral--;
                         }
                         if($nulos->fecha_asignacion == null){
                             $cantidadNullGeneral--;
                         }
                        if($nulos->dt_actualizacion == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_aduana == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_basic == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_28000 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_iso_9001 == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_OEA == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_otras == null){
                            $cantidadNullGeneral--;
                        }
                        if($nulos->cer_ninguna == null){
                            $cantidadNullGeneral--;
                        }
                     }
                    // echo "<br>DESPUES DE VALIDAR NULOS: ".$cantidadNullGeneral;
             }else{
                 $cantidadNullGeneral = $camposTablaGeneral;
             }
            
             $camposCompletosGeneral = $camposTablaGeneral - $cantidadNullGeneral;
             //$camposFaltantes = $camposPorCompletar - $camposCompletos;
             $porcentajeActualModuloGeneral = ($camposCompletosGeneral/$camposTablaGeneral)*100;
             //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
             $porcentajeActualModuloGeneral = round($porcentajeActualModuloGeneral);
 
             $porcentajeGlobalModuloGeneral = round(($porcentajeActualModuloGeneral/$porncentajeCadaModulo)*100);
 
            // echo "<br><br>MODULO GENERAL:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloGeneral."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloGeneral."<br><br>";
 

             //consulta tabla accionistas
            //valido si tiene registros
            $tieneRegistrosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosAcc > 0){
                $camposIncompletosAcc = DB::table('cl_accionistas')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosAcc);
                $camposIncompletosAcc1 = explode('[] =>', $camposIncompletosAcc);
                $separadosAcc = implode(":", $camposIncompletosAcc1);
            
                $cantidadNullAcc = 0;
                $inicioConteoAcc = 0;
                for($i = 0; $i < $camposTablaAcc; $i++){
                    if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoAcc)){
                        $cantidadNullAcc++;
                        $inicioConteoAcc = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }

                 // echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullAcc;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosAcc as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullAcc--;
                    }
                }
                //echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullAcc;
            
            }else{
                $cantidadNullAcc = $camposTablaAcc;
            }
              $camposCompletosAcc = $camposTablaAcc - $cantidadNullAcc;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualModuloAcc = ($camposCompletosAcc/$camposTablaAcc)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualModuloAcc = round($porcentajeActualModuloAcc);

            $porcentajeGlobalModuloAcc = round(($porcentajeActualModuloAcc/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO ACCIONISTAS:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualModuloAcc."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuloAcc."<br><br>";
        
            //consulta tabla junta dir
           //saber si aplica junta
           $NoAplica = DB::table('cl_junta_directiva')
           ->where('usuario_id', '=', $idUsuario)
           ->where('aplica', '=', 'N')
           ->count();
           if($NoAplica > 0){
               $cantidadNullJunta = 0;
           }else{
               //valido que hayan registros
               $tieneRegistrosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->count();
               if($tieneRegistrosJunta > 0){
                   $camposIncompletosJunta = DB::table('cl_junta_directiva')->where('usuario_id', '=', $idUsuario)->get();
                   //var_dump($camposIncompletosJunta);
                   $camposIncompletosJunta1 = explode('[] =>', $camposIncompletosJunta);
                   $separadosAcc = implode(":", $camposIncompletosJunta1);
               
                   $cantidadNullJunta = 0;
                   $inicioConteoJunta = 0;
                   for($i = 0; $i < $camposTablaJunta; $i++){
                       if($posicion_coincidencia = strpos($separadosAcc, 'null', $inicioConteoJunta)){
                           $cantidadNullJunta++;
                           $inicioConteoJunta = $posicion_coincidencia + 1;
                           ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                       }
                   }
                   //  echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullJunta;
                   //validar los campos que no son obligatorios
                   foreach($camposIncompletosJunta as $nulos){
                       if($nulos->fecha_nacimiento == null){
                           $cantidadNullJunta--;
                       }
                   }
                   // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullJunta;
               }else{
                   $cantidadNullJunta = $camposTablaJunta;
               }

           }
          
           $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
           
           //$camposFaltantes = $camposPorCompletar - $camposCompletos;
           $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
           //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
           $porcentajeActualJunta = round($porcentajeActualJunta);

           $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

          // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";

            $camposCompletosJunta = $camposTablaJunta - $cantidadNullJunta;
            
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualJunta = ($camposCompletosJunta/$camposTablaJunta)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualJunta = round($porcentajeActualJunta);

            $porcentajeGlobalModuJunta = round(($porcentajeActualJunta/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO JUNTA:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualJunta."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuJunta."<br><br>";
            
            //consulta tabla represen
            //valido si hay registros
            $tieneRegistrosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->count();
            if($tieneRegistrosRepre > 0){
                $camposIncompletosRepre = DB::table('cl_representantes')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosRepre);
                $camposIncompletosRepre1 = explode('[] =>', $camposIncompletosRepre);
                $separadosRepre = implode(":", $camposIncompletosRepre1);
            
                $cantidadNullRepre = 0;
                $inicioConteoRepre = 0;
                for($i = 0; $i < $camposTablaRepre; $i++){
                    if($posicion_coincidencia = strpos($separadosRepre, 'null', $inicioConteoRepre)){
                        $cantidadNullRepre++;
                        $inicioConteoRepre = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
                   //echo "NULOS ANTES DE VALIDACIÓN: ".$cantidadNullRepre;
                 //validar los campos que no son obligatorios
                 foreach($camposIncompletosRepre as $nulos){
                    if($nulos->fecha_nacimiento == null){
                        $cantidadNullRepre--;
                    }
                }
               // echo "NULOS DESPUES DE VALIDACIÓN: ".$cantidadNullRepre;
            }else{
                $cantidadNullRepre = $camposTablaRepre;
            }
            
            $camposCompletosRepre = $camposTablaRepre - $cantidadNullRepre;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualRepre = ($camposCompletosRepre/$camposTablaRepre)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualRepre = round($porcentajeActualRepre);

            $porcentajeGlobalModuRepre = round(($porcentajeActualRepre/$porncentajeCadaModulo)*100);

           // echo "<br><br>MODULO REPRESENTANTES:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualRepre."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuRepre."<br><br>";
                   //consulta tabla referen bancaria
                   //valido que haya registros
                   $tieneRegistrosBanc = DB::table('cl_referencia_bancarias')->where('usuario_id', '=', $idUsuario)->count();
                    if($tieneRegistrosBanc > 0){
                        $camposIncompletosBanc = DB::table('cl_referencia_bancarias')->where('usuario_id', '=', $idUsuario)->get();
                        //var_dump($camposIncompletosBanc);
                        $camposIncompletosBanc1 = explode('[] =>', $camposIncompletosBanc);
                        $separadosBanc = implode(":", $camposIncompletosBanc1);
                    
                        $cantidadNullBanc = 0;
                        $inicioConteoBanc = 0;
                        for($i = 0; $i < $camposTablaBanc; $i++){
                            if($posicion_coincidencia = strpos($separadosBanc, 'null', $inicioConteoBanc)){
                                $cantidadNullBanc++;
                                $inicioConteoBanc = $posicion_coincidencia + 1;
                                ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                            }
                        }
                    }else{
                        $cantidadNullBanc = $camposTablaBanc;

                    }

                   $camposCompletosBanc = $camposTablaBanc - $cantidadNullBanc;
                   //$camposFaltantes = $camposPorCompletar - $camposCompletos;
                   $porcentajeActualBanc = ($camposCompletosBanc/$camposTablaBanc)*100;
                   //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
                   $porcentajeActualBanc = round($porcentajeActualBanc);
       
                   $porcentajeGlobalModuBanc = round(($porcentajeActualBanc/$porncentajeCadaModulo)*100);
       
                   //echo "<br><br>MODULO REFEREN COMER:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualBanc."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuBanc."<br><br>";
            
             //consulta tabla referen comer
             //valida registros
             $tieneRegistrosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->count();
             if($tieneRegistrosComer > 0){
                $camposIncompletosComer = DB::table('cl_referencia_comerciales')->where('usuario_id', '=', $idUsuario)->get();
                //var_dump($camposIncompletosComer);
                $camposIncompletosComer1 = explode('[] =>', $camposIncompletosComer);
                $separadosComer = implode(":", $camposIncompletosComer1);
            
                $cantidadNullComer = 0;
                $inicioConteoComer = 0;
                for($i = 0; $i < $camposTablaComer; $i++){
                    if($posicion_coincidencia = strpos($separadosComer, 'null', $inicioConteoComer)){
                        $cantidadNullComer++;
                        $inicioConteoComer = $posicion_coincidencia + 1;
                        ////echo "<br> CANTIDAD NULL: ".$cantidadNull."<br> INICIO CONTEO: ".$inicioConteo;
                    }
                }
             }else{
                $cantidadNullComer = $camposTablaComer;
             }
           
            $camposCompletosComer = $camposTablaComer - $cantidadNullComer;
            //$camposFaltantes = $camposPorCompletar - $camposCompletos;
            $porcentajeActualComer = ($camposCompletosComer/$camposTablaComer)*100;
            //$porcentajeActual = ($camposCompletos/$camposPorCompletar)*100;
            $porcentajeActualComer = round($porcentajeActualComer);

            $porcentajeGlobalModuComer = round(($porcentajeActualComer/$porncentajeCadaModulo)*100);

            //echo "<br><br>MODULO REFEREN COMER:<br><br>PORCENTAJE ACTUAL DE ESTE MÓDULO: ".$porcentajeActualComer."<br>PORCENTAJE ACTUAL GLOBAL: ".$porcentajeGlobalModuComer."<br><br>";
            $totalPorcentajeCompletado = $porcentajeGlobalModuloGeneral+$porcentajeGlobalModuloAcc+$porcentajeGlobalModuJunta+$porcentajeGlobalModuRepre+$porcentajeGlobalModuBanc+$porcentajeGlobalModuComer;
            
            return $totalPorcentajeCompletado."/".$porcentajeActualModuloGeneral."/".'espacio_adicional'."/".'espacio_etiquetas'."/".'espacio_calidad'."/".$porcentajeActualModuloAcc."/".$porcentajeActualJunta."/".$porcentajeActualRepre."/".$porcentajeActualBanc."/".$porcentajeActualComer;
            
        }


    }

    function porcentajeModulo($codePais,$codeEmpresa, $idUsuario){
        //NuevoPorcentaje
        $arrayCampos = array();
        $consulta = DB::table('cl_maestra_pais')
        ->where('code_pais', '=', $codePais)
        ->where('id_org', '=', $codeEmpresa)
        ->orderBy('id_nav')
        ->get();
        
        $cantConsul = count($consulta);
        //echo "CANTIDAD DE MODULOS: ".$cantConsul."<br><br>";
        $i= 0; 
        $cantidadNull = 0;
       // echo "<pre>";
       // var_dump($consulta);
        foreach($consulta as $c){
            
                //echo "<br><br> Vuelta: ".$i." -> TABLA: ".$c->nombre_tabla;                
                $arrayCampos[$i][$c->nombre_tabla] =  explode(',', $c->campos);
                //echo "<br>CANTIDAD CAMPOS: ".count($arrayCampos[$i][$c->nombre_tabla]);
                $cantCamposModul = count($arrayCampos[$i][$c->nombre_tabla]);
                for($k = 0; $k  < count($arrayCampos[$i][$c->nombre_tabla]); $k++){
                    $nombreCampo = $arrayCampos[$i][$c->nombre_tabla][$k];
                    
                   // echo "<BR><BR> --------------->NOMBRE DEL CAMPO: ".$nombreCampo;
                    
                    
                    $consultaGene = DB::table($c->nombre_tabla)
                    ->select($nombreCampo)
                    ->where('usuario_id', '=', $idUsuario)
                    ->limit(1)
                    ->get();
                    if(count($consultaGene) > 0){
                        foreach($consultaGene as $cons){
                           // echo " ---> ".$cons->$nombreCampo;
                            if($cons->$nombreCampo == null){
                                $cantidadNull++;
                            }
                        }
                    }else{
                        $cantidadNull++;
                    }
                    //consulto si por lo menos tiene una direccion registrada
                    if($c->nombre_tabla == "cl_info_general"){
                        $consultaDirecc = DB::table('cl_direccion_por_formulario')
                        ->where('usuario_id', '=', $idUsuario)
                        ->limit(1)
                        ->count();
                        if(!$consultaDirecc > 0){
                            $cantidadNull++;

                        }
                    }

                    
                }
                
                $i++;
                
                    //Realizo calculo
                    //echo "<BR>NULOS: ".$cantidadNull;
                    $completosModul = $cantCamposModul - $cantidadNull;
                    //echo "<br>COMPLETOS: ".$completosModul;
                    $porcentajeModul = round(($completosModul / $cantCamposModul)*100);
                   // echo "<br>PROCENTAJE MODULO: ".$porcentajeModul;
                    //actualizo porcentaje
                    DB::table('cl_maestra_pais')
                    ->where('code_pais', '=', $codePais)
                    ->where('id_org', '=', $codeEmpresa)
                    ->where('titulo_modulo', '=', $c->titulo_modulo)
                    ->update(['porcentaje' => $porcentajeModul]) ;

                    $cantidadNull = 0;
                
        }
    }

    public function verificarDireccMatriz(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
        
        $matriz = DB::table('cl_direccion_por_formulario')
        ->where('usuario_id', '=', $idUsuario)
        ->where('tipo_direccion', '=', 1)
        ->count();
        $despacho = DB::table('cl_direccion_por_formulario')
        ->where('usuario_id', '=', $idUsuario)
        ->where('tipo_direccion', '=', 3)
        ->count();
       
        if($matriz > 0 && $despacho > 0){
            echo "e";
        }else{
            echo "n";
        }
    }
       
    public function creditofor(){
        Utilidad::AllUserRol(['Credito']);

        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }
        $jess_users = DB::table('jess_users')->where('id', '=', $idUsuario )->get();
     
        $codePais = $jess_users[0]->cl_cod_pais;
        $codeEmpresa = $jess_users[0]->cl_org_id;
        $email = $jess_users[0]->email;
        $emailcredito= auth()->user()->email; 

        $porcentaje = clienteController::calcularPorcentaje($codePais,$codeEmpresa,$idUsuario);
        $porcentajes = explode('/', $porcentaje);
      
         $distrito         = Utilidad::maestraPais($codePais,'DISTRITO','DISTRITO');
         $distrito_cliente = Utilidad::maestraPais($codePais,'DISTRITO_CLIENTE','DISTRITO_CLIENTE');
         $clase_perfil   = Utilidad::maestraPais($codePais,'CLASE_PERFIL','CLASE_PERFIL');
         $cobrador       = Utilidad::maestraPais($codePais,'COBRADOR','COBRADOR');
         $site_use_code  = Utilidad::maestraPais($codePais,'SITE_USE_CODE','SITE_USE_CODE');
         $tipo_cliente   = Utilidad::maestraPais($codePais,'TIPO_CLIENTE','TIPO_CLIENTE');
         $doc_garantia   = Utilidad::maestraPais($codePais,'DOC_GARANTIA','DOC_GARANTIA');
         $doc_pago       = Utilidad::maestraPais($codePais,'DOC_PAGO','DOC_PAGO');
         $dia_pago       = Utilidad::maestraPais($codePais,'DIAS_PAGO','DIAS_PAGO');
         $canal_bico     = Utilidad::maestraPais($codePais,'CANAL_BICO','CANAL_BICO');
         $tipo_moneda    = Utilidad::maestraPais($codePais,'TIPO_MONEDA','TIPO_MONEDA');
         
         
         $clase_contribuyente = Utilidad::maestra ($codeEmpresa,$codePais,'CLASE_CONTRIBUYENTE','CLASE_CONTRIBUYENTE');
         $lista_precios       = Utilidad::maestra ($codeEmpresa,$codePais,'LISTA_PRECIOS','LISTA_PRECIOS');
         $tipo_pedido         = Utilidad::maestra ($codeEmpresa,$codePais,'TIPO_PEDIDO','TIPO_PEDIDO');
         $bodega              = Utilidad::maestra ($codeEmpresa,$codePais,'BODEGA','BODEGA');
         $termino_pago        = Utilidad::maestra ($codeEmpresa,$codePais,'TERMINO_PAGO','TERMINO_PAGO');
        
         
         $Datacredito   =  DB::table('cl_form_credito')->where('usuario_id', '=', $idUsuario)->limit(1)->get();
         $formulario    =  DB::table('cl_info_general')->where('usuario_id', '=', $idUsuario)->limit(1)->get();
         
         $email_comercial = DB::table('jess_users')->where('id', '=', $formulario[0]->id_user_comercial )->get()[0]->email;

         $termino_actual = !isset($Datacredito[0]->termino_pago )  ? $dataUser[5]: $Datacredito[0]->termino_pago;

         $cupo_actual    = !isset($Datacredito[0]->cupo ) ? $dataUser[6]: $Datacredito[0]->cupo;
         $moneda_actual  = !isset($Datacredito[0]->moneda_cupo )  ? $dataUser[7]: $Datacredito[0]->moneda_cupo;
         
          

         return view('Cliente::creditofor',
        compact(
            'email_comercial',
            'emailcredito',
            'email',
            'formulario',
            'codePais',
            'codeEmpresa',
            'porcentajes',
            'distrito',
            'lista_precios',
            'tipo_pedido',
            'clase_contribuyente',
            'clase_perfil',
            'cobrador',
            'bodega',
            'site_use_code',
            'Datacredito', 
            'idGet',
            'tipo_cliente',
            'doc_garantia',
            'doc_pago',
            'dia_pago',
            'canal_bico',
            'tipo_moneda',
            'termino_pago',
            'termino_actual',
            'cupo_actual',
            'moneda_actual' ,
            'distrito_cliente',
        ));
    }

    public function downloadDoc($url){
        
        Utilidad::AllUserRol(['Credito','Cliente']);
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
       
        $ruta =  Storage::disk('local')->exists('/Soportes_Doc/' . $idUsuario . '/' .$url . '.pdf');
        $headers = ['Content-Type: application/pdf'];
        $newName =$url.'-'.$dataUser[8].'.pdf';
       
        if ($ruta ){
            return Storage::download('/Soportes_Doc/' . $idUsuario . '/' . $url . '.pdf',  $newName, $headers);
        }else{
            abort(404);
        }
        
    }

    

    public function documentoCliente(){
        $dataUser = clienteController::infoUser();
        $idUsuario = $dataUser[3];
          //si viene un id en el get, este será redireccionado entre las páginas
          $idGet = "";
          if($dataUser[4] == "si"){
              $idGet = $idUsuario;
          }

        $jess_users = DB::table('jess_users')->where('id', '=', $idUsuario )->get();
        $codePais = $jess_users[0]->cl_cod_pais;
        $codeEmpresa = $jess_users[0]->cl_org_id;

        $idFormulario = "";
        $formulario = DB::table('cl_info_general')->select('id_info')->where('usuario_id', '=', $idUsuario)->get();

        if ( count( $formulario) <= 0){
           return redirect('subidaDoCliente?id='.$idUsuario)->with('danger','Error, Se debe diligenciar la información general antes de cargar los documentos.'  );
        }

        $tieneDocumentos = DB::table('cl_documentos')
        ->where('usuario_id', '=', $idUsuario)
        ->where('nombre_docu', '=', 'FORMATO_CREACION')
        ->count();
        

        $isfirma_electronica = DB::table('control_firma')
        ->where('usuario_id', '=', $idUsuario)
        ->where('tipo_de_firma', '=', 'ADOBE')
        ->count();

        $isfirma_mano_escrita = DB::table('control_firma')
        ->where('usuario_id', '=', $idUsuario)
        ->where('tipo_de_firma', '=', 'MANO_ESCRITA')
        ->count();

        if ( $isfirma_electronica > 0 ){

         

            $firma = DB::table('control_firma')
                ->where('usuario_id', '=', $idUsuario)
                ->where('tipo_de_firma', '=', 'ADOBE')
                ->where('version' ,
                             DB::table('control_firma')
                             ->where('usuario_id', '=', $idUsuario)
                             ->where('tipo_de_firma', '=', 'ADOBE')
                             ->max('version'))
                ->get();
        
            $api_adobe = new adobe('v5');
            $estado_json = $api_adobe->getLogStatus($firma[0]->agreementid);
            $url_adobe = $api_adobe->getUrlAgreements($firma[0]->agreementid)->url;

        }else{
           
            $estado_json = '';
            $url_adobe ='#';

        }



        return view('Cliente::docCliente',
        compact(
            'url_adobe',
            'isfirma_electronica',
            'isfirma_mano_escrita',
            'estado_json',
            'codePais',
            'codeEmpresa',
            'tieneDocumentos',
            'idGet'
         ));
  

    }

    public function subidaDoCliente(Request $request){

        $dataUser = clienteController::infoUser();
        $codePais = $dataUser[0] ;
        $codeEmpresa = $dataUser[2] ; //trae codigo Empresa
        $idUsuario = $dataUser[3];
        if($request['idget']){
            $idUsuario = $request['idget'];
         }else{
            $dataUser = clienteController::infoUser();
            $idUsuario = $dataUser[3];
        }
        
       
         $filename = '/Repositorio_PortalClientes';

         $idFormulario = "";
         $formulario = DB::table('cl_info_general')->select('id_info')->where('usuario_id', '=', $idUsuario)->get();

         if ( count( $formulario) <= 0){
            return redirect('subidaDoCliente?id='.$idUsuario)->with('danger','Error, Se debe diligenciar la información general antes de cargar los documentos.'  );
         }
        
        foreach($formulario  as $idenfor){
            $idFormulario = $idenfor->id_info;
        }

        DB::table('cl_documentos')->where('usuario_id', $idUsuario)
        ->where('nombre_docu' ,'=','FORMATO_CREACION'  )
        ->delete();

         if (file_exists($filename)) {

                $datos['formato_creacion'] = $request->file('formato_creacion')->storeAs('Soportes_Doc/'.$idUsuario,'FORMATO_CREACION.pdf');
                
                
                DB::table('cl_documentos')->insert([
                    'id_docu' => DB::table('cl_documentos')->max('id_docu') + 1,
                    'nombre_docu' => 'FORMATO_CREACION',
                    'ruta' => $datos['formato_creacion'],
                    'formulario_id' => $idFormulario,
                    'usuario_id' => $idUsuario,
                    'fecha_hora' => date('Y-m-d H:i:s')
                ]);


         }

         return redirect('documentoCliente?id='.$idUsuario);

    }


    public function SolicitarCorrecion(request $request){

    try{  
        
        Utilidad::AllUserRol(['Credito']);
        //Trae informacion de usuario jess_users
       // $dataUser = clienteController::infoUser();
        
       

       $correos =  explode( ',' , $request->email );
     
       $mensaje =  date("Y-m-d H:i:s") . ' :  Se envía solicitud de corrección al cliente ';

        Notification::route('mail',  $correos)
        ->route('nexmo', '5555555555')
        ->route('slack', 'http://localhost:8081/Actualizacion/public/')
        ->notify(new Correcion('', $request->mensaje, auth()->user()->name ));

        DB::table('cl_form_credito')
            ->where('usuario_id', '=', $request['idget'])
            ->update(['observacion' => $request['observacion'] . ' \n ' . $mensaje]) ;

      
        }catch (Swift_SwiftException $e) {
        $e = 'EROROR';
        return response()->json($e);
    }
       
    $e = 'OK';
    return response()->json($e);
    
    }

    
   
}





