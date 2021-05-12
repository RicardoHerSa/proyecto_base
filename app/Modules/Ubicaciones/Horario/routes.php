<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Ubicaciones\Horario\Controllers'), function() {
        
        Route::get('horarios', 'HorarioController@index');
        Route::post('gestionHorario', 'HorarioController@gestionarHorario')->name('gestionHorario');
      

    });

});