<?php

namespace App\Http\Controllers\RelationUserGroup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Usergroup\Usergroup;
use App\Models\User\User;
use App\Models\User\UserPassword;
use App\Models\Cess\Cess;
use App\Models\Relations\RelationsUsersGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Adldap\Laravel\Facades\Adldap;
use Carbon\Carbon;

class RelationUserGroupController extends Controller
{
    
    public function index(Request $request)
    {
        $users = User::get();
        $group = Usergroup::get();
        return view('users.index', compact('users','group'));
    }

    public function create()
    {   
        $arrayGroupUser = [];
        $user = User::get();
        $groups = Usergroup::where('parent_id', 0)->get();
        return view('users.create', compact('groups','user','arrayGroupUser'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'bail|required|min:5|max:100',
            'username' => 'bail|required|unique:jess_users,username',
            'email' => 'bail|required|email|max:140|unique:jess_users,email',
            'password' => 'bail|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[#´`?!@$%^:&*~-]/',
        ]);

        //crear users
        $user = User::create([
                'name' => $request['name'],
                'username' => $request['username'],
                'email' => $request['email'],
                'block' =>  $request['block'],
                'password' => Hash::make($request['password']),
                'profile_orgcountry' => $request['profile_orgcountry'],
                'profile_externalid' => $request['profile_externalid'],
                'profile_ordinal' => $request['profile_ordinal'],
                'gestor_externo' => $request['gestor_externo'],
                'block' => $request['block']
                ]);

        //crear relacion users-groups
        $user->usergroups()->sync($request->get('groups'));
        
        //buscar IdEmpresa con grupos
        $grupos = $request->get('groups');
        if($grupos == null){
            $grupo = 1;
        }else{ $grupo = $grupos[0]; }
        $idEmpresa = Usergroup::select('companycessid')
                ->where('id', '=', $grupo)
                ->get();
        if($idEmpresa[0]->companycessid == null){
            $idempresa = 1;
        }else{ $idempresa = $idEmpresa[0]->companycessid; }

        //crear realcion con tabla Cess_user       
        Cess::create([
                'cess_id_user' => $user['id'],
                'cess_id_org' => $user['profile_orgcountry'],
                'cess_id_ext_per' => $user['profile_ordinal'],
                'cess_or_ext_per' => $user['profile_externalid'],
                'cess_id_company' => $idempresa,
                'cess_dt_start' => $user['created_at'],
                'cess_dt_end' => $user['updated_at'],
                'cess_username' => $user['username']
        ]);
        
        //Agregar registro de contraseña
        UserPassword::create([
            'id_user' => $user['id'],
            'password_user' => $user['password']
        ]);
   
        return redirect('users')->with('success', 'Usuario agregado con éxito!');

    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $consulta = RelationsUsersGroup::select("usergroup_id")
                ->where("user_id", $id)->get();
        return view('users.show', compact('user', 'consulta'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $arrayGroupUser= [];
        $groupUser = RelationsUsersGroup::select("usergroup_id")
                ->where("user_id",$id)->get()->toArray();
        foreach($groupUser as $userGroup){
            $arrayGroupUser[] = $userGroup['usergroup_id'];
        }
        $groups = Usergroup::where('parent_id',0)->get();
        if(Adldap::search()->users()->find($user['username']) == true){
            $visibility = "display:none";
        }else{ $visibility = "";}
        //dd($visibility);
        return view('users.edit', compact('user', 'groups','arrayGroupUser','visibility'));
    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'bail|required|min:5|max:100',
            'username' => 'bail|required',
            'email' => 'bail|required|email|max:140',
        ]);

        $user = User::findOrFail($id);
        $cess = Cess::select('cess_id_user')
                ->where('cess_id_user', '=', $id)
                ->get();

        //buscar IdEmpresa con grupos
        $grupos = $request->get('groups');
        if($grupos == null){
            $grupo = 1;
        }else{ $grupo = $grupos[0]; }
        $idEmpresa = Usergroup::select('companycessid')
                ->where('id', '=', $grupo)
                ->get();
        if($idEmpresa[0]->companycessid == null){
            $idempresa = 1;
        }else{ $idempresa = $idEmpresa[0]->companycessid; }

        //validacion del user en Cess_user
        $now =  Carbon::now()->toDateTimeString();
        if(isset($cess[0]->cess_id_user)){
             $idCess = $cess[0]->cess_id_user;
        }else{
            Cess::create([
                'cess_id_user' => $id,
                'cess_id_org' => $request['profile_orgcountry'],
                'cess_id_ext_per' => $request['profile_externalid'], 
                'cess_or_ext_per' => $request['profile_ordinal'],
                'cess_id_company' => $idempresa,
                'cess_dt_start' => $now,
                'cess_dt_end' => $now,
                'cess_username' => $user->username
            ]);
            $idCess = $id;
        }
        //valirdar password
        if($request['password'] != null){

            $request->validate([
                'password' => 'bail|required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[#´`?!@$%^:&*~-]/',
            ]);

            $Userpass = UserPassword::select('id','password_user')
                ->where('id_user', '=', $id)
                ->get();
            
            if(!isset($Userpass[0]->password_user)){
                $validatePass = Hash::check($request['password'], $request['password_antiguo']);
                if($validatePass == true){
                    $id = (int)$id;
                    return redirect()->route('users.edit', [$id])->with('danger', 'La contraseña No puede ser igual a las utilizadas anteriormente.');
                }
            }else{
                $password = $request['password'];
                $count = 1;
                $position = 0;
                do{
                    $validatePass = Hash::check($password, $Userpass[$position]['password_user']);
                    if($validatePass == true){
                        $id = (int)$id;
                        return redirect()->route('users.edit', [$id])->with('danger', 'La contraseña No puede ser igual a las utilizadas anteriormente.');
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
            User::where('id', $id)
                ->update([
                    'lastresettime' =>  null,
                ]);
            UserPassword::create([
                'id_user' => $id,
                'password_user' => $pass,
            ]);
            
        }

        $user->update([
            'name' => $request['name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => ($request['password'] != null ? $pass : $request['password_antiguo']),
            'profile_orgcountry' => $request['profile_orgcountry'],
            'profile_externalid' => $request['profile_externalid'],
            'profile_ordinal' => $request['profile_ordinal'],
            'gestor_externo' => $request['gestor_externo'],
            'block' => $request['block'],
            ]);

        $user->usergroups()->sync($request->get('groups'));
        
        Cess::where('cess_id_user', $idCess)
                ->update([
                    'cess_id_user' => $user['id'],
                    'cess_id_org' => $user['profile_orgcountry'],
                    'cess_id_ext_per' => $user['profile_externalid'],
                    'cess_or_ext_per' => $user['profile_ordinal'],
                    'cess_id_company' => $idempresa,
                    'cess_dt_start' => $user['created_at'],
                    'cess_dt_end' => $user['updated_at'],
                    'cess_username' => $user['username'],
                ]);

        return redirect('users')->with('success', 'Usuario editado con éxito!');
    }

    public function Destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'Usuario eliminado con éxito.']);
        //return redirect('users')->with('flash_message', 'User deleted!');
    }

    public function UpdateStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->block = $request->block;
        $user->save();

        return response()->json(['message' => 'Estado del Usuario actualizado.']);
    }

