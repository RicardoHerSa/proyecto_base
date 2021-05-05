<?php

namespace App\Models\Cess;

use Illuminate\Database\Eloquent\Model;

class Cess extends Model
{
    protected $table = 'cess_users';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cess_id_user', 'cess_id_org', 'cess_id_ext_per', 'cess_or_ext_per', 'cess_id_company', 'cess_dt_start', 'cess_dt_end', 'cess_username', 'block'
    ];

}