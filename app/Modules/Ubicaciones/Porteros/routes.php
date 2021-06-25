<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Ubicaciones\Porteros\Controllers'), function() {
      //Gestor de Porteros
      Route::resource('Porteros', 'PorterosController')->middleware('authorization');
      Route::post('consultarnombreusuario', 'PorterosController@consultarNombreUsuario')->name('consult.usuario');
      Route::post('actualizarestadoportero', 'PorterosController@actualizarEstado')->name('actual.portero');
      Route::post('eliminarportero', 'PorterosController@eliminarPortero')->name('eliminar.portero');
      Route::get('consultarporteros', 'PorterosController@consultarPorteros')->name('consultar.porteros');
      Route::get('asociarporterias', 'PorterosController@asociarPorterias')->name('asociar.porterias');
      Route::post('porteriasdisponibles', 'PorterosController@porteriasDisponibles')->name('porterias.disponibles');
      Route::post('guardarasociacion', 'PorterosController@guardarAsociacion')->name('guardar.asociacion');
      Route::post('eliminarporteria', 'PorterosController@eliminarPorteria')->name('eliminar.porteria');
      Route::post('recargaporterias', 'PorterosController@recargaPorterias')->name('recarga.porterias');

    });

});