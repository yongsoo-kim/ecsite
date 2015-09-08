<?php
require('./includes/config.inc.php');
redirect_invalid_user();
//remove all the value of $_SESSEION array
$_SESSION = array();
session_destroy();
setcookie(session_name(),'',time()-300);
require(MYSQL);
$page_title ='Logout';
include('./includes/header.html');
echo '<h1>Logged Out</h1><p>Thank you for visiting. You are now logged out. Please come back soon!</p>';
include ('./includes/footer.html');
?>
