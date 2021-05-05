<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\adobe;

/**
 *
 * @author David Guanga <david.guanga@carvajal.com>
 *  
 */

class Utilidad
{
  
   public static  function AllUserRol($rol)
    {
        $query = "select g.title from
        jess_users u ,   jess_user_usergroup_map ug   , jess_usergroups g
        where u.id = ug.user_id
        and ug.usergroup_id = g.id
        and g.parent_id = '1'
        and u.id =".auth()->user()->id ;

       
        $RolUser = DB::select($query);
        
        if (isset($RolUser[0]->title)){

              foreach ( $RolUser as $i) {
                    
                    if(in_array(  $i->title , $rol)){

                            return true;
                    }
                        
                }      

            }
            return false;
        
  
    }

    
   public static function UserRol(){
      
        $query = "select g.title from
        jess_users u ,   jess_user_usergroup_map ug   , jess_usergroups g
        where u.id = ug.user_id
        and ug.usergroup_id = g.id
        and g.parent_id = '1'
        and u.id =". auth()->user()->id . ' limit 1 ';

        $RolUser = DB::select($query);
        
        if (isset($RolUser[0]->title)){

           return $RolUser[0]->title;
        }

        abort(403);

    }
    
    

    public static  function AllUserRolView($rol){

        $query = "select g.title from
        jess_users u ,   jess_user_usergroup_map ug   , jess_usergroups g
        where u.id = ug.user_id
        and ug.usergroup_id = g.id
        and g.parent_id = '1'
        and u.id =".auth()->user()->id ;

        $RolUser = DB::select($query);
        $RolUser = DB::select($query);
        
        if (isset($RolUser[0]->title)){

              foreach ( $RolUser as $i) {
                    
                    if(in_array(  $i->title , $rol)){

                            return true;
                    }
                        
                }      

            }
            
        return false;

    }

     
}