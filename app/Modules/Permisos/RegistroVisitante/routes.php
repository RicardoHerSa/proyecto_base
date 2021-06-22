<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
            use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
            use App\Jobs\registrarPermisos;

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitante\Controllers'), function() {
        
        Route::get('registro-visitante', 'RegistroVisitanteController@index')->middleware('authorization');
        Route::post('consultarHora', 'RegistroVisitanteController@consultarHora')->name('consultarHora')->middleware('authorization');
        Route::post('registraranexos', 'RegistroVisitanteController@registrarVisitante')->name('registraranexos')->middleware('authorization');
        
        //Firma de URL
        Route::get('solicitud/{solicitud}/{ingreso}/{sede}', 'RegistroVisitanteController@subscribe')->name('event.subscribe')->middleware('authorization');
        Route::get('solicitud/link', 'RegistroVisitanteController@getLinkSubscribe')->name('event.getLinkSubscribe')->middleware('authorization');

        Route::post('validarSolicitud', 'RegistroVisitanteController@validarSolicitud')->name('validarSolicitud')->middleware('authorization');
        Route::post('consultasedes', 'RegistroVisitanteController@consultaSedes')->name('consultasedes')->middleware('authorization');
        Route::post('empresavisitar', 'RegistroVisitanteController@empresaVisitar')->name('empresavisitar')->middleware('authorization');

        Route::get('actualizarSedes/{sedes}', 'RegistroVisitanteController@actualizarSedes')->name('actualizarSedes');

    });
     

});