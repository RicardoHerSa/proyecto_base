<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\AsignacionCodigos\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('asigna-codigos', 'AsignacionCodigosController@index');
        Route::post('consultaVisitante', 'AsignacionCodigosController@consultaVisitante')->name('consultaVisitante');
        Route::get('consultaAlClickearTabla', 'AsignacionCodigosController@consultarClickTabla');
        Route::post('registrarCodigo', 'AsignacionCodigosController@registrarCodigo')->name('registrarCodigo');

    });

});