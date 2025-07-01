<?php

/**
 * @todo Eliminar rastros de bd y cambiarlo por dataProvider
 * @todo revisar condicionesrelaciones
 * @todo revisar getWhere - bdCondiciones ¿deprecated?
 * @todo eliminar el termino vista por modelfield
 * @todo revisar transresult
 * @todo revisar aesword
 */
abstract class Model implements Iterator {

    protected $_db;
    protected $_qb;
    protected $_modelFields;
    public $_item;
    protected $_items;
    protected $_vistaActual;
    protected $_vistas;
    protected $_indiceItems;
    protected $_indiceItemsIndex;
    protected $_where;
    protected $_vals;
    protected $_modoVista;
    protected $_returnHeader;

    /** Indice que apunta al item acutal *//* deprecated */
    public $vistaItemIndex;
    public $vistaItemsIndex;



    /* protected $proyecto; */
    protected $config; //creo que no usado
    protected $i18n;
//protected $bd; //no usado
//protected $bdDistinct;
    public $bdCondiciones;
    public $bdCondicionesRelaciones;
    protected $bdOrden;
    protected $bdLimitDesde;
    protected $bdLimitCantidad;
    protected $log;
    private $AESWord;

    /*
     * Vistas predefinidas
     */

//revisar: la mayoria pueden ser deprecated
    const VISTA_ITEM_DEFAULT = "item";
    const VISTA_ITEMS_DEFAULT = "items";
    const VISTA_FORMULARIO = "formulario";
    const VISTA_LISTADO = "listado";
    const VISTA_LISTADO_EXCEL = "listado_excel";
    const VISTA_MINILISTA = "minilista";
    const VISTA_FILTRO = "filtro";
    const VISTA_GUARDAR = "guardar";
//definitivos
    const VISTA_BASE = "base";
    const VISTA_LISTA = "lista";



    /*
     * Tipo de info de los items
     */
    const ITEM_VISTA = "item_vista";
    const ITEM_DATOS = "item_datos";

    /*
     * Tipos de Orden
     */
    const ORDEN_ASC = "ASC";
    const ORDEN_DESC = "DESC";

    /*
     * Tipos de resultados de transacciones
     */

    private $transResult;

    const TRANS_NOTHINGDONE = -2;
    const TRANS_ERROR = -1;
    const TRANS_NORESULT = 0;
    const TRANS_OK = 1;
    const MODO_VISTA_LISTA = 1;
    const MODO_VISTA_EDITABLE = 2;
    const MODO_VISTA_RAW = 3;

    /*
     * Relaciones entre tablas
     */
    /* const CLAVE_PRIMARIA = bd::CLAVE_PRIMARIA;
      const CLAVE_AJENA = bd::CLAVE_AJENA;
      const JOIN = "join";
      const JOIN_INNERJOIN = bd::JOIN_INNERJOIN;
      const JOIN_LEFTJOIN = bd::JOIN_LEFTJOIN;
      const JOIN_RIGHTJOIN = bd::JOIN_RIGHTJOIN;
      const JOIN_TABLA = bd::JOIN_TABLA;
      const JOIN_ALIAS = bd::JOIN_ALIAS; */

    /*
     * Campos de las tablas o propiedades
     */

    protected $propTablas;
    protected $_log;
    
    protected $_standAlone;

    /**
     * @todo unificar todos los inits
     * @todo revisar iscLogger a static
     * @param type $i18n
     * @param type $logger
     */
    public function __construct($i18n = false, $logger = null, $standAlone = false) {

        $this->_standAlone = $standAlone;
        $this->_db = new Database();   
        if (!$this->_standAlone) {
            $this->_db = new Database();
            $this->_qb = new QueryBuilder();
        }
        $this->_modelFields = array();

        $modelFieldsFile = ROOT . DIR_CORE . DS . DIR_MODELS . DS . 'default' . DS . "_defaultModelFields.php";
        if (is_readable($modelFieldsFile)) {
            require $modelFieldsFile;
            $this->_modelFields = array_merge($this->_modelFields, $_modelFields);
            unset($_modelFields);
        }

        $modelFieldsCustomFile = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $this->getClassName() . DS . '_' . get_called_class() . "Fields.php";
        $modelFieldsBaseFile = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $this->getClassName() . DS . '_' . get_called_class() . "Fields.php";
        $modelFieldsCustomParentFile = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $this->getParentClassName() . DS . '_' . $this->getParentClass() . "Fields.php";
        $modelFieldsBaseParentFile = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $this->getParentClassName() . DS . '_' . $this->getParentClass() . "Fields.php";

        $modelFieldsFile = "";
        if (is_readable($modelFieldsBaseFile)) {
            require $modelFieldsBaseFile;
            $this->_modelFields = array_merge($this->_modelFields, $_modelFields);
        }
        if (is_readable($modelFieldsCustomFile)) {
            require $modelFieldsCustomFile;
            $this->_modelFields = array_merge($this->_modelFields, $_modelFields);
        }
        if (is_readable($modelFieldsCustomParentFile)) {
            require $modelFieldsCustomParentFile;
            $this->_modelFields = array_merge($this->_modelFields, $_modelFields);
        }
        if (is_readable($modelFieldsBaseParentFile)) {
            require $modelFieldsBaseParentFile;
            $this->_modelFields = array_merge($this->_modelFields, $_modelFields);
        }
        unset($_modelFields);

        if (defined("AES_WORD")) {
            $this->setAESWord(AES_WORD);
        }

        $this->setTransResult(self::TRANS_NOTHINGDONE);

        $this->configModel();

        $this->_log = new iscLogger();

        $this->init();
        $this->initBd();
    }

    private function getClassName() {
        $className = str_replace("BaseModel", "", get_called_class());
        $className = str_replace("Model", "", $className);
        return $className;
    }

    private function getParentClass() {
        return get_parent_class(get_called_class());
    }

    private function getParentClassName() {
        $className = $this->getParentClass();
        $className = str_replace("BaseModel", "", $className);
        $className = str_replace("Model", "", $className);
        return $className;
    }

    /*     * ***********************************************
     *  Inicializacion
     * ************************************************* */

    private function init() {
        $this->_modoVista = self::MODO_VISTA_RAW;

        $this->initItem();
        $this->initItems();

        $this->initBd();

        $this->propTablas = array();
        $this->_returnHeader = true;

//$this->setIdioma(null);

        $this->rewind();
    }

    public function __sleep() {
        return [
            "_modelFields",
            "_item",
            "_items",
            "_vistaActual",
            "_vistas",
            "_indiceItems",
            "_indiceItemsIndex",
            "_where",
            "_vals",
            "_modoVista",
            "_returnHeader"];
    }

    public function __wakeup() {
        $this->initBd();
    }

    public function __toString() {
        $res = $this->getClassName() . "Model";
        $campoTitulo = $this->getVista()->getCampoTitulo();
        if ($this->issetItem() && !is_null($campoTitulo)) {
            $res = $this->show($campoTitulo);
        }
        return $res;
    }

