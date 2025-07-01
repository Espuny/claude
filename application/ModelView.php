<?php
/**
 * @todo simplificar
 * @todo ver posibilidad de renombrar la clase
 * @todo renombrar los metodos in camelCase
 * @todo renombrar los atributos private a $_
 */
class ModelView {

    private $nombre;
    private $tablas;
    private $tabla_principal;
    private $relaciones;
    private $groupBy;
    private $tituloObjeto;
    private $ordenListado;
    private $sentidoOrdenListado;
    private $busquedaListado;
    private $busquedaSortable;
    public $propiedades;
    public $modelFields;
    private $filtros;
    private $_camposBusqueda;
    private $_distinct = false;
    //private $_modelFields;
    
    private $_query;
    
    private $_orderBy;

    private $botonNuevo=true;
    
    private $_campoTitulo;
    
    


    /*
     * Propiedades estandar
     */
    private $PROP_lista;

    const PROP_ID = "id";
    const PROP_SECURE_ID = "secure_id";
    const PROP_REFERENCIA = "referencia";
    const PROP_IDIOMA = "idioma";
    const PROP_TITULO = "titulo";
    const PROP_NOMBRE = "nombre";
    const PROP_NOMBRECOMPLETO = "nombre_completo";
    const PROP_DESCRIPCION = "descripcion";
    const PROP_CONTENIDO = "contenido";
    const PROP_SYS_NOMBRE = "sys_nombre";
    const PROP_VISIBILIDAD = "visibilidad";
    const PROP_ID_USUARIO = "id_usuario";
    const PROP_FECHA = "fecha";
    const PROP_HORA = "hora";
    const PROP_HORA_CORTA = "hora_corta";
    const PROP_GUARDADO = "guardado";
    const PROP_PARAMETROS = "parametros";
    const PROP_ICONO_EDITAR = "editar";
    const PROP_ICONO_BORRAR = "borrar";
    const PROP_ICONO_BORRAR_MINI = "borrar_mini";
    

    /*
     * Campos a configurar para cada una de las propiedades
     */

    private $FLD_lista;

    const FLD_TABLA = "tabla";
    const FLD_CAMPO = "campo";
    const FLD_FUNCION = "funcion";
    const FLD_TITULO = "titulo";
    const FLD_DEFECTO = "defecto";
    const FLD_VISIBILIDAD = "visibilidad";
    const FLD_TIPO = "tipo";
    const FLD_OBJETO = "objeto";
    const FLD_OBJETO_CAMPO = "objeto_campo";
    const FLD_ADD_METODO = "add_metodo";
    const FLD_REMOVE_METODO = "remove_metodo";
    const FLD_REFRESH_CONTROLADOR = "refresh_controlador";
    const FLD_REFRESH_METODO = "refresh_metodo";
    const FLD_CONDICION = "condicion";
    const FLD_ENUM = "lista";
    const FLD_READWRITE = "read_write";
    const FLD_READONLY = "read_only";
    const FLD_ENCRYPT = "encrypt";
    const FLD_FILE_UPLOAD_DIR = "file_upload_dir";
    const FLD_FILE_UPLOAD_DIR_ID = "file_upload_dir_id";
    const FLD_FILE_UPLOAD_MAXSIZE = "file_upload_maxsize";
    const FLD_IMAGE_UPLOAD_MAXWIDTH = "image_upload_maxwidth";
    const FLD_IMAGE_UPLOAD_MAXHEIGHT = "image_upload_maxheight";
    const FLD_IMAGE_UPLOAD_MINWIDTH = "image_upload_minwidth";
    const FLD_IMAGE_UPLOAD_MINHEIGHT = "image_upload_minheight";
    const FLD_IMAGE_UPLOAD_WIDTH = "image_upload_width";
    const FLD_IMAGE_UPLOAD_HEIGHT = "image_upload_height";
    const FLD_FILE_UPLOAD_EXTENSIONS = "file_upload_extensions";
    const FLD_FILE_UPLOAD_DATA_ID = "file_upload_data_id";
    const FLD_FILE_UPLOAD_DATA_PARENT = "file_upload_data_parent";
    const FLD_FILE_UPLOAD_AUTORENAME = "file_upload_autorename";
    const FLD_IMAGE_MAX_WIDTH = "image_max_width";
    const FLD_IMAGE_MAX_HEIGHT = "image_max_height";
    const FLD_I18N = "i18n";
    const FLD_SORTABLE = "sortable";
    const FLD_UNICO = "unico";
    const FLD_UNICO_OBJ = "unico_obj";
    const FLD_UNICO_CAMPO = "unico_campo";
    const FLD_LISTA = "lista";
    const FLD_ICONO = "icono";
    const FLD_MAX = "maximo";
    const FLD_MIN = "minimo";
    const FLD_CALCULADO = "calculado";
    const FLD_EXTRADATA = "extra_data";
    const FLD_UNIDADES = "unidades";
    const FLD_AUTONUMERICO = "autonumerico";
    const FLD_MODELVIEW = "modelview";

    /*
     * Tipos de visibilidad
     */

    private $VIS_lista;

    const VIS_SYSTEM = -100;
    const VIS_OCULTO = -1;
    const VIS_TOTAL = 10;
    const VIS_ID = 20;
    const VIS_SUPERADMIN = 30;
    const VIS_AUTO = 40;
    const VIS_ADMIN = 50;
    const VIS_USUARIOAVANZADO = 55;
    const VIS_USUARIOPLUS = 58;
    const VIS_USUARIO = 60;
    const VIS_FRONTEND = 100;

    /*
     * Tipos
     */

    private $TIPO_lista;

