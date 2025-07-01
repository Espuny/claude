<?php

class Tools {

    private static $diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sabado", "Domingo"];
    private static $diasSemanaCorto = ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"];
    private static $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    private static $mesesCorto = ["", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

    /*     * *************************************************
     *  Metodos y funciones fechas
     * ************************************************* */

    public static function hoy() {
        return strtotime(date("Y-m-d 00:00:00"));
    }

    public static function diaSemana($dia) {
        return self::$diasSemana[$dia];
    }

    public static function diaSemanaCorto($dia) {
        return self::$diasSemanaCorto[$dia];
    }

    public static function nombreMes($mes) {
        return self::$meses[$mes];
    }

    public static function nombreMesCorto($mes) {
        return self::$mesesCorto[$mes];
    }

    public static function diasSemana() {
        return self::$diasSemana;
    }

    public static function diasSemanaCorto() {
        return self::$diasSemanaCorto;
    }

    public static function nombresMeses() {
        return self::$meses;
    }

    public static function nombresMesesCorto() {
        return self::$mesesCorto;
    }

    public static function primerDiaSemanaTimestamp($fecha=null) {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;

        $diaSemanaDia1 = date("w", $fecha);
        
        if ($diaSemanaDia1 == 0) {
            $diaSemanaDia1 = 7;
        }
        $diaSemanaDia1--;
        //$fecha = self::fecha_es2timestamp($fecha);
        //echo "-".$diaSemanaDia1." day". $fecha;
        $result = strtotime("-" . $diaSemanaDia1 . " day ", $fecha);

        return $result;
    }

    public static function primerDiaSemana($fecha = null, $formatoMySql = "false") {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        $resultTimeStamp = self::primerDiaSemanaTimestamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $resultTimeStamp);
        } else {
            $result = date("d/m/Y", $resultTimeStamp);
        }

