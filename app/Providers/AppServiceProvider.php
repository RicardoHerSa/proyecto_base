<?php

namespace App\Providers;

use App\Models\Menu\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        view()->composer('layouts.app', function($view) {
            $view->with('menus', Menu::menus());
        });
        
    //Mostrar vista desde otro directorio
    $this->loadViewsFrom(__DIR__.'../../Modules/UsuarioP/Views', 'UsuarioP');

        }
}