    const TIPO_TEXTO = "text";
    const TIPO_FECHA = "date";
    const TIPO_HORA = "time";
    const TIPO_HORA_CORTA = "time_corta";
    const TIPO_CORREO = "email";
    const TIPO_URL = "url";
    const TIPO_NUM = "number";
    const TIPO_FLOAT = "float";
    const TIPO_AREATEXTO = "textarea";
    const TIPO_BOOLEAN = "boolean";
    const TIPO_CONTRASENA = "password";
    const TIPO_OCULTO = "hidden";
    
    const TIPO_TEXTOLARGO = "texto_largo";
    const TIPO_TEXTOCORTO = "texto_corto";    
    const TIPO_TEXTOPLANO = "texto_plano";
    const TIPO_DNI = "dni";
    const TIPO_MONEDA = "moneda";
    const TIPO_MONEDA_EXT = "moneda_ext";
    const TIPO_USUARIO = "usuario";

    const TIPO_OBJETO = "obj";
    const TIPO_OBJETO_EXT = "obj_ext";
    const TIPO_OBJETOMULTI = "objmulti";
    const TIPO_OBJETOMULTI_EXT = "objmulti_ext";
    const TIPO_IDIOMA = "idioma";
    const TIPO_REFERENCIA = "referencia";
    const TIPO_IMAGEN = "imagen";
    const TIPO_ARCHIVO = "archivo";
    const TIPO_SCRIPT = "script";  
    const TIPO_TIMESTAMP = "timestamp";
    const TIPO_ENUM = "enum";
    const TIPO_FECHAHORA = "fecha_hora";
    const TIPO_CONSTANTE = "constante";
    const TIPO_ICONO = "icono";
    const TIPO_COLOR = "color";
    
    const TIPO_TIEMPO = "tiempo";
    
    private $_tiposExcluidos;
    
    const FILTRO_NORMAL = "filtro_normal";
    const FILTRO_IGUAL = "filtro_igual";
    const FILTRO_LIKE = "filtro_like";
    const FILTRO_ENTRE = "filtro_entre";
    const FILTRO_BUSQUEDA = "filtro_busqueda";
    const FILTRO_SEPARADOR = "filtro_separador";

    /*
     * Lectura-escritura
     */
    const RW_LECTURA = "lectura";
    const RW_ESCRITURA = "escritura";

    /*
     * Multiidioma
     */

    private $i18n;
    private $idioma;

    const TBL_TABLA = "tabla";
    const TBL_I18N = "i18n";

    /*
     * Relaciones entre tablas
     */
    const CLAVE_PRIMARIA = "clave_primaria";
    const CLAVE_AJENA = "clave_ajena";
    const JOIN_INNERJOIN = "INNER JOIN";
    const JOIN_LEFTJOIN = "LEFT JOIN";
    const JOIN_RIGHTJOIN = "RIGHT JOIN";
    const JOIN_TABLA = "join_tabla";
    const JOIN_ALIAS = "join_alias";

    /*
     * Constructor
     */

    function __construct($nombre="base") {
        $nombre = explode(DS,str_replace(".php", "", $nombre));
        $this->nombre=  array_pop( $nombre);
        $this->config();
    }

    /*
     * Inicializacion
     */

    public function config() {
        $this->setI18n(false);
        
        $this->_tiposExcluidos = [
            ModelView::TIPO_OBJETOMULTI,
            ModelView::TIPO_OBJETOMULTI_EXT
            ];
    }

    /*
     * Revisar incorporación automática de las constantes a los arrays
     */

    private function init_listas() {

        /* $constantes = get_defined_constants(true);

          foreach ($constantes["user"] as $constante => $valor) {

          } */

        $this->VIS_lista = array(
            self::VIS_SYSTEM,
            self::VIS_OCULTO,
            self::VIS_TOTAL,
            self::VIS_ID,
            self::VIS_ADMIN,
            self::VIS_SUPERADMIN,
            self::VIS_AUTO,
            self::VIS_USUARIO,
            self::VIS_FRONTEND,
        );
        $this->TIPO_lista = array(
            self::TIPO_NUM,
            self::TIPO_FECHA,
            self::TIPO_FECHAHORA,
            self::TIPO_HORA,
            self::TIPO_CORREO,
            self::TIPO_TEXTO,
            self::TIPO_TEXTOLARGO,
            self::TIPO_AREATEXTO,
            self::TIPO_OBJETO,
            self::TIPO_OBJETOMULTI,
            self::TIPO_BOOLEAN,
            self::TIPO_IDIOMA,
            self::TIPO_REFERENCIA,
            self::TIPO_IMAGEN,
            self::TIPO_SCRIPT,
            self::TIPO_MONEDA,
            self::TIPO_TIMESTAMP,
            self::TIPO_ENUM,
            self::TIPO_DNI,
            self::TIPO_FECHAHORA,
            self::TIPO_CONSTANTE
        );
        
        
    }

    
    public function campoEsExcluido($campo){
        return in_array($this->getTipo($campo), $this->_tiposExcluidos);
    }
    
    /*
     * Tablas
     */

    public function getNombre() {
        return $this->nombre;
    }
    
    public function getTablas() {
        return $this->tablas;
    }
    
    

    public function getTablaPrincipal() {
        $result = "";
        if (!is_null($this->tabla_principal)) {
            $result = $this->tabla_principal;
        } else {
            if (sizeof($this->tablas) > 0) {
                $result = $this->tablas[0];
            }
        }
        return $result;
    }

