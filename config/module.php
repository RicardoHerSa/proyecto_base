<?php 
# config/module.php

return  [
    'modules' => [
       'Cliente',
       'Permisos' => [
           'Permisos',
           'PermisosMasivos',
           'PermisosUnitarios',
           'AsignacionCodigos',
           'RegistroVisitante',
           'RegistroVisitanteTemporal'
       ],
       'Ubicaciones' => [
           'Ubicaciones',
           'Porteria',
           'Horario',
           'Empresas',
           'Porteros'
       ],
       'CargueMasivo',
       'Reportes' => [
            'Reportes',
            'ReporteIngreso',
            'ReporteParqueadero'
        ],
        'IngresoVisitante'
      ]
];
?>