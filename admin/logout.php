<?php

session_start();


unset($_SESSION['login']);
session_destroy();

echo "<script>window.open('login.php','_self')</script>";

?>