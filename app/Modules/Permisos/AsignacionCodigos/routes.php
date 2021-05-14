<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\AsignacionCodigos\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('asigna-codigos', 'AsignacionCodigosController@index');
        Route::post('consultaVisitante', 'AsignacionCodigosController@consultaVisitante')->name('consultaVisitante');
        Route::get('consultaAlClickearTabla', 'AsignacionCodigosController@consultarClickTabla');
        Route::post('registrarCodigos','AsignacionCodigosController@registrarCodigo')->name('registrarCodigos');
        Route::get('tomarFoto/{cedula}', 'AsignacionCodigosController@retornarVistaFoto');
        Route::post('guardarFotoAsignaCodigos', 'AsignacionCodigosController@guardarFoto')->name('guardarFotoAsignaCodigos');

    });

});