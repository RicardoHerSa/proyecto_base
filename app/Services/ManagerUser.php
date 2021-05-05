<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 *
 * @author David Guanga <david.guanga@carvajal.com>
 * 
 * Esta clase es la encargada de Crear usuario , Actualizar Jefe e inactivar usuarios
 * 
 */

class ManagerUser
{

    /**
     * @return void
     * @author David Guanga <david.guanga@carvajal.com>
     * 
     * PROCESO CREACION DE USUARIO
     * 
     * 1) Se consulta la infomracion y configuración por empresa de la tabla  cess_esb_schedule_params  con la palbra clave NEW_USER
     * 2) Se consulta los nuevos usuarios
     * 3) Se obtine el id Laravel
     * 4) Se inserta en la tabla jess_users
     * 5) Se insert en la tabla jess_user_usergroup_map los grupos correspondiente Empresa y Colaborador 
     *
     *
     */
    function CeateUser()
    {
        /** 
         * Obtiene las empresas activas para crear usuario
         * 
         * Configuracion tabla cess_esb_schedule_params
         * 
         * cess_attribute_1 : Grupo sociedad
         * cess_attribute_2 : Grupo colaborador
         * cess_attribute_3 : Visual    
         * cess_attribute_4 : Jefe
         * cess_attribute_5 : Estudiante
         * cess_attribute_6 : Prefijo Usuario
         * cess_attribute_7 : Si la empresa usa estructura
         * cess_attribute_8 : Si la empresa usa usuarios de dominio
         * cess_attribute_9 : Definición de Contraseña  ALL = Contraseaña igual a usuario  o  -4 utlimos 4 digitos del usuario
         *          
         */
        $Inicio =  time();
        $Empresas = DB::table('cess_esb_schedule_params')->select(DB::raw('*'))
            ->where('cess_id_dataservice', 'NEW_USER')
            ->where('cess_status', 'ACT')->get();

        foreach ($Empresas as $cofig_empresa) { //Empresa
            set_time_limit(20000);
            //Obtiene nuevos usuarios
            $query = "select 
                       p.cess_id_org, 
                       e.cess_id_ext_per, 
                       e.cess_or_ext_per, 
                       p.cess_full_name,
                       p.cess_nat_identifier,
                       e.cess_id_company, 
                       e.cess_company, 
                       p.cess_email,  
                       p.cess_nat_identifier_2,
                       e.cess_id_contract

                    from cess_person_inf  p , cess_employee_inf e
                    where 
                        p.cess_id_org ='" . $cofig_empresa->cess_id_org .
                "' and p.cess_id_org = e.cess_id_org 
                       and P.cess_id_ext_per = e.cess_id_ext_per 
                       and e.cess_id_emp_type not in ('6' ,'5')  
                       and  not exists (select 'X' from jess_users  
                                        where  profile_externalid = e.cess_id_ext_per 
                                        and profile_orgcountry = e.cess_id_org) 
                       and e.cess_id_company = '" . $cofig_empresa->cess_id_company . "' 
                       order by  e.cess_id_company , p.cess_full_name , e.cess_id_ext_per";

            $NewUser = DB::select($query);


            foreach ($NewUser as $user) { //Usuarios

                $username= '';
                if ($cofig_empresa->cess_attribute_6 == 'MX' &&  $user->cess_nat_identifier_2 <> ''){
                    $username=$cofig_empresa->cess_attribute_6 . $user->cess_nat_identifier_2;
                }else{
                    $username = $cofig_empresa->cess_attribute_6 . $user->cess_nat_identifier;
                }


                $k = $username;

                if ($cofig_empresa->cess_attribute_9 != 'ALL') {

                    $k = substr( $username, intval($cofig_empresa->cess_attribute_9));
                }
                 
                 



                // Insertar Nuevo Usuario
                DB::table('jess_users')->insert([
                    [
                        'id' => DB::raw("nextval('jess_users_id_seq')"),
                        'name' => $user->cess_full_name,
                        'username' => $username,
                        'email'  => $user->cess_email,
                        'password'  => Hash::make($k),
                        'block'  => '0',
                        'sendemail'  => '0',
                        'registerdate'  => DB::raw("current_date"),
                        'lastvisitdate'  => DB::raw('null'),
                        'activation'  => '',
                        'params'  => DB::raw('null'),
                        'lastresettime'  => DB::raw('null'),
                        'resetcount'  => 0,
                        'otpkey'  => '',
                        'otep'  => '',
                        'requirereset'  => 0,
                        'created_at' => DB::raw("current_date"),
                        'updated_at'  => DB::raw("current_date"),
                        'profile_orgcountry'  => $user->cess_id_org,
                        'profile_externalid'  => $user->cess_id_ext_per,
                        'profile_ordinal'  => '1',
                        'gestor_externo' => 0,
                        'remember_token' => DB::raw('null')
                    ]
                ]);

                // Obtener el ID usuario Laravel
                $id = DB::table('jess_users')->select(DB::raw('id'))
                    ->where('profile_orgcountry', $user->cess_id_org)
                    ->where('profile_externalid', $user->cess_id_ext_per)->get();

                $idLaravel = $id[0]->id;

                // Iserta jess_users

                DB::table('cess_users')->insert([
                    [
                        'cess_id_user' => $idLaravel,
                        'cess_id_org' => $user->cess_id_org,
                        'cess_id_ext_per' => $user->cess_id_ext_per,
                        'cess_or_ext_per' => '1',
                        'cess_id_company' => $cofig_empresa->cess_id_company,
                        'cess_dt_start' => DB::raw("current_date"),
                        'cess_dt_end' =>  DB::raw("current_date"),
                        'cess_username' => $username,
                        'block' => '1',
                        'cess_id_org_working_obo' => $user->cess_id_org,
                        'cess_id_company_working_obo' => $cofig_empresa->cess_id_company,
                        'cess_id_ext_per_working_obo' => $user->cess_id_ext_per,
                        'cess_or_ext_per_working_obo' => '1',
                        'cess_dt_create' => DB::raw("current_date"),
                        'cess_dt_update' => DB::raw('null'),
                        'cess_document' => $username,
                        'updated_at' => DB::raw("current_date"),
                        'created_at' => DB::raw("current_date")
                    ]
                ]);

                // Asignar Grupos Basico

                //Grupo empresa
                DB::table('jess_user_usergroup_map')->insert([
                    [
                        'id' => DB::raw("nextval('jess_user_usergroup_map_seq')"),
                        'user_id' => $idLaravel,
                        'usergroup_id' => $cofig_empresa->cess_attribute_1
                    ]
                ]);

                //Grupo Colaborador
                 //Grupo Colaborador
                 if ( $user->cess_id_contract != 'APS'){
                        DB::table('jess_user_usergroup_map')->insert([
                            [
                                'id' => DB::raw("nextval('jess_user_usergroup_map_seq')"),
                                'user_id' => $idLaravel,
                                'usergroup_id' => $cofig_empresa->cess_attribute_2
                            ]
                        ]);
                    }
                
                //Grupo Estudiante
                if ( $user->cess_id_contract == 'APS'){
                    DB::table('jess_user_usergroup_map')->insert([
                        [
                            'id' => DB::raw("nextval('jess_user_usergroup_map_seq')"),
                            'user_id' => $idLaravel,
                            'usergroup_id' => $cofig_empresa->cess_attribute_5
                        ]
                    ]);
                }
                
                //Grupo Visual
                DB::table('jess_user_usergroup_map')->insert([
                    [
                        'id' => DB::raw("nextval('jess_user_usergroup_map_seq')"),
                        'user_id' => $idLaravel,
                        'usergroup_id' => $cofig_empresa->cess_attribute_3
                    ]
                ]);
            }

            DB::table('cess_esb_schedule_params')
                ->where('cess_id', $cofig_empresa->cess_id)
                ->update(['cess_attribute_11' => DB::raw("current_date")]);
        } //Empresa


        $Fin = time();
        $Total = ($Fin - $Inicio) / 60;
        DB::table('cess_log_schedule_laravel')->insert(
            [
                'id' =>  DB::raw("current_date"),
                'key' => 'CREATE_USER',
                'atrr1'    => $Total,
                'atrr2'    => 'TAREA_PROGRAMADA'
            ]
        );
    }

