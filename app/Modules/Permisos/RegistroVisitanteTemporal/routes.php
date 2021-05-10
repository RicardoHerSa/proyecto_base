<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitanteTemporal\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('registro-visitante-temporal', 'RegistroVisitanteTemporalController@index');
        Route::post('consultaVisitante', 'RegistroVisitanteTemporalController@consultaVisitante')->name('consultaVisitante');
        Route::get('consultaAlClickearTabla', 'RegistroVisitanteTemporalController@consultarClickTabla');
        Route::post('registrarCodigo', 'RegistroVisitanteTemporalController@registrarCodigo')->name('registrarCodigo');

    });

});