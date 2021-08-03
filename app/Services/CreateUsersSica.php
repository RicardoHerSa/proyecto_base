<?php namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReporteSoporte;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;
USE App\Services\Exception;

Class CreateUsersSica {

    public $User_Total;

    public  function   __construct()
    {
        $this->User_Total = DB::table('cvj_co_interfaz_sica_prd')->count();
    }
    
   /**
    * Undocumented function
    *
    * @return string
    * @author David Guanga <david.guanga@carvajal.com>
    */
    public  function getUserTotal(){
        return  $this->User_Total;
    }
    
    public function run(){
            $user_new = array();
            $user_grupo = array();
        
   
            $user =  new CreateUsersSica();
            $Total = $user->getUserTotal();
            // Identificar Lo Nuevo  
            if ($Total > 0) {
            //Identificar lo que se debe actualizar 
            $Crear =  DB::table('cvj_co_interfaz_sica_prd')
            ->whereNotNull('UsuarioDominio')
            ->where('UsuarioDominio' ,'<>','')
            ->where('email' ,'<>','')
            // ->where('identificacion',['1130614392','66953471'])
            ->whereNotIn('UsuarioDominio' , function($q){
                $q->select('username')->from('jess_users')->where('block',0);
            })
            ->get();
            
    
            set_time_limit(0);
            foreach ($Crear as $user) {
                
                    $temp_id =  DB::raw("nextval('jess_users_id_seq')");
                    DB::table('jess_users')->insert( [
                                'id' =>  $temp_id,
                                'name' => $user->nombre . ' ( ' . $user->identificacion . ' )' ,
                                'username' =>  $user->UsuarioDominio,
                                'email'  => $user->email,
                                'password'  => Hash::make($user->UsuarioDominio),
                                'block'  => '0',
                                'sendemail'  => '0',
                                'registerdate'  => DB::raw("current_date"),
                                'lastvisitdate'  => DB::raw('null'),
                                'activation'  => '',
                                'params'  => DB::raw('null'),
                                'lastresettime'  => now(),
                                'resetcount'  => 0,
                                'otpkey'  => '',
                                'otep'  => '',
                                'requirereset'  => 0,
                                'created_at' => DB::raw("current_date"),
                                'updated_at'  => DB::raw("current_date"),
                                'profile_orgcountry'  =>  $user->empresa,
                                'profile_externalid'  =>  $user->empresa,
                                'profile_ordinal'  => '1',
                                'gestor_externo' => 0,
                                'remember_token' => DB::raw('null'),
                                'usuario_lda' =>  'IS_LDA'
                            ]) ;
    
                            // Obtener el ID usuario Laravel
                            $id = DB::table('jess_users')->select(DB::raw('id'))
                            ->where('name',  $user->nombre . ' ( ' . $user->identificacion . ' )')
                            ->where('username' ,  $user->UsuarioDominio)->get();
    
                            $idLaravel = $id[0]->id;
    
                                // Asignar Grupos Basico
                            DB::table('jess_user_usergroup_map')->insert( [
                                'user_id' =>$idLaravel,
                                'usergroup_id' => 3
                            ]);
    
            }
            
            $Inactivar =  DB::table('jess_users')->whereNotIn('username' , function($q){
                $q->select('UsuarioDominio')->from('cvj_co_interfaz_sica_prd');
            })->where('block',0)
            ->get();
    
            foreach ($Inactivar as $user) {
                DB::table('jess_users')
                    ->where('id', $user->id)
                    //->where('username','not like','%gestor%')
                    //->where('username','not like','%ext-%')
                    ->where('usuario_lda' ,'<>',  'NO_BORRAR')
                    ->where('block' ,  0)
                    ->update(['block' => 1 ,'username'=>'INA-'.date('Y-m-d H:i').'- '.$user->username , 'email'=> 'INA-'.now().'-'.$user->email ]);
                }
                Notification::route('mail',['david.guanga@carvajal.com','Elizabeth.Casas@carvajal.com'])->notify(new ReporteSoporte( count($Crear),count( $Inactivar)-1 ,'OK'));
                
                return json_encode(array('message' => 'Operacion Realizada'  ,"status"=> "success"));

            }else{
                Notification::route('mail',['david.guanga@carvajal.com','Elizabeth.Casas@carvajal.com'])->notify(new ReporteSoporte( '','' ,'ERROR'));
                return json_encode( array('message' => 'Operacion NO Realizada'  ,"status"=> "error"));
            
            }

      
          
    }


}