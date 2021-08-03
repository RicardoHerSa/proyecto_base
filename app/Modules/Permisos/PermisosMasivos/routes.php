<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\PermisosMasivos\Controllers'), function() {
        
        Route::get('permisos-masivos', 'PermisosMasivosController@index');
        Route::post('consultarEmpresas', 'PermisosMasivosController@consultarEmpresas')->name('consultarEmpresas');
        Route::post('consultarPermisosMasivos', 'PermisosMasivosController@consultarPermisosMasivos')->name('consultarPermisosMasivos');
        Route::post('insertarRegistrosMasivos', 'PermisosMasivosController@insertarRegistrosMasivos')->name('insertarRegistrosMasivos');
        Route::get('carga-usuario', 'PermisosMasivosController@run')->name('carga-usuario');
    });

});