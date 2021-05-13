<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Reportes\ReporteIngreso\Controllers'), function() {
        
        Route::get('reporte-ingreso', 'ReporteIngresoController@index');
        Route::post('consultarIngresoPersona', 'ReporteIngresoController@consultarIngresoPersona')->name('consultarIngresoPersona');
        Route::post('descargarExcelReporteIngreso', 'ReporteIngresoController@descargarExcelReporteIngreso')->name('descargarExcelReporteIngreso');
    });

});