<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use  App\Services\Utilidad ;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
      
        if (auth()->user()->profile_externalid == 'MANAGER'){
            
            $userAt = DB::table('jess_users')
            ->select(DB::raw('count(*)'))
            ->where('block','0')
            ->get();

            $userAtlda = DB::table('jess_users')
            ->select(DB::raw('count(*)'))
            ->where('block','0')
            ->where('usuario_lda','IS_LDA')
            ->get();

            $userAtNolda = DB::table('jess_users')
            ->select(DB::raw('count(*)'))
            ->where('block','0')
            ->whereNull('usuario_lda')
            ->get();
             

            $UsuariosxGEmpresa = DB::select(DB::raw("select 
            COUNT (ju.id) total  , upper( g.title) grupo
            from jess_users ju , jess_usergroups g , jess_user_usergroup_map gm
            where ju.block  =0 
            and gm.user_id = ju.id 
            and gm.usergroup_id = g.id
            group by  g.title 
            order by 1"));
            
           

             $usuariosAct = $userAt[0]->count;
             $usuariosActlda = $userAtlda[0]->count;
             $usuariosActNolda = $userAtNolda[0]->count;
            return view('homemanager',compact('usuariosAct','usuariosActlda','usuariosActNolda','UsuariosxGEmpresa'));


        }else{
                 
           return view('inicio');
           
        }
    }
}

