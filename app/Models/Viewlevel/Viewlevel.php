<?php

namespace App\Models\Viewlevel;

use Illuminate\Database\Eloquent\Model;
use App\Models\Usergroup\Usergroup;

class Viewlevel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jess_viewlevels';

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
    protected $fillable = ['title', 'ordering', 'rules'];

    public function usergroups()
    {
        return $this->belongsToMany(Usergroup::class, 'jess_usergroup_viewlevel');
    }
    
}
