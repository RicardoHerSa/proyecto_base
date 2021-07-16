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
        Route::post('consultasedesemp', 'RegistroVisitanteController@consultaSedes')->name('consulta.sedesd')->middleware('authorization');
        Route::post('empresavisitar', 'RegistroVisitanteController@empresaVisitar')->name('empresavisitar')->middleware('authorization');

        Route::get('actualizarSedes/{sedes}', 'RegistroVisitanteController@actualizarSedes')->name('actualizarSedes')->middleware('authorization');;

        //Pantalla para ver solicitudes creadas por el usuario
        Route::get('missolicitudes', 'RegistroVisitanteController@misSolicitudes')->middleware('authorization');;
        Route::get('detallesdesolicitud/{idSolicitud}/{tipoIngreso}/{sedeId}/{estado}', 'RegistroVisitanteController@verSolicitud')->middleware('authorization');;
        Route::get('consultrmissolicitudes', 'RegistroVisitanteController@consultarMisSolicitudes')->name('consultar.missolicitudes')->middleware('authorization');;
        Route::post('asignarvisto', 'RegistroVisitanteController@asignarVisto')->name('asignar.visto')->middleware('authorization');;
        Route::post('filtrarestado', 'RegistroVisitanteController@filtrarEstado')->name('filtrar.estado')->middleware('authorization');;

    });
     

});