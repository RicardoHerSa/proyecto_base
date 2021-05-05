<?php
namespace App\Models\Relations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationsGroupViewlevel  extends Model
{
    protected $table = 'jess_usergroup_viewlevel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id', 'viewlevel_id', 'usergroup_id'
    ];

    public function getNombre($id){
        $query = DB::table('jess_usergroups')
                        ->select("title")            
                        ->where("id",$id)
                        ->get();
        return $query[0]->title;
    }
}