    public function setTablas($tablas) {
        $result = null;

        if (!is_null($tablas) && $tablas != "") {
            if ($this->getI18n()) {
                if (is_array($tablas)) {

                    $result = array();
                    $relaciones = array();

                    foreach ($tablas as $tabla => $valores) {
                        $result[] = $tabla;
                        if (sizeof($relaciones) == 0) {
                            $relaciones[$tabla] = null;
                        }
                        if ($valores[self::TBL_I18N]) {
                            $result[] = $tabla . "_lang";

                            $relaciones[$tabla . "_lang"] = array(
                                self::JOIN => self::JOIN_LEFTJOIN,
                                self::CLAVE_PRIMARIA => $tabla . ".id",
                                self::CLAVE_AJENA => $tabla . "_lang" . ".id_" . $tabla,
                            );
                        }
                    }
                    $this->setRelaciones($relaciones);
                }
            } else {
                if (is_array($tablas)) {
                    $result = $tablas;
                } else {
                    $result = array($tablas);
                }
            }
        }
        $this->tablas = $result;
        return $result;
    }

    public function setTablaPrincipal($tabla = "") {

        $this->tabla_principal = $tabla;
    }
    
    public function getQuery() {
        return $this->_query;
    }
    
    public function setQuery($query){
        $this->_query = $query;
    }
    
    public function setCampoTitulo($campo){
        $this->_campoTitulo = $campo;
    }
    
    public function getCampoTitulo(){
        $res = null;
        if (!is_null($this->_campoTitulo) && $this->existePropiedad($this->_campoTitulo)){
            $res = $this->_campoTitulo;
        }else if ($this->existePropiedad(self::PROP_TITULO)){
            $res = self::PROP_TITULO;
        }else if ($this->existePropiedad(self::PROP_NOMBRE)){
            $res = self::PROP_NOMBRE;
        }
        return $res;
    }

    /*
     * Idiomas
     */

    public function setI18n($i18n) {
        $this->i18n = $i18n;
    }

    public function getI18n($propiedad = null) {
        $result = null;
        if (is_null($propiedad)) {
            $result = $this->i18n;
        } else {
            $result = $this->getValorEnField($propiedad, self::FLD_I18N);
        }
        return $result;
    }

    public function getIdioma() {
        return $this->idioma;
    }

    public function setIdioma($idioma) {
        $this->idioma = $idioma;
    }
    
    public function getFiltros() {
        return $this->filtros;
    }

    public function setFiltros($filtros) {
        $this->filtros = $filtros;
    }
    
    public function getBotonNuevo() {
        return $this->botonNuevo;
    }

    public function setBotonNuevo($mostraNuevo=true) {
        $this->botonNuevo = $mostraNuevo;
    }

    /*
     * Propiedades
     */
    
    public function setDistinct($distinct = "") {

        $this->_distinct = $distinct;
    }
    
    public function getDistinct() {

        return $this->_distinct;
    }

    public function setPropiedades($propiedades) {
        $result = null;

        if (is_array($propiedades)) {
            $result = array();
            foreach ($propiedades as $propiedad => $valores) {
                $this->setPropiedad($propiedad,$valores);
            }
        } else {
            $this->propiedades = null;
        }
    }
    
    public function setPropiedad($propiedad, $valores){
        if (is_null($valores)){
            echo "Error de propiedad en vista: ".$propiedad."<br/>";
        }
        if (!array_key_exists(ModelView::FLD_TABLA, $valores)) {
            $valores[ModelView::FLD_TABLA] = $this->getTablaPrincipal();
        }
        if (array_key_exists(ModelView::FLD_I18N, $valores)) {
            $valores[ModelView::FLD_TABLA].="_lang";
        }
        $this->propiedades[$propiedad] = $valores;
    }
    
    public function setModelFields($fields) {
        $result = null;

        if (is_array($fields)) {
            $result = array();
            foreach ($fields as $field => $valores) {
                $this->setModelField($field,$valores);
            }
        } else {
            $this->modelFields = null;
        }
    }
    
    public function setModelField($field, $valores){
        if (is_null($valores)){
            echo "Error de propiedad comun: ".$field."<br/>";
        }
        if (!array_key_exists(ModelView::FLD_TABLA, $valores)) {
            $valores[ModelView::FLD_TABLA] = $this->getTablaPrincipal();
        }
        if (array_key_exists(ModelView::FLD_I18N, $valores)) {
            $valores[ModelView::FLD_TABLA].="_lang";
        }
        $this->modelFields[$field] = $valores;
    }
    
    public function setCampo($propiedad, $campo, $valor){
        if (array_key_exists($propiedad, $this->propiedades)){
            $this->propiedades[$propiedad][$campo] = $valor;
        }
    }

    public function getPropiedadJson($propiedad) {
        return json_encode($this->propiedades[$propiedad]);
    }

    public function getPropiedades() {
        return $this->propiedades;
    }
    
    public function existePropiedad($propiedad){
        return array_key_exists($propiedad, $this->propiedades);
    }

    /*
     * Relaciones
     */

    public function getRelaciones() {
        $result = null;

        if (!is_array($this->relaciones) && !is_null($this->relaciones)) {
            $result = array($this->relaciones);
        } else {
            $result = $this->relaciones;
        }

        return $result;
    }

    public function setRelaciones($relaciones) {
        $result = null;

        if (!is_null($relaciones)) {
            if (is_array($relaciones)) {
                if (!is_null($this->relaciones)) {
                    $result = array_merge($relaciones, $this->relaciones);
                } else {
                    $result = $relaciones;
                }
            } else {
                $rel = new bdRelaciones;
                $result = $rel->get($relaciones);
            }
        }

        /* if (!is_null($relaciones) && $relaciones!=""){
          $result=$relaciones;
          } */

        $this->relaciones = $result;
        return $result;
    }

    /*
     * Agrupaciones (necesaria para los campos COUNT)
     */

    public function getGroupBy() {
        $result = null;

        if (!is_array($this->groupBy) && !is_null($this->groupBy)) {
            $result = array($this->groupBy);
        } else {
            $result = $this->groupBy;
        }

        return $result;
    }

