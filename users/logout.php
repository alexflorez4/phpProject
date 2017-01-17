<?php

session_start();


unset($_SESSION['login']);
unset($_SESSION['sellerNm']);
unset($_SESSION['sellerId']);
session_destroy();

echo "<script>window.open('login.php','_self')</script>";

?>