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
       'ubicaciones' => [
           'UbicacionesHorario',
           'Porteria',
           'Horario'
       ],
       'CargueMasivo',
       'Reportes'
      ]
];
?>