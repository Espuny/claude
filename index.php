<?php
try{
    require_once 'core/config/config.php';
    require_once 'custom/'.PRODUCTO.'/config/config.php';
    require_once 'core/config/system.php';
    require_once 'application/Autoload.php';    Session::init();

    Bootstrap::run(new Request(I18N));

} catch (Exception $e) {
    echo $e->getMessage();
}





