<?php

require('./includes/password.php');

$login_errors = array();

if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
	$e = escape_data($_POST['email'],$dbc);
}else{
	$login_errors['email'] = 'Please enter a valid email address';
}
if(!empty($_POST['pass'])){
	$p = $_POST['pass'];
}else{
	$login_errors['pass'] = 'Please enter your password!';
}

if(empty($login_errors)){
	$q = "SELECT id,username,type,pass,IF(date_expires>=NOW(),true,false) AS expired FROM users WHERE email='$e'";
	$r = mysqli_query($dbc,$q);
	
	if(mysqli_num_rows($r) === 1){
		$row = mysqli_fetch_array($r,MYSQLI_ASSOC);
		if(password_verify($p,$row['pass'])){
			//if the user is admin
			if($row['type'] === 'admin'){
				session_regenerate_id(true);
				$_SESSION['user_admin'] = true;
			}
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['username'] = $row['username'];
			if($row['expired'] === '1'){
				$_SESSION['user_not_expired'] = true;
			}
		}else{
			$login_errors['login'] = 'The email address and password do not match those on file.';
		}
	}else{
		$login_errors['login'] = 'The email address and password do not match those on file';	
	}
}//End of login errors IF