<?php

/**
 * @todo Excepcion en render()
 */
require_once ROOT . DIR_LIBS . DS . 'Smarty' . DS . 'libs' . DS . 'Smarty.class.php';

class View extends Smarty {

    private $_controlador;
    private $_metodo;
    private $_parametros;
    private $_consts;
    private $_params;
    private $_js;
    private $_jsParent;
    private $_layoutJs;
    private $_css;
    private $_cssParent;
    private $_showHeader;
    private $_showNav;
    private $_showUser;
    private $_showFooter;
    private $_parent;

    /**
     * @todo cambiar nombres de constantes URL_PATH etc...
     * @param Request $peticion
     */
    public function __construct(Request $peticion) {
        parent::__construct();
        $this->_controlador = $peticion->getControlador();
        $this->_metodo = $peticion->getMetodo();
        $this->_parametros = $peticion->getArgs();

        $this->addTemplateDir(ROOT . DIR_CORE . DS . DIR_LAYOUT . DS . CURRENT_LAYOUT . DS);
        $this->addTemplateDir(ROOT . DIR_CORE . DS . DIR_LAYOUT . DS . CURRENT_LAYOUT . DS . "components");

        if (is_readable(ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_controlador . DS)) {
            $this->addTemplateDir(ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_controlador . DS);
        }
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_controlador . DS)) {
            $this->addTemplateDir(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_controlador . DS);
        }
        

        //$this->setConfigDir(ROOT . DIR_VIEWS . DS . 'layout' . DS . CURRENT_LAYOUT . DS . 'configs' . DS);
        $this->setCacheDir(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_TMP . DS . 'cache' . DS);
        $this->setCompileDir(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_TMP . DS . 'template' . DS);

        $this->setPluginsDir(ROOT . DIR_LIBS . DS . 'Smarty' . DS . 'libs' . DS . 'plugins');

        $this->_consts = [];
        $this->_consts = unserialize(USER_CONSTS);
        $this->_params = [];
        $this->_js = [];
        $this->_jsParent = [];
        $this->_layoutJs = [];
        $this->_css = [];
        $this->_cssParent = [];
        $this->_showHeader = SHOW_HEADER;
        $this->_showNav = SHOW_NAV;
        $this->_showUser = SHOW_USER;
        $this->_showFooter = SHOW_FOOTER;
    }

    public function setParams($params) {
        $this->_params = $params;
    }

    public function __set($propiedad, $valor) {

        if (!property_exists($this, $propiedad)) {
            $this->_params[$propiedad] = $valor;
        } else {
            $this->$propiedad = $valor;
        }
    }

    public function __get($propiedad) {
        $result = null;

        if (!property_exists($this, $propiedad)) {
            if (array_key_exists($propiedad, $this->_params)) {
                $result = $this->_params[$propiedad];
            }
        } else {
            $result = $this->$propiedad;
        }

        return $result;
    }

    public function setParent($parent) {
        if (is_readable(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $parent . DS)) {
            $this->addTemplateDir(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $parent . DS);
        }
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $parent . DS)) {
            $this->addTemplateDir(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $parent . DS);
        }

        $this->_parent = $parent;
    }

    public function setJs($js) {
        if (!is_array($js)) {
            $this->_js[] = $js;
        } else {
            $this->_js = array_merge($this->_js, $js);
        }
    }

    public function setParentJs($js) {
        if (!is_array($js)) {
            if ($this->_parent && file_exists(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS . 'js' . DS . $js)) {
                $this->_jsParent[] = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/js/' . $js;
            }
            if ($this->_parent && file_exists(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS . 'js' . DS . $js)) {
                $this->_jsParent[] = BASE_URL . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . '/js/' . $js;
            }
        } else {
            foreach ($js as $archivo) {
                if ($this->_parent && file_exists(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS . 'js' . DS . $archivo)) {
                    $this->_jsParent[] = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/js/' . $archivo;
                }
                if ($this->_parent && file_exists(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS . 'js' . DS . $archivo)) {
                    $this->_jsParent[] = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . $this->_parent . '/js/' . $archivo;
                }
            }
        }
    }

    public function setLayoutJs($js) {
        if (!is_array($js)) {
            $this->_layoutJs[] = $js;
        } else {
            $this->_layoutJs = $js;
        }
    }

    public function setCss($css) {
        if (!is_array($css)) {
            $this->_css[] = $css;
        } else {
            $this->_css = $css;
        }
    }
    
    public function setParentCss($css) {
        if (!is_array($css)) {
            if ($this->_parent && file_exists(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS . 'css' . DS . $css)) {
                $this->_cssParent[] = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/css/' . $css;
            }
            if ($this->_parent && file_exists(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS . 'css' . DS . $css)) {
                $this->_cssParent[] = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . $this->_parent . '/css/' . $css;
            }
        } else {
            foreach ($css as $archivo) {
                if ($this->_parent && file_exists(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS . 'css' . DS . $archivo)) {
                    $this->_cssParent[] = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/css/' . $archivo;
                }
                if ($this->_parent && file_exists(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS . 'css' . DS . $archivo)) {
                    $this->_cssParent[] = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . $this->_parent . '/css/' . $archivo;
                }
            }
        }
    }

    public function showHeader($show = true) {
        $this->_showHeader = $show;
    }

    public function showNav($show = true) {
        $this->_showNav = $show;
    }

    public function showUser($show = true) {
        $this->_showUser = $show;
    }

    public function showFooter($show = true) {
        $this->_showFooter = $show;
    }

    /**
     * @todo añadir menu si autentificado tutorial 09-13:47
     * @param type $vista
     * @param type $item
     * @throws Exception
     */
    public function render($template, $view = null/* $item = false */) {
        $view = !is_null($view) ? $view : $this->_controlador;

        $dirRoot = ROOT;
        $dirBase = BASE_URL;
        $dirPublic = BASE_URL . DIR_PUBLIC;
        $dirPrivate = BASE_URL . DIR_PRIVATE;
        $dirPublicImg = $dirPublic . "/img/";

        $dirLayout = BASE_URL . DIR_CORE . '/' . DIR_LAYOUT . '/' . CURRENT_LAYOUT . '/';
        $dirCustom = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/';
        $dirCustomLayout = $dirCustom . DIR_LAYOUT . '/';
        $dirUploads = $dirCustom . DIR_UPLOADS . '/';
        $dirActualController = BASE_URL . $this->_controlador;
        $dirActual = $dirActualController;
        $dirActual .= $this->_metodo ? "/" . $this->_metodo : "";
        $dirActualNoParams = $dirActual;
        if (sizeof($this->_parametros) > 0) {
            foreach ($this->_parametros as $key => $parametro) {
                $dirActual .= PS . $key . PA . $parametro;
            }
        }

        /*$dirParentView = "";
        $rutaParentView = "";
        if ($this->_parent){
            if (is_readable(ROOT . DIR_VIEWS . DS . 'custom' . DS . $this->_parent . DS)) {
                $rutaParentView = ROOT . DIR_VIEWS . DS . 'custom' . DS . $this->_parent . DS;
                $dirView = BASE_URL . DIR_VIEWS . '/custom/' . $this->_parent . '/';
            }else if (is_readable(ROOT . DIR_VIEWS . DS . 'base' . DS . $this->_parent . DS)) {
                $rutaParentView = ROOT . DIR_VIEWS . DS . 'base' . DS . $this->_parent . DS;
                $dirView = BASE_URL . DIR_VIEWS . '/base/' . $this->_parent . '/';
            }
        }
        
        $rutaView = "";
        $dirView = "";
        $rutaViewBase = "";
        $dirViewBase = "";
        if (is_readable(ROOT . DIR_VIEWS . DS . 'custom' . DS . $view . DS)) {
            $rutaView = ROOT . DIR_VIEWS . DS . 'custom' . DS . $view . DS;
            $dirView = BASE_URL . DIR_VIEWS . '/custom/' . $view . '/';
            $rutaViewBase = ROOT . DIR_VIEWS . DS . 'base' . DS . $view . DS;
            $dirViewBase = BASE_URL . DIR_VIEWS . '/base/' . $view . '/';
        }else if (is_readable(ROOT . DIR_VIEWS . DS . 'base' . DS . $view . DS)) {
            $rutaView = ROOT . DIR_VIEWS . DS . 'base' . DS . $view . DS;
            $dirView = BASE_URL . DIR_VIEWS . '/base/' . $view . '/';
        }*/

        $rutaView = "";
        $dirView = "";
        $rutaViewBase = "";
        $dirViewBase = "";
        $rutaViewParent = "";
        $dirViewParent = "";
        $rutaViewBaseParent = "";
        $dirViewBaseParent = "";
        
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $view . DS)) {
            $rutaView = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $view . DS;
            $dirView = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . '/' . $view . '/';
            $rutaViewBase = ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $view . DS;
            $dirViewBase = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $view . '/';
        }else if (is_readable(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $view . DS)) {
            $rutaView = ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $view . DS;
            $dirView = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $view . '/';
        }
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS)) {
            $rutaViewParent = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS;
            $dirViewParent = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . $this->_parent . '/';
            if (is_readable(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS)) {
                $rutaViewBaseParent = ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS;
                $dirViewBaseParent = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/';
            }
        }else if (is_readable(ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS)) {
            $rutaViewParent = ROOT . DIR_CORE . DS .DIR_VIEWS . DS . $this->_parent . DS;
            $dirViewParent = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/';
        }
        
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_LAYOUT . DS .'js' . DS . "index.js")) {
            $this->assign('_customIndexJs', true);
        }
        
        $this->assign('_dirRoot', $dirRoot);
        $this->assign('_dirBase', $dirBase);
        $this->assign('_controlador', $this->_controlador);
        $this->assign('_dirActual', $dirActual);
        $this->assign('_dirActualController', $dirActualController);
        $this->assign('_dirActualNoParams', $dirActualNoParams);
        $this->assign('_dirPublicImg', $dirPublicImg);
        $this->assign('_dirPrivate', $dirPrivate);
        $this->assign('_dirViewParent', $dirViewParent);
        $this->assign('_dirViewBaseParent', $dirViewBaseParent);
        $this->assign('_dirView', $dirView);
        $this->assign('_dirViewBase', $dirViewBase);
        $this->assign('_rutaView', $rutaView);
        $this->assign('_rutaViewBase', $rutaViewBase);
        $this->assign('_rutaViewParent', $rutaViewParent);
        $this->assign('_rutaViewBaseParent', $rutaViewBaseParent);
        $this->assign('_dirLayout', $dirLayout);
        $this->assign('_dirCustom', $dirCustom);
        $this->assign('_dirCustomLayout', $dirCustomLayout);
        $this->assign('_dirUploads', $dirUploads);
        $this->assign('_consts', $this->_consts);
        $this->assign('_params', $this->_params);
        $this->assign('_rolClass', Session::getRolName());

        /*foreach ($this->_params as $var => $valor) {
            $this->assign($var, $valor);
        }*/

        $this->assign('_jsConsts', unserialize(JS_CONSTS));
        if (sizeof($this->_js) > 0) {
            $this->assign('_js', $this->_js);
        }
        if (sizeof($this->_jsParent) > 0) {
            $this->assign('_jsParent', $this->_jsParent);
        }
        if (sizeof($this->_layoutJs) > 0) {
            $this->assign('_layoutJs', $this->_layoutJs);
        }
        if (sizeof($this->_css) > 0) {
            $this->assign('_css', $this->_css);
        }
        if (sizeof($this->_cssParent) > 0) {
            $this->assign('_cssParent', $this->_cssParent);
        }

        if ($this->_showHeader)
            $this->assign('header', PRIVATE_TEXT_HEADER);
        if ($this->_showNav)
            $this->assign('nav', Session::get('menu'));
        if ($this->_showUser)
            $this->assign('usuario', Session::get('user'));
        if ($this->_showFooter)
            $this->assign('footer', null);
        if (file_exists($rutaView . $template . '.tpl')) {
            $this->assign('_contenido', $rutaView .  $template . '.tpl');
        }else if (file_exists($rutaViewBase . $template . '.tpl')) {
            $this->assign('_contenido', $rutaViewBase .  $template . '.tpl');
        }else if (file_exists($rutaViewParent .  $template . '.tpl')) {
            $this->assign('_contenido', $rutaViewParent . $template . '.tpl');
        }else if (file_exists($rutaViewBaseParent .  $template . '.tpl')) {
            $this->assign('_contenido', $rutaViewBaseParent . $template . '.tpl');
        }else{
            throw new Exception('Error de vista: ' . $rutaView . ", " . $rutaViewParent . " (" . $template.")");
        }

        $this->display('template.tpl');
    }

    public function renderHTML($archivo, $view = null) {
        $view = !is_null($view) ? $view : $this->_controlador;
        
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $view . DS)) {
            define('PATH_CORE', ROOT . DIR_CORE . DS);
            define('URL_CORE', BASE_URL . DIR_CORE . '/');
            define('RELATIVE_URL_CORE', RELATIVE_URL . DIR_CORE . '/');
            define('PATH_CUSTOM', ROOT . DIR_CUSTOM . DS . PRODUCTO . DS);
            define('URL_CUSTOM', BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/');
            define('RELATIVE_URL_CUSTOM', RELATIVE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/');
            define('PATH_VIEW', PATH_CUSTOM . DIR_VIEWS . DS . $view . DS);
            define('URL_VIEW', URL_CUSTOM . DIR_VIEWS . '/' . $view . "/");
            define('RELATIVE_URL_VIEW', RELATIVE_URL_CUSTOM . DIR_VIEWS . '/' . $view . "/");
        }else {
            define('PATH_VIEW', ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $view . DS);
            define('URL_VIEW', BASE_URL . DIR_CORE . "/" . DIR_VIEWS . "/" . $view . "/");
            define('RELATIVE_URL_VIEW', RELATIVE_URL . DIR_CORE . "/" . DIR_VIEWS . "/" . $view . "/");
        }

        define('PATH_LAYOUT', ROOT . DIR_CORE . DS . DIR_LAYOUT . DS . CURRENT_LAYOUT . DS);
        define('DIR_ACTUAL_LAYOUT', BASE_URL . "/" . DIR_CORE . "/" . DIR_LAYOUT . "/" . CURRENT_LAYOUT . "/");
        $_jsConsts = unserialize(JS_CONSTS);
        $params = $this->_params;
        
        foreach ($this->_params as $var => $valor) {
            $$var = $valor;
        }
        require_once PATH_VIEW . $archivo;
    }

    public function renderAjax($template, $view = null/* $item = false */) {
        $view = !is_null($view) ? $view : $this->_controlador;

        $dirPublic = BASE_URL . DIR_PUBLIC;
        $dirPrivate = BASE_URL . DIR_PRIVATE;
        $dirPublicImg = $dirPublic . "/img/";
        //$dirView = BASE_URL.DIR_VIEWS.'/'.$view.'/';
        $dirLayout = BASE_URL . "/" . DIR_CORE . "/" . DIR_LAYOUT . "/" . CURRENT_LAYOUT . "/";
        $dirUploads = BASE_URL . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_UPLOADS . '/';
        //$rutaView = ROOT.DIR_VIEWS.DS.$view.DS;

        /*$dirView = BASE_URL . DIR_VIEWS . '/' . $view . '/';
        $rutaView = ROOT . DIR_VIEWS . DS . $view . DS;
        if (is_readable(ROOT . DIR_VIEWS . DS . 'custom' . DS . $view . DS)) {
            $rutaView = ROOT . DIR_VIEWS . DS . 'custom' . DS . $view . DS;
            $dirView = BASE_URL . DIR_VIEWS . '/custom/' . $view . '/';
        }

        if (is_readable(ROOT . DIR_VIEWS . DS . 'base' . DS . $view . DS)) {
            $rutaView = ROOT . DIR_VIEWS . DS . 'base' . DS . $view . DS;
            $dirView = BASE_URL . DIR_VIEWS . '/base/' . $view . '/';
        }*/
        
        $rutaView = "";
        $dirView = "";
        $rutaViewBase = "";
        $dirViewBase = "";
        $rutaViewParent = "";
        $dirViewParent = "";
        $rutaViewBaseParent = "";
        $dirViewBaseParent = "";
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $view . DS)) {
            $rutaView = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $view . DS;
            $dirView = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . '/' . $view . '/';
            $rutaViewBase = ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $view . DS;
            $dirViewBase = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $view . '/';
        }else if (is_readable(ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $view . DS)) {
            $rutaView = ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $view . DS;
            $dirView = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $view . '/';
        }
        if (is_readable(ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS)) {
            $rutaViewParent = ROOT . DIR_CUSTOM . DS . PRODUCTO . DS . DIR_VIEWS . DS . $this->_parent . DS;
            $dirViewParent = BASE_URL . DIR_CUSTOM . '/' . PRODUCTO . '/' . DIR_VIEWS . $this->_parent . '/';
            if (is_readable(ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_parent . DS)) {
                $rutaViewBaseParent = ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_parent . DS;
                $dirViewBaseParent = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/';
            }
        }else if (is_readable(ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_parent . DS)) {
            $rutaViewParent = ROOT . DIR_CORE . DS . DIR_VIEWS . DS . $this->_parent . DS;
            $dirViewParent = BASE_URL . DIR_CORE . '/' .DIR_VIEWS . '/' . $this->_parent . '/';
        }
        
        foreach ($this->_params as $var => $valor) {
            $$var = $valor;
        }

        $this->assign('_dirPublicImg', $dirPublicImg);
        $this->assign('_dirPrivate', $dirPrivate);
        $this->assign('_dirView', $dirView);
        $this->assign('_dirViewBase', $dirViewBase);
        $this->assign('_dirViewParent', $dirViewParent);
        $this->assign('_dirViewBaseParent', $dirViewBaseParent);
        $this->assign('_rutaView', $rutaView);
        $this->assign('_rutaViewBase', $rutaViewBase);
        $this->assign('_rutaViewParent', $rutaViewParent);
        $this->assign('_rutaViewBaseParent', $rutaViewBaseParent);
        $this->assign('_dirLayout', $dirLayout);
        $this->assign('_dirUploads', $dirUploads);
        $this->assign('_consts', $this->_consts);
        $this->assign('_params', $this->_params);

        $this->display( $template . '.tpl');
    }

}
