<?php
/**
 *
 * @todo Excepcion de metodo loadModel
 * @todo FIltrado de html
 */
abstract class Controller {

    protected $_view;
    protected $_request;
    protected $_log;

    protected $_model;
    protected $_menuHelper; // Nuevo: Helper para menús

    public function __construct() {
        $this->_request = new Request();
        $this->_view = new View(new Request());

        // Inicializar MenuHelper para compatibilidad con PHP 7.4
        $this->initMenuHelper();

        if (!Session::get("cfgLoaded")) {
            $conf = $this->loadModel("configuracion");
            foreach ($conf->cargaValores() as $key => $valor) {
                Session::set($key, $valor);
            }
            Session::set("cfgLoaded", 1);
            $this->_log = new iscLogger();
        }

        if (!Session::get("nivelesAcceso")) {
            $roles = $this->loadModel("rol");
            $roles->consulta();

            $na = [];
            foreach ($roles as $rol) {
                $na[$rol->sys_nombre] = $rol->visibilidad;
            }

            Session::set("nivelesAcceso", $na);
        }

        //setlocale(LC_ALL,LOCALE);
    }

    abstract public function index();

    protected function loadModel($modelo) {
        $result = null;

        if (class_exists($modelo . "Model")) {
            $modelo.= "Model";
            $result = new $modelo;
        }else if (class_exists($modelo . "BaseModel")) {
            $modelo.= "BaseModel";
            $result = new $modelo;
        }else if (class_exists($modelo)) {
            $result = new $modelo;
        }else{
            throw new Exception('Modelo no encontrado (loadModel): ' . $modelo);
        }

        return $result;
    }

    /**
     * @todo revisar exception
     * @param type $libreria
     */
    protected function getLibrary($libreria) {
        $rutaLibreria = ROOT . DIR_LIBS . DS . $libreria . '.php';

        if (is_readable($rutaLibreria)) {
            require_once $rutaLibreria;
        } else {
            throw new Exception('Error de libreria');
        }
    }

    public function getController() {
        $res = str_replace("BaseController", "", get_called_class());
        $res = str_replace("Controller", "", $res);
        return $res;
    }

    protected function cargaPagina($direccion = BASE_URL) {
        header('location:' . BASE_URL . $direccion);
    }

    /**
     * @deprecated
     * @param type $direccion
     */
    protected function redireccionar($direccion = BASE_URL) {
        $this->cargaPagina($direccion);
    }

    protected function getParam($param) {
        if (isset($_POST[$param])) {
            return $_POST[$param];
        } else {
            $args = $this->_request->getArgs();
            if (isset($args[$param])) {
                return $args[$param];
            }
        }
    }

    protected function setParam($param, $valor) {
        $_POST[$param] = $valor;
    }

    protected function existsParam($param) {
        $res = false;

        if (array_key_exists($param, $_POST)) {
            $res = true;
        } else {
            $args = $this->_request->getArgs();
            if (array_key_exists($param, $args)) {
                $res = true;
            }
        }
        return $res;
    }

    protected function getParams() {
        return array_merge($_POST, $this->_request->getArgs());
    }

    /**
     * @todo Errores si no archivo
     * @param type $archivo
     */
    public function ajaxHTML($archivo,$view=null) {

        if ($archivo){
            $this->_view->setParams($this->getParams());
            $this->_view->renderHTML($archivo,$view);
        }
    }

    public function outputJSON($datos, $tipo = null) {
        if (is_null($tipo)) {
            $tipo = $this->_request->getControlador();
        }
        header('Content-type: application/json');
        echo json_encode([$tipo => $datos]);
    }

    /**
     * Inicializa el helper de menús para compatibilidad PHP 7.4
     */
    protected function initMenuHelper() {
        try {
            if (file_exists('application/MenuHelper.php')) {
                require_once 'application/MenuHelper.php';
                $this->_menuHelper = new MenuHelper();
            }
        } catch (Exception $e) {
            error_log('Error inicializando MenuHelper: ' . $e->getMessage());
            $this->_menuHelper = null;
        }
    }    /**
     * Obtiene el ID del rol del usuario actual desde las variables de sesión existentes
     */
    private function getRolUsuarioActual() {
        // Primero intentar con la variable que usa nuestro sistema de pruebas
        $rolId = Session::get('id_sys_roles');

        if ($rolId) {
            return $rolId;
        }

        // Si no, intentar obtenerlo del objeto user del sistema existente
        $user = Session::get('user');
        if ($user && isset($user->rol)) {
            // Mapear el rol del usuario al ID del rol del sistema
            Session::set('id_sys_roles', $user->rol); // Guardar para futuras referencias
            return $user->rol;
        }

        // Si no, intentar obtenerlo del objeto rol del sistema existente
        $rol = Session::get('rol');
        if ($rol && isset($rol->id)) {
            Session::set('id_sys_roles', $rol->id); // Guardar para futuras referencias
            return $rol->id;
        }

        return null;
    }

