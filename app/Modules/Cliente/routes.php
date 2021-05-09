<?php


Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Cliente\Controllers'), function() {
            
        Route::resource('/Cliente', 'ClienteController')->middleware('authorization'); 
    });

});