    protected function configModel() {
        $this->_vistas = array();

        $modelFieldsCustomPath = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $this->getClassName() . DS;
        $modelFieldsBasePath = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $this->getClassName() . DS;
        $modelFieldsCustomParentPath = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $this->getParentClassName() . DS;
        $modelFieldsBaseParentPath = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $this->getParentClassName() . DS;

        $modeloCustom = str_replace("Model", "", $this->getClass());
        $rutaModelViewsCustom = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $modeloCustom . DS;
        $modeloBase = str_replace("Model", "", $this->getClass());
        $rutaModelViewsBase = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $modeloBase . DS;

        $modeloParent = str_replace("Model", "", $this->getParentClassName());
        $rutaModelViewsCustomParent = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_MODELS . DS . $modeloParent . DS;
        $rutaModelViewsBaseParent = ROOT . DIR_CORE . DS . DIR_MODELS . DS . $modeloParent . DS;


        $paths = [
            $modelFieldsBaseParentPath,
            $modelFieldsCustomParentPath,
            $modelFieldsBasePath,
            $modelFieldsCustomPath,
        ];
        foreach ($paths as $path) {
            if (is_readable($path)) {
                foreach (glob($path . '*.php') as $archivo) {
                    if (substr(basename($archivo), 0, 1) !== "_") {
                        $nombreVista = str_replace('.php', '', basename($archivo));
                        $modelViewFile = $path . $nombreVista . '.php';
                        $this->addVista($nombreVista, $modelViewFile);
                    }
                }
            }
        }

        $this->setVista();
    }

    /**
     * @todo deprecated eliminar y tomar solo lo neceario
     */
    protected function initBd() {
        if (!is_null($this->bd)) {
            $this->bd->init_query();
        }
//$this->bdDistinct = false;
        $this->bdCondiciones = null;
        $this->bdCondicionesRelaciones = null;
//$this->bdOrden = null;
//$this->bdLimitDesde = 0;
//$this->bdLimitCantidad = -1;
        $this->_items = [];
    }

    /* *************************************************
     *  Implementación de Iterator
     * ************************************************* */

    public function rewind() {
        $this->_indiceItemsIndex = 0;
    }

    public function current() {
        $this->initItem();
        $this->selectNth($this->_indiceItemsIndex);
//return $this->_item;
        return $this;
    }

    public function key() {
//return $this->_indiceItemsIndex;
        return $this->_item->id;
    }

    public function next() {
        $this->confirma();
        ++$this->_indiceItemsIndex;
    }

    public function valid() {
        return isset($this->_items[$this->_indiceItemsIndex]);
    }

    /*     * ***********************************************
     *  get-set
     * ************************************************* */

    public function setModoVista($modo = self::MODO_VISTA_EDITABLE) {
        $this->_modoVista = $modo;
        $this->_item->setModoVista($modo);
    }

    public function getModoVista() {
        return $this->_modoVista;
    }

    /* public function setConfig($config) {
      $this->config = $config;
      }* */

    /* public function getConfig() {
      return $this->config;
      } */

    public function setTransResult($result) {
        $this->transResult = $result;
    }

    public function getTransResult() {
        return $this->transResult;
    }

    /* public function setI18n($i18n) {
      $this->i18n = $i18n;
      }

      public function getI18n() {
      return $this->i18n;
      }

      public function getIdioma() {
      return $this->idioma;
      }

      public function setIdioma($idioma) {
      //$this->initBd();
      $this->idioma = $idioma;
      } */

    public function setAESWord($AESWord) {
        $this->AESWord = $AESWord;
    }

    public function getAESWord() {
        return $this->AESWord;
    }

    protected function loadModel($modelo) {
        $result = null;

        if (class_exists($modelo . "Model")) {
            $modelo.= "Model";
            $result = new $modelo;
        } else if (class_exists($modelo . "BaseModel")) {
            $modelo.= "BaseModel";
            $result = new $modelo;
        } else if (class_exists($modelo)) {
            $result = new $modelo;
        } else {
            throw new Exception('Modelo no encontrado (loadModel): ' . $modelo);
        }

        return $result;
    }

    /*     * ************************************************
     *  Items
     * ************************************************* */

    public function initItem($item = null) {

        $this->_item = new ItemModel($this->getClass(), $this->log);
        $this->_item->setVista($this->_vistas[$this->_vistaActual]);
        $this->_item->setModoVista($this->getModoVista());
    }

    public function resetItem() {
//$this->item->initItem();
        $this->_item->setVista($this->_vistas[$this->_vistaActual]);
    }

    public function reset() {
        $this->setVista($this->_vistas[$this->_vistaActual]);
//$this->_item->setVista($this->_vistas[$this->_vistaActual]);
    }

    public function setItem($item) {
        $this->_item = $item;
    }

    public function getItem() {
        return $this->_item;
    }

    /**
     * @todo revisar funcion en php que transforma keys en valores en un array
     */
    public function getItemsIds() {
        $res = [];
        if ($this->_indiceItems && sizeof($this->_indiceItems)) {
            foreach ($this->_indiceItems as $id => $index) {
                $res[] = $id;
            }
        }
        return $res;
    }

    public function issetItem() {
        return $this->_item->id > 0;
    }

    public function initItems() {
        $this->_items = [];
    }

    public function itemIsset() {
        return $this->_item->id != -1;
    }

    /*
     * Seleccion de items
     */

    public function select($id) {
        if (!is_null($this->_indiceItems) && array_key_exists($id, $this->_indiceItems)) {
            $this->selectNth($this->_indiceItems[$id]);
        }
    }

    public function selectItemCampo($campo, $valor) {
        $existe = false;
        if (sizeof($this->_items) > 0) {
            foreach ($this->_items as $key => $item) {
                if (property_exists($item, $campo) && $item->$campo == $valor) {
                    $this->selectNth($key);
                    $existe = true;
                    break;
                }
            }
        }
        $result = null;
        if ($existe) {
            $result = $this->getItem();
        } else {
            $this->initItem();
        }

        return $result;

//return $this->getItem();
    }

    /**
     * @todo ¿para que sirve? revisar
     * @param type $campos
     * @return type
     */
    public function selectItemCampos($campos) {
        if (sizeof($this->_items) > 0) {
            if ($campos && is_array($campos)) {

                foreach ($this->_items as $key => $item) {
                    $existe = true;
                    foreach ($campos as $campo => $valor) {
                        if (!property_exists($item, $campo) || $item->$campo != $valor) {
                            $existe = false;
                        }
                    }
                    if ($existe) {
                        $this->selectNth($key);
                        break;
                    }
                }
            }
        }
        $result = null;
        if ($existe) {
            $result = $this->getItem();
        } else {
            $this->initItem();
        }
        return $result;
    }

    public function selectPrimero() {
        $this->selectNth(0);
    }

    public function selectUltimo() {
        $this->selectNth($this->getNumItems() - 1);
    }

    public function selectSiguiente() {
        if ($this->_indiceItemsIndex < $this->getNumItems()) {
            $this->confirma();
            $this->initItem();
            $this->selectNth(++$this->_indiceItemsIndex);
        }
    }

    public function selectAnterior() {
        if ($this->_indiceItemsIndex > 0) {
            $this->confirma();
            $this->initItem();
            $this->selectNth(--$this->_indiceItemsIndex);
        }
    }

    public function selectNth($n) {
        if ($this->getNumItems() > $n) {
            $this->_indiceItemsIndex = $n;
            $this->_item = new ItemModel($this->getClass(), $this->log);
            $this->_item->setVista($this->_vistas[$this->_vistaActual]);

            foreach ($this->_items[$n] as $key => $value) {
                $this->_item->$key = $value;
            }
        }
    }

    public function selectRandom() {
        $num = rand(0, $this->getNumItems() - 1);
        $this->selectNth($num);
    }

    /*
     * Gestion de items
     */

    public function existe($id) {
        return array_key_exists($id, $this->_indiceItems);
    }