    public function UpdateStatusmassive(Request $request)
    {
        
        if($user = $request['massive']){
        
            foreach ($user as $users) {
                $idmassive = User::where('id', '=', $users)->firstOrFail();
                $idmassive->block = '1';
                $idmassive->save();
            }

        }   elseif($user = $request['massiveright']){
                foreach ($user as $users) {
                    $idmassive = User::where('id', '=', $users)->firstOrFail();
                    $idmassive->block = '0';
                    $idmassive->save();
                }
            
            }   else {
                return response()->json(['messageError' => 'Error en el Cambio de estado Masivo del Usuario.']);
            } 
        return response()->json(['message' => 'Estado Masivo del Usuario actualizado con éxito!']);
    }

    public function Cess($view){

        $cess = Cess::findOrFail($view[0]->id);
        dd($cess);

        Cess::create([
            'cess_id_user' => $view['id'],
            'cess_id_org' => $view['profile_orgcountry'],
            'cess_id_ext_per' => $view['profile_ordinal'],
            'cess_or_ext_per' => $view['profile_externalid'],
            'cess_id_company' => 1,
            'cess_dt_start' => $view['created_at'],
            'cess_dt_end' => $view['updated_at'],
            'cess_username' => $view['username'],
        ]);
    }
    
    
     /*  Se modifica cosnultas sql x funciones Query Builder  de Laravel  e-fergua */

