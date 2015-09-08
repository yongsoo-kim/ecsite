<?php
require('./includes/config.inc.php');


// $_SESSION['user_id'] =1;
// $_SESSION['user_admin'] = true;
require(MYSQL);

if($_SERVER['REQUEST_METHOD'] ==='POST'){
	include('./includes/login.inc.php');
}

include('./includes/header.html');
?>
<h3>Welcome</h3>
<p class="lead">Welcome to Knowledge is Power, a site dedicated to keeping you up-to-date on the Web security and programming information you need to know, Blah,Blah,Blah. Yadday,yadda,yadda</p>
<?php 
include('./includes/footer.html');
?>