    /**
     * @return void
     * @author David Guanga <david.guanga@carvajal.com>
     * 
     * Actualiza los jefes de la empresa  que no cuenta con estructura
     */
    function UpdateJefeSinEstructura()
    {

        $Inicio =  time();
        //Obtiene empresa 
        $Empresas = DB::table('cess_esb_schedule_params')
            ->select(DB::raw('*'))
            ->where('cess_id_dataservice', 'NEW_USER')
            ->where('cess_attribute_7', 'SIN_POSITION')
            ->where('cess_status', 'ACT')->get();

        foreach ($Empresas as $cofig_empresa) { //Empresa
            set_time_limit(20000);
            //Consulta de Jefes
            $Jefes = DB::table('cess_employee_inf')
                ->select('cess_id_org_sup', 'cess_id_ext_per_sup')
                ->distinct()
                ->where('cess_id_org_sup', $cofig_empresa->cess_id_org)
                ->where('cess_id_company', $cofig_empresa->cess_id_company)
                ->where('cess_id_ext_per_sup', '<>', '0')
                ->whereNotNull('cess_id_ext_per_sup')
                ->get();

            //ELmina persona dentro del grupo de jefe de la empresa
            DB::table('jess_user_usergroup_map')->where('usergroup_id', $cofig_empresa->cess_attribute_4)->delete();

            foreach ($Jefes  as $Jefe) { //Empresa

                // Obtener el ID usuario Laravel
                $id = DB::table('jess_users')->select(DB::raw('id'))
                    ->where('profile_orgcountry', $Jefe->cess_id_org_sup)
                    ->where('profile_externalid', $Jefe->cess_id_ext_per_sup)->get();

                if (isset($id[0]->id)) {
                    $idLaravel = $id[0]->id;
                    //Inserta Grupo Jefe
                    DB::table('jess_user_usergroup_map')->insert([
                        [
                            'id' => DB::raw("nextval('jess_user_usergroup_map_seq')"),
                            'user_id' => $idLaravel,
                            'usergroup_id' => $cofig_empresa->cess_attribute_4
                        ]
                    ]);
                }
            }
        } //Empresa

        $Fin = time();
        $Total = ($Fin - $Inicio) / 60;
        DB::table('cess_log_schedule_laravel')->insert(
            [
                'id' =>  DB::raw("current_date"),
                'key' => 'UPDATE_JEFE_SIN_ESTRUCTURA',
                'atrr1'    => $Total,
                'atrr2'    => 'TAREA_PROGRAMADA'
            ]
        );
    }

