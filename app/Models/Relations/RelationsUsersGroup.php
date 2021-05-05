<?php
namespace App\Models\Relations;
use App\Models\Usergroup\Usergroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationsUsersGroup  extends Model
{

    protected $table = 'jess_user_usergroup_map';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id', 'user_id', 'usergroup_id'
    ];
    
    public function getNombre($id){
        $query = DB::table('jess_usergroups')
                        ->select("title")            
                        ->where("id",$id)
                        ->get();
        return $query[0]->title;
    }
}
