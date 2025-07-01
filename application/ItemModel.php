<?php

/**
 * Clase de acceso a datos para items - Active object
 * 
 * Clase que implementa Active object para la abstracción del acceso a bases de datos de un item.
 * @author Orion ISC
 * @version v1.0
 * @todo Revisar entrega en formatos json y mobile
 * @todo Uso de logger
 * @todo Formatos para todos los tipos contemplados
 * @todo Refactorizar y revisar formatoMysql() y formatoCliente(). Incluir uso de formatoCliente()
 * @todo Revisar multiidioma
 */

class ItemModel implements Iterator{

    
    public $atributos;
    public $vista;
    public $clase;

    protected $i18n;
    protected $idioma;
    protected $log;

    protected $_indexAtributos;
    
    protected $_modoVista;
    /*
     * Campos de las tablas o propiedades
     */

    protected $propTablas;
    
    const MODO_VISTA_LISTA = Model::MODO_VISTA_LISTA;
    const MODO_VISTA_EDITABLE = Model::MODO_VISTA_EDITABLE;
    const MODO_VISTA_RAW = Model::MODO_VISTA_RAW;


    function __construct($clase,$logger = null,$i18n = false) {
        if (!is_null($logger)) {
            $this->configLogger($logger);
        }
        $this->setClass($clase);
        $this->setI18n($i18n);
        $this->init();
    }

    /* *************************************************
     *  Inicializacion
     * ************************************************* */

    private function init() {
        $this->vista = null;
        $this->atributos = array();
        $this->propTablas = array();

        $this->setIdioma(null);
        $this->setModoVista();
    }

    function configLogger($logger) {
        $this->log = $logger;
    }


    /* *************************************************
     *  Metodos y funciones varios
     * ************************************************* */
    public function setClass($clase) {
        $this->clase = $clase;
    }

    public function getClass() {
        return $this->clase;
    }

    public function setI18n($i18n) {
        $this->i18n = $i18n;
    }

    public function getI18n() {
        return $this->i18n;
    }

    public function getIdioma() {
        return $this->idioma;
    }

    public function setIdioma($idioma) {
        $this->idioma = $idioma;
    }
    
    /* *************************************************
     *  Implementación de Iterator
     * ************************************************* */

    public function rewind() {
        $this->_indexAtributos = 0;
    }

    public function current() {
        $attr = array_keys($this->atributos)[$this->_indexAtributos];
        return $this->$attr;
    }

    public function key() {
        //return $this->_indiceItemsIndex;
        return array_keys($this->atributos)[$this->_indexAtributos];
    }

    public function next() {
        ++$this->_indexAtributos;
    }

    public function valid() {
        return isset(array_keys($this->atributos)[$this->_indexAtributos]);
    }

    /* *************************************************
     *  Vistas
     * ************************************************* */

    public function setVista($vista) {
        $result = null;

        if (is_a($vista, "ModelView")) {
            $this->vista = $vista;

            $result = $vista;
            $this->initItem();
        }

        return $result;
    }

    public function getVista() {
        return $this->vista;
    }


    /* *************************************************
     *  Items
     * ************************************************* */

    public function initItem() {
        if (!is_null($this->getVista())){
            foreach ($this->getVista()->getPropiedades() as $propiedad => $valores) {
                $valor = null;
                if (array_key_exists(ModelView::FLD_DEFECTO, $valores)) {
                    $valor = $valores[ModelView::FLD_DEFECTO];
                    $this->set($propiedad,$valor);
                }
            }
        }
    }
    
    public function existeAtributo($atributo){           
        return array_key_exists($atributo,$this->atributos);
    }
    
    public function getAtributos(){
        return array_keys($this->atributos);
    }
    
    public function resetValores(){
        $this->atributos = array();
    }
    
    public function getValores($incluyeId=false){
        $valores = array();
        foreach ($this->getVista()->getPropiedades() as $id => $atributo) {           
            if ($incluyeId || (!$incluyeId && $id!="id") && !$this->getVista()->campoEsExcluido($id)){
                $valores[$id] = $this->atributos[$id];          
            }
        }
        return $valores;
    }
    
    public function setModoVista($modo=self::MODO_VISTA_RAW) {
        $this->_modoVista = $modo;
    }

    public function getModoVista() {
        return $this->_modoVista;
    }
    
    /**
     * @deprecated
     * @param type $incluyeId
     * @return type
     */
    /*public function getAtributosMysql($incluyeId=false){
        $atributos = array();
        foreach ($this->atributos as $id => $atributo){
            if ($incluyeId || (!$incluyeId && $id!="id")){
                $atributos[$id] = $this->formatoMysql($atributo, $id);          
            }
        }
        return $atributos;
    }*/
    
    

