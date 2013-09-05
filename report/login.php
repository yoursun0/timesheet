<?php
require 'config.php';

session_start();

if (isset($_POST['passwd'])) {
    if ($_POST['passwd'] == include "password.php") {
        $_SESSION['logined'] = true;
        redirect();
    }
}

include template('login');
?>
