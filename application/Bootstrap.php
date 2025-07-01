<?php
/**
 * 
 * @todo Excepcion de metodo run
 */

class Bootstrap
{
    public static function run(Request $peticion){
        $controllerName = $peticion->getControlador().'Controller';
        $rutaControlador = ROOT.DIR_CUSTOM.DS.PRODUCTO.DS.DIR_CONTROLLERS.DS.$controllerName.'.php';
        if (!is_readable($rutaControlador)){
            $controllerName = $peticion->getControlador().'BaseController';
            $rutaControlador = ROOT.DIR_CORE.DS.DIR_CONTROLLERS.DS.$controllerName.'.php';
        }

        $metodo = $peticion->getMetodo();
        $args = $peticion->getArgs();

        //Controlamos que no se intenta acceder a un archivo que no existe
        //por error, como una imagen, etc..          
        if (!$peticion->archivoNoEncontrado()){
            if (is_readable($rutaControlador)){
                require_once($rutaControlador);

                $controller = new $controllerName;

                if (is_callable(array($controller, $metodo))){
                    $metodo = $peticion->getMetodo();
                }else{
                    $metodo = 'index';
                }

                if (isset($args)){
                    $f = new ReflectionMethod($controller, $metodo);
                    $paramNames = [];
                    foreach ($f->getParameters() as $param) {
                        $paramNames[$param->name] = $param->isDefaultValueAvailable()? $param->getDefaultValue():null;
                    }
                    $args = array_merge($paramNames, $args);

                    call_user_func_array(array($controller, $metodo), $args);
                }else{
                    call_user_func(array($controller, $metodo));
                }
            }else{
                throw new Exception('No encontrado');
            }
        }
    }
}