    /*public static function show($propiedad, $modoVista, $tipo) {
        /*$tipo = is_null($tipo) ? $this->getVista()->getPropiedad($propiedad) : $tipo;
        $modoVista = is_null($modoVista) ? $this->getModoVista() : $modoVista;*/
/*
        if ($this->existeAtributo($propiedad)){
            switch ($modoVista) {
                case self::MODO_VISTA_LISTA:
                    return $this->getFormatoLista($this->atributos[$propiedad], $tipo);
                    break;
                case self::MODO_VISTA_EDITABLE:
                    return $this->getFormatoEditable($this->atributos[$propiedad], $tipo);
                    break;
                default: //self::MODO_VISTA_RAW
                    return $this->atributos[$propiedad];
                    break;
            }
        }
    }*/
    
    

    public function __get($propiedad) {
        $res = null;
        if (array_key_exists($propiedad, $this->atributos)){
            $res = $this->atributos[$propiedad];
        }
      
        /*if ($this->getVista()->getEncriptacion($propiedad)){
            $res = Tools::decrypt($res, AES_WORD);
        }*/
        return $res;
    }

    public function getId() {
        return $this->id;
    }

    public function __set($propiedad, $valor) {
        //$this->set($propiedad, $valor);
        $this->atributos[$propiedad] = $valor;
        //$this->atributos[$propiedad] = $this->setFormatoRaw($this->getVista()->getPropiedad($propiedad),$valor);
    }
    
    public function set($propiedad, $valor/*,$desdeDb=false*/) {
        $this->atributos[$propiedad] = $this->setFormatoRaw($this->getVista()->getPropiedad($propiedad),$valor);
    }
 
    

    /*  *************************************************
     *  Tools
     * ************************************************* */
    /*
    private function formatoRaw($valor, $propiedad) {
        $result = null;
        //echo "formatoRaw ".$propiedad[vista::FLD_TIPO]." ".$valor."<br/>";
        if (is_null($valor)) {
            $result = null;
        } else {
            switch ($propiedad[ModelView::FLD_TIPO]) {
                case ModelView::TIPO_FLOAT:
                case ModelView::TIPO_MONEDA:
                    $valor = str_replace(".", "", $valor);
                    $valor = str_replace(",", ".", $valor);
                    //$valor = number_format(floatval($valor),2,".",",");
                    $result = $valor;
                    break;
                
                
                
                case ModelView::TIPO_FECHA:
                    if ($valor != "") {
                        preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,4})/", $valor, $fechaTemp);
                        if (sizeof($fechaTemp)>0){	
                            $result=$fechaTemp[3]."-".$fechaTemp[2]."-".$fechaTemp[1];
                        }else{
                            $result = $valor;
                        }
                    } else {
                        $result = null;
                    }
                    break;
                
                case ModelView::TIPO_BOOLEAN:
                    //echo "jal boolean ".$propiedad[vista::FLD_CAMPO].", ".(!$valor);
                    $result = !$valor || $valor===0 || $valor===false? 0:1;
                    break;
                case ModelView::TIPO_CONTRASENA:                   
                    $result = md5($valor);
                    break;
                default:
                    $result = $valor;
                    break;
            }
        }
        //echo "formatoRaw ".$propiedad[vista::FLD_TIPO]." ".$valor.", ".$result."<br/>";
        return $result;
    }*/
    
