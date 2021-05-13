<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Reportes\ReporteParqueadero\Controllers'), function() {
        
        Route::get('reporte-parqueadero', 'ReporteParqueaderoController@index');
        Route::post('consultarReporteParqueadero', 'ReporteParqueaderoController@consultarReporteParqueadero')->name('consultarReporteParqueadero');
       
    });

});