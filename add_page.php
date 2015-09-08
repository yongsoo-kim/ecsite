<?php
require('./includes/config.inc.php');

redirect_invalid_user('user_admin');
require(MYSQL);
$page_title ='Add a Site Content Page';
include('./includes/header.html');

$add_page_errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if(!empty($_POST['title'])){
		//use strip tags, as there will be no HTML tags in the title!
		$t = escape_data(strip_tags($_POST['title']),$dbc);
	}else{
		$add_page_errors['title'] = 'Please enter the title!';
	}
	
	if(filter_var($_POST['category'],FILTER_VALIDATE_INT,array('min_range'=>1))){
		$cat = $_POST['category'];
	}else{// no category selected
		$add_page_errors['category'] = 'Please select a category';
	}
	
	if(!empty($_POST['description'])){
		$d = escape_data(strip_tags($_POST['description']),$dbc);
	}else{
		$add_page_errors['description'] = 'Please enter the description!';
	}
	
	if(!empty($_POST['content'])){
		$allowed ='<div><p><span><br><a><img><h1><h2><h3><h4><ul><ol><li><blockquote>';
		$c = escape_data(strip_tags($_POST['content'],$allowed),$dbc);
	}else{
		$add_page_errors['content'] = 'Please enter the content!';
	}
	
	
	//if there are no errors, add the record to the database.
	if(empty($add_page_errors)){
		$q = "INSERT INTO pages(categories_id,title,description,content)
			  VALUES($cat,'$t','$d','$c')";
		$r = mysqli_query($dbc, $q);
		if(mysqli_affected_rows($dbc) === 1){
			echo '<div class="alert alert-success"><h3>The page has been added!</h3></div>';
			// Clear $_POST:
			$_POST = array();
		}else{// If it did not run OK.
			trigger_error('The page could not be added due to a system error. We apologize for any inconvenience.');
		}
	}//End of $add_page_errors IF
}//End of the mai form submission conditional
require('includes/form_functions.inc.php');
?>
<h1>Add a site Contetn Page</h1>
<form action="add_page.php" method="post" accept-charset="utf-8">
<fieldset><legend>Fill out the form to add a page of content:</legend>
<?php 

create_form_input('title', 'text', 'Title', $add_page_errors);
//add category menu
echo '<div class="form-group';
if(array_key_exists('category', $add_page_errors)){
	echo ' has-error';	
}
echo '"><label for ="category" class="control-label">Category</label>
	 <select name="category" class="form-control">
		<option>Select One</option>';

$q = "SELECT id, category FROM categories ORDER BY category ASC";
$r = mysqli_query($dbc, $q);
while($row = mysqli_fetch_array($r, MYSQLI_NUM)){
	echo "<option value=\"$row[0]\"";
	if(isset($_POST['category']) && ($_POST['category'] == $row[0])){
		echo ' selected = "selected"';
	}
	echo ">$row[1]</option>\n";
}
echo '</select>';

if(array_key_exists('category', $add_page_errors)){
	echo '<span class="help-block">'.
	$add_page_errors['category'] .'</span>';
}
echo '</div>';
//complete form

create_form_input('description', 'textarea','Description',$add_page_errors);
create_form_input('content', 'textarea','Content',$add_page_errors);

?>
<input type="submit" name="submit_button" value="Add This Page" id="submit_button" class="btn btn-default" />
</fieldset>
</form>
<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinyMCE.init({
	selector:"#content",
	width:800,
	height:400,
	browser_spellcheck:true,
	plugins:
		"paste,searchreplace,fullscreen,hr,link,anchor,image,charmap,media,autoresize,autosave,contextmenu,wordcount",
		toolbar1: "cut,copy,paste,|,undo,redo,removeformat,|hr,|,link,unlink,anchor,image,|,charmap,media,|,search,replace,|,fullscreen",
		toolbar2:	"bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignjustify,|,formatselect,|,bullist,numlist,|,outdent,indent,blockquote,",

		// Example content CSS (should be your site CSS)
		content_css : "/ex1/html/css/bootstrap.min.css",

	});
</script>

<?php 
include('./includes/footer.html');
?>