    /**
     * @todo Revisar
     */
    private function setFormatoRaw($propiedad,$valor) {
        $result = null;
        if (is_null($valor)) {
            $result = null;
        } else {
            switch ($propiedad[ModelView::FLD_TIPO]) {
                case ModelView::TIPO_NUM:
                    $valor = str_replace(".", "", $valor);
                    $result = (int) $valor;
                    break;
                case ModelView::TIPO_FLOAT:
                case ModelView::TIPO_MONEDA:
                    $valor = str_replace(".", "", $valor);
                    $valor = str_replace(",", ".", $valor);
                    $result = (double) $valor;
                    break;
                case ModelView::TIPO_FECHA:
                    if ($valor != "") {
                        preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,4})/", $valor, $fechaTemp);
                        if (sizeof($fechaTemp)>0){	
                            $result=$fechaTemp[3]."-".$fechaTemp[2]."-".$fechaTemp[1];
                        }else{
                            $result = $valor;
                        }
                    } else {
                        $result = null;
                    }
                    break;             
                case ModelView::TIPO_BOOLEAN:
                    $result = !$valor || $valor===0 || $valor===false? 0:1;
                    break;
                case ModelView::TIPO_CONTRASENA:                   
                    $result = md5($valor);
                    break;
                default:
                    $result = $valor;
                    break;
            }
        }
        return $result;
    }
    /**
     * @deprecated
     * @todo eliminar este paso hasta llegar a formatoraw
     * @param type $valor
     * @param type $atributo
     * @return type
     */
    /*private function formatoMySql($valor, $atributo){
        $result = null;
        
        $propiedad = $this->getVista()->getPropiedad($atributo);

        if (is_null($valor)) {
            $result = "NULL";
        } else {
            switch ($propiedad[vista::FLD_TIPO]) {
                case vista::TIPO_REFERENCIA:
                case vista::TIPO_BOOLEAN:
                case vista::TIPO_ENUM:
                case vista::TIPO_MONEDA: 
                case vista::TIPO_NUM:
                    /*$result = $this->formatoRaw($valor, $propiedad);
                    break;*//*
                default:
                    //$result = $this->formatoRaw($valor, $propiedad);
                    $result = $valor;
                    break;
            }
        }
        return $result;
    }*/
    
    /**
     * @todo Revisar
     */
    /*private function formatoCliente($valor, $propiedad) {
        $result = "";
//echo "formatocliente: ".$valor." ".$propiedad;
        if (is_array($propiedad)) {
            $tipo = $propiedad[ModelView::FLD_TIPO];
        } else {
            $tipo = $propiedad;
        }

        switch ($tipo) {
            case ModelView::TIPO_FECHA:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_TIMESTAMP:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = strtotime($valor);
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_NUM:
                $result = number_format($valor, 2, ',', '');
                break;
            case ModelView::TIPO_FLOAT:
            case ModelView::TIPO_MONEDA:
                //$result = number_format($valor, 2, ',', '.');
                //$result = str_replace(".", ",", $valor);
                //$result = $valor;
                $result = number_format($valor, 2, ',', '');
                break;
            case ModelView::TIPO_BOOLEAN:
                $result = (boolean) $valor;
                break;
            default:
                $result = $valor;
                break;
        }

        return $result;
    }*/
    
    /*
    private function getFormatoEditable($valor, $propiedad) {
        $result = null;
        
        if (is_array($propiedad)) {
            $tipo = $propiedad[ModelView::FLD_TIPO];
        } else {
            $tipo = $propiedad;
        }

        switch ($tipo) {
            case ModelView::TIPO_FLOAT:
            case ModelView::TIPO_MONEDA:
                $result = is_numeric($valor)? number_format($valor, 2, ',', '.'):$result;
                
                //$result = $valor;
                break;
            case ModelView::TIPO_NUM:
                $result = is_numeric($valor)? number_format($valor, 0, ',', '.'):$result;
                break;
            case ModelView::TIPO_FECHA:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_BOOLEAN:
                $result = (boolean) $valor;
                break;
                
            
            
            case ModelView::TIPO_TIMESTAMP:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = strtotime($valor);
                } else {
                    $result = $valor;
                }
                break;
            default:
                $result = $valor;
                break;
        }

        return $result;
    }*/
           
    /*
    private function getFormatoLista($valor, $propiedad) {
        $result = "";
//echo "formatocliente: ".$valor." ".$propiedad;
        if (is_array($propiedad)) {
            $tipo = $propiedad[ModelView::FLD_TIPO];
        } else {
            $tipo = $propiedad;
        }

        switch ($tipo) {
            case ModelView::TIPO_FLOAT:
                $result = is_numeric($valor)? number_format($valor, 2, ',', '.'):$result;
                break;
            case ModelView::TIPO_MONEDA:
                $result = is_numeric($valor)? number_format($valor, 2, ',', '.')." €":$result;
                break;
            case ModelView::TIPO_NUM:
                $result = is_numeric($valor)? number_format($valor, 0, ',', '.'):$result;
                break;
            case ModelView::TIPO_FECHA:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_BOOLEAN:
                $result = (boolean) $valor;
                break;
            case ModelView::TIPO_ENUM:
                $result = "";
                if ($valor > 0) {
                    $result = array_key_exists(ModelView::FLD_ENUM, $propiedad) ? $propiedad[ModelView::FLD_ENUM][intval($valor)] : "";
                }
                break;
            
                
            case ModelView::TIPO_TIMESTAMP:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = strtotime($valor);
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_OBJETO:
                $result = $valor."OBJ";
                break;
            default:
                $result = $valor;
                break;
        }

        return $result;
    }

    */

    /* *************************************************
     *  Salida Json
     * ************************************************* */
    
    /**
     * @deprecated
     */
    public function getItemMobile($vista = true) {
        return $this->getMobile($vista);
    }
        
    public function getMobile($vista = true) {
        $result = array();

        /*
         * modificamos la salida a texto plano en los casos necesarios
         */
        $datos = $this->item[self::ITEM_DATOS];
        $tmp = $this->getVistaActualItems();
        if (sizeof($datos)){
            foreach ($tmp->propiedades as $idProp => $propiedad) {
                if ($propiedad[ModelView::FLD_TIPO]==ModelView::TIPO_TEXTOPLANO){
                        $datos[$idProp]=$this->stripHtmlTags($datos[$idProp]);
                }
            }
        }
        
        if ($vista){
            $result[self::ITEM_VISTA] = $this->getVista();
            $result[self::ITEM_DATOS] = $datos;
        }else{
           $result = $datos;
        }
        

        return $result;
    }

    /**
     * @deprecated
     */
    public function getItemJson($vista=true) {
        return $this->getJson($vista);
    }
    public function getJson($vista=true) {

        $result = $this->getItem($vista);

        return json_encode($result);
    }

    /**
     * @deprecated
     */
    public function getItemJsonMobile($root){
    
        return $this->getJsonMobile($root);
    }
    
    public function getJsonMobile($root){
        $result = $this->getMobile(false);

        $result = array($root => $result);
        return json_encode($result);
    }
}

?>