        return $result;
    }

    public static function primerDiaMesTimestamp($fecha=null) {
        $result = null;
        
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        
        $mes = date('m',$fecha);
        $anio = date('Y',$fecha);
        $result = mktime(0, 0, 0, $mes, 1, $anio);

        return $result;
    }

    public static function primerDiaMes($fecha = null, $formatoMySql = "false") {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        $resultTimeStamp = self::primerDiaMesTimestamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $resultTimeStamp);
        } else {
            $result = date("d/m/Y", $resultTimeStamp);
        }

        return $result;
    }

    public static function ultimoDiaSemanaTimeStamp($fecha=null) {
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        
        $result = strtotime("+6 days ", self::primerDiaSemanaTimestamp($fecha));

        return $result;
    }

    public static function ultimoDiaMesTimestamp($fecha=null) {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;

        $mes = date('m',$fecha);
        $anio = date('Y',$fecha);
        //$dia = strtotime("-1 day", date("d", mktime(0, 0, 0, $mes + 1, 0, $anio)));

        $result = strtotime("-1 day",mktime(0, 0, 0, $mes+1, 1, $anio));

        return $result;
    }

    public static function ultimoDiaMes($fecha = null, $formatoMySql = "false") {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        $resultTimeStamp = self::ultimoDiaMesTimestamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $resultTimeStamp);
        } else {
            $result = date("d/m/Y", $resultTimeStamp);
        }

        return $result;
    }

    public static function ultimoDiaSemana($fecha = null, $formatoMySql = "false") {
        $result = null;
        $fecha = is_null($fecha) ? self::hoy() : $fecha;
        $resultTimeStamp = self::ultimoDiaSemanaTimeStamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $resultTimeStamp);
        } else {
            $result = date("d/m/Y", $resultTimeStamp);
        }

        return $result;
    }

    public static function primerDiaMesEnCalendarioTimestamp($fecha=null) {
        $result = null;
        
        $fecha = is_null($fecha) ? self::hoy() : $fecha;

        $dia = date("d", $fecha);
        $mes = date("m", $fecha);
        $anio = date("Y", $fecha);

        $diaSemanaDia1 = date("w", strtotime($anio . "-" . $mes . "-01"));
        if ($diaSemanaDia1 == 0) {
            $diaSemanaDia1 = 7;
        }

        return strtotime("-" . ($diaSemanaDia1 - 1) . " day", strtotime($anio . "-" . $mes . "-01"));
    }

    public static function primerDiaMesEnCalendario($fecha, $formatoMySql = "false") {
        $result = null;

        $primerDiaCalendario = self::primerDiaMesEnCalendarioTimestamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $primerDiaCalendario);
        } else {
            $result = date("d/m/Y", $primerDiaCalendario);
        }

        return $result;
    }

    public static function ultimoDiaMesEnCalendarioTimestamp($fecha=null) {
        $result = null;
        
        $fecha = is_null($fecha) ? Tools::hoy() : $fecha;

        $dia = date("d", $fecha);
        $mes = date("m", $fecha);
        $anio = date("Y", $fecha);

        $diaSemanaDia1 = date("w", strtotime($anio . "-" . $mes . "-01"));
        if ($diaSemanaDia1 == 0) {
            $diaSemanaDia1 = 7;
        }
        //$primerDiaCalendario = strtotime( "-".($diaSemanaDia1-1)." day" , strtotime($anio."-".$mes."-01"));	

        $diaSemanaDiaUltimo = date("w", strtotime("+1 month -1 day", strtotime($anio . "-" . $mes . "-01")));
        if ($diaSemanaDiaUltimo == 0) {
            $diaSemanaDiaUltimo = 7;
        }

        return strtotime("+" . (7 - $diaSemanaDiaUltimo) . " day", strtotime("+1 month -1 day", strtotime($anio . "-" . $mes . "-01")));
    }

    public static function ultimoDiaMesEnCalendario($fecha, $formatoMySql = "false") {

        $ultimoDiaCalendario = self::ultimoDiaMesEnCalendarioTimestamp($fecha);

        if ($formatoMySql) {
            $result = date("Y-m-d", $ultimoDiaCalendario);
        } else {
            $result = date("d/m/Y", $ultimoDiaCalendario);
        }

        return $result;
    }

    public static function cadenaAleatoria($longitud = 8, $mayusculas = true, $numeros = true, $caracteres = false, $minusculas = true) {
        $result = "";

        if ($minusculas)
            $fuente = 'abcdefghijklmnopqrstuvwxyz';
        if ($mayusculas)
            $fuente .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($numeros)
            $fuente .= '1234567890';
        if ($caracteres == 1)
            $fuente .= '|@#~$%()=^*+[]{}-_';
        if ($longitud > 0) {
            $fuente = str_split($fuente, 1);
            for ($i = 1; $i <= $longitud; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($fuente));
                $result .= $fuente[$num - 1];
            }
        }
        return $result;
    }

    function normalizaCadena($cadena) {
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
        $cadena = strtolower($cadena);
        return utf8_encode($cadena);
    }

    /*     * *************************************************
     *  Metodos y funciones varios
     * ************************************************* */

    /*public static function cargaPagina($destino, $parametros = "", $blank = false) {
        if ($blank) {
            echo "<script>";
            echo "window.open('" . $destino . "?" . $parametros . "', '_blank')";
            echo "</script>";
        } else {
            echo "<script>";
            echo "location.replace('" . $destino . "?" . $parametros . "')";
            echo "</script>";
        }
    }*/
    
    public static function cargaPagina($controlador, $metodo=null, $parametros = null) {
        $url = BASE_URL;
        $url .= $controlador;
        if (!is_null($metodo)){
            $url.= "/$metodo";
            if (!is_null($parametros)){
                $url.= "/?";
                $tmp = [];
                foreach ($parametros as $key => $parametro) {
                    $tmp[] = $key."=".$parametro;
                }
                $url .= implode("&",$tmp);
            }
        }

        header("Location: ".$url);
    }

    public static function muestraEmergente($titulo = "Atención", $texto = "Error no definido", $siNo = false) {//deprecated

        /* $parametros = "titulo=".$titulo."&texto=".$texto;
          if ($siNo){
          $parametros .= "&sino=1";
          } */
        echo "<script>";
        echo "$.abreEmergente({";
        echo "titulo:'" . $titulo . "',";
        echo "contenido:'" . $texto . "',";
        echo "sino: " . ($siNo ? "true" : "false");
        echo "})";
        //echo "abre_emergente('adm_emergente.php','".$parametros."');";
        echo "</script>";
    }

    public static function abreEmergente($titulo = "Atención", $contenido = "Error no definido", $siNo = false) {//deprecated
        $siNo = $siNo ? "true" : "false";

        echo "<script>";
        echo "	$.abreEmergente({";
        echo "       				titulo : '" . $titulo . "',";
        echo "       				contenido : '" . $contenido . "',";
        echo "       				sino: " . $siNo;
        echo "       				});";
        echo "</script>";
    }

    public static function cierraEmergente($selector = "#emergente") {
        echo "<script>";
        //echo "cierra_emergente();";
        echo "	$.cierraEmergente();";
        echo "</script>";
    }

    public static function paginaActual() {
        $result = $_SERVER["PHP_SELF"];

        $result = explode("/", $result);

        return end($result);
    }

    public static function cierraVentana() {
        echo "<script>";
        echo "window.close()";
        echo "</script>";
    }

    public static function ejecutaFuncionJquery($funcion, $parametros = "") {
        echo "<script>";
        echo $funcion . "(" . $parametros . ");";
        echo "</script>";
    }

    public static function cargaAjax($parametros) {
        self::ejecutaFuncionJquery("$.cargaPagina", $parametros);
    }

    public static function archivoActual() {
        return basename($_SERVER["PHP_SELF"]);
    }

    public static function redimensiona_img($archivo, $carpeta, $ancho, $alto, $calidad) {


        //Creamos una variable imagen a partir de la imagen original
        $img_original = imagecreatefromjpeg($carpeta . "/" . $archivo);

        //Se define el maximo ancho y alto que tendra la imagen final
        $max_ancho = $ancho;
        $max_alto = $alto;

        //Ancho y alto de la imagen original
        list($ancho, $alto) = getimagesize($carpeta . "/" . $archivo);

        //Se calcula ancho y alto de la imagen final
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;


        //Si el ancho y el alto de la imagen no superan los maximos,
        //ancho final y alto final son los que tiene actualmente
        if (($ancho <= $max_ancho) && ($alto <= $max_alto)) {//Si ancho
            $ancho_final = $ancho;
            $alto_final = $alto;
        }
        /*
         * si proporcion horizontal*alto mayor que el alto maximo,
         * alto final es alto por la proporcion horizontal
         * es decir, le quitamos al ancho, la misma proporcion que
         * le quitamos al alto
         *
         */ elseif (($x_ratio * $alto) < $max_alto) {
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        }
        /*
         * Igual que antes pero a la inversa
         */ else {
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }


        //Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
        $tmp = imagecreatetruecolor($ancho_final, $alto_final);

        //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
        imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);

        //Se destruye variable $img_original para liberar memoria
        imagedestroy($img_original);

        //Definimos la calidad de la imagen final
        $calidad = 85;
        //Se crea la imagen final en el directorio indicado
        imagejpeg($tmp, $carpeta . "/" . $archivo, $calidad);
    }

    public static function fecha_es2mysql($fecha) {
        preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,4})/", $fecha, $fechaTemp);
        if (sizeof($fechaTemp) > 0) {
            $fechaMysql = $fechaTemp[3] . "-" . $fechaTemp[2] . "-" . $fechaTemp[1];
        } else {
            $fechaMysql = $fecha;
        }
        return $fechaMysql;
    }

    public static function fecha_es2timestamp($fecha) {

        return strtotime(self::fecha_es2mysql($fecha));
    }

    public static function fecha_mysql2es($fecha) {

        preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $fechaTemp);
        if (sizeof($fechaTemp) > 0) {
            $fechaEs = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
        } else {
            $fechaEs = $fecha;
        }
        return $fechaEs;
    }

    public static function hora_mysql2es($fecha) {

        /* preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $fechaTemp);
          if (sizeof($fechaTemp)>0){
          $fechaEs=$fechaTemp[3]."/".$fechaTemp[2]."/".$fechaTemp[1];
          }else{
          $fechaEs = $fecha;
          } */
        return substr($fecha, 0, 5);
    }

    public static function hora_es2timestamp($hora) {
        $hora = "1970-01-01 " . $hora;
        $hora = strtotime($hora);
        $hora += (60 * 60); //GMT+1
        return $hora;
    }

    public static function detectaNavegador() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $result = "Desconocido";

        $navegadores = array(
            'Opera' => 'Opera',
            'Mozilla Firefox' => '(Firebird)|(Firefox)',
            'Galeon' => 'Galeon',
            'Mozilla' => 'Gecko',
            'MyIE' => 'MyIE',
            'Lynx' => 'Lynx',
            'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
            'Konqueror' => 'Konqueror',
            'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
            'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
            'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
            'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
        );
        foreach ($navegadores as $navegador => $pattern) {
            echo $user_agent . "<br/>";
            /* if (preg_match("/".$pattern."/i", $user_agent))
              $result =  $navegador; */
        }
    }

    public static function esURL($url, $conHttp = true) {
        $result = true;

        if ($conHttp) {
            if (substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://") {
                $result = false;
            }
        }

        if (!strpos($url, ".")) {
            $result = false;
        } else {
            $ultimoCaracter = substr($url, strlen($url) - 1, 1);
            if ($ultimoCaracter == "." || $ultimoCaracter == ",") {
                $result = false;
            }
        }



        return $result;
    }

    public static function getHref($enlace) {
        $result = "";

        $result = str_replace('"', "'", $enlace);

        $result = substr($result, strpos($result, "href=") + 6);

        $result = substr($result, 0, strpos($result, "'"));

        return $result;
    }

    public static function getSrc($imagen) {
        $result = "";

        $result = str_replace('"', "'", $imagen);

        $result = substr($result, strpos($result, "src=") + 5);

        $result = substr($result, 0, strpos($result, "'"));

        return $result;
    }

    public static function stripHtmlTags($text) {
        $text = preg_replace(
                array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu'        
                ), array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0"
                ), $text);
        return strip_tags($text);
    }

    public static function resumeString($string, $maxCaracteres) {
        if (strlen($string) > $maxCaracteres) {
            $string = substr($string, 0, $maxCaracteres) . "...";
        }

        return $string;
    }

    public static function secure($id = null) {
        $base = ["4", "L", "3", "x", "V", "1", "H", "b", "l", "6", "A", "Q", "e", "j", "9",
            "2", "B", "R", "q", "z", "M", "U", "p", "7", "w", "S", "t", "W", "v", "0",
            "i", "y", "s", "k", "d", "r", "X", "N", "P", "J", "5", "u", "n", "O", "Y",
            "F", "I", "Z", "E", "G", "C", "T", "m", "c", "f", "h", "D", "a", "g", "K",
            "8", "o"];

        $tmp = $id . strrev(time()) . $id;
        $res = null;
        while (strlen($tmp) > 0) {
            $pieza = substr($tmp, 0, 2);
            if (intval($pieza) >= sizeof($base)) {
                $pieza = substr($tmp, 0, 1);
                $tmp = substr($tmp, 1);
            } else {
                $tmp = substr($tmp, 2);
            }
            $res .= $base[intval($pieza)];
        }

        return $res;
    }

    public static function getExtension($archivo) {
        $ext = explode('.', $archivo);
        $ext = array_pop($ext);
        return strtolower($ext);
    }

    public static function show($valor, $tipo = null, $modoVista = Model::MODO_VISTA_EDITABLE, $lista = null) {
        $tipo = is_null($tipo) ? self::parseTipo($valor) : $tipo;

        switch ($modoVista) {
            case Model::MODO_VISTA_LISTA:
                return self::getFormatoLista($valor, $tipo, $lista);
                break;
            case Model::MODO_VISTA_EDITABLE:
                return self::getFormatoEditable($valor, $tipo, $lista);
                break;
            default: //self::MODO_VISTA_RAW
                return $valor;
                break;
        }
    }

    public static function creaImagen($filepath) {
        $type = exif_imagetype($filepath);
        $allowedTypes = array(
            1, // [] gif 
            2, // [] jpg 
            3, // [] png 
            6   // [] bmp 
        );
        if (!in_array($type, $allowedTypes)) {
            return false;
        }
        switch ($type) {
            case 1 :
                $im = imageCreateFromGif($filepath);
                break;
            case 2 :
                $im = imageCreateFromJpeg($filepath);
                break;
            case 3 :
                $im = imageCreateFromPng($filepath);
                break;
            case 6 :
                $im = imageCreateFromBmp($filepath);
                break;
        }
        return $im;
    }

    private static function parseTipo($valor) {

        $res = modelView::TIPO_TEXTO;

        switch (gettype($valor)) {
            case "integer":
                $res = ModelView::TIPO_NUM;
                break;
            case "double":
                $res = ModelView::TIPO_FLOAT;
                break;
            default:
                $tipo = $res;

                //Comprobar si es fecha
                $formato = "Y-m-d";
                $parsed = date_parse_from_format($formato, $valor);
                if ($parsed["year"] && $parsed["month"] && $parsed["day"]) {
                    $tipo = ModelView::TIPO_FECHA;
                }
                //Comprobar si es hora larga
                $formato = "H:i:s";
                $parsed = date_parse_from_format($formato, $valor);
                if ($parsed["hour"] && $parsed["minute"] && $parsed["second"]) {
                    $tipo = ModelView::TIPO_HORA;
                }
                //Comprobar si es hora corta
                $formato = "H:i";
                $parsed = date_parse_from_format($formato, $valor);
                if ($parsed["hour"] && $parsed["minute"]) {
                    $tipo = ModelView::TIPO_HORA_CORTA;
                }


                $res = $tipo;
                break;
        }

        return $res;
    }

    private static function getFormatoEditable($valor, $tipo, $lista = null) {
        $result = null;

        switch ($tipo) {
            case ModelView::TIPO_FLOAT:
            case ModelView::TIPO_MONEDA:
                $result = is_numeric($valor) ? number_format($valor, 2, ',', '.') : $result;

                //$result = $valor;
                break;
            case ModelView::TIPO_NUM:
                $result = is_numeric($valor) ? number_format($valor, 0, ',', '.') : $result;
                break;
            case ModelView::TIPO_FECHA:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_HORA_CORTA:
                $result = substr($valor, 0, 5);
                break;
            case ModelView::TIPO_BOOLEAN:
                $result = (boolean) $valor;
                break;



            case ModelView::TIPO_TIMESTAMP:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = strtotime($valor);
                } else {
                    $result = $valor;
                }
                break;
            default:
                $result = $valor;
                break;
        }

        return $result;
    }

    private static function getFormatoLista($valor, $tipo, $lista = null) {
        $result = "";

        switch ($tipo) {
            case ModelView::TIPO_FLOAT:
                $result = is_numeric($valor) ? number_format($valor, 2, ',', '.') : $result;
                //$result = $valor;
                break;
            case ModelView::TIPO_MONEDA:
                $result = is_numeric($valor) ? number_format($valor, 2, ',', '.') . " €" : $result;
                break;
            case ModelView::TIPO_NUM:
                $result = is_numeric($valor) ? number_format($valor, 0, ',', '.') : $result;
                break;
            case ModelView::TIPO_FECHA:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = $fechaTemp[3] . "/" . $fechaTemp[2] . "/" . $fechaTemp[1];
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_HORA:
                $result = $valor != "" ? $valor . "h" : "";
                break;
            case ModelView::TIPO_HORA_CORTA:
                $result = $valor != "" ? substr($valor, 0, 5) . "h" : "";
                break;
            case ModelView::TIPO_BOOLEAN:
                $result = (boolean) $valor ? "Si" : "No";
                break;
            case ModelView::TIPO_ENUM:
                $result = "";
                if ($valor > 0) {
                    $result = array_key_exists(intval($valor), $lista) ? $lista[intval($valor)] : "";
                }
                break;


            case ModelView::TIPO_TIMESTAMP:
                preg_match("/([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})/", $valor, $fechaTemp);
                if (sizeof($fechaTemp) > 0) {
                    $result = strtotime($valor);
                } else {
                    $result = $valor;
                }
                break;
            case ModelView::TIPO_OBJETO:
            case ModelView::TIPO_OBJETO_EXT:
                $result = array_key_exists(intval($valor), $lista) ? $lista[intval($valor)] : "";
                break;
            case ModelView::TIPO_TIEMPO:
                $result = ($valor % 60)>0? ($valor % 60)."m":"";
                $tmp = $valor/60;
                if ($tmp>0){
                    $result = floor($tmp) ."h ".$result;
                    //$tmp = $tmp/60;
                    /*if ($tmp>0){
                        $result = ($result % 24)."dias ".$result;
                    }*/
                }else{
                    $result = "0h ".$result;
                }
                break;
            default:
                $result = $valor;
                break;
        }

        return $result;
    }
    
    public static function getFormatoRaw($valor, $tipo, $lista = null) {
        $result = null;

        if (is_null($valor)) {
            $result = null;
        } else {
            switch ($tipo) {
                case ModelView::TIPO_NUM:
                    $valor = str_replace(".", "", $valor);
                    $result = (int) $valor;
                    break;
                case ModelView::TIPO_FLOAT:
                case ModelView::TIPO_MONEDA:
                    $valor = str_replace(".", "", $valor);
                    $valor = str_replace(",", ".", $valor);
                    $result = (double) $valor;
                    break;
                case ModelView::TIPO_FECHA:
                    if ($valor != "") {
                        preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,4})/", $valor, $fechaTemp);
                        if (sizeof($fechaTemp)>0){	
                            $result=$fechaTemp[3]."-".$fechaTemp[2]."-".$fechaTemp[1];
                        }else{
                            $result = $valor;
                        }
                    } else {
                        $result = null;
                    }
                    break;             
                case ModelView::TIPO_BOOLEAN:
                    $result = !$valor || $valor===0 || $valor===false? 0:1;
                    break;
                case ModelView::TIPO_CONTRASENA:                   
                    $result = md5($valor);
                    break;
                default:
                    $result = $valor;
                    break;
            }
        }

        return $result;
    }

    public static function encrypt($sValue, $sSecretKey){
        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $sSecretKey, $sValue, 
                    MCRYPT_MODE_ECB, 
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256, 
                            MCRYPT_MODE_ECB
                        ), 
                        MCRYPT_RAND)
                    )
                ), "\0"
            );
    }

    public static function decrypt($sValue, $sSecretKey){
        $res =  null;
        
        if (!is_null($sValue)){
            $res = rtrim(
                mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_256, 
                    $sSecretKey, 
                    base64_decode($sValue), 
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256,
                            MCRYPT_MODE_ECB
                        ), 
                        MCRYPT_RAND
                    )
                ), "\0"
            );
        }
        
        return $res;
    }
    
    public static function rrmdir($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    rrmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }
    
    public static function insertWidget($valor){
        $encontrado = preg_match_all("/\{\*.*?\*\}/", $valor,$coincidencias);

        if ($encontrado){
            foreach ($coincidencias[0] as $c){
                $widgetCode = $c;
                $widgetCode = str_replace("{*", "", $widgetCode);
                $widgetCode = str_replace("*}", "", $widgetCode);
                $widgetJSON = json_decode($widgetCode);
                
                if (!is_null($widgetJSON)){                   
                    $html = "<div class='widget' data-tipo='".$widgetJSON->tipo."' data-id='$widgetJSON->id'></div>";              
                    $valor = str_replace($c, $html, $valor);
                }
                
            }
        }
        return $valor;//str_replace("a", "e", $valor);
    }
}