    /**
     * @todo ¿para que sirve? revisar
     * @param type $campo
     * @param type $valor
     * @return boolean
     */
    public function existeItemCampo($campo, $valor) {
        $result = false;
        if (sizeof($this->_items[self::ITEM_DATOS]) > 0) {
            foreach ($this->_items[self::ITEM_DATOS] as $id => $item) {
                if (array_key_exists($campo, $item) && $item[$campo] == $valor) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public function incluye() {
        return $this->item2Raw();
    }

    public function confirma() {
        $result = null;
        if (array_key_exists($this->_item->id, $this->_indiceItems)) {
            $result = $this->item2Raw($this->_item, $this->_item->id);
        }
        return $result;
    }

    public function retira($id = null) {
        $id = is_null($id) ? $this->_item->id : $id;
        if (array_key_exists($id, $this->_indiceItems)) {
            unset($this->_items[$this->_indiceItems[$id]]);
            unset($this->_indiceItems[$id]);
        }
    }

    public function getItems() {
        return $this->_items;
    }

    /**
     * @todo formatear datos de items
     * @todo eliminar html para mobile. ver funcion stripHtmlTags
     */
    public function getItemJson($root = "item") {
        $attr = $this->getAtributos($vista);
        return json_encode(array($root => $attr));
    }

    /**
     * @todo formatear datos de items
     * @todo eliminar html para mobile. ver funcion stripHtmlTags
     */
    public function getItemsJson($root = "items") {
        $attr = $this->_items;
        return json_encode(array($root => $attr));
    }

    public function getNumItems() {
        return is_null($this->_items) ? 0 : sizeof($this->_items);
    }

    /**
     * @todo Revisar
     * @param type $campo
     * @param type $valor
     */
    /* public function sacaItemCampo($campo, $valor) {
      if (sizeof($this->_items[self::ITEM_DATOS]) > 0) {
      foreach ($this->_items[self::ITEM_DATOS] as $id => $item) {
      if (array_key_exists($campo, $item) && $item[$campo] == $valor) {
      $this->sacaItem($id);
      }
      }
      }
      } */



    public function __get($propiedad) {
        return $this->_item->$propiedad;
    }

    /**
     * @todo Hecho
     * @param type $propiedad
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @todo revistar formatoCliente y probablemente eliminar
     * @param string $propiedad
     * @param string $tipo
     * @return varios
     */
    public function show($propiedad, $modoVista = null, $tipo = null) {
        $tipo = is_null($tipo) ? $this->getVista()->getPropiedad($propiedad) : $tipo;
        $modoVista = is_null($modoVista) ? $this->getModoVista() : $modoVista;
        $lista = null;

        if ($tipo[ModelView::FLD_TIPO] == ModelView::TIPO_ENUM) {
            $lista = $tipo[ModelView::FLD_ENUM];
        } else if ($tipo[ModelView::FLD_TIPO] == ModelView::TIPO_OBJETO || $tipo[ModelView::FLD_TIPO] == ModelView::TIPO_OBJETO_EXT) {
            $lista = $tipo[ModelView::FLD_LISTA];
        }
        return Tools::show($this->_item->$propiedad, $tipo[ModelView::FLD_TIPO], $modoVista, $lista);
    }

    public function __set($propiedad, $valor) {
        $this->_item->$propiedad = $valor;
    }

    /**
     * @todo resolver cuando $id!=null
     * @param type $propiedad
     * @param type $valor
     */
    public function set($propiedad, $valor, $id = null) {
        if (!is_null($id)) {
            $this->select($id);
        }
        $this->_item->set($propiedad, $valor);
    }

    /* public function setToItems($id, $propiedad, $valor) {
      $this->_items[self::ITEM_DATOS][$id][$propiedad] = $valor;
      } */



    /*     * ***********************************************
     * Operaciones con base de Datos
     * ************************************************* */

    public function resetConsulta() {
        if (!is_null($this->bd)) {
            $this->bd->reset_query();
        }
        $this->initBd();
    }

    /**
     * @todo revisar trans si no esSelect
     * @param type $query
     */
    public function query($query = null) {
        if (!is_null($query)) {
            $resultBd = $this->bd->query($query);
            $esSelect = !is_array($query) && strpos($query, "SELECT") === true;
            if ($esSelect) {
                if (!is_null($resultBd)) {
                    $this->bd2raw($resultBd);
                    $this->setTransResult(self::TRANS_OK);
                } else {
                    $this->setTransResult(self::TRANS_NORESULT);
                }
            }
        }
    }

    /**
     * @todo revisar las asignaciones $this->_qb->setTablas etc...
     * @todo revisar que devuelve
     * @todo implementar condicionesrelaciones
     * @param type $getQuery
     * @return array
     */
    public function consulta($getQuery = false) {
        $result = [];
        $this->_items = [];

//$this->_qb->setModelView($this->getVista());
//$this->_qb->setTablas($this->getVista()->getTablas());
//$this->_qb->setCampos($this->getCampos($this->getVista(), false, true));
//$this->_qb->setDistinct($this->getDistinct());
////$this->_db->set_condiciones_relaciones($this->getCondicionesRelaciones());
//$this->_qb->setRelaciones($this->getVista()->getRelaciones());
//print_r($this->_where);
//$this->_qb->setWhere($this->getWhere($this->_where));
//print_r($this->getWhere());
//$this->_qb->setGroupBy($this->getVista()->getGroupBy());
//$this->_qb->setOrderBy($this->getOrden());
//$this->_qb->setLimit($this->getLimitDesde(), $this->getLimitCantidad());
        if ($this->_qb) {
            $q = "";
            $query = $this->_qb->select();
            if ($getQuery) {
                $result = $query;
            } else {
                try {
                    $q = $this->_db->prepare($query);
                    if ($q->execute($this->_qb->getValues()) && $q->rowCount() > 0) {
                        $resultado = $q->fetchAll(PDO::FETCH_OBJ);
                        $this->bd2raw($resultado);
                        $this->setTransResult(self::TRANS_OK);
                    } else {
                        $this->setTransResult(self::TRANS_NORESULT);
                    }
                    $result = $q->rowCount();
                    $this->_log->LogDebug($query);
                    $this->_log->LogDebug("Valores: " . implode(",", $this->_qb->getValues()));
                } catch (Exception $e) {
                    $this->setTransResult(self::TRANS_ERROR);
                    $this->_log->LogError($query);
                    $this->_log->LogError($this->_db->errorInfo()[2]);
                }
            }
        }
        return $result;
    }

    public function cuenta($getQuery = false) {
        $result = -1;

        /* $this->_qb->setTablas($this->getVista()->getTablas());
          $this->_qb->setCampos($this->getCampos($this->getVista(), false, true));
          $this->_qb->setDistinct($this->getDistinct());
          //$this->_db->set_condiciones_relaciones($this->getCondicionesRelaciones());
          $this->_qb->setRelaciones($this->getVista()->getRelaciones());
          $this->_qb->setWhere($this->getWhere($this->_where));
          $this->_qb->setLimit(); */
        if ($this->_qb) {
            $query = $this->_qb->cuenta();
            if ($getQuery) {
                $result = $query;
            } else {
                try {
                    $q = $this->_db->prepare($query);
                    if ($q->execute($this->_qb->getValues())) {
                        $this->setTransResult(self::TRANS_OK);
                        $resultado = $q->fetch(PDO::FETCH_BOTH);
                        $result = $resultado[0];
                    } else {
                        $this->setTransResult(self::TRANS_ERROR);
                        $result = 0;
                    }
                    $this->_log->LogDebug($query);
                    $this->_log->LogDebug("Valores: " . implode(",", $this->_qb->getValues()));
                } catch (Exception $e) {
                    $this->setTransResult(self::TRANS_ERROR);
                    $this->_log->LogError($this->_qb->select());
                    $this->_log->LogError($this->_db->errorInfo()[2]);
                }
            }

            return $result;
        }
    }

    public function inserta($getQuery = false) {
        $result = null;

        if ($this->_qb) {
            $query = $this->_qb->insert();
            $valores = array_values($this->getItem()->getValores());

            if ($getQuery) {
                $result = $query;
                
            } else {
                try {
                    
                    $q = $this->_db->prepare($query);                         
                    if ($q->execute($valores)) {
                        $this->id = $this->_db->lastInsertId();
                        $result = $this->getId();
                        $this->setTransResult(self::TRANS_OK);
                    } else {
                        $this->setTransResult(self::TRANS_NORESULT);
                    }
                    $this->_log->LogDebug($query);
                    $this->_log->LogDebug("Valores: " . implode(",", $valores));
                    $this->_log->LogDebug("transresult:  ".$this->getTransResult());
                } catch (Exception $e) {
                    $this->setTransResult(self::TRANS_ERROR);
                    $this->_log->LogError($query);
                    $this->_log->LogError("Valores: " . implode(",", $this->_qb->getValues()));
                    $this->_log->LogError("Error: " . implode(", ", $this->_db->errorInfo()));
                }
            }
            return $result;
        }
    }

    public function actualiza($getQuery = false) {
        $result = [];

        /* $this->_qb->setTablas($this->getVista()->getTablas());
          $this->_qb->setCampos($this->getCampos($this->getVista(), true, false));
          $this->_qb->setWhere(array($this->getVista()->getCampo("id"), "=")); */

        if ($this->_qb) {
            $this->_qb->setWhere(["id", "=", $this->getVista()->getCampo("id")]);

            $query = $this->_qb->update();
            $valores = array_values($this->getItem()->getValores());
            $valores[] = $this->getId();

            if ($getQuery) {
                $result = $query;
            } else {
                try {
                    $q = $this->_db->prepare($query);
                    if ($q->execute($valores)) {
                        $result = $this->_item;
                        $this->setTransResult(self::TRANS_OK);
                    } else {
                        $this->setTransResult(self::TRANS_NORESULT);
                    }
                    $this->_log->LogDebug($query);
                    $this->_log->LogDebug("Valores: " . implode(",", $valores));
                } catch (Exception $e) {
                    $this->setTransResult(self::TRANS_ERROR);
                    $this->_log->LogError($this->_qb->select());
                    $this->_log->LogError($this->_db->errorInfo()[2]);
                }
            }

            return $result;
        }
    }

    public function guarda($getQuery = false) {
        $result = null;
        Session::destroy("lista_".$this->getClassName());
        if (!$this->issetItem()) {
//if ($this->getId() == -1 || $this->getId() == "") {
            $result = $this->inserta(false, $getQuery);
        } else {
            $result = $this->actualiza($getQuery);
        }

        return $result;
    }

    /**
     * @todo Revisar, las queries no reciben valores
     */
    public function guardaItems() {
        $queries = array();
        foreach ($this->_items as $id => $item) {
            $this->selectNth($id);
            $queries[] = $this->guarda(true);
        }
        $this->query($queries);
    }

    public function carga($campo, $valor) {
        $r = null;

        if (!is_array($campo)) {
            $this->setWhere([$campo, "=", $valor]);
            $this->consulta();
        } else {
            $condiciones = [];
            for ($n = 0; $n < sizeof($campo); $n++) {
                $condiciones[] = [$campo[$n], "=", $valor[$n]];
                $condiciones[] = "AND";
            }
            array_pop($condiciones);
            $this->setWhere($condiciones);
            $this->consulta();
        }

        if ($this->getNumItems() > 0) {
            $this->selectPrimero();
            $r = $this->getItem();
        }
        return $r;
    }

    public function cargaId($id) {
        return $this->carga(ModelView::PROP_ID, $id);
    }

    /**
     * @todo ver pq no devuelve la query si getQuery=true
     * @todo ¿deberia ser un borra item actual?
     * @param type $id
     * @param type $getQuery
     * @return type
     */
    public function borra($borraItem = true, $getQuery = false) {

        $result = "";

//$this->_qb->setTablas($this->getVista()->getTablas());
        /* $params = null;
          if (!is_null($id)) {
          $this->_qb->setWhere(["id", "=", $id]);
          $params = array($id);
          } else {
          $this->_qb->setWhere($this->getWhere($this->_where));
          $params = $this->_getVals();
          } */
        if ($this->_qb) {
            if ($borraItem) {
                if ($this->issetItem()) {
                    $this->_qb->setWhere(["id", "=", $this->id]);
                } else {
                    $this->_qb->setWhere(["id", "=", -1]);
                }
            }

            $query = $this->_qb->delete();
            if ($getQuery) {
                $result = $query;
            } else {
                try {
                    $q = $this->_db->prepare($query);
                    if ($q->execute($this->_qb->getValues())) {
                        $this->setTransResult(self::TRANS_OK);
                    }
                    $this->_log->LogDebug($query);
                    $this->_log->LogDebug("Valores: " . implode(",", $this->_qb->getValues()));
                } catch (Exception $e) {
                    $this->setTransResult(self::TRANS_ERROR);
                    $this->_log->LogError($this->_qb->select());
                    $this->_log->LogError($this->_db->errorInfo()[2]);
                }
            }


            return $result;
        }
    }

   /* public function borraItems() {
        if ($this->_items) {
            //$queries = [];
            foreach ($this->_items as $id => $item) {
                //$this->select($id);
                //$queries[] = $this->borra();
                //$this->borra();
                $this->_qb->setWhere(["id", "=", $this->id]);
            }
             if (sizeof($queries)){
              $this->query($queries);
              } 
        }
        
        return $result;
    }*/

    /*     * ************************************************
     *  Vistas
     * ************************************************* */

    public function addVista($nombre, $modelViewFile, $modelFieldsFile = null) {
//$modelFieldsFile = ROOT.DIR_MODELS.DS.str_replace("Model","",get_called_class()).DS.$file.".php";

        if (is_readable($modelViewFile)) {
            require $modelViewFile;
            $vista->setModelFields($this->_modelFields);
            
            if (is_a($vista, "ModelView")) {
                $this->_vistas[$nombre] = $vista;
            }
        }
    }

    public function setVista($nombreVista = self::VISTA_BASE) {

        $this->_vistaActual = $nombreVista;
        $this->cargaVistaLista($nombreVista);
        if ($this->_qb) {
            //$this->_qb->setModelFields($this->_modelFields);
            $this->_qb->setModelView($this->getVista($nombreVista));
        }

        if (!isset($this->_item)) {
            $this->initItem();
        }
        $this->_item->setVista($this->_vistas[$this->_vistaActual]);
        $this->setOrden($this->getVista()->getOrderBy());
    }

    public function existsVista($nombreVista) {
        return array_key_exists($nombreVista, $this->_vistas);
    }

    public function getVista($nombre = null) {
        $res = null;
        $nombre = !is_null($nombre) ? $nombre : $this->_vistaActual;
        if (array_key_exists($nombre, $this->_vistas)) {
            $res = $this->_vistas[$nombre];
        }
        return $res;
    }

    public function cargaVistaLista($nombre = null) {
        $nombre = !is_null($nombre) ? $nombre : $this->_vistaActual;
        $vista = $this->_vistas[$nombre];

        foreach ($vista->getPropiedades() as $key => $propiedad) {
            //echo "jal: $key";
            //echo "<br/>";
            if ($propiedad[ModelView::FLD_TIPO] == ModelView::TIPO_ENUM) {
//$vista->setCampo($key, ModelView::FLD_LISTA, $propiedad[ModelView::FLD_ENUM]);
            } else if ($propiedad[ModelView::FLD_TIPO] == ModelView::TIPO_OBJETO || $propiedad[ModelView::FLD_TIPO] == ModelView::TIPO_OBJETO_EXT) {
                if (!Session::exists("lista_" . $propiedad[ModelView::FLD_OBJETO])) {
                    Session::set("lista_" . $propiedad[ModelView::FLD_OBJETO],[]);
                    $campoNombre = array_key_exists(ModelView::FLD_OBJETO_CAMPO, $propiedad) ? $propiedad[ModelView::FLD_OBJETO_CAMPO] : ModelView::PROP_NOMBRE;

                    $items = $this->loadModel($propiedad[ModelView::FLD_OBJETO]);
                    if ($items->existsVista("select")){
                        $items->setVista("select");
                        $campoNombre= "selectNombre";
                    }
                    $items->setOrden($campoNombre);
                    $items->consulta();
                    $lista = [];
                    foreach ($items as $item) {
                        $lista[$item->id] =$item->show($campoNombre);// clone($item);
                    }
                    $vista->setCampo($key, ModelView::FLD_LISTA, $lista);
                    Session::set("lista_" . $propiedad[ModelView::FLD_OBJETO], $lista);
                } else {
                    $lista = Session::get("lista_" . $propiedad[ModelView::FLD_OBJETO]);
                    //print_r($lista);
                    $vista->setCampo($key, ModelView::FLD_LISTA, $lista);
                }
            }

            /*
              if (!array_key_exists(ModelView::FLD_LISTA, $propiedad) && array_key_exists(ModelView::FLD_OBJETO, $propiedad)) {
              $items = $this->loadModel($propiedad[ModelView::FLD_OBJETO]);
              $items->consulta();
              $lista = [];
              //echo "<pre>";
              foreach ($items as $item) {
              //echo $item->id;
              $lista[$item->id] = clone($item);
              }

              //print_r($lista);
              //echo "</pre>";
              $vista->setCampo($key, ModelView::FLD_LISTA, $lista);
              } */
        }
    }

    /**
     * @deprecated
     * @param type $nombre
     * @param type $vista
     */
    /* public function insertVista($nombre, $vista) {
      if (is_a($vista, "ModelView")) {
      $this->_vistas[$nombre] = $vista;
      }
      } */

    /**
     * @deprecated
     * @param type $nombreVista
     */
    /* public function asignaVistaItem($nombreVista = self::VISTA_ITEM_DEFAULT) {
      //$this->initItem();
      $this->vistaItemIndex = $nombreVista;
      $this->_item->setVista($this->_vistas[$this->vistaItemIndex]);
      } */

    /**
     * @todo revisar la problematica de que coger de la vista y que del objeto al hacer consulta
     * @deprecated
     * @param type $nombreVista
     */
    /* public function asignaVistaItems($nombreVista = self::VISTA_ITEMS_DEFAULT) {
      $this->vistaItemsIndex = $nombreVista;
      $this->setOrden($this->getVista()->getOrderBy());
      } */

    /**
     * @todo Hecho
     * @deprecated
     * @return type
     */
    /* public function getVistaItem() {
      return $this->_item->getVista();
      } */

    /**
     * @deprecated
     * @return type
     */
    /* public function getVistaItems() {
      return $this->getVista($this->vistaItemsIndex);
      } */

    public function addProp($nombre, $propiedad = array()) {
        if (!array_key_exists($nombre, $this->propTablas)) {
            $this->propTablas[$nombre] = $propiedad;
        }
    }

    public function getProp($nombre, $propiedadCustom = null) {
        $result = null;

        if (array_key_exists($nombre, $this->_modelFields)) {
            $result = $this->_modelFields[$nombre];
        } else {
            $vista = new ModelView();
            $result = $vista->getPropiedad($nombre);
        }

        if (!is_null($propiedadCustom)) {
            foreach ($propiedadCustom as $prop => $valor) {
                if (is_null($valor) && array_key_exists($prop, $result)) {
//Eliminamos la propiedad
                    unset($result[$prop]);
                } else {
                    $result[$prop] = $valor;
                }
            }
        }

        return $result;
    }

    public function getPropTabla($nombre, $tabla, $propiedadCustom = array()) {
        $propiedadCustom[ModelView::FLD_TABLA] = $tabla;

        return $this->getProp($nombre, $propiedadCustom);
    }

    public function getPropI18n($nombre, $propiedadCustom = array()) {
        $propiedadCustom[ModelView::FLD_I18N] = true;

        return $this->getProp($nombre, $propiedadCustom);
    }

    /*
     * Preparacion consulta
     */

    /**
     * @deprecated sustituir por $this->getVistaItems()->getTablas()
     * @param type $vista
     * @return type
     */
    private function getTablas($vista) {

        $result = $vista->getTablas();

        return $result;
    }

    /**
     * @todo simplificar esto
     * @param type $vista
     * @param type $paraInsert
     * @param type $incluirId
     * @param type $visibilidad
     * @return type
     */
    private function getCampos($vista, $paraInsert = true, $incluirId = false, $visibilidad = ModelView::VIS_TOTAL) {

        $result = $vista->getCampos($incluirId, $visibilidad);

        foreach ($result as $key => $campo) {
//if ($vista->getTipo($key)!=vista::TIPO_ARCHIVO || !$paraInsert){

            $campoDef = "";

            if (false/* $vista->getEncriptacion($key) */) {
                if (strpos($campo, ",") !== false) {
                    $campo = explode(",", $campo);
                }
                if (is_array($campo)) {
                    $campoDef = "CONCAT(";
                    foreach ($campo as $value) {
                        $campoDef .= "aes_decrypt(" . $value . ",(" . $this->getAESWord() . "'), ' ', ";
                    }
                    $campoDef = substr($campoDef, 0, strlen($campoDef) - 7) . ")";
                    $result[$key] = $campoDef;
                } else {
                    if ($paraInsert) {
                        $result[$key] = $campo;
                    } else {
                        $result[$key] = "aes_decrypt(" . $campo . ",'" . $this->getAESWord() . "')";
                    }
                }
            } else {
                if (is_array($campo)) {
                    $campoDef = "CONCAT(";
                    foreach ($campo as $value) {
                        $campoDef .= $value . ", ' ', ";
                    }
                    $campoDef = substr($campoDef, 0, strlen($campoDef) - 7) . ")";
                } else {
                    $campoDef = $campo;
                }
                $result[$key] = $campoDef;
            }
//echo $campoDef."<br/>";
//}
        }

        return $result;
    }

    private function getCamposInsert($vista, $i18n = false) {
        $visibilidad = ModelView::VIS_TOTAL;
        $result = $vista->getCampos(false, $visibilidad, !$vista->getI18n(), $i18n);

        return $result;
    }

    private function getCondicionesCampos($vista, $condicionesCampos = null) {

        $result = null;
        /* echo "<br/><br/>";
          print_r($this->bdCondiciones); */
        if (is_null($condicionesCampos)) {
            $result = $this->bdCondiciones["campos"];
        } else {
            $result = $condicionesCampos;
        }

        for ($n = 0; $n < sizeof($result); $n++) {
            if (!is_array($result[$n])) {
                /* if ($vista->getEncriptacion($result[$n])){
                  $result[$n] = "aes_decrypt(".$this->key2campo($result[$n],$vista).",'12345678')";
                  }else{
                  $result[$n] = $this->key2campo($result[$n],$vista);
                  } */
                $result[$n] = $this->key2campo($result[$n], $vista, $vista->getEncriptacion($result[$n]));
            } else {
                $result[$n] = $this->getCondicionesCampos($vista, $result[$n]);
            }
        }

        return $result;
    }

    public function setWhere($condiciones) {
        /* $result = array();

          if (is_null($condiciones) || !is_array($condiciones)) {
          $result = null;
          } else {
          $result = $condiciones;
          }
          $this->_where = $result; */ //$this->fixLike($result);
        if ($this->_qb) {
            $this->_qb->setWhere($condiciones);
        }
    }

    /**
     * @todo revisar ¿igual que setWhere?
     * @param type $condiciones
     */
    public function addWhere($condiciones, $operador = "AND") {
        /* $result = array();
          $actual = $this->_where;

          if (is_null($condiciones) || !is_array($condiciones) || sizeof($condiciones) == 0) {
          $result = $actual;
          } else {
          if (is_null($actual)) {
          $result = $condiciones;
          } else {
          $result = [$actual, $operador, $condiciones];
          }
          }

          $this->_where = $result; //$this->fixLike($result); */
        if ($this->_qb) {
            $this->_qb->addWhere($condiciones, $operador);
        }
    }

    public function setWhereJson($condiciones) {
        $this->setWhere(json_decode($condiciones));
    }

    public function addWhereJson($condiciones, $operador = "AND") {
        $this->addWhere(json_decode($condiciones), $operador);
    }

    public function getWhereJson() {
        if ($this->_qb) {
            return json_encode($this->_qb->getWhere());
        }
    }

    /* private function fixLike($where=null){
      $result = $where;

      if (is_array($where)){
      if (in_array("AND", $where) || in_array("OR", $where)){
      $result = [$this->fixLike($where[0]), $where[1], $this->getWhere($where[2])];
      }else{
      if (sizeof($where)===3){
      switch ($where[1]){
      case "LIKE":
      case "NOT LIKE":
      $result[2] = "%".$where[2]."%";
      break;
      }
      }
      }
      }
      return $result;
      } */

    /**
     * @todo en el futuro no intervendrá aquí la vista, lo hará en querybuilder
     * @todo revisar si $where[0] es null
     * @deprecated
     * @param type $where
     * @return string
     */
    public function getWhere($where = null) {
        $result = null;

//$where = is_null($where)? $this->_where : $where;
//echo "jal ".(!is_null($where) && is_array($where) && count($where));
//print_r($where);
//echo "<br/>";
        if (!is_null($where) && is_array($where) && count($where)) {
//echo "jel ".in_array("AND", ["AS","AND"]);
//print_r($where);
            if (in_array("AND", $where, true) || in_array("OR", $where, true)) {
//echo "jols".in_array("OR", $where);
//foreach ($where as $condicion) {
                /* $wh1=null;
                  if (!is_null($where[0])){
                  $wh1 = $this->getWhere($where[0]);
                  }
                  $wh2=null;
                  if (!is_null($where[2])){
                  $wh2 = $this->getWhere($where[2]);
                  }
                  if (is_null($wh1) || !is_array($wh1)){
                  $result = $wh2;
                  }else if (is_null($wh2)|| !is_array($wh2)){
                  $result = $wh1;
                  }else{
                  $result = array($this->getWhere($where[0]),$where[1],$this->getWhere($where[2]));
                  } */
//}
                $result = [];
                foreach ($where as $index => $condicion) {
                    if ($index % 2 == 0) {
                        $result[] = $this->getWhere($where[$index]);
                    } else {
                        $result[] = $where[$index];
                    }
                }
            } else {

                $hecho = false;
                $vista = $this->getVista();
//$propiedad = $this->getVista()->propiedades;
                switch ($where[1]) {
                    case "=":
                    case "<":
                    case ">":
                    case "<=":
                    case ">=":
                    case "!=":
                    case "<>":
                    case "IS NOT":
                    case "IS NULL":
//$where[0] = $propiedad[$where[0]]["tabla"].".".$propiedad[$where[0]]["campo"];
                        $where[0] = $vista->getCampo($where[0]);
//echo "jal".$where[0];
                        $result = $where;
                        $hecho = true;
                        break;
                    case "LIKE":
                    case "NOT LIKE":
//$where[0] = $propiedad[$where[0]]["tabla"].".".$propiedad[$where[0]]["campo"];
                        $where[0] = $vista->getCampo($where[0]);
                        $where[2] = "%" . $where[2] . "%";
                        $result = $where;
                        $hecho = true;
                        break;
                }

                if (!$hecho) {
                    switch ($where[0]) {
                        case "ISNULL":
                        case "NOT ISNULL":
//$where[1] = $propiedad[$where[1]]["tabla"].".".$propiedad[$where[1]]["campo"];
                            $where[1] = $vista->getCampo($where[1]);
                            $result = $where;
                            $hecho = true;
                            break;
                    }
                }
            }
        }
//print_r($result)."</br>";
        return $result;
    }

    private function _getVals($vals = null) {
        $result = [];
        if ($this->_qb) {
            $vals = is_null($vals) ? $this->_qb->getWhere() : $vals;

            if (is_array($vals) && count($vals)) {
                if (in_array("AND", $vals) || in_array("OR", $vals)) {
                    $result = [];
                    foreach ($vals as $index => $condicion) {
                        if ($index % 2 == 0) {
                            $result = array_merge($result, $this->_getVals($vals[$index]));
                        }
                    }
                } else {
                    $hecho = false;
                    switch ($vals[1]) {
                        case "=":
                        case "<":
                        case ">":
                        case "<=":
                        case ">=":
                        case "!=":
                        case "<>":
                            /* case "IS NOT":
                              case "IS NULL": */
                            $result = [$vals[2]];
//$hecho = true;
                            break;
                        case "LIKE":
                        case "NOT LIKE":
                            $result = ["%" . $vals[2] . "%"];
//$hecho = true;
                            break;
                    }

                    /* if (!$hecho) {
                      switch ($vals[0]) {
                      case "ISNULL":
                      case "NOT ISNULL":
                      //$result = array($vals[1]);
                      $hecho = true;
                      break;
                      }
                      } */
                }
            }
//print_r($result);
//echo "</br>";
            return $result;
        }
    }

    /*
      public function addCondicion($campo, $operador, $valor, $AndOr = "AND") {

      if (is_array($campo) && (!is_array($operador) || !is_array($valor) || !is_array($AndOr))) {
      $tempOperador = array();
      $tempValor = array();
      $tempAndOr = array();
      for ($n = 0; $n < sizeof($campo); $n++) {
      if (!is_array($operador)) {
      $tempOperador[] = $operador;
      }
      if (!is_array($valor)) {
      $tempValor[] = $valor;
      }
      if (!is_array($AndOr)) {
      $tempAndOr[] = $AndOr;
      }
      }
      if (!is_array($operador)) {
      $operador = $tempOperador;
      }
      if (!is_array($valor)) {
      $valor = $tempValor;
      }
      if (!is_array($AndOr)) {
      $AndOr = $tempAndOr;
      }
      }


      $this->bdCondiciones["campos"][] = $campo;
      $this->bdCondiciones["operadores"][] = $operador;
      $this->bdCondiciones["valores"][] = $valor;
      $this->bdCondiciones["andor"][] = $AndOr;

      /* if (is_array($campo) && (!is_array($operador) || !is_array($valor) || !is_array($AndOr))){
      print_r($this->bdCondiciones);
      } *//*
      } */

    public function addCondicionRelaciones($relaciones) {

        foreach ($relaciones as $key => $relacion) {
            $this->bdCondicionesRelaciones[$key]["campos"][] = $relacion["campos"];
            $this->bdCondicionesRelaciones[$key]["operadores"][] = $relacion["operadores"];
            $this->bdCondicionesRelaciones[$key]["valores"][] = $relacion["valores"];
            $this->bdCondicionesRelaciones[$key]["andor"][] = $relacion["andor"];
        }
    }

    public function getCondicionesRelaciones() {
        return $this->bdCondicionesRelaciones;
    }

    /**
     * @deprecated
     * @return type
     */
    public function getCondicionesJson() {
//return json_encode($this->bdCondiciones);
        if ($this->_qb) {
            return json_encode($this->_qb->getWhere());
        }
    }

    /**
     * @deprecated
     * @param type $condiciones
     */
    public function setCondicionesJson($condiciones) {
//$this->bdCondiciones = json_decode($condiciones, true);
//$this->bdCondiciones = $this->reparaCondiciones($this->bdCondiciones);
//echo "jal:".$condiciones;
//print_r(json_decode($condiciones, true));
//$this->bdCondiciones = $this->reparaCondiciones(json_decode($condiciones, true));
//$this->bdCondiciones = json_decode($condiciones, true);
        if ($this->_qb) {
            $this->_qb->setWhere(json_decode($condiciones));
        }
    }

    /**
     * @deprecated
     * @param type $condiciones
     * @return string
     */
    /* private function reparaCondiciones($condiciones) {
      $result = null;
      if (is_array($condiciones["campos"])) {
      for ($n = 0; $n < sizeof($condiciones["campos"]); $n++) {
      $temp = $this->reparaCondiciones(array(
      "campos" => $condiciones["campos"][$n],
      "operadores" => $condiciones["operadores"][$n],
      "valores" => $condiciones["valores"][$n],
      "andor" => $condiciones["andor"][$n]
      ));
      $condiciones["campos"][$n] = $temp["campos"];
      $condiciones["operadores"][$n] = $temp["operadores"];
      $condiciones["valores"][$n] = $temp["valores"];
      $condiciones["andor"][$n] = $temp["andor"];
      }
      $result = $condiciones;
      } else {
      if ($condiciones["operadores"] == "LIKE") {
      $condiciones["valores"] = "%" . $condiciones["valores"] . "%";
      }
      if (strpos($condiciones["valores"], "%") || $condiciones["valores"] == "%") {
      $condiciones["operadores"] = "LIKE";
      }

      if ($condiciones["valores"] == "null") {
      $condiciones["operadores"] = "IS NULL";
      $condiciones["valores"] = "";
      }
      if ($condiciones["valores"] == "notnull") {
      $condiciones["operadores"] = "IS NOT NULL";
      $condiciones["valores"] = "";
      }

      $result = $condiciones;
      }

      return $result;
      } */

    private function key2campo($campos, $vista, $encriptado = false) {
        $result = array();
        $tmp = $vista->getCampo($campos);

        if (strpos($tmp, ",") !== false) {
            $tmp = explode(",", $tmp);
        }

        if (is_array($tmp)) {
            $campoDef = "CONCAT(";
            foreach ($tmp as $value) {
                if ($encriptado) {
                    $campoDef .= "CONVERT(aes_decrypt(" . $value . ",'" . $this->getAESWord() . "') USING utf8)" . ", ' ', ";
                } else {
                    $campoDef .= $value . ", ' ', ";
                }
            }
            $campoDef = substr($campoDef, 0, strlen($campoDef) - 7) . ")";
            $result = $campoDef;
        } else {
            if ($encriptado) {
                $result = "CONVERT(aes_decrypt(" . $tmp . ",'" . $this->getAESWord() . "') USING utf8)";
            } else {
                $result = $vista->getCampo($campos);
            }
        }

        return $result;
    }

    /**
     * @todo eliminar la forma de guardar antigua (campos por un lado y sentidos por otro)
     *       ahora array asociativo con "campo"=>"sentido"
     * @param type $campos
     * @param type $sentido
     */
    public function setOrden($orden/* , $sentido=null */) { //Revisar NUEVO
        /* $result = array();
          //if (!is_null($sentido)){
          /* $result = array(
          "campos" => $campos,
          "sentido" => $sentido
          ); *//*

          //}else{
          if (is_array($orden)) {
          $result = $orden;
          }

          $this->bdOrden = $result; */
//print_r($this->bdOrden);
        if ($this->_qb) {
            $this->_qb->setOrderBy($orden);
        }
    }

    public function getOrden() {
        return $this->bdOrden;
    }

    public function getOrdenJson() {
        return json_encode($this->bdOrden, true);
    }

    public function setOrdenJson($orden) {
        //print_r($this->bdOrden);
        //$this->bdOrden = json_decode($orden, true);
        $this->setOrden(json_decode($orden, true));
    }

    public function setDistinct($distinct = false) {
//$this->bdDistinct = $distinct;
        if ($this->_qb) {
            $this->_qb->setDistinct($distinct);
        }
    }

    public function getDistinct() {
        if ($this->_qb) {
            return $this->_qb->getDistinct();
        }
    }

    public function setLimit($desde = 0, $cantidad = -1) {
        if ($this->_qb) {
            $this->_qb->setLimit($desde, $cantidad);
        }
        /* $this->bdLimitDesde = $desde;
          $this->bdLimitCantidad = $cantidad; */
    }

    public function getLimitDesde() {
        return $this->bdLimitDesde;
    }

    public function getLimitCantidad() {
        return $this->bdLimitCantidad;
    }

    /*     * ************************************************
     *  Tools
     * ************************************************* */

    /**
     * @todo revisar la asignacion $this->$key = $value; según tipos. boolean conflicitivo
     * @todo revisar si se sigue utilizando
     * @param type $resultadoBd
     */
    private function bd2Item($resultadoBd) {
        if (sizeof($resultadoBd) > 0) {

            $this->resetItem();

            foreach ($resultadoBd[0] as $key => $value) {
                if (is_bool($this->$key)) {
//echo "bool";
                    $value = $value == 0 ? false : true;
                }
                $this->$key = $value;
            }
        }
    }

    /**
     * Introduce en el array de items el contenido de un registro, y añade el 
     * indice en el array de indices de items
     * @param array $resultadoBd
     * @todo Hecho
     */
    private function bd2raw(&$items) {
        $this->_items = $items;
//print_r($this->_items);
//echo "\n\n";
        unset($items);
        for ($n = 0; $n < sizeof($this->_items); $n++) {
            $this->_indiceItems[$this->_items[$n]->id] = $n;
        }
    }

    public function raw2Item($id) {
        $result = null;
        if (array_key_exists($id, $this->_indiceItems)) {
            $itemTemp = new ItemModel($this->getClass(), $this->log);
            $itemTemp->setVista($this->getVista());
            $indice = $this->_indiceItems[$id];
            foreach ($this->_items[$indice] as $key => $valor) {
                $itemTemp->$key = $valor;
            }
            $result = $itemTemp;
        }
        return $result;
    }

    public function item2Raw($item = null, $id = null) {
        $result = null;

        if (is_null($item)) {
            $item = $this->_item;
        }

        if (!is_null($id) && array_key_exists($id, $this->_indiceItems)) {

            foreach ($item->getAtributos() as $atributo) {
                $this->_items[$this->_indiceItems[$id]]->$atributo = $item->$atributo;
            }
            $result = $id;
        } else {

//$item->id = max(array_keys($this->indiceItems))+1;
            /* $this->items[] = $item;
              $this->indiceItems[] = $item->id;
              $result = $item->id; */
            $indice = sizeof($this->_items);
            foreach ($item->getAtributos() as $atributo) {
//echo array_key_exists($indice, $this->_items)? "Si":"No";
                if (!array_key_exists($indice, $this->_items)) {
                    $this->_items[$indice] = new stdClass();
                    $this->_indiceItems[] = $indice;
                }
                $this->_items[$indice]->$atributo = $item->$atributo;
            }
            $result = $item->id;
        }
        return $result;
    }

    /**
     * @todo Revisar NO USADO
     * @todo Revisar si es necesario
     */
    /* private function formatoMysql($valor, $propiedad) {
      $result = "''";

      if (is_null($valor)) {
      $result = "NULL";
      } else {
      switch ($propiedad[vista::FLD_TIPO]) {
      case vista::TIPO_FECHA:
      if ($valor != "") {
      preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);

      if (sizeof($fechaTemp) > 0) {
      //$fechaEs=$fechaTemp[3]."-".$fechaTemp[2]."-".$fechaTemp[1];
      $fechaEs = $valor;
      } else {

      list($dia, $mes, $ano) = explode("/", $valor);
      $fechaEs = "$ano-$mes-$dia";
      }
      $result = "'" . $fechaEs . "'";
      } else {
      $result = "NULL";
      }
      break;
      case vista::TIPO_NUM:
      $result = $valor;
      break;

      case vista::TIPO_MONEDA:
      $result = $valor;//str_replace(",", ".", $valor);
      break;
      default:
      $result = "'" . $valor . "'";
      break;
      }
      }

      if (array_key_exists(vista::FLD_ENCRYPT, $propiedad) && $propiedad[vista::FLD_ENCRYPT]) {
      $result = "aes_encrypt(" . $result . ",'".$this->getAESWord()."')";
      }

      return $result;
      } */

    /* private function formatoRaw($valor, $propiedad) {
      $result = "";

      if (is_array($propiedad)) {
      $tipo = $propiedad[vista::FLD_TIPO];
      } else {
      $tipo = $propiedad;
      }
      switch ($tipo) {
      case vista::TIPO_FECHA:
      $result = Tools::fecha_es2mysql($valor);
      break;
      case vista::TIPO_CONTRASENA:
      $result = md5($valor);
      break;

      default:
      $result = $valor;
      break;
      }

      return $result;
      } */

    /**
     * @todo Revisar
     * @todo Revisar si es necesario
     */
    /* private function formatoCliente($valor, $propiedad, $form = false) {
      $result = "";

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
      case ModelView::TIPO_HORA_CORTA:
      $result = date("H:i", strtotime($valor));
      break;
      case ModelView::TIPO_TIMESTAMP:
      preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
      if (sizeof($fechaTemp) > 0) {
      $result = strtotime($valor);
      } else {
      $result = $valor;
      }
      break;
      case ModelView::TIPO_FLOAT:
      case ModelView::TIPO_MONEDA:
      $valor = str_replace(",", ".", $valor);
      $valor = number_format(floatval($valor), 2, ",", "");
      $result = $valor;
      break;
      case ModelView::TIPO_BOOLEAN:
      $result = (boolean) $valor;
      break;
      default:
      $result = $valor;
      break;
      }

      return $result;
      } */
    /*
      private function formatoClienteLista($valor, $propiedad, $form = false) {
      $result = "";

      if (is_array($propiedad)) {
      $tipo = $propiedad[ModelView::FLD_TIPO];
      } else {
      $tipo = $propiedad;
      $propiedad = $this->getVista()->getPropiedad($propiedad);
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
      case ModelView::TIPO_HORA:
      if ($valor) {
      $result = $valor . " h";
      } else {
      $result = "";
      }
      break;
      case ModelView::TIPO_HORA_CORTA:
      if ($valor) {
      $result = date("H:i", strtotime($valor)) . " h";
      } else {
      $result = "";
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
      case ModelView::TIPO_FLOAT:
      $valor = str_replace(",", ".", $valor);
      $result = number_format(floatval($valor), 2, ",", ".");
      break;
      case ModelView::TIPO_MONEDA:
      $valor = str_replace(",", ".", $valor);
      $valor = number_format(floatval($valor), 2, ",", ".");
      $result = $valor . " €";
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
      case ModelView::TIPO_OBJETO:
      $result = "";
      if ($valor > 0) {
      $campo = array_key_exists(ModelView::FLD_OBJETO_CAMPO, $propiedad) ? $propiedad[ModelView::FLD_OBJETO_CAMPO] : ModelView::PROP_NOMBRE;
      $result = array_key_exists(ModelView::FLD_LISTA, $propiedad) ? $propiedad[ModelView::FLD_LISTA][$valor]->$campo : $valor;
      }
      break;
      default:
      $result = $valor;
      break;
      }

      return $result;
      }
     */
    /**
     * @todo Revisar NO USADO
     * @todo Revisar si es necesario
     */
    /* private function formatoCustom($valor, $propiedad) { //deprecated
      $result = "''";

      switch ($propiedad) {
      case vista::TIPO_FECHA:
      preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
      if (sizeof($fechaTemp) > 0) {
      $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
      } else {
      $result = $valor;
      }
      break;
      case vista::TIPO_TIMESTAMP:
      preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,4})/", $valor, $fechaTemp);
      if (sizeof($fechaTemp) > 0) {
      $result = strtotime($fechaTemp[3] . "-" . $fechaTemp[2] . "-" . $fechaTemp[1]);
      } else {
      $result = $valor;
      }
      break;
      /* case vista::TIPO_ENUM:
      $result = null;
      $valoresPosibles = $this->getVistaItems()->getEnum($valor);
      print_r($valor);
      print_r($valoresPosibles); *//*
      /* if (array_key_exists($valor, $valoresPosibles)){
      $result=$valoresPosibles[$this->getVistaItems()->getEnum($valor)];
      } *//*


      break;
      default:
      $result = $valor;
      break;
      }

      return $result;
      } */



    /*
     * Varios
     */

    /**
     * @todo no se si será necesario...
     * @param type $objeto
     * @return \objeto
     */
    public function getObj($objeto) {
        $result = null;

        if (!class_exists($objeto)) {
            $this->log->LogError("Error intentando crear la clase $objeto. La clase no existe");
        } else {
            switch ($objeto) {
                /* case 'correoElectronico':
                  $result = new $objeto($this->bd, $this->getI18n(), $this->log);
                  $result->setMetodoEnvio(correoElectronico::METODO_ENVIO_PHPMAILER);
                  $result->setSMTPHost(CORREO_SMTP_HOST);
                  $result->setSMTPUser(CORREO_SMTP_USER);
                  $result->setSMTPPass(CORREO_SMTP_PASS);

                  $result->setFrom(CORREO_SMTP_FROM);
                  $result->setFromNombre(CORREO_SMTP_FROM_NAME);
                  break;

                  case 'notificaciones':
                  $result = new notificaciones($this->bd, $this->getI18n(), $this->log);
                  if ($this->getI18n()) {
                  $result->setIdioma($this->getIdiomaActual());
                  }
                  //$result->setCorreoElectronico($this->getObj("correoElectronico"));
                  break; */

                default:
                    $result = new $objeto($this->bd, $this->getI18n(), $this->log);
                    if ($this->getI18n()) {
                        $result->setIdioma($this->getIdiomaActual());
                    }
                    if (!is_null($this->getAESWord()) && method_exists($result, "getAESWord")) {
                        $result->setAESWord($this->getAESWord());
                    }

                    break;
            }
        }
        return $result;
    }

    public function getClass() {
        return get_called_class();
    }

    public function getModel() {
        return str_replace("Model", "", get_called_class());
    }

    private function tablaEsI18n($tabla, $vista) {
        return in_array($tabla . "_lang", $vista->getTablas());
    }

    public function outputJSON($datos, $tipo = null) {
        if (is_null($tipo)) {
            $tipo = $this->getModel();
        }
        if ($this->_returnHeader) {
            header('Content-type: application/json');
            echo json_encode([$tipo => json_encode($datos)]);
        } else {
            return $datos;
        }
    }

    /**
     * @description establece si se devuelve un json con headers o no, por si 
     * se utiliza con una llamada ajax o no
     * @param type $return
     */
    public function returnHeader($return = false) {
        $this->_returnHeader = $return;
    }

}
