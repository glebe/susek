<?
/**
 * Store external url into storage folder
 */
	require "init.inc";

	if($_SERVER['REQUEST_METHOD']=='POST'){
		echo $_POST['url'];
		//DIR_SAVED
	}else{
?>
<form method="post">
url: <input type="text" name="url" size=50> <input type="submit" value="get">
</form>
<?
	}
	
	finish();
?>