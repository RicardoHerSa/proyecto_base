<?php

namespace App\Models\Usergroup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

class Usergroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jess_usergroups';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'lft', 'rgt', 'title', 'companycessid', 'level'];

    public function getQueryUsergroupTitle($parent,$id){

            if($parent == 0 || $parent == null){
                $parent = $id;
            }
            $query = Usergroup::select('title')
                        ->where("id",$parent)
                        ->get();

          return $query[0]->title;
    }

    protected function consultaGruposUsers()
    {
        return DB::select('SELECT  u.id, string_agg(distinct ug.title, ', ') AS nombre 
                            FROM jess_usergroups ug 
                            INNER JOIN jess_user_usergroup_map uu ON uu.usergroup_id = ug.id 
                            INNER JOIN jess_users u ON uu.user_id = u.id 
                            WHERE u.id IN (SELECT jess_users.id FROM jess_users) 
                            GROUP BY u.id;');
    }

    public function subcategory(){
        return $this->hasMany('App\Models\Usergroup\Usergroup', 'parent_id');
    }

    public function recursiveParent($id, $var){
        $query = DB::table('jess_usergroups')
                        ->select("parent_id")            
                        ->where("id",$id)
                        ->get();
        
        if($query[0]->parent_id != 0 || $query[0]->parent_id != null){
            $var ++;
            return $this->recursiveParent($query[0]->parent_id,$var);
        }else{
            return $var;
        }
    }
    public function subParent($id){
        $var = 1;
        return $this->recursiveParent($id, $var);
    }
    
    public function getNameParent($id){
        if($id == 0){
            $id = 1;
        }
        $query = DB::table('jess_usergroups')
                        ->select("title")            
                        ->where("id",$id)
                        ->get();
        return $query[0]->title;
    }

}
