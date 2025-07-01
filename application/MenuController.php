<?php

class MenuController extends Controller
{
    private $menuHelper;

    public function __construct() {
        parent::__construct();
        require_once 'application/MenuHelper.php';
        $this->menuHelper = new MenuHelper();
    }

    /**
     * Obtiene el menú para el usuario actual en formato JSON
     */
    public function getMenuUsuario() {
        try {
            // Obtener el rol del usuario actual de la sesión
            $rolUsuario = Session::get('id_sys_roles');

            if (!$rolUsuario) {
                throw new Exception('Usuario no autenticado o sin rol asignado');
            }

            // Obtener los elementos de menú para el rol
            $menuItems = $this->menuHelper->getMenuPorRol($rolUsuario);

            // Organizar en estructura jerárquica
            $menuJerarquico = $this->menuHelper->organizarMenuJerarquico($menuItems);

            // Devolver como JSON
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => true,
                'menu' => $menuJerarquico,
                'total' => count($menuItems)
            ));

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Obtiene el menú en formato HTML para incluir en las vistas
     */
    public function getMenuHtml() {
        try {
            $rolUsuario = Session::get('id_sys_roles');

            if (!$rolUsuario) {
                return '<li><a href="/login">Iniciar Sesión</a></li>';
            }

            $menuItems = $this->menuHelper->getMenuPorRol($rolUsuario);
            $menuJerarquico = $this->menuHelper->organizarMenuJerarquico($menuItems);

            return $this->menuHelper->generarHtmlMenu($menuJerarquico, BASE_URL);

        } catch (Exception $e) {
            error_log('Error generando menú HTML: ' . $e->getMessage());
            return '<li><span style="color: red;">Error cargando menú</span></li>';
        }
    }

    /**
     * Acción AJAX para obtener el menú dinámicamente
     */
    public function ajax() {
        $this->getMenuUsuario();
    }

    /**
     * Verifica si el usuario tiene acceso a una URL específica
     */
    public function verificarAcceso($url) {
        try {
            $rolUsuario = Session::get('id_sys_roles');

            if (!$rolUsuario) {
                return false;
            }

            $menuItems = $this->menuHelper->getMenuPorRol($rolUsuario);

            foreach ($menuItems as $item) {
                if ($item->enlace == $url) {
                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            error_log('Error verificando acceso: ' . $e->getMessage());
            return false;
        }
    }
}
?>
