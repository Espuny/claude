<?php

require_once ROOT . DIR_LIBS . DS . 'iscLogger' . DS . 'iscLogger.php';

class QueryBuilder {

    /**
     * @todo condiciones-relaciones
     * @todo i18n
     * @var type 
     */
    private $_query;
    private $_modelView;
    private $_modelFields;
    private $_tablas;
    private $_distinct;
    private $_campos;
    private $_relaciones; //todo
    private $_condicionesRelaciones; //todo
    private $_condiciones;
    private $_groupBy;
    private $_orderBy;
    private $_limiteDesde;
    private $_limiteCantidad;

    //private $_tiposExcluidos;

    const JOIN = "join";
    const CLAVE_PRIMARIA = "clave_primaria";
    const CLAVE_AJENA = "clave_ajena";
    const JOIN_INNERJOIN = "INNER JOIN";
    const JOIN_LEFTJOIN = "LEFT JOIN";
    const JOIN_RIGHTJOIN = "RIGHT JOIN";
    const JOIN_TABLA = "join_tabla";
    const JOIN_ALIAS = "join_alias";

    //protected $_log;

    public function __construct() {
        $this->init();

        $this->_log = new iscLogger();
    }

    function init() {
        $this->_modelView = null;
        $this->_modelFields = null;
        $this->_query = null;
        $this->_tablas = null;
        $this->_campos = null;
        $this->_relaciones = null;
        $this->reset();
    }

    function reset() {
        $this->_distinct = null;
        $this->_condiciones = null;
        $this->_groupBy = null;
        $this->_orderBy = null;
        $this->_limiteDesde = 0;
        $this->_limiteCantidad = -1;
    }

    /* private function _campoEsExcluido($campo){
      return in_array($this->_modelView->getTipo($campo), $this->_tiposExcluidos);
      } */

    /*
     * get-set
     */

    public function getModelFields() {
        return $this->_modelFields;
    }

    public function setModelFields($modelFiels) {
        $this->_modelFields = $modelFiels;
    }

    public function getModelView() {
        return $this->_modelView;
    }

    public function setModelView($modelView) {

        $this->_modelView = $modelView;

        $this->_query = $this->_modelView->getQuery();
        $this->_tablas = $this->_modelView->getTablas();
        $this->_distinct = $this->_modelView->getDistinct();
        $this->_campos = $this->_modelView->getCampos(true);
        $this->_relaciones = $this->_modelView->getRelaciones();
        $this->_groupBy = $this->_modelView->getGroupBy();
        $this->_orderBy = $this->_modelView->getOrderBy();
    }

    public function setTablas($tablas) {
        $result = array();

        if (!is_array($tablas) && !is_null($tablas)) {
            $result[$tablas] = $tablas;
        } else {
            $result = $tablas;
        }

        $this->_tablas = $result;
    }

    public function getTablas() {
        return $this->_tablas;
    }

    public function setDistinct($set = true) {
        $this->_distinct = $set;
    }

    public function getDistinct($set = true) {
        return $this->_distinct;
    }

    public function setCampos($campos = null) {
        $result = array();

        if (!is_array($campos) && !is_null($campos)) {
            $result[$campos] = $campos;
        } else {
            $result = $campos;
        }

        $this->_campos = $result;
    }

    public function getCampos() {
        return $this->_campos;
    }

    public function setRelaciones($relaciones) {
        $this->_relaciones = is_array($relaciones) ? $relaciones : null;
    }

    public function getRelaciones() {
        return $this->_relaciones;
    }

    public function setWhere($condiciones = null) {
        $result = [];

        if (is_null($condiciones) || !is_array($condiciones)) {
            $result = null;
        } else {
            $result = $condiciones;
        }

        $this->_condiciones = $result;
    }

    public function addWhere($condiciones, $operador = "AND") {
        $result = [];
        $actual = $this->_condiciones;

        if (is_null($condiciones) || !is_array($condiciones) || sizeof($condiciones) == 0) {
            $result = $actual;
        } else {
            if (is_null($actual)) {
                $result = $condiciones;
            } else {
                $result = [$actual, $operador, $condiciones];
            }
        }

        $this->_condiciones = $result; //$this->fixLike($result);
    }

    public function getWhere() {
        return $this->_condiciones;
    }

