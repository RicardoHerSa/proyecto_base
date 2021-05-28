<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\CargueMasivo\Controllers'), function() {
            
        Route::get('cargue-masivo', 'CargueMasivoController@index')->middleware('authorization'); 
        Route::post('cargarColborador', 'CargueMasivoController@cargarColaboradores')->name('cargarColborador');
    });

});