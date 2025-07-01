<?php

spl_autoload_register("autoloadCore");
spl_autoload_register("autoloadLibs");


function autoloadCore($class){
    if (file_exists(ROOT.'application'.DS.$class.'.php')){
        include_once ROOT.'application'.DS.$class.'.php';
    }
}
/**
 * @todo mejorar autoloadLibs según necesidades
 * @param type $class
 */
function autoloadLibs($class){
    
    if (file_exists(ROOT.DIR_LIBS.DS.$class.'.php')){
        include_once ROOT.DIR_LIBS.DS.$class.'.php';
    }
    if (file_exists(ROOT.DIR_LIBS.DS.$class.DS.$class.'.php')){
        include_once ROOT.DIR_LIBS.DS.$class.DS.$class.'.php';
    }
    
    if (file_exists(ROOT.DIR_CORE.DS.DIR_CONTROLLERS.DS.$class.'.php')){
        include_once ROOT.DIR_CORE.DS.DIR_CONTROLLERS.DS.$class.'.php';
    }else if (file_exists(ROOT.DS.DIR_CUSTOM.DS.PRODUCTO.DS.DIR_CONTROLLERS.DS.$class.'.php')){
        include_once ROOT.DS.DIR_CUSTOM.DS.PRODUCTO.DS.DIR_CONTROLLERS.DS.$class.'.php';
    }
    
    if (strpos($class, "Model")!==false){
        $className = str_replace("Model","",$class);
        
        $rutaCustom = ROOT.DS.DIR_CUSTOM.DS.PRODUCTO.DS.DIR_MODELS.DS.$className.DS.'_'.$className.'Model.php';
        $rutaBase = ROOT.DIR_CORE.DS.DIR_MODELS.DS.$className.DS.'_'.$className.'BaseModel.php';
        
        $classDirecta = str_replace("Base","",$class);
        $rutaDirectaCustom = ROOT.DS.DIR_CUSTOM.DS.PRODUCTO.DS.DIR_MODELS.DS.str_replace("Model","",$class).DS.'_'.$class.'.php';
        $rutaDirectaBase = ROOT.DIR_CORE.DS.DIR_MODELS.DS.str_replace("BaseModel","",$class).DS.'_'.$class.'.php';
        
        if (file_exists($rutaCustom)){
            include_once $rutaCustom;
        }else if (file_exists($rutaBase)){
            include_once $rutaBase;
        }
        
        if (file_exists($rutaDirectaCustom)){
            include_once $rutaDirectaCustom;
        }else if (file_exists($rutaDirectaBase)){
            include_once $rutaDirectaBase;
        }
    }
}

