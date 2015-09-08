<?php

require('./includes/config.inc.php');
require(MYSQL);
require('./includes/password.php');
$page_title = 'Register';
include('./includes/header.html');

// For storing registration errors:
$reg_errors = array();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	
	//first name validation	
	if(preg_match('/^[A-Z\'.-]{2,45}$/i',$_POST['first_name'])){
		$fn = escape_data($_POST['first_name'],$dbc);
	}else{
		$reg_errors['first_name'] = 'Please enter your first name!';
	}
	//last name validataion
	if(preg_match('/^[A-Z\'.-]{2,45}$/i', $_POST['last_name'])){
		$ln = escape_data($_POST['last_name'],$dbc);
	}else{
		$reg_errors['last_name'] = 'Please enter your last name!';
	}

	//user name validation
	if(preg_match('/^[A-Z0-9]{2,45}$/i', $_POST['username'])){
		$u = escape_data($_POST['username'],$dbc);
	}else{
		$reg_errors['username'] = 'Please enter a desired name using only letters and numbers';
	}
	
	//email validation
	if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
		$e = escape_data($_POST['email'],$dbc);
	}
	else{
		$reg_errors['email'] = 'Please enter a valid email address!';
	}
	//check for a password
	if (preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $_POST['pass1']) ) {
		if ($_POST['pass1'] === $_POST['pass2']) {
			$p = $_POST['pass1'];
		} else {
			$reg_errors['pass2'] = 'Your password did not match the confirmed password!';
		}
	} else {
		$reg_errors['pass1'] = 'Please enter a valid password!';
	}
	
	//if there is no error check the availability of the email address and user name
	if(empty($reg_errors)){
		
		$q = "SELECT email,username FROM users WHERE email ='$e' OR username='$u'";
		$r = mysqli_query($dbc, $q);
		$rows = mysqli_num_rows($r);
		
	
		if($rows === 0){
		
			$q = "INSERT INTO users (username, email, pass, first_name, last_name,date_created,date_expires) VALUES ('$u', '$e', '"  .  password_hash($p, PASSWORD_BCRYPT) .  "', '$fn', '$ln',NOW(),ADDDATE(NOW(), INTERVAL 1 MONTH) )";
				
				  $r = mysqli_query($dbc, $q);
				  if(mysqli_affected_rows($dbc) === 1){
				  	
				  	echo '<div class="alert alert-success"><h3>Thanks!</h3><p>Thank you for registering! You may now log in and access the site\'s content</p></div>';
				  	
				  	$body = "Thank you for registering at Yongsoo\'s ec site. So,so much!\n
				  			 If you need any more information, Please send your email to yongsookim.com@gmail.com anytime!\n";
				  	
				  //send mail.
				   mail($_POST['email'],'Registration Confirmation',$body,'From:yongsookim.com@gmail.com');
				   include ('./includes/footer.html');
				   exit();
				  
				  }else{
				  	trigger_error('You could not be registered due to a system error. We aplologize for any inconvenience. We will correct the error ASAP');
				  	
				  }
				  
		}else{
			if($rows === 2){
				$reg_errors['email'] = 'This email address has already been registered. If you have forgotten your password, use the link at left to have your password sent to you.';
				$reg_errors['username'] = 'This username has already been registered. Please try another.';
			}else{
				
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				if(($row[0] === $_POST['email']) && ($row[1] === $_POST['username'])){
					$reg_errors['email'] ='This email address has already been registerd. If you have forgotten your password, use the link at left to have your password sent to you.';
					$reg_errors['username'] ='This username has already been registerd. If you have forgotten your password, use the link at left to have your password sent to you.';
				}elseif($row[0] === $_POST['email']){
					$reg_errors['email'] = 'This email address has already been registered. If you have forgotten your password, use the link at left to have your password sent to you.';
				}elseif($row[1] === $_POST['username']){
					$reg_errors['username'] = 'this username has already been registered. Please try another';
				}
			} //End of $rows === 2ELSE.
		}
	}
}


require_once('./includes/form_functions.inc.php');
?>
<h1>Register</h1>
<p>Access to the site's content is available to registered users at a cost of $10.00(US) per year. Use the form below to begin the registration process.
<strong>Note:All fields are required.</strong>
After completing the form, you'll be presented with the opportunity to securely pay for your yearly subscription via <a href="http://www.paypal.com">PayPal</a>.</p>
<br>
<form action="register.php" method="post" accept-chatset="utf-8">
<?php 

//Not necessary anymore.
//$reg_errors = array();

create_form_input('first_name', 'text','First Name',$reg_errors);
create_form_input('last_name', 'text','Last Name',$reg_errors);
create_form_input('username', 'text','Desired Username',$reg_errors);

echo '<span class="help-block">Only letters and numbers are allowed.</span>';

create_form_input('email','email','Email Address',$reg_errors);
create_form_input('pass1','password','Password',$reg_errors);

echo '<span class="help-block">Must be at least 6 characters long, with at leat one lowercase letter, one upppercase letter, and one number</span>';
create_form_input('pass2', 'password', 'Confirm Password', $reg_errors);
?>

<input type="submit" name="submit_button" value="Next&rarr;" id="submit_button" class="btn btn-default" />
</form>

<?php 
include ('./includes/footer.html');
?>