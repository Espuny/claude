<?php

/**
 * Adaptador para integrar el MenuHelper con Smarty Templates
 * Permite usar el sistema de menús corregido con las plantillas existentes
 */
class MenuSmartyAdapter {

    private $menuHelper;

    public function __construct() {
        require_once 'application/MenuHelper.php';
        $this->menuHelper = new MenuHelper();
    }

    /**
     * Obtiene el menú en formato compatible con Smarty
     * Retorna un array de objetos con la estructura esperada por las plantillas
     */
    public function getMenuParaSmarty($rolId) {
        try {
            $menuItems = $this->menuHelper->getMenuPorRol($rolId);

            // Convertir a formato esperado por Smarty
            $menuParaSmarty = array();

            foreach ($menuItems as $item) {
                // Crear objeto compatible con el template existente
                $menuItem = new stdClass();
                $menuItem->id = $item->id;
                $menuItem->sys_nombre = $item->sys_nombre;
                $menuItem->titulo = $item->titulo;
                $menuItem->enlace = $item->enlace;
                $menuItem->icono = $item->icono;
                $menuItem->es_separador = $item->es_separador;
                $menuItem->padre = $item->padre;
                $menuItem->blank = $item->blank;
                $menuItem->orden = $item->orden;

                $menuParaSmarty[] = $menuItem;
            }

            return $menuParaSmarty;

        } catch (Exception $e) {
            error_log('Error en MenuSmartyAdapter: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Obtiene el menú jerárquico para Smarty
     */
    public function getMenuJerarquicoParaSmarty($rolId) {
        try {
            $menuItems = $this->menuHelper->getMenuPorRol($rolId);
            $menuJerarquico = $this->menuHelper->organizarMenuJerarquico($menuItems);

            return $this->convertirParaSmarty($menuJerarquico);

        } catch (Exception $e) {
            error_log('Error en MenuSmartyAdapter jerárquico: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Convierte objetos para que sean compatibles con Smarty
     */
    private function convertirParaSmarty($items) {
        $resultado = array();

        foreach ($items as $item) {
            $menuItem = new stdClass();
            $menuItem->id = $item->id;
            $menuItem->sys_nombre = $item->sys_nombre;
            $menuItem->titulo = $item->titulo;
            $menuItem->enlace = $item->enlace;
            $menuItem->icono = $item->icono;
            $menuItem->es_separador = $item->es_separador;
            $menuItem->padre = $item->padre;
            $menuItem->blank = $item->blank;
            $menuItem->orden = $item->orden;

            // Convertir hijos si existen
            if (isset($item->hijos) && count($item->hijos) > 0) {
                $menuItem->hijos = $this->convertirParaSmarty($item->hijos);
            } else {
                $menuItem->hijos = array();
            }

            $resultado[] = $menuItem;
        }

        return $resultado;
    }

    /**
     * Método estático para uso directo en controladores
     */
    public static function obtenerMenuUsuario($rolId = null) {
        if (!$rolId) {
            $rolId = Session::get('id_sys_roles');
        }

        if (!$rolId) {
            return array();
        }

        $adapter = new self();
        return $adapter->getMenuParaSmarty($rolId);
    }
}
