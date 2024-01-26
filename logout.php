<?php
// alisin mga var sa session then destroy
session_start();
$_SESSION = array();
session_destroy();
header("location: login.php");
exit;
?>