    public function getValues($condicion = null) {
        $result = [];

        $vals = is_null($condicion) ? $this->_condiciones : $condicion;

        if (is_array($vals) && count($vals)) {
            if (in_array("AND", $vals) || in_array("OR", $vals)) {
                $result = [];
                foreach ($vals as $index => $condicion) {
                    if ($index % 2 == 0) {
                        $result = array_merge($result, $this->getValues($condicion));
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
                        $result = [$vals[2]];
                        break;
                    case "LIKE":
                    case "NOT LIKE":
                        $result = ["%" . $vals[2] . "%"];
                        break;
                }
            }
        }

        return $result;
    }

    public function setGroupBy($campos = null) {
        $result = array();

        if (!is_array($campos) && !is_null($campos)) {
            $result[$campos] = $campos;
        } else {
            $result = $campos;
        }

        $this->_groupBy = $result;
    }

    public function getGroupBy() {
        return $this->_groupBy;
    }

    public function setOrderBy($campos = null) {
        $result = null;
        $campos = is_array($campos) ? $campos : null;

        if (!is_null($campos) && sizeof($campos) > 0) {
            $result = $campos;
        }

        $this->_orderBy = $result;
    }

    public function getOrderBy() {
        return $this->_orderBy;
    }

    public function setLimit($desde = 0, $cantidad = -1) {
        $this->_limiteDesde = $desde;
        $this->_limiteCantidad = $cantidad;
    }

    public function getLimit() {
        return [$this->_limiteDesde, $this->_limiteCantidad];
    }

    public function getQuery(){
        return $this->_query;
    }
    /*
     * Private
     */

    private function _getTablaPrincipal() {
        return sizeof($this->_tablas) > 0 ? array_values($this->_tablas)[0] : null;
    }

    private function _getTablas() {
        $result = null;
        if ((is_null($this->_getRelaciones()) || $this->_getRelaciones() == "") && sizeof($this->_tablas) > 0) {
            $result = implode(", ", $this->_tablas);
        } else {
            $result = $this->_getRelaciones();
        }
        return $result;
    }

    /**
     * @todo implementar campos sin id y condiciones_relaciones
     * @param type $as
     * @return type+
     */
    private function _getRelaciones() {
        $result = "";

        if (is_array($this->_relaciones) && sizeof($this->_relaciones) > 0 && !is_null($this->_relaciones)) {
            $flag = false;
            foreach ($this->_relaciones as $tabla => $relacion) {
                if (!$flag) {
                    $result .= $tabla . " ";
                    $flag = true;
                } else {
                    $tbl = array_key_exists(self::JOIN_TABLA, $relacion) ? $relacion[self::JOIN_TABLA] : $tabla;
                    $alias = array_key_exists(self::JOIN_ALIAS, $relacion) ? " " . $relacion[self::JOIN_ALIAS] : "";
                    $result .= $relacion["join"] . " " . $tbl . $alias . " ON " . $relacion[self::CLAVE_PRIMARIA] . "=" . $relacion[self::CLAVE_AJENA] . " ";
                    /* if (is_array($this->query_condiciones_relaciones) && array_key_exists($tabla, $this->query_condiciones_relaciones)) {
                      $result .= $this->query_condiciones_relaciones[$tabla] . " ";
                      } */
                }
            }
        }

        return $result;
    }

    private function _getDistinct($set = true) {
        return $this->_distinct ? "DISTINCT " : "";
    }

    /**
     * @todo implementar campos sin id
     * @param type $as
     * @return type+
     */
    private function _getCampos($as = false, $inclId = true) {
        $result = "*";

        $this->_campos = $this->_modelView->getCampos($inclId);
        if (sizeof($this->_campos) > 0) {

            $tmp = array();
            foreach ($this->_campos as $key => $campo) {
                if (($key != "id" || ($key == "id" && $inclId)) && $campo && !$this->_modelView->campoEsExcluido($key)) {
                    if ($as) {
                        if ($this->_modelView->getEncriptacion($key)) {
                            if (strpos($campo, "CONCAT") == -1) {
                                $campo = "CAST(aes_decrypt($campo,'" . AES_WORD . "') AS CHAR)";
                            } else {
                                $campoTmp = str_replace("CONCAT(", "", $campo);
                                $campoTmp = str_replace(")", "", $campoTmp);
                                $campoTmp = str_replace("\" \",", "", $campoTmp);

                                $t = [];
                                foreach (array_map('trim', explode(",", $campoTmp)) as $c) {
                                    $t[] = "CAST(aes_decrypt($c,'" . AES_WORD . "')  AS CHAR)";
                                    $t[] = "' '";
                                }
                                array_pop($t);

                                $campo = "CONCAT(" . implode(", ", $t) . ")";
                            }
                        }
                        $campo .= " as " . $key;
                    }
                    $tmp[] = $campo;
                }
            }

            $result = implode(", ", $tmp);

            //$result = $tmp;
        }

        return $result;
    }

    private function _getValues($inclId = true) {
        $res = "*";

        if (sizeof($this->_campos) > 0) {
            $res = [];
            foreach ($this->_campos as $key => $valor) {
                if (($key != "id" || ($key == "id" && $inclId)) && $valor && !$this->_modelView->campoEsExcluido($key)) {
                    if ($this->_modelView->getEncriptacion($key)) {
                        $res[$key] = "aes_encrypt(?,'" . AES_WORD . "')";
                    } else {
                        $res[$key] = "?";
                    }
                }
            }
        }

        return $res;
    }

    private function _getValuesInsert() {
        $res = null;
        $tmp = $this->_getValues(false);
        if (is_array($tmp)) {
            $res = implode(", ", $this->_getValues(false));
        }
        return $res;
    }

    private function _getValuesUpdate() {
        $res = null;
        $tmp = $this->_getValues(false);
        $camposBD = $this->getCampos();

        if (is_array($tmp)) {
            $res = [];
            foreach ($tmp as $campo => $valor) {
                $res[] = $camposBD[$campo] . "=" . $valor;
            }
            $res = implode(", ", $res);
        }
        return $res;
    }

    private function _getCampoId($as = true) {
        $result = "*";

        if (sizeof($this->_campos) > 0) {
            if ($as) {
                $result = array_key_exists("id", $this->_campos) ? $this->_campos["id"] : null;
            } else {
                $result = array_key_exists("id", $this->_campos) ? "id" : null;
            }
        }
        return $result;
    }

    /**
     * @todo revisar tipos y operadores (completar) reescribir cuando tengamos la info de la vista completa para
     *       las tablas de cada campo. ahora se hace desde Model en getWhere
     * @param type $where
     * @return string
     */
    private function _getWhere($where = null) {
        $result = "";

        $where = is_null($where) ? $this->_condiciones : $where;
        //print_r($where);
        if (is_array($where)) {
            if (in_array("AND", $where) || in_array("OR", $where)) {
                $result = " (";
                /* echo "tamaño: ".sizeof($where)."</br>"; */
                //print_r($where);
                foreach ($where as $index => $condicion) {
                    //echo $index."</br>";
                    if ($index % 2 == 0) {
                        $result .= $this->_getWhere($where[$index]) . " ";
                    } else {
                        $result .= $where[$index] . " ";
                    }
                }
                $result .= ") ";
            } else {
                $hecho = false;

                switch ($where[1]) {
                    case "=":
                    case "<":
                    case ">":
                    case "<=":
                    case ">=":
                    case "!=":
                    case "<>":
                    /* case "IS NOT":
                      case "IS NULL":
                      $where[2] = "?";
                      $result = "(" . implode(" ", $where) . ")";
                      $hecho = true;
                      break; */
                    case "LIKE":
                    case "NOT LIKE":
                        //echo $where[0];
                        $campo = $this->getModelView()->getCampo($where[0]);
                        if ($this->_modelView->getEncriptacion($where[0])) {
                            //$where[0] = "LOWER(aes_decrypt($campo,'" . AES_WORD . "'))";
                            //$where[0] = "aes_decrypt($campo,'" . AES_WORD . "')";
                            if (strpos($campo, "CONCAT") == -1) {
                                $where[0] = "CAST(aes_decrypt($campo,'" . AES_WORD . "') AS CHAR)";
                            } else {
                                $campoTmp = str_replace("CONCAT(", "", $campo);
                                $campoTmp = str_replace(")", "", $campoTmp);
                                $campoTmp = str_replace("\" \",", "", $campoTmp);

                                $t = [];
                                foreach (array_map('trim', explode(",", $campoTmp)) as $c) {
                                    $t[] = "CAST(aes_decrypt($c,'" . AES_WORD . "')  AS CHAR)";
                                    $t[] = "' '";
                                }

                                $where[0] = "CONCAT(" . implode(", ", $t) . ")";
                            }
                        } else {
                            $where[0] = $campo;
                        }
                        $where[2] = "?";
                        $result = "(" . implode(" ", $where) . ")";
                        $hecho = true;
                        break;
                }

                if (!$hecho) {
                    $campo = $this->getModelView()->getCampo($where[1]);
                    if ($this->_modelView->getEncriptacion($where[1])) {
                        $campo = "CAST(aes_decrypt($campo,'" . AES_WORD . "') AS CHAR)";
                    }
                    switch ($where[0]) {
                        case "ISNULL":
                            $result = "($campo IS NULL)";
                            break;
                        case "NOT ISNULL":
                            $result = "($campo NOT IS NULL)";
                            break;
                    }
                }
            }
        }

        return $result;
    }
    
    

    private function _getGroupBy() {
        return is_array($this->_groupBy) ? "GROUP BY " . implode(", ", $this->_groupBy) . " " : "";
    }

    private function _getOrderBy() {
        $result = "";

        if (is_array($this->_orderBy)) {
            $tmp = array();
            foreach ($this->_orderBy as $campo => $sentido) {
                if ($this->_modelView->getEncriptacion($campo)) {
                    $nombreCampo = $this->_modelView->getCampo($campo);
                    //echo $campo;
                    if (strpos($nombreCampo, "CONCAT") == -1) {
                        $campoDef = "CAST(aes_decrypt($nombreCampo,'" . AES_WORD . "') AS CHAR)";
                    } else {
                        $campoTmp = str_replace("CONCAT(", "", $nombreCampo);
                        $campoTmp = str_replace(")", "", $campoTmp);
                        $campoTmp = str_replace("\" \",", "", $campoTmp);

                        $t = [];
                        foreach (array_map('trim', explode(",", $campoTmp)) as $c) {
                            $t[] = "CAST(aes_decrypt($c,'" . AES_WORD . "')  AS CHAR)";
                            $t[] = "' '";
                        }
                        array_pop($t);

                        $campoDef = "CONCAT(" . implode(", ", $t) . ")";
                    }
                    $tmp[] = $campoDef . ' ' . $sentido;
                } else {
                    $tmp[] = $campo . ' ' . $sentido;
                }
            }
            $result = "ORDER BY " . implode(", ", $tmp) . " ";
        }

        return $result;
    }

    private function _getLimit() {
        $result = "";

        if ($this->_limiteDesde !== 0 || $this->_limiteCantidad !== -1) {
            $result .= "LIMIT ";
        }
        if ($this->_limiteDesde !== 0) {
            $result .= $this->_limiteDesde;
        }
        if ($this->_limiteDesde !== 0 && $this->_limiteCantidad !== -1) {
            $result .= ", ";
        }
        if ($this->_limiteCantidad !== -1) {
            $result .= $this->_limiteCantidad . " ";
        }
        return $result . " ";
    }
    
    private function _getQuery(){
        return $this->_query;
    }

    
    /*
     * Generación de consultas
     */

    /**
     * @todo query en modo MODE_QUERY
     * @return string
     */
    public function insert() {
        $query = null;

        if (sizeof($this->_tablas) > 0) {
            $query = "";
            $query .= "INSERT INTO ";
            $query .= $this->_getTablaPrincipal() . " ";
            $query .= "(" . $this->_getCampos(false, false) . ") ";
            $query .= "VALUES ";
            //$query.= "(".implode(", ",array_fill(0,sizeof($this->_campos),"?")).")";            
            $query.= "(" . $this->_getValuesInsert() . ")";
        }
        $this->_log->LogDebug($query);

        return trim($query);
    }

    /**
     * @todo revisar excepciones como numero de valores != numero de campos
     * @return string
     */
    public function update() {
        $query = null;

        if (sizeof($this->_tablas) > 0) {
            $set = [];
            $campos = $this->_getCampos(false, false);

            for ($n = 0; $n < sizeof($campos); $n++) {
                $set[] = $campos[$n] . " = ?";
            }

            $query = "";
            $query .= "UPDATE ";
            $query .= $this->_getTablaPrincipal() . " ";
            $query .= "SET ";
            $query .= $this->_getValuesUpdate();
            $where = $this->_getWhere();
            if ($where != "") {
                $query .= " ";
                $query .= "WHERE ";
                $query .= $where;
            }
        }

        $this->_log->LogDebug($query);

        return trim($query);
    }

    public function select() {
        $query = null;

        $query = "";
        if (is_null($this->_getQuery())){
            $query .= "SELECT ";
            $query .= $this->_getDistinct();
            $query .= $this->_getCampos(true) . " ";
            $query .= "FROM ";
            $query .= $this->_getTablas() . " ";
        }else{
            $query.= $this->_getQuery()." ";
        }
        $where = $this->_getWhere();
        if ($where != "") {
            $query .= "WHERE ";
            $query .= $where . " ";
        }
        $query .= $this->_getGroupBy();
        $query .= $this->_getOrderBy();
        $query .= $this->_getLimit();
        //echo $query;
        //$this->_log->LogDebug($query);

        return trim($query);
    }

    public function cuenta() {
        $query = null;

        $query = "";
        $query .= "SELECT ";
        $query .= $this->_getDistinct();
        $query .= "COUNT(" . $this->_getCampoId() . ") as num ";
        $query .= "FROM ";
        $query .= $this->_getTablas() . " ";
        $where = $this->_getWhere();
        if ($where != "") {
            $query .= "WHERE ";
            $query .= $where . " ";
        }
        //$query .= $this->_getLimit();
        //$this->_log->LogDebug($query);

        return trim($query);
    }

    public function delete() {
        $query = null;

        $query = "";
        $query .= "DELETE FROM ";
        $query .= $this->_getTablaPrincipal();
        $where = $this->_getWhere();
        if ($where != "") {
            $query .= " ";
            $query .= "WHERE ";
            $query .= $where;
        }

        $this->_log->LogDebug($query);

        return trim($query);
    }

}
