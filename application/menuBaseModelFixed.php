<?php

class menuBaseModelFixed extends Model
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Configuración del modelo evitando problemas de carga de vistas
     */
    public function configModel(){
        // Inicializar array de vistas vacío para evitar errores
        $this->_vistas = array();

        // Intentar cargar la configuración normal, pero capturar errores
        try {
            parent::configModel();
        } catch (Exception $e) {
            // Si falla la configuración normal, configurar manualmente
            error_log("Problema en configModel, usando configuración manual: " . $e->getMessage());
            $this->configModelManual();
        }
    }

    /**
     * Configuración manual del modelo cuando falla la automática
     */
    private function configModelManual() {
        // Cargar solo las vistas esenciales manualmente
        $menuPath = ROOT . DIR_CORE . DS . DIR_MODELS . DS . 'menu' . DS;

        if (is_readable($menuPath . 'porRol.php')) {
            try {
                // Cargar la vista porRol manualmente
                require_once $menuPath . 'porRol.php';
                if (isset($vista) && is_object($vista)) {
                    $this->_vistas['porRol'] = $vista;
                }
            } catch (Exception $e) {
                error_log("Error cargando vista porRol: " . $e->getMessage());
            }
        }

        // Establecer vista por defecto
        $this->_vistaActual = 'porRol';
    }

    /**
     * Método porRol corregido para compatibilidad PHP 7.4
     */
    public function porRol($rol) {
        try {
            // Verificar si la vista porRol está disponible
            if (!isset($this->_vistas['porRol'])) {
                error_log("Vista porRol no disponible, usando método directo");
                return $this->porRolDirecto($rol);
            }

            $this->setVista("porRol");
            $this->setDistinct(true);

            // Usar sintaxis de array compatible con PHP 5.3
            $orden = array("orden" => "ASC");
            $this->setOrden($orden);

            // Configurar WHERE con sintaxis compatible
            $where = array("rol", "=", $rol);
            $this->setWhere($where);

            // Ejecutar consulta
            $resultado = $this->consulta();

            return $resultado;

        } catch (Exception $e) {
            // Log del error para debugging
            error_log("Error en menuBaseModelFixed::porRol(): " . $e->getMessage());
            // Fallback al método directo
            return $this->porRolDirecto($rol);
        }
    }

    /**
     * Método alternativo usando SQL directo si el modelo falla
     */
    public function porRolDirecto($rol) {
        try {
            $sql = "
                SELECT DISTINCT
                    sys_menu.id,
                    sys_menu.sys_nombre,
                    sys_menu.tipo,
                    sys_menu.padre,
                    sys_menu.desplegado,
                    sys_menu.enlace,
                    sys_menu.parametros,
                    sys_menu.titulo,
                    sys_menu.icono,
                    sys_menu.es_separador,
                    sys_menu.blank,
                    sys_menu.orden
                FROM sys_menu
                INNER JOIN sys_menu_permisos ON sys_menu.id = sys_menu_permisos.id_sys_menu
                INNER JOIN sys_roles ON sys_roles.id = sys_menu_permisos.id_sys_roles
                WHERE sys_roles.id = ?
                ORDER BY sys_menu.orden ASC
            ";

            $db = new Database();
            $stmt = $db->prepare($sql);
            $stmt->execute(array($rol));

            $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Convertir a formato compatible con el modelo original
            $this->_items = array();
            foreach ($resultados as $item) {
                $this->_items[] = $item;
            }

            return $resultados;

        } catch (Exception $e) {
            error_log("Error en menuBaseModelFixed::porRolDirecto(): " . $e->getMessage());
            return array();
        }
    }
}
?>
