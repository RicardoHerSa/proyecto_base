<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('Autorizado', function($user){
            session_start(); 
            if($user->lastresettime == null){
                return $user->block == 1;
            }else{
                $_SESSION['user_laravel'] = $user->id;
                return $user->block == 0;
            }
            
        });

        $gate->define('AutorizadoSidebar', function($user){
            if($user->lastresettime == null){
                return $user->block == 1;
            }else{
                $_SESSION['user_laravel'] = $user->id;
                return $user->block == 0;
            }
            
        });

        $gate->define('No_Autorizado', function($user){
            return $user->block == 1;
        });

    }
}
