<?php
/**
 * @todo añadir multiidioma
 */
class Request
{
    private $_controlador;
    private $_metodo;
    private $_argumentos;

    private $_archivoNoEncontrado = false;

    public function __construct(){
          $url = null;
        if (isset($_GET['url'])){
            $url = filter_input(INPUT_GET, 'url', FILTER_UNSAFE_RAW);
            $url = urldecode($url);
            //$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
        }else{
            $tmp = getopt("", ["url:"]);
            $url = isset($tmp["url"]) ? $tmp["url"] : null;
        }

        $url = $url ? explode("/", $url) : [];
        if (!empty($url)){
            $url = array_filter($url);
            $this->_controlador = array_shift($url);
            $this->_metodo = array_shift($url);
            if ($this->_metodo && strpos($this->_metodo, PA)!==false){
                array_push($url,$this->_metodo);
                $this->_metodo = DEFAULT_METHOD;
            }

            /*foreach ($url as $arg) {

                //$argArr = explode(PA,$arg);
                //if (sizeof($argArr)==2){
                    //$this->_argumentos[$argArr[0]] = str_replace("|||", "/", $argArr[1]);
                    //$argArr[0] = str_replace("?", "", $argArr[0]);
                //}
                //Controlamos que no se intenta acceder a un archivo que no existe
                //por error, como una imagen, etc..
                if (implode(",",$argArr)==$arg && (substr($arg,  strlen($arg)-4,1)=="." || substr($arg,  strlen($arg)-5,1))=="."){
                    $this->_archivoNoEncontrado = true;
                }
            }
            $url[0] = substr($url[0],0,1)=="?"? substr($url[0],1):$url[0];
            $this->_argumentos = $url;*/
            $this->_argumentos = array_merge($_GET,$_POST);
            unset($this->_argumentos["url"]);
        }

        if (!$this->_controlador){
            $this->_controlador = DEFAULT_CONTROLLER;
        }

        if (!$this->_metodo){
            $this->_metodo = 'index';
        }

        if (!isset($this->_argumentos)){
            $this->_argumentos = array();
        }

       /* echo $this->_controlador;
        echo "</br>";
        echo $this->_metodo;
        echo "</br>";
        echo implode(", ",$this->_argumentos);
        echo "</br>";*/
    }

    public function getControlador(){
        return $this->_controlador;
    }

    public function getMetodo(){
        return $this->_metodo;
    }

    public function getArgs(){
        return $this->_argumentos;
    }

    public function archivoNoEncontrado(){
        return $this->_archivoNoEncontrado;
    }

}

