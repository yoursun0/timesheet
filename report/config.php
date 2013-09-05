<?php

define('APP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('TPL_DIR', APP_DIR . 'template' . DIRECTORY_SEPARATOR);
define('INC_DIR', APP_DIR . 'include' . DIRECTORY_SEPARATOR);

error_reporting(E_ALL);
require_once "../grab_globals.inc.php";
include "../config.inc.php";
include "../functions.inc";
include "../$dbsys.inc";

/**
 * get the submitted value
 * 
 * @param string $name
 * @param string $scope
 * @param mixed $default
 * @return string 
 */
function arg($name, $scope = 'GP', $default = NULL, $trim = TRUE) {
    $scope = strtoupper($scope);
    $L = strlen($scope);
    $i = 0;
    $v = $default;
    while ($i < $L) {
        $s = $scope[$i++];
        if (('G' == $s) && isset($_GET[$name])) {
            $v = $_GET[$name];
            break;
        } elseif (('P' == $s) && isset($_POST[$name])) {
            $v = $_POST[$name];
            break;
        } elseif (('C' == $s) && isset($_COOKIE[$name])) {
            $v = $_COOKIE[$name];
            break;
        } elseif (('S' == $s) && isset($_SESSION[$name])) {
            $v = $_SESSION[$name];
            break;
        }
    }
    if ($trim && is_string($v)) {
        $v = trim($v);
    }
    return $v;
}

/**
 * get an array contains the submitted values
 * 
 * @param type $names
 * @param type $scope
 * @param type $prefix
 * @param type $default
 * @return type 
 */
function args($names, $scope = 'GP', $prefix = '', $default = NULL, $trim = TRUE) {
    $args = array();
    foreach ($names as $name) {
        $args[$name] = arg($prefix . $name, $scope, $default, $trim);
    }
    return $args;
}

function template($name) {
    if (file_exists(TPL_DIR . $name . '.php')) {
        return TPL_DIR . $name . '.php';
    } elseif (TPL_DIR . 'template_not_found.php') {
        return TPL_DIR . 'template_not_found.php';
    }
    exit('Error: template not found');
}

function header_tpl() {
    return template('header');
}

function footer_tpl() {
    return template('footer');
}

function redirect($url = 'index.php') {
    header('Location: ' . $url);
    exit;
}

function check_login() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['logined'])) {
        redirect('login.php');
    }
}

function sum(&$var, $value) {
    if (isset($var)) {
        $var += $value;
    } else {
        $var = $value;
    }
}

function getDisplay(&$val, $empty = '-', $format = true) {
    if (isset($val)) {
        return $format ? number_format($val, 2) : $val;
    } else {
        return $empty;
    }
}

function display(&$value, $empty = '-') {
    echo getDisplay($value, $empty);
}

?>
