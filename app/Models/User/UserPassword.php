<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserPassword extends Model
{
    protected $table = 'jess_password';

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'id_user', 'password_user' ];

}