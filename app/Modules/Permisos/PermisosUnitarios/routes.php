<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\PermisosUnitarios\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('permisos-unitarios', 'PermisosUnitariosController@permisosUnitarios')->name('permisosUnitarios');
        Route::post('consultarCedula', 'PermisosUnitariosController@consultarCedulaVisitante')->name('consultarCedula');
        Route::post('actualizarVisitante', 'PermisosUnitariosController@actualizarCliente')->name('actualizarVisitante');

    });

});