<?php

namespace App\Models\ResetPassword;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'jess_password_resets';

    protected $primaryKey = 'id';

    protected $fillable = [
        'email', 'token'
    ];
}
