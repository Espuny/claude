<?php

abstract class StandaloneModel extends Model {
    public function __construct($i18n = false, $logger = null) {
        parent::__construct($i18n,$logger,true);
    }

    private function getParentClass() {
        return get_parent_class(get_parent_class(get_called_class()));
    }

    
    public function setVista($nombreVista = self::VISTA_BASE) {
        $this->_vistaActual = $nombreVista;
        $this->cargaVistaLista($nombreVista);

        if (!isset($this->_item)) {
            $this->initItem();
        }
        $this->_item->setVista($this->_vistas[$this->_vistaActual]);
        $this->setOrden($this->getVista()->getOrderBy());
    }
    
    public function setOrden($orden) {
        
    }
    
    public function query($query = null) {

    }

    public function consulta() {

    }

    public function cuenta() {

    }

    public function inserta() {

    }

    public function actualiza() {
        
    }

    public function guarda() {

    }

    public function guardaItems() {

    }

    public function carga($campo, $valor) {

    }

    public function cargaId($id) {
        
    }

    public function borra($borraItem=true, $getQuery = false) {

    }

    public function borraItems() {
        
    }

    /**
     * @deprecated sustituir por $this->getVistaItems()->getTablas()
     * @param type $vista
     * @return type
     */
    private function getTablas($vista) {
        return [];
    }


    private function getCampos($vista, $paraInsert = true, $incluirId = false, $visibilidad = ModelView::VIS_TOTAL) {
        /*
         * Eliminar en Model
         */
    }

    private function getCamposInsert($vista, $i18n = false) {
         /*
         * Eliminar en Model
         */
    }

    private function getCondicionesCampos($vista, $condicionesCampos = null) {

    }

    public function setWhere($condiciones) {
        
    }

    public function addWhere($condiciones, $operador = "AND") {
       
    }

    public function setWhereJson($condiciones) {
        
    }

    public function addWhereJson($condiciones, $operador = "AND") {
        
    }

    public function getWhereJson() {
        
    }
}