    /**
     * Undocumented function
     * Se actualiza los usuarios de Lda
     *
     * @return void
     * @author David Guanga <david.guanga@carvajal.com>
     */

    function updatelda()
    {

        $Inicio =  time();
        $Empresas = DB::table('cess_esb_schedule_params')->select(DB::raw('*'))
            ->where('cess_id_dataservice', 'NEW_USER')
            ->where('cess_attribute_8', 'USER_LDA')   // Empresas que usan  usuario lda
            ->where('cess_status', 'ACT')->get();

        foreach ($Empresas as $cofig_empresa) { //Empresa
            set_time_limit(20000);
            $sql = "select
                    usr.*
                from
                    cess_employee_inf emp ,
                    jess_users usr
                where
                    emp.cess_id_org = '" . $cofig_empresa->cess_id_org . "'
                    and emp.cess_id_company = '" . $cofig_empresa->cess_id_company . "'
                    and usr.profile_externalid = emp.cess_id_ext_per 
                    and usr.profile_orgcountry = '" . $cofig_empresa->cess_id_org . "'
                    and usr.usuario_lda is null";

            $users =    DB::select($sql);


            //MX Se localiza el usuario con el correo
            if ($cofig_empresa->cess_attribute_6 == 'MX') {

                foreach ($users as  $user) {
                    //upper 

                    $lda = DB::table('cess_user_ldap')
                        ->SELECT(DB::raw('*'))
                        ->where(DB::raw('upper (email)'), strtoupper($user->email))
                        ->where('cedula', 'like',  str_replace("-", "" ,substr($user->username, 0, 11)) . '%')
                        ->get();

                    if (count($lda) > 0) {
                        // Actualizar Usuario jess_user

                        DB::table('jess_users')
                            ->where('id', $user->id)
                            ->update([
                                'username' => $lda[0]->username,
                                'lastresettime' => DB::raw("current_date"),
                                'usuario_lda' => 'IS_LDA'
                            ]);


                        // Actualizar Usuarion Cess_users

                        DB::table('cess_users')
                            ->where('cess_id_user', $user->id)
                            ->update([
                                'cess_document' => DB::raw('cess_username'),
                                'cess_username' => $lda[0]->username,
                                'cess_dt_update' => DB::raw("current_date")
                            ]);
                    }
                }
            } //Fin Mx

            //CO Se localiza el usuario con la cedula
            if ($cofig_empresa->cess_attribute_6 == 'CO') {

                foreach ($users as  $user) {
                    //upper

                    $lda = DB::table('cess_user_ldap')
                        ->SELECT(DB::raw('*'))
                        ->where(DB::raw('upper (email)'), strtoupper($user->email))
                        ->where('cedula', $user->username)
                        ->get();

                    if (count($lda) > 0) {
                        // Actualizar Usuario jess_user
                        DB::table('jess_users')
                            ->where('id', $user->id)
                            ->update([
                                'username' => $lda[0]->username,
                                'lastresettime' => DB::raw("current_date"),
                                'usuario_lda' => 'IS_LDA'
                            ]);


                        // Actualizar Usuarion Cess_users
                        DB::table('cess_users')
                            ->where('cess_id_user', $user->id)
                            ->update([
                                'cess_document' => DB::raw('cess_username'),
                                'cess_username' => $lda[0]->username,
                                'cess_dt_update' => DB::raw("current_date")
                            ]);
                    }
                }
            } //Fin Mx

        }

        $Fin = time();
        $Total = ($Fin - $Inicio) / 60;
        DB::table('cess_log_schedule_laravel')->insert(
            [
                'id' =>  DB::raw("current_date"),
                'key' => 'UPDATE_LDA',
                'atrr1'    => $Total,
                'atrr2'    => 'TAREA_PROGRAMADA'
            ]
        );
    }

    function disableusers()
    {
        set_time_limit(20000);
        $Inicio =  time();

        DB::table('jess_users')
            ->whereNotIn('profile_externalid', function ($query) {

                $query->select(DB::raw('cess_id_ext_per'))
                    ->from('cess_employee_inf')
                    ->where('cess_id_ext_per', 'not like', '%ges0%');
            })
            ->where('username', 'not like', '%INA%')
            ->where('username', 'not like', '%soporte%')
            ->where('profile_externalid', 'not like', '%ges0%')
            ->where('id', '<>', 388)
            ->where('block', '=', '0')
            ->update([
                'username' => DB::raw("'INA'||to_char(current_date, 'YYMMDD')|| '-' || username"),
                'email' => DB::raw("'INA'||to_char(current_date, 'YYMMDD')|| '-' || email"),
                'updated_at' => DB::raw("current_date"),
                'block' => '1'
            ]);

        $Fin = time();
        $Total = ($Fin - $Inicio) / 60;
        DB::table('cess_log_schedule_laravel')->insert(
            [
                'id' =>  DB::raw("current_date"),
                'key' => 'DISABLE_USERS',
                'atrr1'    => $Total,
                'atrr2'    => 'TAREA_PROGRAMADA'
            ]
        );
    }
}
