<?php
/**
 * para restringir a una funcion de un controlador según el nivel de acceso que se tenga
 * colocar al principio de la funcion Session::acceso($nivelDeAccesoPermitido)
 * Tambien se puede usar en la vista para zonas concretas
 */
class Session
{
    public static function init(){
        session_start();
    }
    
    public static function exists($var){
        return isset($_SESSION[$var]); 
    }
    
    public static function destroy($vars=false){
        if ($vars){
            if (is_array($vars)){
                foreach ($vars as $key => $var) {
                    if (isset($_SESSION[$var])){
                        unset($_SESSION[$var]);
                    }
                }
            }else{
                if (isset($_SESSION[$vars])){
                    unset($_SESSION[$vars]);
                }
            }
        }else{
            session_destroy();
        }
    }
    
    public static function set($var, $valor){
        if (!empty($var)){
            if (!is_object($valor) && !is_array($valor)){
                $_SESSION[$var] = $valor;
            }else{
                $_SESSION[$var] = serialize($valor);
            }
        }
    }
    
    public static function get($var){
        if (isset($_SESSION[$var])){
            
            $varUnserialized = @unserialize($_SESSION[$var]);

            if ($varUnserialized === FALSE){                
                return $_SESSION[$var];
            }else{
                return $varUnserialized;
            }
        }            
    }
    
    public static function getAll(){
        return $_SESSION;
    }
    
    
    
    
    public static function setUser($usuario){
        self::set("user");
    }
    
    public static function getUser(){
        return self::get("user");
    }
    
    public static function getUserId(){  
        return self::get("user")? self::get("user")->id:null;
    }
    
    public static function setRol($rol){
        self::set("rol");
    }
    
    public static function getRol(){
        return self::get("rol");
    }
    
    public static function getRolId(){
        return self::get("user")? self::get("user")->rol:null;
    }
    
    public static function getRolName(){    
        return self::get("rol")? self::get("rol")->sys_nombre:null;
    }
    
    public static function getNivelAcceso(){
        return self::get("rol")? self::get("rol")->visibilidad:null;
    }
    
    
    public static function acceso($nivel="logueado"){
        $conAcceso = true;

        if (!Session::get('autentificado')){
            $conAcceso = false;
        }else if (!is_array($nivel)){
            Session::tiempo();
            
            if (Session::getLevel($nivel) < Session::getNivelAcceso()){
                $conAcceso = false;
            }            
        }else{
            if (count($nivel)){
                if (!in_array(self::getRolName(), $nivel)){
                    $conAcceso = false;
                }
            }else{
                $conAcceso = false;
            }
        }
        
        if (!$conAcceso){
            header('location:'.BASE_URL.'error/acceso/5050');
            exit;
        }  
    }
    
    
    /**
     * @todo revisar exception
     * @param type $nivel
     */
    public static function getLevel($nivel){
        /*$rol['cliente'] = 3;
        $rol['admin'] = 2;
        $rol['superadmin'] = 1;*/
        $rol = Session::get("nivelesAcceso");
        
        if (!array_key_exists($nivel, $rol)){
            return 100000;
        }
        else{
            return $rol[$nivel];
        }
    }
    
    /**
     * @todo revisar excepcion
     */
    public static function tiempo(){
        if (!Session::get('tiempo') || !defined('SESSION_TIME')){
            throw new Exception ('No se ha definido el tiempo de sesion');
        }
        
        //Tiempo de sesión indefinido
        if (SESSION_TIME==0){
            return;
        }
        
        if (time() - Session::get('tiempo') > (SESSION_TIME*60)){
            Session::destroy();
            header('location:'.BASE_URL.'error/acceso/8080');
        }else{
            Session::set('tiempo',time());
        }
    }
}

