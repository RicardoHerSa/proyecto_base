<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitante\Controllers'), function() {
        
        Route::get('registro-visitante', 'RegistroVisitanteController@index');
        Route::post('consultarHora', 'RegistroVisitanteController@consultarHora')->name('consultarHora');
        Route::post('registraranexos', 'RegistroVisitanteController@registrarVisitante')->name('registraranexos');
        
        //Firma de URL
        Route::get('solicitud/{solicitud}', 'RegistroVisitanteController@subscribe')->name('event.subscribe');
        Route::get('solicitud/link', 'RegistroVisitanteController@getLinkSubscribe')->name('event.getLinkSubscribe');

        Route::post('validarSolicitud', 'RegistroVisitanteController@validarSolicitud')->name('validarSolicitud');
    });

});