<?php
session_start();
setcookie('remember', '', time() - 3600);
session_destroy();
header("Location: login.php");
?>