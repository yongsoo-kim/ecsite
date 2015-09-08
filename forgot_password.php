<?php
require('./includes/config.php');
require(MYSQL);
require('./includes/password.php');
$page_title ='Forgot your password?';
include('./includes/header.html');

$pass_errors = array();

if($_SERVER['REQUEST_METHOD'] ==='POST'){
	if(filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL)){
		$q = 'SELECT id FROM users WHERE email="'.escape_data($_POST['email'], $dbc).'"';
		$r = mysqli_query($dbc, $q);
		if(mysqli_num_rows($r) === 1){
			list($uid) = mysqli_fetch_array($r, MYSQLI_NUM);
		}else{
			$pass_errors['email'] = 'The submitted email address does not match those on file!';
		}
	}else{
		$pass_errors['email'] = 'Please enter a valid email address!';
	}//End of $_POST['email'] IF
	
	
	if(empty($pass_errors)){
		//set new password
		$p = substr(md5(uniqid(rand(),true)),10,15);
		//DB update.
		$q = "UPDATE users SET pass='".password_hash($p,PASSWORD_BCRYPT)."'WHERE id = $uid LIMIT 1";
		$r = mysqli_query($dbc,$q);
		if(mysqli_affected_rows($dbc) === 1){
			$body ="Your password to log into yongsoo\'s EC site has been temporalily changed to '$p' Please log in using that password and this email address.Then you may change your password to something more familiar";
			mail($_POST['email'], 'Your temporary password.',$body, 'From:yongsookim');
			echo '<h1>Your password has been changed.</h1>
				  <p>You will receive the new,temporary password via email. Once you have logged in with this new password, you may change it by clicking on the "change it by clicking on the "Change Password" link</p>
				 ';
			include('./includes/footer.html');
			exit();
		}else{ //If it did not run ok...
			trigger_error('Your password could nbot be changed due to a system error. We aplogize for any inconvenience.');
		}
	}//End of $uid If
}//End of the main Submit conditional

require_once('./includes/form_functions.inc.php');
?>

<h1>Reset Your Password</h1>
<p>Enter your email address below to resite your password.</p>
<form action="forgot_password.php" method="POST" accept-charset="utf-8">
<?php 
	create_form_input('email', 'email','Email Address',$pass_errors);
?>
<input type="submit" name ="submit_button" value="Reset &rarr;" id="sumbit_button" class="btn btn-default"/>
</form>

<?php include('./includes/footer.html');?>