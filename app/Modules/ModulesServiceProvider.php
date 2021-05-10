<?php namespace app\Modules;
 
/**
* ServiceProvider
*
* The service provider for the modules. After being registered
* it will make sure that each of the modules are properly loaded
* i.e. with their routes, views etc.
*
* @author 
* @package App\Modules
*/

use Illuminate\Support\Facades\Log;
class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Will make sure that the required modules have been fully loaded
     * @return void
     */
    public function boot()
    {
        // For each of the registered modules, include their routes and Views
        $modules = config("module.modules");
        //echo "<pre>";
        //print_r($modules);
        foreach($modules as $module) {
            if(is_array($module)){
                
                for($i = 1; $i < count($module); $i++){
                    //echo $module[$i]." ".__DIR__.'/'.$module[0].'/'.$module[$i].'/routes.php'."<br>";
                    // Load the routes for each of the modules
                    if(file_exists(__DIR__.'/'.$module[0].'/'.$module[$i].'/routes.php')) {
                        include __DIR__.'/'.$module[0].'/'.$module[$i].'/routes.php';
                    }
                     // Load the views
                    if(is_dir(__DIR__.'/'.$module[0].'/'.$module[$i].'/Views')) {
                        $this->loadViewsFrom(__DIR__.'/'.$module[0].'/'.$module[$i].'/Views', $module[0]);
                    }
                    
                    //Load the lng
                    if(is_dir(__DIR__.'/'.$module[0].'/'.$module[$i].'/lang'.$module[$i])) {
                        $this->loadTranslationsFrom(__DIR__.'/'.$module[0].'/'.$module[$i].'/lang'.$module[$i], 'lang'.$module[$i] );
                     }
                }
            }else{

               // echo $module." ".__DIR__.'/'.$module.'/routes.php'."<br>";
                if(file_exists(__DIR__.'/'.$module.'/routes.php')) {
                    include __DIR__.'/'.$module.'/routes.php';
                }
    
                // Load the views
                if(is_dir(__DIR__.'/'.$module.'/Views')) {
                    $this->loadViewsFrom(__DIR__.'/'.$module.'/Views', $module);
                }
    
                 // Load the lang
               
               //echo __DIR__.'/'.$module.'/lang'."<br>";
                 if(is_dir(__DIR__.'/'.$module.'/lang'.$module)) {
                   
                    $this->loadTranslationsFrom(__DIR__.'/'.$module.'/lang'.$module, 'lang'.$module);
                 }
            }
        }
    }

    public function register() {
      
       

    }

}
?>