<?php
namespace App\Models\Relations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationMenuViewlevel  extends Model
{
    protected $table = 'jess_menu_viewlevel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'id';
    
    protected $fillable = [ 'id', 'menu_id', 'viewlevel_id' ];

    public function getLevel($id){
        $query = DB::table('jess_viewlevels')
                        ->select("title")            
                        ->where("id",$id)
                        ->get();
        return $query[0]->title;
    }
}
