<?php
if(!defined('LIVE')){DEFINE('LIVE',false);}
DEFINE('CONTACT_EMAIL','yongsookim.com@gmail.com');

define('BASE_URI' , '/home/smilesigma/ecsite/');
define('BASE_URL' , 'ecsite.yongsookim.com/');
define('MYSQL' , BASE_URI.'../mysql.inc.php');
define('PDFS_DIR', BASE_URI.'../pdfs/');

session_start();

function my_error_handler($e_number,$e_message,$e_file,$e_line,$e_vars){
	$message = "An error occurred in script '$e_file' on line $e_line:\n$e_message\n";
	$message .= "<pre>".print_r(debug_backtrace(),1)."<pre>\n";
	
	if(!LIVE){
		echo '<div class="alert alert-danger">' .nl2br($message).'</div>';
	}else{
		error_log($message,1,CONTACT_EMAIL,'From:yongsookim.com@gmail.com');
		if($e_number != E_NOTICE){
			echo '<div class="alert alert-danger">A system error occured. We apologize for the inconvenience.</div>';
		}
	}//End of $live IF-ELSE
	return true;
}//End of my_error handler()definition

set_error_handler('my_error_handler');

function redirect_invalid_user($check='user_id', $destination='index.php', $protocol='http://'){
	if(!isset($_SESSION[$check])){
		$url = $protocol.BASE_URL.$destination;
		header("Location:$url");
		exit();
	}
}
