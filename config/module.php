<?php 
# config/module. , desde aquí se indicará a LARAVEL, cuales módulos pueden cargar. Para eso debes tener en cuenta el archivo: app/modules/ModulesServiceProvider.php. Este archivo hará referencia hasta esta ruta, para acceder a cada uno de los módulos y submodulos y así poder reconocer la ubicación de cada archivo y rutas.

return  [
    'modules' => [
       'Cliente',
       'Permisos' => [
           'Permisos',
           'PermisosMasivos',
           'PermisosUnitarios'
       ],
       
      ]
];
?>