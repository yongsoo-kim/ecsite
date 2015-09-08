<?php
require('./includes/config.inc.php');
require(MYSQL);


//validate the page ID
if(isset($_GET['id']) && filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range' => 1))){
	$page_id = $_GET['id'];
	$q = 'SELECT title,description,content FROM pages WHERE id='.$page_id;
	$r = mysqli_query($dbc, $q);
	
	//if no rows were returned, print an error
	if(mysqli_num_rows($r) !== 1){
		$page_title = 'Error!';
		include('./includes/header.html');
		echo '<div class="alert alert-danger">This page has been accessed in error.</div>';
		include('./includes/footer.html');
		exit();
	}//end of myslqi_affeted_rows
	
	$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	$page_title = $row['title'];
	include('./includes/header.html');
	echo '<h1>'.htmlspecialchars($page_title).'</h1>';
	
	//display the content if the user's account is current
	if(isset($_SESSION['user_not_expired'])){
		echo "<div>{$row['content']}</div>";
	}elseif (isset($_SESSION['user_id'])){
		echo '<div class="alert"><h4>Expired Accont</h4>'
			.'Thank you for your interest in this content, but your account is no longer current. '.
			'Please <a href="renew.php">renew your accont</a>in order to view this page in its entirety.</div>';
		echo '<div>'.htmlspecialchars($row['description']).'</div>';
		
	}else{
		echo '<div class="alert"> Thank you for your interest in this content. You must be logged in ad a registered user to view this page in its entirety.</div>';
		echo '<div>'.htmlspecialchars($row['description']).'</div>';
	}	
}else{// no Valid ID
	$page_id = 'Error!';
	include('includes/header.html');
	echo '<div class="alert alert-danger-danger">This page has been accessed in error.</div>';
}//End of primary IF

include('./includes/footer.html');
?>