    /**
     * Obtiene el menú HTML para el usuario actual
     */
    protected function getMenuHtml() {
        if (!$this->_menuHelper) {
            return '<li><span style="color: red;">Menú no disponible</span></li>';
        }

        try {
            $rolUsuario = $this->getRolUsuarioActual();

            if (!$rolUsuario) {
                return '<li><a href="' . BASE_URL . 'login">Iniciar Sesión</a></li>';
            }

            $menuItems = $this->_menuHelper->getMenuPorRol($rolUsuario);
            $menuJerarquico = $this->_menuHelper->organizarMenuJerarquico($menuItems);

            return $this->_menuHelper->generarHtmlMenu($menuJerarquico, BASE_URL);

        } catch (Exception $e) {
            error_log('Error generando menú HTML: ' . $e->getMessage());
            return '<li><span style="color: red;">Error en menú</span></li>';
        }
    }    /**
     * Obtiene el menú en formato array para el usuario actual
     */
    protected function getMenuData() {
        if (!$this->_menuHelper) {
            return array('error' => 'MenuHelper no disponible');
        }

        try {
            $rolUsuario = $this->getRolUsuarioActual();

            if (!$rolUsuario) {
                return array('error' => 'Usuario no autenticado');
            }

            $menuItems = $this->_menuHelper->getMenuPorRol($rolUsuario);
            $menuJerarquico = $this->_menuHelper->organizarMenuJerarquico($menuItems);

            return array(
                'items' => $menuItems,
                'jerarquico' => $menuJerarquico,
                'total' => count($menuItems),
                'html' => $this->_menuHelper->generarHtmlMenu($menuJerarquico, BASE_URL)
            );

        } catch (Exception $e) {
            error_log('Error obteniendo datos de menú: ' . $e->getMessage());
            return array('error' => $e->getMessage());
        }
    }    /**
     * Verifica si el usuario actual tiene acceso a una URL
     */
    protected function verificarAccesoMenu($url) {
        if (!$this->_menuHelper) {
            return false;
        }

        try {
            $rolUsuario = $this->getRolUsuarioActual();

            if (!$rolUsuario) {
                return false;
            }

            $menuItems = $this->_menuHelper->getMenuPorRol($rolUsuario);

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

    /**
     * Obtiene el menú en formato compatible con Smarty
     * Para usar en las vistas que usan plantillas Smarty
     */
    protected function getMenuParaSmarty() {
        try {
            require_once 'application/MenuSmartyAdapter.php';

            $rolUsuario = $this->getRolUsuarioActual();

            if (!$rolUsuario) {
                return array();
            }

            return MenuSmartyAdapter::obtenerMenuUsuario($rolUsuario);        } catch (Exception $e) {
            error_log('Error obteniendo menú para Smarty: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Asigna automáticamente el menú a la vista Smarty
     */
    protected function asignarMenuAVista() {
        try {
            $menuParaSmarty = $this->getMenuParaSmarty();

            // Asignar a la vista actual (para controladores que usan assign directamente)
            $this->_view->assign('nav', $menuParaSmarty);

            // También asignar a la sesión para compatibilidad con el sistema original
            Session::set('menu', $menuParaSmarty);

            $this->_view->assign('usuario', $this->getUsuarioActual());

        } catch (Exception $e) {
            error_log('Error asignando menú a vista: ' . $e->getMessage());
            $this->_view->assign('nav', array());
            Session::set('menu', array());
        }
    }/**
     * Obtiene información del usuario actual para las vistas
     */
    private function getUsuarioActual() {
        $usuario = new stdClass();

        // Intentar obtener del objeto user del sistema existente
        $userSession = Session::get('user');
        if ($userSession) {
            $usuario->id = $userSession->id ?? null;
            $usuario->usuario = $userSession->usuario ?? null;
            $usuario->nombre = $userSession->nombre ?? null;
            $usuario->apellido1 = $userSession->apellido1 ?? null;
            $usuario->id_sys_roles = $userSession->rol ?? null;
        } else {
            // Fallback a las variables individuales (para compatibilidad con sistema de pruebas)
            $usuario->id = Session::get('id');
            $usuario->usuario = Session::get('usuario');
            $usuario->nombre = Session::get('nombre');
            $usuario->apellido1 = Session::get('apellido1');
            $usuario->id_sys_roles = Session::get('id_sys_roles');
        }

        return $usuario;
    }
}
