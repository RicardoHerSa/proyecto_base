<?php

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Ubicaciones\Empresas\Controllers'), function() {
       //Gestor de Empresas
        Route::resource('Empresas', 'EmpresasController')->middleware('authorization');
        Route::post('actualizarestadoempresa', 'EmpresasController@actualizarEstado')->name('actual.estado');
        Route::post('consultarnombreempresa', 'EmpresasController@consultarNombreEmpresa')->name('consult.empresa');
        Route::post('consutarsedesempresa', 'EmpresasController@consultarSedesEmpresa')->name('consult.sedes');
        Route::post('actualizarsedesempresa', 'EmpresasController@actualizarSedesEmpresa')->name('actualiza.sedes');
        Route::post('registraempresa', 'EmpresasController@registrarEmpresa')->name('registra.empresa');
        Route::post('eliminarsede', 'EmpresasController@eliminarSede')->name('elimina.sede');
        Route::post('eliminarempresa', 'EmpresasController@eliminarEmpresa')->name('eliminar.empresa');
        Route::get('consultarempresas', 'EmpresasController@consultarEmpresas')->name('consultar.empresas');

    });

});