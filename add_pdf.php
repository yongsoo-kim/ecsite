<?php

require('./includes/config.inc.php');
redirect_invalid_user('user_admin');
require(MYSQL);

$page_title='Add a PDF';
include('./includes/header.html');

$add_pdf_errors=array();

//if the form was submitted, calidate the title and description.

if($_SERVER['REQUEST_METHOD']==='POST'){
	if(!empty($_POST['title'])){
		$t = escape_data(strip_tags($_POST['title']),$dbc);
	}else{
		$add_pdf_errors['title'] = 'Please enter the title!';
	}
	if(!empty($_POST['description'])){
		$d = escape_data(strip_tags($_POST['description']),$dbc);
	}else{
		$add_pdf_errors['description'] = 'Please enter the description!';
	}

	//check for PDF
	if(is_uploaded_file($_FILES['pdf']['tmp_name']) && ($_FILES['pdf']['error'] === UPLOAD_ERR_OK)){
		$file = $_FILES['pdf'];
		//validate the file information
		$size = ROUND($file['size']/1024);
		if($size>5120){
			$add_pdf_errors['pdf'] = 'The uploaded file was too large.';
		}
		//examine whether this is pdf or not!
		$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
		if(finfo_file($fileinfo,$file['tmp_name'])!=='application/pdf'){
			$add_pdf_errors['pdf'] = 'The uploaded file was not a PDF.';
		}
		
		finfo_close($fileinfo);
		
		//if there is no errors,create the file's new name and destinations
		if(!array_key_exists('pdf',$add_pdf_errors)){
			$tmp_name = sha1($file['name']).uniqid('',true);
			$dest = PDFS_DIR.$tmp_name.'_tmp';
		
			if(move_uploaded_file($file['tmp_name'],$dest)){
				
				//Store the data in the session for later use:
				$_SESSION['pdf']['tmp_name'] = $tmp_name;
				$_SESSION['pdf']['size'] = $size;
				$_SESSION['pdf']['file_name'] = $file['name'];
				
				//Print a message
				
				echo '<div class="alert alert-success"><h3>The file has been uploaded!</h3></div>';
			}else{
				trigger_error('The file could not be moved.');
				unlink($file['tmp_name']);
			}
			
			
		}//End of array_key_exists()IF
		
	}elseif(!isset($_SESSION['pdf'])){
		switch ($_FILES['pdf']['error']){
			case 1:
			case 2:
				$add_pdf_errors['pdf'] = 'The uploaded file was too large.';
				break;
			case 3:
				$add_pdf_errors['pdf'] = 'The file was noly partially uploaded.';
				break;
			case 6:
			case 7:
			case 8:
				$add_pdf_errors['pdf'] = 'The file could not be uploadded to to a system error.';
				break;
			case 4:
			default: 
				$add_pdf_errors['pdf'] = 'No file was uploaded.';
		}//End of SWITCH
	}//End of $FILES IF-ELSEIF-ELSE
	
	//Add the PDF to the database
	if(empty($add_pdf_errors)){
		$fn = escape_data($_SESSION['pdf']['file_name'],$dbc);
		$tmp_name = escape_data($_SESSION['pdf']['tmp_name'],$dbc);
		$size = (int)$_SESSION['pdf']['size'];
		$q = "INSERT INTO pdfs(title, description, tmp_name, file_name, size) VALUES('$t','$d','$tmp_name','$fn', $size)";
		$r = mysqli_query($dbc,$q);
		
		//if the query worked, rename the file
		if(mysqli_affected_rows($dbc) === 1){
			$original = PDFS_DIR.$tmp_name.'_tmp';
			$dest = PDFS_DIR.$tmp_name;
			rename($original,$dest);
			//_tmp will be removed
			
			//Indicate the success to the user and clear the values
			echo '<div class="alert alert-success"><h3>The PDF has been added!</h3></div>';
			$_POST = array();
			$_FILES[] = array();
			unset($file,$_SESSION['pdf']);
		}else{ //If id did not run OK
			trigger_error('The PDF could not be added due to a system error. We apologize for any inconvenience.');
			unlink($dest);
		}
	}//End of $errors IF
	
	
}else{ //clear a Get REQUEST
	unset($_SESSION['pdf']);
}//End of the submission IF

//Begin the form

require('includes/form_functions.inc.php');
?>
<h1>Add a PDF</h1>
<form enctype="multipart/form-data" action="add_pdf.php" method="post" accept-charset="utf-8">
<input type="hidden" name="MAX_FILE_SIZE" value="5242880">
<fieldset>
	<legend>Fill out the form to add a PDF to the site:</legend>

<?php
create_form_input('title','text','Title',$add_pdf_errors);
create_form_input('description','textarea','Description',$add_pdf_errors);

//start creating the file input
//do not use create_form_input, because it is different from all those text,password,email and so on

echo '<div class="form-group';

//check for an error or successful upload
if(array_key_exists('pdf',$add_pdf_errors)){
	echo ' has-error';
}else if(isset($_SESSION['pdf'])){
	echo ' has-success';
}

//add the file input
echo '"><label for ="pdf" class="control-label">PDF</label><input type="file" name="pdf" id="pdf">';

//check for an error to be displayed

if(array_key_exists('pdf',$add_pdf_errors)){
	echo '<span class="help-block">'.$add_pdf_errors['pdf'].'</span>';

}else{//No error
	if(isset($_SESSION['pdf'])){
		echo '<p class="lead">Currently:"'.$_SESSION['pdf']['file_name'].'"</p>';
	}
}//end of errors IF-ELSE

//Complete the file input DIV:26

echo '<span class="help-block">PDF only,5MB Limit</span></div>';
?>
<!-- complete the form and the page -->
<input type="submit" name="submit_button" value="Add This PDF" id="submit_button" class="btn btn-default" />
</fieldset>
</form>
<?php include('./includes/footer.html')?>

