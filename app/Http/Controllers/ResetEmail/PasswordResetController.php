<?php

namespace App\Http\Controllers\ResetEmail;

use App\Models\User\User;
use App\Models\User\UserPassword;
use Carbon\Carbon;
use App\Models\ResetPassword\PasswordReset;
use App\Models\Cess\Cess;
use App\Models\cess_config_access_company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function index(Request $request)
    {
        return view('resetemail.index',compact('request'));
    }
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {   

        
       /** 14-05-2020 e-fergua    */ 
        $input = $request->all();
            array_walk_recursive($input, function(&$input) {
            $input = strip_tags($input);
         });
        $request->merge($input);

        $request->validate([
            'email' => 'required|string|min:5',
        ]);

        $user = User::where('email', $request->email)->first();
        //dd($user);
        if (!$user)
            $user = User::where('username', $request->email)->first();
            if (!$user)
                return redirect()->route('resetemail.index')
                ->with('danger','No se ha encontrado un usuario asociado a los datos suministrados!');
            
        if(Adldap::search()->users()->find($user->username) == true)
            return redirect()->route('resetemail.index')
            ->with('danger',' Fallo en el restablecimiento de contraseña: 
                    Su usuario es un usuario del directorio activo. Para restablecer su contraseña
                    por favor comuníquese con la línea 25000.');

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );
        
        if ($user && $passwordReset)

            Notification::send($user, new PasswordResetRequest($passwordReset->token));
           
            return redirect()->route('resetemail.index')
            ->with('status', 'Se ha enviado un correo de restablecimiento de contraseña a su correo electrónico! <br>
                    Si no encuentras el correo en la bandeja de entrada, por favor revisa tu carpeta de Spam.');

    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function show($passwordReset)
    {
        $token = PasswordReset::select('token','email')
                            ->where('id', $passwordReset)
                            ->get();
        return view('resetemail.resetEmail', compact('token'));
    }

    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset)
            return redirect()->route('resetemail.index')
            ->with('danger','El Token es invalido');
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(30)->isPast()) {
            $passwordReset->delete();
            return redirect()->route('resetemail.index')
            ->with('danger','El token ha caducado!');
        }
        return redirect()->route('resetemail.show',$passwordReset);
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {   

        /** 14-05-2020 e-fergua    */ 
        $input = $request->all();
            array_walk_recursive($input, function(&$input) {
            $input = strip_tags($input);
         });
        $request->merge($input);


        $request->validate([
            'email' => 'required|string',
            'password' => 'bail|required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[#´`?!@$%^:&*~-]/',
            'token' => 'required|string'
        ]);
        
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset)
            return redirect()->route('resetemail.show')
            ->with('danger','El token de restablecimiento de contraseña no es válido.');
        $user = User::where('email', $request->email)->get();
        if (!$user)
            $user = User::where('username', $request->email)->get();
            if (!$user)
                return redirect()->route('resetemail.show', $passwordReset)
                ->with('danger','No se ha encontrado un usuario asociado a los datos suministrados!');

        $id = $user[0]->id;
        $validate = $this->validatePassword($request, $id);
        if($validate == true){
            $pass = Hash::make($request->password);
            $now =  Carbon::now()->toDateTimeString();
            User::where('id', $id)
                ->update([
                    'password' => $pass,
                    'lastresettime' => $now,
                ]);
            $passwordReset->delete();
            Notification::send($user, new PasswordResetSuccess($passwordReset));
            return redirect()->route('home')
                ->with('success', 'La contraseña se ha restablecido exitosamente!');
        }else{
            return redirect()->route('resetemail.show', $passwordReset)
                ->with('danger','La contraseña No puede ser igual a las utilizadas anteriormente!');
        }
    }

    public function returnViewResetPassword(Request $request){
        $id = auth()->user()->id;
        return view('resetemail.resetPassword', compact('id'));
    }

    public function verifyPassword(Request $request){
       
        $request->validate([
            'password' => 'bail|required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[#´`?!@$%^:&*~-]/',
            'password_confirmation' => 'bail|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[#´`?!@$%^:&*~-]/',
        ]);

        $id = auth()->user()->id;
        $validate = $this->validatePassword($request, $id);
      
        if($validate == true){
            $user = User::where('id', $id)->first();
            $pass = Hash::make($request['password']);
            $now =  Carbon::now()->toDateTimeString();
            $user->update([
                'password' => $pass,
                'lastresettime' => $now,
            ]);
            return redirect()->route('home')
                ->with('success', 'Cambio de contraseña exitoso!');
        }else{
            return redirect()->route('home')->with('danger', 'La contraseña No puede ser igual a las utilizadas anteriormente.');
        }
    }

    public function validatePassword(Request $request, $id){
    
        $Userpass = UserPassword::select('id','password_user')
                    ->where('id_user', '=', $id)
                    ->get();
        $passUser = User::select('id','password')
                    ->where('id', '=', $id)
                    ->get();
            if(!isset($Userpass[0]->password_user)){
                $validatePass = Hash::check($request['password'], $passUser[0]->password);
                if($validatePass == true){
                    $id = (int)$id;
                    return false;
                }
            }else{
                $password = $request['password'];
                $count = 1;
                $position = 0;
                do{
                    $validatePass = Hash::check($password, $Userpass[$position]->password_user);
                    if($validatePass == true){
                        $id = (int)$id;
                        return false;
                    }
                    $count ++;
                    $position ++;
                }
                while(count($Userpass) >= $count);
            }

            //Eliminar el primer password utilizado si ya tiene 5
            if(count($Userpass) >= 5){
                UserPassword::destroy($Userpass[0]['id']);
            }

            $pass = Hash::make($request['password']);
            UserPassword::create([
                'id_user' => $id,
                'password_user' => $pass,
            ]);
            return true;
    }

        /**
         * Undocumented function
         *
         * @param Request $request
         * @return void
         * @author David Guanga <david.guanga@carvajal.com>
         * 
         * Carga la pagina de Gestión de  contraseña  para persona de Gestion Humana
         * 
         */
        public function ResetGH(Request $request)
        {


            $user = Cess::select('*')
            ->where('cess_id_user', '=', auth()->user()->id)
            ->get();
            
            $Access=cess_config_access_company::select('*')
            ->where('cess_id_org',      $user[0]['cess_id_org']    )
            ->where('cess_id_ext_per',  $user[0]['cess_id_ext_per'])
            ->where('cess_or_ext_per',  $user[0]['cess_or_ext_per'])
            ->where('cess_id_req_type',  'ADMIN_USER')->get();

            return view('resetemail.resetPassGh',compact('Access'));
        }
    
        /**
         * Undocumented function
         *
         * @return Json user
         * @author David Guanga <david.guanga@carvajal.com>
         * @method post
         * 
         * Retorna  la informcion de un usuario 
         * 
         */
        public function getUser(){

            $username =  $_GET['user'];
             
            $userLog = Cess::select('*')
            ->where('cess_id_user', '=', auth()->user()->id)
            ->get();
        
            $user = DB::table(DB::raw( 'jess_users jess_u , cess_users cess_u ' ))
                    ->select(DB::raw('jess_u.* '))
                    ->where (  'jess_u.profile_externalid','=', DB::raw('cess_u.cess_id_ext_per' ))
                    ->where (  'jess_u.profile_orgcountry','=',DB::raw( 'cess_u.cess_id_org'))
                    ->where (  'jess_u.block','=',0 )
                    ->whereIn('cess_u.cess_id_company',function($query)use( $userLog){

                         $query->select('cess_id_company')->from('cess_config_access_company') 
                         ->where('cess_id_org',       $userLog[0]['cess_id_org']    )
                         ->where('cess_id_ext_per',   $userLog[0]['cess_id_ext_per'])
                         ->where('cess_or_ext_per',   $userLog[0]['cess_or_ext_per'])
                         ->where('cess_id_req_type',  'ADMIN_USER') ;
                    } ) 
                    ->where ('jess_u.username','=',$username)
                    ->get();     
           
        
            if (isset ($user[0])){
                echo json_encode ($user[0]);
            }else{
              
                echo 'false'; 
               
            }
        }

        /**
         * Undocumented function
         *
         * @return void
         * @author David Guanga <david.guanga@carvajal.com>
         * 
         * Actualiza la contraseña y e-mail desde la pantaga de Admnistración
         * de usuarios
         * 
         */
        public function save()
        {   
            $username =  $_GET['user'];
            if(Adldap::search()->users()->find($username) == true){
                  
                echo 'false' ;
            }else{
                $id = $_GET['id'];
                $mail = $_GET['email'];
                $user = User::where('id', DB::raw($id))->first();
                $pass = Hash::make($_GET['pass']);
                $user->update([
                    'password' => $pass,
                    'email' => strtolower(trim($mail)),
                    'lastresettime' => null,
                ]);

                Notification::send($user, new PasswordResetSuccess(''));
                echo 'La contraseña fue actualizada';
            }
               
        }

        /**
         * Undocumented function
         *
         * @return void
         * @author David Guanga <david.guanga@carvajal.com>
         * 
         * Envia token bajo demanda  desde el modulo de administración de usuario
         *  
         */
        public function sendmail (){

            $username =  $_GET['user'];
            if(Adldap::search()->users()->find($username) == true){
                  
                echo 'false' ;
            }else{
               
                $id = $_GET['id'];
                $mail = $_GET['email'];
                $user = User::where('id', DB::raw($id))->first();
               
                
                $user->update([
                    'email' => strtolower(trim($mail)),
                ]);

                $user = User::where('id', DB::raw($id))->first();
                $passwordReset = PasswordReset::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'email' => $user->email,
                        'token' => Str::random(60)
                    ]
                );
                
                Notification::send($user, new PasswordResetRequest($passwordReset->token));

                echo 'OK';

            }



        }

}
