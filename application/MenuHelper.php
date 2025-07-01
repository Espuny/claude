<?php

class MenuHelper {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Obtiene los elementos de menú para un rol específico
     */
    public function getMenuPorRol($rolId) {
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
            WHERE sys_roles.id = :rol_id
            ORDER BY sys_menu.orden ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':rol_id', $rolId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Alias para compatibilidad
     */
    public function obtenerMenuPorRol($rolId) {
        return $this->getMenuPorRol($rolId);
    }

    /**
     * Organiza los elementos de menú en una estructura jerárquica
     */
    public function organizarMenuJerarquico($menuItems) {
        $menu = [];
        $itemsIndexados = [];

        // Indexar todos los items por su ID
        foreach ($menuItems as $item) {
            $itemsIndexados[$item->id] = $item;
            $item->hijos = [];
        }

        // Organizar en estructura jerárquica
        foreach ($menuItems as $item) {
            if (empty($item->padre)) {
                // Es un item padre
                $menu[] = $item;
            } else {
                // Es un item hijo
                if (isset($itemsIndexados[$item->padre])) {
                    $itemsIndexados[$item->padre]->hijos[] = $item;
                }
            }
        }

        return $menu;
    }

    /**
     * Genera HTML para el menú
     */
    public function generarHtmlMenu($menuItems, $baseUrl = '') {
        $html = '';

        foreach ($menuItems as $item) {
            if ($item->es_separador) {
                $html .= '<li class="separador"></li>';
                continue;
            }

            $claseActiva = '';
            $enlace = $baseUrl . $item->enlace;
            $target = $item->blank ? ' target="_blank"' : '';
            $icono = $item->icono ? '<i class="' . $item->icono . '"></i> ' : '';

            if (count($item->hijos) > 0) {
                // Item con hijos (submenú)
                $html .= '<li class="dropdown">';
                $html .= '<a href="#" class="dropdown-toggle"' . $target . '>';
                $html .= $icono . htmlspecialchars($item->titulo);
                $html .= ' <span class="caret"></span></a>';
                $html .= '<ul class="dropdown-menu">';
                $html .= $this->generarHtmlMenu($item->hijos, $baseUrl);
                $html .= '</ul>';
                $html .= '</li>';
            } else {
                // Item simple
                $html .= '<li>';
                if ($item->enlace) {
                    $html .= '<a href="' . htmlspecialchars($enlace) . '"' . $target . '>';
                    $html .= $icono . htmlspecialchars($item->titulo);
                    $html .= '</a>';
                } else {
                    $html .= '<span>' . $icono . htmlspecialchars($item->titulo) . '</span>';
                }
                $html .= '</li>';
            }
        }

        return $html;
    }
}
?>