    public function setGroupBy($groupBy) {
        $result = null;

        if (!is_null($groupBy) && $groupBy != "") {
            $result = $groupBy;
        }

        $this->groupBy = $result;

        return $result;
    }

    /*
     * Titulo
     */

    /**
     * @todo revisar tituloobjeto, etc
     * @return type
     */
    public function getTituloVista() {

        return $this->tituloObjeto;
    }
    /**
     * @deprecated
     * @return type
     */
    public function getTituloObjeto() {

        return $this->tituloObjeto;
    }

    public function setTituloObjeto($titulo) {

        $this->tituloObjeto = $titulo;
    }

    /*
     * Manejo de propiedades
     */


    /*
     * Revisar exceptions
     */

    private function getValorEnField($propiedad, $field) {
        $result = null;

        if (!is_null($this->propiedades) || !is_null($this->modelFields)) {
            if (array_key_exists($propiedad, $this->propiedades) || array_key_exists($propiedad, $this->modelFields)) {
                $propCompleta = array_key_exists($propiedad, $this->propiedades)? $this->propiedades[$propiedad] : $this->modelFields[$propiedad];
                if (array_key_exists($field, $propCompleta)) {
                    if ($field == self::FLD_CAMPO) {
                        //Se añade la funcion en caso de que exista
                        if (array_key_exists(self::FLD_FUNCION, $propCompleta)) {
                            if (!is_array($propCompleta[self::FLD_FUNCION])) {
                                $tabla = array_key_exists(self::FLD_TABLA, $propCompleta) ? $propCompleta[self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";

                                //$tabla .=  $this->propiedades[$propiedad][self::FLD_I18N]? "_lang" : "";

                                if (is_array($propCompleta[$field])) {
                                    /*foreach ($this->propiedades[$propiedad][$field] as $key => $value) {
                                        //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                        $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                        $this->propiedades[$propiedad][$field][$key] = $tablaTemp . $value;
                                    }
                                    //$result = implode(",",$this->propiedades[$propiedad][$field]);
                                    $result = $this->propiedades[$propiedad][$field];*/
                                    $result = [];
                                    foreach ($propCompleta[$field] as $key => $value) {
                                        $c = $this->getValorEnField($value, self::FLD_CAMPO);
                                        $result[] = $c;//? $c:$value;
                                    }
                                    
                                } else {
                                    //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                    $tabla = strpos($propCompleta[$field], ".") ? "" : $tabla;
                                    $result = $tabla . $propCompleta[$field];
                                }
                                switch ($propCompleta[self::FLD_FUNCION]) {
                                    case 'CONCAT':
                                        if (is_array($result)) {
                                            //$result = implode(", \" \", ", $this->propiedades[$propiedad][$field]);
                                            $result = implode(", \" \", ",$result);
                                        }
                                        $result = "CONCAT(" . $result . ")";
                                        break;
                                    case 'SUMA':
                                        if (is_array($result)) {
                                            $result = implode("+", $propCompleta[$field]);
                                        }
                                        $result = "(" . $result . ")";
                                        break;
                                    case 'IS NULL':
                                        //$result = "(".$result." IS NULL) ";
                                        if (is_array($result)) {
                                            $result = implode(", ", $propCompleta[$field]);
                                        }
                                        $result = "ISNULL(" . $result . ") ";
                                        break;
                                    case 'NOT IS NULL':
                                        //$resultTemp = "(".$resultTemp." IS NULL) ";
                                        if (is_array($result)) {
                                            $result = implode(", ", $propCompleta[$field]);
                                        }
                                        $result = "NOT ISNULL(" . $result . ") ";
                                        break;
                                    case 'COUNT IF FALSE':
                                        $result = "SUM(ISNULL(" . $result . ") OR " . $result . "=0)";
                                        break;
                                    case 'COUNT IF TRUE':
                                        $result = "SUM(NOT ISNULL(" . $result . ") OR " . $result . "=1)";
                                        break;
                                    /*case 'IF IS NULL':
                                        $result =  "(".$result . " IS NULL)";
                                        break;*/
                                    default:
                                        if (is_array($result)) {
                                            $result = implode(", ", $propCompleta[$field]);
                                        }
                                        $result = $propCompleta[self::FLD_FUNCION] . "(" . $result . ")";

                                        break;
                                }
                            } else {//hay varias funciones
                                
                                $tabla = array_key_exists(self::FLD_TABLA, $propCompleta) ? $propCompleta[self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";
                                //$tabla .=  $this->propiedades[$propiedad][self::FLD_I18N]? "_lang" : "";
                                if ($propCompleta[self::FLD_FUNCION][0]=="IF"){
                                    
                                    if ($propCompleta[self::FLD_TIPO]==self::TIPO_ICONO){
                                        $result = "'".$propCompleta[self::FLD_ICONO]."'";
                                        $result = "IF(".$this->getValorEnField($propCompleta[self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $propCompleta[self::FLD_FUNCION][2].
                                                $propCompleta[self::FLD_FUNCION][3].",".
                                                $result.",'')";
                                    }else if ($propCompleta[self::FLD_TIPO]==self::TIPO_CONSTANTE){
                                        $result = "IF(".$this->getValorEnField($propCompleta[self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $propCompleta[self::FLD_FUNCION][2].
                                                $propCompleta[self::FLD_FUNCION][3].",".
                                                "'".$propCompleta[self::FLD_CAMPO]."'".",'')";
                                    }else{
                                        $result = "IF(".$this->getValorEnField($propCompleta[self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $propCompleta[self::FLD_FUNCION][2].
                                                $propCompleta[self::FLD_FUNCION][3].",".
                                                $propCompleta[self::FLD_CAMPO].",'')";
                                    }
                                }else{
                                    $resultTemp = "";


                                    for ($n = 0; $n < sizeof($propCompleta[self::FLD_FUNCION]); $n++) {

                                        if (is_array($propCompleta[$field][$n])) {
                                            foreach ($propCompleta[$field][$n] as $key => $value) {
                                                //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                                $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                                $propCompleta[$field][$n][$key] = $tablaTemp . $value;
                                            }
                                            $resultTemp = $propCompleta[$field][$n];
                                        } else {
                                            //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                            $tabla = strpos($propCompleta[$field][$n], ".") ? "" : $tabla;
                                            $resultTemp = $tabla . $propCompleta[$field][$n];
                                        }
                                        switch ($propCompleta[self::FLD_FUNCION][$n]) {
                                            case 'CONCAT':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", \" \", ", $propCompleta[$field][$n]);
                                                }
                                                $resultTemp = "CONCAT(" . $resultTemp . ")";
                                                break;
                                            case 'IS NULL':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $propCompleta[$field][$n]);
                                                }
                                                //$resultTemp = "(".$resultTemp." IS NULL) ";
                                                $resultTemp = "ISNULL(" . $resultTemp . ") ";
                                                break;
                                            case 'NOT IS NULL':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $propCompleta[$field][$n]);
                                                }
                                                //$resultTemp = "(".$resultTemp." IS NULL) ";
                                                $resultTemp = "NOT ISNULL(" . $resultTemp . ") ";
                                                break;
                                            default:
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $propCompleta[$field][$n]);
                                                }
                                                $resultTemp = $propCompleta[self::FLD_FUNCION][$n] . "(" . $resultTemp . ")";
                                                break;
                                        }

                                        $result .= $resultTemp . " AND ";
                                        $result = substr($result, 0, strlen($result) - 5);
                                    }

                                    
                                    //echo $result;
                                }
                            }
                        }else{//No se ha definido funcion
                            //Se añade la tabla al campo en caso de que este presente en los campos el campo tabla (cuidado con los campos concatenados)
                            switch ($propCompleta[self::FLD_TIPO]){
                                case self::TIPO_CONSTANTE:
                                    $result = "'".$propCompleta[$field]."'";
                                    break;
                                case self::TIPO_ICONO:
                                    //$result = "'".$this->propiedades[$propiedad][$field]."'";
                                    $result = "'".$propCompleta[self::FLD_ICONO]."'";
                                    //$result = "'<img src=\"".$this->propiedades[$propiedad][$field]."\" class=\"".$propiedad."\"  title=\"".$this->propiedades[$propiedad][self::FLD_TITULO]."\">'";
                                    break;
                                default:
                                   $tabla = array_key_exists(self::FLD_TABLA, $propCompleta) ? $propCompleta[self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";

                                    if (is_array($propCompleta[$field])) {
                                        foreach ($propCompleta[$field] as $key => $value) {
                                            //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                            $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                            $propCompleta[$field][$key] = $tablaTemp . $value;
                                        }
                                        $result = implode(",", $propCompleta[$field]);
                                    } else {
                                        //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                        $tabla = strpos($propCompleta[$field], ".") ? "" : $tabla;
                                        $result = $tabla . $propCompleta[$field];
                                    } 
                            }
                        }
                    }else {
                        $result = $propCompleta[$field];
                    }
                }
            } else {
                //excepcion
            }
        } else {
            //excepcion
        }

        return $result;
    }
 /*   
    private function getValorEnModelField($propiedad, $field) {
        $result = null;

        if (!is_null($this->_)) {
            if (array_key_exists($propiedad, $this->propiedades)) {
                if (array_key_exists($field, $this->propiedades[$propiedad])) {
                    if ($field == self::FLD_CAMPO) {
                        //Se añade la funcion en caso de que exista
                        if (array_key_exists(self::FLD_FUNCION, $this->propiedades[$propiedad])) {
                            if (!is_array($this->propiedades[$propiedad][self::FLD_FUNCION])) {
                                $tabla = array_key_exists(self::FLD_TABLA, $this->propiedades[$propiedad]) ? $this->propiedades[$propiedad][self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";

                                //$tabla .=  $this->propiedades[$propiedad][self::FLD_I18N]? "_lang" : "";

                                if (is_array($this->propiedades[$propiedad][$field])) {
                                    /*foreach ($this->propiedades[$propiedad][$field] as $key => $value) {
                                        //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                        $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                        $this->propiedades[$propiedad][$field][$key] = $tablaTemp . $value;
                                    }
                                    //$result = implode(",",$this->propiedades[$propiedad][$field]);
                                    $result = $this->propiedades[$propiedad][$field];*//*
                                    $result = [];
                                    foreach ($this->propiedades[$propiedad][$field] as $key => $value) {
                                        $c = $this->getValorEnField($value, self::FLD_CAMPO);
                                        $result[] = $c;//? $c:$value;
                                    }
                                    
                                } else {
                                    //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                    $tabla = strpos($this->propiedades[$propiedad][$field], ".") ? "" : $tabla;
                                    $result = $tabla . $this->propiedades[$propiedad][$field];
                                }
                                switch ($this->propiedades[$propiedad][self::FLD_FUNCION]) {
                                    case 'CONCAT':
                                        if (is_array($result)) {
                                            //$result = implode(", \" \", ", $this->propiedades[$propiedad][$field]);
                                            $result = implode(", \" \", ",$result);
                                        }
                                        $result = "CONCAT(" . $result . ")";
                                        break;
                                    case 'SUMA':
                                        if (is_array($result)) {
                                            $result = implode("+", $this->propiedades[$propiedad][$field]);
                                        }
                                        $result = "(" . $result . ")";
                                        break;
                                    case 'IS NULL':
                                        //$result = "(".$result." IS NULL) ";
                                        if (is_array($result)) {
                                            $result = implode(", ", $this->propiedades[$propiedad][$field]);
                                        }
                                        $result = "ISNULL(" . $result . ") ";
                                        break;
                                    case 'NOT IS NULL':
                                        //$resultTemp = "(".$resultTemp." IS NULL) ";
                                        if (is_array($result)) {
                                            $result = implode(", ", $this->propiedades[$propiedad][$field]);
                                        }
                                        $result = "NOT ISNULL(" . $result . ") ";
                                        break;
                                    case 'COUNT IF FALSE':
                                        $result = "SUM(ISNULL(" . $result . ") OR " . $result . "=0)";
                                        break;
                                    case 'COUNT IF TRUE':
                                        $result = "SUM(NOT ISNULL(" . $result . ") OR " . $result . "=1)";
                                        break;
                                    /*case 'IF IS NULL':
                                        $result =  "(".$result . " IS NULL)";
                                        break;*//*
                                    default:
                                        if (is_array($result)) {
                                            $result = implode(", ", $this->propiedades[$propiedad][$field]);
                                        }
                                        $result = $this->propiedades[$propiedad][self::FLD_FUNCION] . "(" . $result . ")";

                                        break;
                                }
                            } else {//hay varias funciones
                                
                                $tabla = array_key_exists(self::FLD_TABLA, $this->propiedades[$propiedad]) ? $this->propiedades[$propiedad][self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";
                                //$tabla .=  $this->propiedades[$propiedad][self::FLD_I18N]? "_lang" : "";
                                if ($this->propiedades[$propiedad][self::FLD_FUNCION][0]=="IF"){
                                    
                                    if ($this->propiedades[$propiedad][self::FLD_TIPO]==self::TIPO_ICONO){
                                        $result = "'".$this->propiedades[$propiedad][self::FLD_ICONO]."'";
                                        $result = "IF(".$this->getValorEnField($this->propiedades[$propiedad][self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][2].
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][3].",".
                                                $result.",'')";
                                    }else if ($this->propiedades[$propiedad][self::FLD_TIPO]==self::TIPO_CONSTANTE){
                                        $result = "IF(".$this->getValorEnField($this->propiedades[$propiedad][self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][2].
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][3].",".
                                                "'".$this->propiedades[$propiedad][self::FLD_CAMPO]."'".",'')";
                                    }else{
                                        $result = "IF(".$this->getValorEnField($this->propiedades[$propiedad][self::FLD_FUNCION][1], self::FLD_CAMPO).
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][2].
                                                $this->propiedades[$propiedad][self::FLD_FUNCION][3].",".
                                                $this->propiedades[$propiedad][self::FLD_CAMPO].",'')";
                                    }
                                }else{
                                    $resultTemp = "";


                                    for ($n = 0; $n < sizeof($this->propiedades[$propiedad][self::FLD_FUNCION]); $n++) {

                                        if (is_array($this->propiedades[$propiedad][$field][$n])) {
                                            foreach ($this->propiedades[$propiedad][$field][$n] as $key => $value) {
                                                //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                                $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                                $this->propiedades[$propiedad][$field][$n][$key] = $tablaTemp . $value;
                                            }
                                            $resultTemp = $this->propiedades[$propiedad][$field][$n];
                                        } else {
                                            //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                            $tabla = strpos($this->propiedades[$propiedad][$field][$n], ".") ? "" : $tabla;
                                            $resultTemp = $tabla . $this->propiedades[$propiedad][$field][$n];
                                        }
                                        switch ($this->propiedades[$propiedad][self::FLD_FUNCION][$n]) {
                                            case 'CONCAT':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", \" \", ", $this->propiedades[$propiedad][$field][$n]);
                                                }
                                                $resultTemp = "CONCAT(" . $resultTemp . ")";
                                                break;
                                            case 'IS NULL':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $this->propiedades[$propiedad][$field][$n]);
                                                }
                                                //$resultTemp = "(".$resultTemp." IS NULL) ";
                                                $resultTemp = "ISNULL(" . $resultTemp . ") ";
                                                break;
                                            case 'NOT IS NULL':
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $this->propiedades[$propiedad][$field][$n]);
                                                }
                                                //$resultTemp = "(".$resultTemp." IS NULL) ";
                                                $resultTemp = "NOT ISNULL(" . $resultTemp . ") ";
                                                break;
                                            default:
                                                if (is_array($resultTemp)) {
                                                    $resultTemp = implode(", ", $this->propiedades[$propiedad][$field][$n]);
                                                }
                                                $resultTemp = $this->propiedades[$propiedad][self::FLD_FUNCION][$n] . "(" . $resultTemp . ")";
                                                break;
                                        }

                                        $result .= $resultTemp . " AND ";
                                        $result = substr($result, 0, strlen($result) - 5);
                                    }

                                    
                                    //echo $result;
                                }
                            }
                        }else{//No se ha definido funcion
                            //Se añade la tabla al campo en caso de que este presente en los campos el campo tabla (cuidado con los campos concatenados)
                            switch ($this->propiedades[$propiedad][self::FLD_TIPO]){
                                case self::TIPO_CONSTANTE:
                                    $result = "'".$this->propiedades[$propiedad][$field]."'";
                                    break;
                                case self::TIPO_ICONO:
                                    //$result = "'".$this->propiedades[$propiedad][$field]."'";
                                    $result = "'".$this->propiedades[$propiedad][self::FLD_ICONO]."'";
                                    //$result = "'<img src=\"".$this->propiedades[$propiedad][$field]."\" class=\"".$propiedad."\"  title=\"".$this->propiedades[$propiedad][self::FLD_TITULO]."\">'";
                                    break;
                                default:
                                   $tabla = array_key_exists(self::FLD_TABLA, $this->propiedades[$propiedad]) ? $this->propiedades[$propiedad][self::FLD_TABLA] . "." : $this->getTablaPrincipal() . ".";

                                    if (is_array($this->propiedades[$propiedad][$field])) {
                                        foreach ($this->propiedades[$propiedad][$field] as $key => $value) {
                                            //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                            $tablaTemp = strpos($value, ".") ? "" : $tabla;
                                            $this->propiedades[$propiedad][$field][$key] = $tablaTemp . $value;
                                        }
                                        $result = implode(",", $this->propiedades[$propiedad][$field]);
                                    } else {
                                        //determinamos si añadimos el prefijo en funcion de si tiene puesto el nombre de la tabla a mano en el campo FLD_CAMPO
                                        $tabla = strpos($this->propiedades[$propiedad][$field], ".") ? "" : $tabla;
                                        $result = $tabla . $this->propiedades[$propiedad][$field];
                                    } 
                            }
                        }
                    }else {
                        $result = $this->propiedades[$propiedad][$field];
                    }
                }
            } else {
                //excepcion
            }
        } else {
            //excepcion
        }

        return $result;
    }
*/
    /*
     * Revisar exceptions
     */

    private function getValoresEnField($field, $visibilidad, $incluirId, $todos = true, $i18n = false) {
        $result = array();

        if (!is_null($field)) {
            foreach ($this->propiedades as $propiedad => $fields) {
                if ($fields[self::FLD_VISIBILIDAD] >= $visibilidad) {

                    if (($incluirId && $propiedad == self::PROP_ID) || $propiedad != self::PROP_ID) {
                        if ($todos) {
                            $result[$propiedad] = $this->getValorEnField($propiedad, $field);
                        } else {

                            $esI18n = array_key_exists(self::FLD_I18N, $fields) && $fields[self::FLD_I18N];
                            //if ((!$i18n && !$fields[self::FLD_I18N]) || ($i18n && $fields[self::FLD_I18N])){
                            if (!$i18n && !$esI18n || ($i18n && $esI18n)) {
                                $result[$propiedad] = $this->getValorEnField($propiedad, $field);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function esI18n($propiedad) {
        return array_key_exists(self::FLD_I18N, $this->propiedades[$propiedad]) && $this->propiedades[$propiedad][self::FLD_I18N];
    }
    
    public function getTabla($propiedad) {
        //echo "jal".$propiedad. " ". $this->getValorEnField($propiedad, self::FLD_CAMPO)."<br/>";
        return $this->getValorEnField($propiedad, self::FLD_TABLA);
    }

    public function getCampo($propiedad) {
        //echo "jal".$propiedad. " ". $this->getValorEnField($propiedad, self::FLD_CAMPO)."<br/>";
        return $this->getValorEnField($propiedad, self::FLD_CAMPO);
    }

    public function getCampos($incluirId = false, $visibilidad = self::VIS_TOTAL, $todos = true, $i18n = false) {
        $field = self::FLD_CAMPO;
        $campos = $this->getValoresEnField($field, $visibilidad, $incluirId, $todos, $i18n);
        return $campos;
    }

    public function getTitulo($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_TITULO);
    }

    public function getTitulos($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_TITULO;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getDefecto($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_DEFECTO);
    }

    public function getDefectos($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_DEFECTO;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getVisibilidad($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_VISIBILIDAD);
    }

    public function getVisibilidades($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_VISIBILIDAD;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getEncriptacion($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_ENCRYPT);
    }

    public function getEncriptaciones($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_ENCRYPT;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getTipo($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_TIPO);
    }

    public function getTipos($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_TIPO;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getObjeto($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_OBJETO);
    }

    public function getObjetos($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_OBJETO;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getEnum($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_ENUM);
    }
    
    public function setEnum($propiedad,$valor){
        $result = null;

        if (!is_null($this->propiedades)) {
            if (array_key_exists($propiedad, $this->propiedades)) {
                $this->propiedades[$propiedad][ModelView::FLD_ENUM] = $valor;
            }
        }
    }

    public function getEnums($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_ENUM;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getFileUploadDir($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_FILE_UPLOAD_DIR);
    }

    public function getFileUploadDirs($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_FILE_UPLOAD_DIR;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getImageMaxWidth($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_IMAGE_MAX_WIDTH);
    }

    public function getImageMaxWidths($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_IMAGE_MAX_WIDTH;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    public function getImageMaxHeigth($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_IMAGE_MAX_HEIGHT);
    }

    public function getImageMaxHeigths($incluirId = false, $visibilidad = self::VIS_TOTAL) {

        $field = self::FLD_IMAGE_MAX_HEIGHT;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }
    
    public function getUnidad($propiedad) {
        return $this->getValorEnField($propiedad, self::FLD_UNIDADES);
    }

    public function getUnidades($incluirId = false, $visibilidad = self::VIS_TOTAL) {
        $field = self::FLD_UNIDADES;
        return $this->getValoresEnField($field, $visibilidad, $incluirId);
    }

    /*
     * Deprecated
     */

    public function get_titulos($visibilidad) {
        $temp = array();
        foreach ($this->propiedades as $key => $value) {
            if ($value["visibilidad"] >= $visibilidad) {
                $temp[] = $value["titulo"];
            }
        }
        return $temp;
    }

    public function get_campos_as($visibilidad) {
        $temp = array();
        //echo var_dump($this->propiedades);
        foreach ($this->propiedades as $key => $value) {
            //echo $visibilidad.$value["campo"]."<br/><br/>";
            if ($value["visibilidad"] >= $visibilidad) {
                $temp[] = $value["campo"] . " as " . $key;
            }
        }
        //print_r($temp);
        return $temp;
    }

    public function get_campos($visibilidad) {
        $temp = array();
        //echo var_dump($this->propiedades);
        foreach ($this->propiedades as $key => $value) {
            //echo $visibilidad.$value["campo"]."<br/><br/>";
            if ($value["visibilidad"] >= $visibilidad) {
                $temp[] = $value["campo"];
            }
        }
        //print_r($temp);
        return $temp;
    }

    public function get_campos_noid($visibilidad) {
        $temp = array();
        //echo var_dump($this->propiedades);
        foreach ($this->propiedades as $key => $value) {
            if ($key != "id") {
                if ($value["visibilidad"] >= $visibilidad) {
                    $temp[] = $value["campo"];
                }
            }
        }
        return $temp;
    }

    public function get_campos_as_noid($visibilidad) {
        $temp = array();
        //echo var_dump($this->propiedades);
        foreach ($this->propiedades as $key => $value) {
            if ($key != "id") {
                if ($value["visibilidad"] >= $visibilidad) {
                    $temp[] = $value["campo"] . " as " . $key;
                }
            }
        }
        return $temp;
    }

    public function get_tipos($visibilidad) {
        $temp = array();
        foreach ($this->propiedades as $key => $value) {
            if ($value["visibilidad"] >= $visibilidad) {
                $temp[] = $value["tipo"];
            }
        }
        return $temp;
    }

    public function get_tipo($campo) {
        $result = null;

        if (array_key_exists($campo, $this->propiedades)) {
            $result = $this->propiedades[$campo]["tipo"];
        }

        return $result;
    }

    public function get_tipo_objeto($campo) {
        $result = null;

        if (array_key_exists($campo, $this->propiedades)) {
            $result = $this->propiedades[$campo]["objeto"];
        }

        return $result;
    }

    public function get_campo_objeto($campo) {
        $result = null;

        if (array_key_exists($campo, $this->propiedades)) {
            $result = $this->propiedades[$campo]["obj_propiedad"];
        }

        return $result;
    }

    public function get_objetos($visibilidad) {
        $temp = array();
        foreach ($this->propiedades as $key => $value) {
            if ($value["visibilidad"] >= $visibilidad) {
                if (array_key_exists("objeto", $value)) {
                    $temp[] = $value["objeto"];
                } else {
                    $temp[] = null;
                }
            }
        }
        return $temp;
    }

    public function get_condiciones($visibilidad) {
        $temp = array();
        foreach ($this->propiedades as $key => $value) {
            if ($value["visibilidad"] >= $visibilidad) {
                if (array_key_exists("condicion", $value)) {
                    $temp[] = $value["condicion"];
                } else {
                    $temp[] = null;
                }
            }
        }
        return $temp;
    }

    public function get_visibilidades($visibilidad) {
        $temp = array();
        foreach ($this->propiedades as $key => $value) {
            if ($value["visibilidad"] >= $visibilidad) {
                $temp[] = $value["visibilidad"];
            }
        }
        return $temp;
    }

    private function get_bd_table() {
        return $this->bd_tablas;
    }


    public function set_propiedades_relacionadas($propiedades) {
        $result = null;

        if (is_array($propiedades)) {
            $result = $propiedades;
        } else {
            $result = null;
        }

        $this->propiedades_relacionadas = $result;
    }

    public function set_campos($campos) {
        $result = null;

        if (is_null($campos) || $campos == "") {
            $result = array("*");
        } else {
            $result = $campos;
        }

        $this->bd_campos = $result;
    }
    
    public function setOrderBy($campos=null){
        $result = null;
        $campos = is_array($campos)? $campos:null;
        
        if (!is_null($campos)){
            $result=$campos;
        }
        
        $this->_orderBy = $result;
    }
    
    public function getOrderBy(){
        return $this->_orderBy;
    }
    
    public function getOrderByJson(){
        return json_encode($this->_orderBy,true);
    }

    
    public function getCamposBusqueda(){
        return $this->_camposBusqueda;
    }
    
    public function setCamposBusqueda($campos){
        if (is_array($campos)){
            $this->_camposBusqueda = $campos;
            
        }else{
            $this->_camposBusqueda[] = $campos;
        }       
    }
    /**
     * @deprecated
     * @return type
     */
    public function getBusquedaListado() {
        $result = null;

        if (!is_array($this->busquedaListado) && !is_null($this->busquedaListado)) {
            $result = array($this->busquedaListado);
        } else {
            $result = $this->busquedaListado;
        }

        return $result;
    }

    /**
     * @deprecated
     * @param type $busquedaListado
     * @return type
     */
    public function setBusquedaListado($busquedaListado) {
        $result = null;

        if (!is_null($busquedaListado) && $busquedaListado != "") {
            $result = $busquedaListado;
        }

        $this->busquedaListado = $result;

        return $result;
    }

    /**
     * @deprecated
     * @return type
     */
    public function getBusquedaSortable() {
        $result = null;

        if (!is_array($this->busquedaSortable) && !is_null($this->busquedaSortable)) {
            $result = array($this->busquedaSortable);
        } else {
            $result = $this->busquedaSortable;
        }

        return $result;
    }
    /**
     * @deprecated
     * @param type $busquedaSortable
     * @return type
     */
    public function setBusquedaSortable($busquedaSortable) {
        $result = null;

        if (!is_null($busquedaSortable) && $busquedaSortable != "") {
            $result = $busquedaSortable;
        }

        $this->busquedaSortable = $result;

        return $result;
    }

    public function getPropiedad($propiedad) {
        $result = null;

        if (!is_null($this->propiedades) && array_key_exists($propiedad, $this->propiedades)) {
            $result = $this->propiedades[$propiedad];
        }elseif (!is_null($this->modelFields) && array_key_exists($propiedad, $this->modelFields)) {
            $result = $this->modelFields[$propiedad];
        }

        return $result;
    }

}

?>