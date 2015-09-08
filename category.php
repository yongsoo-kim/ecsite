<?php
require('./includes/config.inc.php');
require(MYSQL);

if(filter_var($_GET['id'],FILTER_VALIDATE_INT,array('min_range' => 1))){
	$cat_id = $_GET['id'];
	//get the category title
	$q = 'SELECT category FROM categories WHERE id ='.$cat_id;
	$r = mysqli_query($dbc, $q);

	if(mysqli_num_rows($r) !== 1 ){
		$page_title = 'Error!';
		include('./includes/header.html');
		echo '<div class="alert alert-danger">This page has been accessed in error.</div>';
		include('./includes/footer.html');
		exit();
	}//end of category FIND error
	//If things are going well...
	
	list($page_title) = mysqli_fetch_array($r, MYSQLI_NUM);
	include('./includes/header.html');
	echo '<h1>'.htmlspecialchars($page_title).'</h1>';
	
	//if user's service period has been expired.
	if(!isset($_SESSION['user_id']) && !isset($_SESSION['user_not_expired'])){
		echo '<div class="alert">
				<h4>Expired Account</h4>
				Thank you for your interest in this content. Unfortunately your account has expired. Please <a href ="renew.php">renew your account</a> in order to access site content</div>';
	//If user didn't log in	
	}elseif(!isset($_SESSION['user_id'])){
		echo '<div class="alert">
				<h4>Expired Account</h4>
				Thank you for your interest in this content. You must be logged in as a rigistered user to view site content.</div>';
		
	}
	//get the pages associaged with this page.
	
	$q = 'SELECT id,title,description FROM pages WHERE categories_id='.$cat_id.' ORDER BY date_created DESC';
	$r = mysqli_query($dbc, $q);
	
	if(mysqli_num_rows($r)>0){
		while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
			echo '<div><h4><a href="page.php?id='.$row['id'].'">'.htmlspecialchars($row['title']).'</a></h4><p>'.
					htmlspecialchars($row['description']).'</p></div>';
		}//End of WHILE loop
	}else{ //no page available.
		echo '<p>There are currently no pages of content associated with this category. Please check back again!</p>';
	}	
}else{
	$page_title = 'Error!';
	include('./includes/header.html');
	echo '<div class="alert alert-danger">This page has been accessed in error</div>';
}

include('./includes/footer.html');
?>
