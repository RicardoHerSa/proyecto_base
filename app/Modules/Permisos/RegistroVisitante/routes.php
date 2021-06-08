<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
            use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Route::group(['middleware' => 'web'], function () { 
     Route::group(array('namespace' => 'App\Modules\Permisos\RegistroVisitante\Controllers'), function() {
        
        Route::get('registro-visitante', 'RegistroVisitanteController@index')->middleware('authorization');
        Route::post('consultarHora', 'RegistroVisitanteController@consultarHora')->name('consultarHora')->middleware('authorization');
        Route::post('registraranexos', 'RegistroVisitanteController@registrarVisitante')->name('registraranexos')->middleware('authorization');
        
        //Firma de URL
        Route::get('solicitud/{solicitud}/{ingreso}/{sede}', 'RegistroVisitanteController@subscribe')->name('event.subscribe')->middleware('authorization');
        Route::get('solicitud/link', 'RegistroVisitanteController@getLinkSubscribe')->name('event.getLinkSubscribe')->middleware('authorization');

        Route::post('validarSolicitud', 'RegistroVisitanteController@validarSolicitud')->name('validarSolicitud')->middleware('authorization');

        Route::get('excel', function () {
            
           require 'C:\xampp\htdocs\sica\vendor\autoload.php';

           $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
           $reader->setReadDataOnly(TRUE);
           $spreadsheet = $reader->load('C:xampp\htdocs\sica\public\hola.xlsx');
           
           $worksheet = $spreadsheet->getActiveSheet();
           $i = 1;
           $arrayPerson = array();
           $arraynom = array();
           foreach ($worksheet->getRowIterator() as $row) {
               
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(TRUE);
                $j = 0;
                $array = array();
                foreach ($cellIterator as $cell) {
                    if($i != 1){
                        $array[$j] = $cell->getValue();
                        if($j%2==0){
                            $arrayPerson[] = array('iden'=>$array[$j]);
                        }else{
                            $arraynom[] = array('nom'=>$array[$j]);
                        }
                       
                        echo $cell->getValue() .PHP_EOL;
                    }
                    $j++;
                }
                $i++;
           }
           echo "<pre>";
           print_r($arrayPerson);
           print_r($arraynom);
           
           for ($i=0; $i < count($arrayPerson) ; $i++) { 
                echo $arrayPerson[$i]['iden']." ".$arraynom[$i]['nom']."<br>";
           }
        });
    });

});