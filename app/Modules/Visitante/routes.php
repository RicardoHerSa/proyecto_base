<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Visitante\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('permisos-unitarios', 'VisitanteController@permisosUnitarios')->name('permisosUnitarios');
        Route::post('consultarCedula', 'VisitanteController@consultarCedulaVisitante')->name('consultarCedula');
        Route::get('actualizarVisitante', 'VisitanteController@actualizarCliente')->name('actualizarVisitante');

    });

});