    public function pagination(){
       
       /* $searchByGroups=$_GET['draw'];
        $sel =  DB::table('jess_users as jssu')->select(DB::raw('*'))->wherein( 'jssu.id',
        function ($query ) use($searchByGroups) {
            $query->select('u.id')->from('jess_users as u')
                  ->join('jess_user_usergroup_map as uu' ,'uu.user_id','=','u.id')
                  ->join('jess_usergroups  as ug' ,'uu.usergroup_id','=','ug.id')
                 ->Where('ug.id','=',$searchByGroups)
                 ->groupBy('u.id');

          }  )->get();

        dd($sel);*/

        ## leer datos 
        $draw = $_GET['draw'];
        $row = $_GET['start']; 
        $rowperpage = $_GET['length']; // filas por pagina
        $columnIndex = $_GET['order'][0]['column']; // Columna index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Columna nombre
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // buscador
        $searchByGroups = $_GET['searchByGroups']; // filtro por groups
        $totalRecordwithFilter=0;
        ## buscador/filtro
        $searchQuery = " ";
        $num = 1;
        if($searchByGroups != '' && $searchByGroups != '0'){

           /* $searchQuery = $searchQuery."1 and  jssu.id in (select u.id
                                    from jess_users u
                                    join jess_user_usergroup_map uu on uu.user_id = u.id
                                    join jess_usergroups ug on uu.usergroup_id = ug.id
                                    where ug.id = '".$searchByGroups."'
                                    group by u.id)";*/

            ## Numero total de datos con filtro
               
                /*$sel = DB::select(DB::raw("select count(*) as allcount from jess_users jssu where ".$num." = ".$searchQuery));    
                $records = $sel[0];
                $totalRecordwithFilter = $records->allcount;*/

                $sel =  DB::table('jess_users as jssu')->select(DB::raw('count(*) as allcount'))->wherein( 'jssu.id',
                function ($query ) use($searchByGroups) {
                    $query->select('u.id')->from('jess_users as u')
                          ->join('jess_user_usergroup_map as uu' ,'uu.user_id','=','u.id')
                          ->join('jess_usergroups  as ug' ,'uu.usergroup_id','=','ug.id')
                         ->Where('ug.id','=',$searchByGroups)
                         ->groupBy('u.id');

                  }  )->get();

                $records = $sel[0];
                $totalRecordwithFilter = $records->allcount;
        

 
            ## buscador de datos
            // $empQuery = DB::select(DB::raw("select jssu.* from jess_users jssu where ".$num." = ".$searchQuery." order by ".$columnName." asc offset "."$row"." limit ".$rowperpage));
             
            
             $empQuery =  DB::table('jess_users as jssu')->select(DB::raw('jssu.*'))->wherein( 'jssu.id',
             function ($query )use($searchByGroups) {
                 $query->select('u.id')->from('jess_users as u')
                       ->join('jess_user_usergroup_map as uu' ,'uu.user_id','=','u.id')
                       ->join('jess_usergroups  as ug' ,'uu.usergroup_id','=','ug.id')
                      ->Where('ug.id','=',$searchByGroups)
                      ->groupBy('u.id');

               }  )->orderBy($columnName, 'asc')->skip($row)->take($rowperpage)->get();
     
            
           
           
           
             $data = array();


        }

        if($searchValue != '' && $searchByGroups != '' && $searchByGroups != '0'){

            /*$searchQuery = $searchQuery."and (name like '%".$searchValue."%' or 
                    username like '%".$searchValue."%' or
                    email like '%".$searchValue."%' ) ";*/

             ## Numero total de datos con filtro
               /* $sel = DB::select(DB::raw("select count(*) as allcount from jess_users jssu where ".$num." = ".$searchQuery));    
                $records = $sel[0];
                $totalRecordwithFilter = $records->allcount;*/

                $sel = DB::table('jess_users as jssu')->select(DB::raw('count(*) as allcount'))
                ->wherein( 'jssu.id',
                function ($query )use($searchByGroups) {
                    $query->select('u.id')->from('jess_users as u')
                        ->join('jess_user_usergroup_map as uu' ,'uu.user_id','=','u.id')
                        ->join('jess_usergroups  as ug' ,'uu.usergroup_id','=','ug.id')
                        ->Where('ug.id','=',$searchByGroups)
                        ->groupBy('u.id');

                }  )->where(function($query2) use ($searchValue)
                    {
                        $query2->where( 'name','iLIKE','%'.$searchValue.'%')
                               ->orWhere('username','iLIKE','%'.$searchValue.'%')
                               ->orWhere('email','iLIKE','%'.$searchValue.'%');
                })->get();

                $records = $sel[0];
                $totalRecordwithFilter = $records->allcount;

            ## buscador de datos
             //$empQuery = DB::select(DB::raw("select jssu.* from jess_users jssu where ".$num." = ".$searchQuery." order by ".$columnName." asc offset "."$row"." limit ".$rowperpage));
             
             $empQuery = DB::table('jess_users as jssu')->select(DB::raw('jssu.*'))
                ->wherein( 'jssu.id',
                function ($query ) use($searchByGroups){
                    $query->select('u.id')->from('jess_users as u')
                        ->join('jess_user_usergroup_map as uu' ,'uu.user_id','=','u.id')
                        ->join('jess_usergroups  as ug' ,'uu.usergroup_id','=','ug.id')
                        ->Where('ug.id','=',$searchByGroups)
                        ->groupBy('u.id');

                }  )->where(function($query2) use($searchValue)
                    {
                        $query2->where( 'name','iLIKE','%'.$searchValue.'%')
                               ->orWhere('username','iLIKE','%'.$searchValue.'%')
                               ->orWhere('email','iLIKE','%'.$searchValue.'%')
                               ->orWhere('profile_externalid','iLIKE','%'.$searchValue.'%');
                })->orderBy($columnName, 'asc')->skip($row)->take($rowperpage)->get();
             
             $data = array();
        
        }

        if($searchValue != '' && $searchByGroups == '0'){
            /*$searchQuery = $searchQuery."1 and (name like '%".$searchValue."%' or 
                     username like '%".$searchValue."%' or
                     email like '%".$searchValue."%' ) ";*/

            ## Numero total de datos con filtro
            $sel = DB::table('jess_users as jssu')->select(DB::raw('count(*) as allcount'))
            ->where(function($query2) use ($searchValue)
                {
                    $query2->where( 'name','iLIKE','%'.$searchValue.'%')
                           ->orWhere('username','iLIKE','%'.$searchValue.'%')
                           ->orWhere('email','iLIKE','%'.$searchValue.'%')
                           ->orWhere('profile_externalid','iLIKE','%'.$searchValue.'%');
            })->get();
              
            $records = $sel[0];
            $totalRecordwithFilter = $records->allcount;
            
            ## buscador de datos
               /*$empQuery = DB::select(DB::raw("select jssu.* from jess_users jssu where ".$num." = ".$searchQuery." order by ".$columnName.
               " asc offset "."$row"." limit ".$rowperpage));*/

               $empQuery = DB::table('jess_users as jssu')->select(DB::raw('jssu.*'))
               ->where(function($query2) use ($searchValue)
                   {
                       $query2->where( 'name','iLIKE','%'.$searchValue.'%')
                              ->orWhere('username','iLIKE','%'.$searchValue.'%')
                              ->orWhere('email','iLIKE','%'.$searchValue.'%')
                              ->orWhere('profile_externalid','iLIKE','%'.$searchValue.'%');
               })->orderBy($columnName, 'asc')->skip($row)->take($rowperpage)->get();
               $data = array();

        }




        if($searchValue == '' && ($searchByGroups == '' || $searchByGroups == '0')){
           // $searchQuery = "1";

              ## Numero total de datos con filtro
                $sel = DB::table('jess_users')->select(DB::raw('count(*) as allcount'))->get();
                $records = $sel[0];
                $totalRecordwithFilter = $records->allcount;

               ## buscador de datos
                  //$empQuery = DB::select(DB::raw("select jssu.* from jess_users jssu where ".$num." = ".$searchQuery." order by ".$columnName." asc offset "."$row"." limit ".$rowperpage));
                  
                $empQuery = DB::table('jess_users as jssu')->select(DB::raw('jssu.*'))
                 ->orderBy($columnName, 'asc')->skip($row)->take($rowperpage)->get();
                  $data = array();

         }
        

     


        ## Numero total de datos sin filtro
        $sel = DB::select(DB::raw('select count(*) as allcount from jess_users jssu'));
        $records = $sel[0];
        $totalRecords = $records->allcount;
       
        ## Numero total de datos con filtro
       /* $sel = DB::select(DB::raw("select count(*) as allcount from jess_users jssu where ".$num." = ".$searchQuery));    
        $records = $sel[0];
        $totalRecordwithFilter = $records->allcount;*/
        
        
        ## buscador de datos
       /*$empQuery = DB::select(DB::raw("select jssu.* from jess_users jssu where ".$num." = ".$searchQuery." order by ".$columnName." asc offset "."$row"." limit ".$rowperpage));
        $data = array();*/

        foreach($empQuery as $row){
            $data[] = array(
                    "id" => $row->id,
                    "name" => $row->name,
                    "username"=>$row->username,
                    "email"=>$row->email,
                    "lastvisitdate"=>$row->lastvisitdate,
                    "block"=>$row->block
                );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );
   
        echo json_encode($response);
                
    }

}
