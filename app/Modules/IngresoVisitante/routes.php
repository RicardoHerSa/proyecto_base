<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\IngresoVisitante\Controllers'), function() {
        
        Route::get('ingreso-visitante', 'IngresoController@index');
        Route::post('consultarRegistroIngreso', 'IngresoController@consultarRegistroIngreso')->name('consultarRegistroIngreso');

    });

});