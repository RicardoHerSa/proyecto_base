<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitanteTemporal\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('registro-visitante-temporal', 'RegistroVisitanteTemporalController@index');
        Route::post('consultaVisitanteTemporal', 'RegistroVisitanteTemporalController@consultaVisitanteTemporal')->name('consultaVisitanteTemporal');
        Route::post('registrarCodigo', 'RegistroVisitanteTemporalController@registrarCodigo')->name('registrarCodigo');

    });

});