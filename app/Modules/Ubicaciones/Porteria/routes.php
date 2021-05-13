<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Ubicaciones\Porteria\Controllers'), function() {
        
        //Route::resource('/', 'PruebaDosController'); 
        Route::get('porteria', 'PorteriaController@index');
        Route::post('actualizarNodo', 'PorteriaController@actualizarNodo')->name('actualizarNodo');
        Route::post('registrarNodo', 'PorteriaController@registrarNodo')->name('registrarNodo');
   

    });

});