
## Carvajal Portal Base

<p>
    Proyecto base para futuros desarrollos con la versión de laravel: 8.35.1.
    Tener en cuenta los siguientes items:
</p>
<ul>
<li>Los scripts de las tablas principales Jees_users, se encuentran en la ruta: database/SCRIPTS TABLAS JESS. Crear primero las secuencias, y posteriormente las tablas. </li>
<li>Los módulos que contenga el portal, se crean en la ruta: app/modules. Para esto se debe revisar el archivo: ModulesServiceProvider.php ubicado en esta misma raíz, así como los archivos: config/module.php y el array de providers ubicado en: config/app.php</li>
<li>El archivo de entorno (.env) , ha sido renombrado como: .proyectoBase11052022$ , en la raíz del proyecto, con el objetivo de generar un filtro más de seguridad. Esta configuración de nombre de archivo, está ubicada en: bootstrap/app.php , loadEnvironmentFrom() </li>
</ul>

## ---
