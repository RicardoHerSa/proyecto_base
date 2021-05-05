<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Usergroup\Usergroup;
use App\Models\Cess\Cess;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'jess_users';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'block', 'profile_orgcountry',
        'profile_externalid', 'profile_ordinal', 'gestor_externo','lastresettime',
        'cl_org_id' , 'cl_cod_pais' , 'cl_idioma' ,'cl_cod_pais_cliente',
        'cl_cupo_sugerido',
        'cl_terminos_pago',
        'tipo_moneda',
        'id_comercial',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function usergroups() 
    {
        return $this->belongsToMany(Usergroup::class, 'jess_user_usergroup_map');
    }

    public function userCess() 
    {
        return $this->hasOne(Cess::class, 'cess_users');
    }

}