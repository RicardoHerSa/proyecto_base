<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitante\Controllers'), function() {
        
        Route::get('registro-visitante', 'RegistroVisitanteController@index');
        Route::post('consultarHora', 'RegistroVisitanteController@consultarHora')->name('consultarHora');
        Route::post('registraranexos', 'RegistroVisitanteController@registrarVisitante')->name('registraranexos');
    });

});