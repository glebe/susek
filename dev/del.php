<?
	define('STAT_ENABLE', false);
	require("../init.inc");
	$ID = $_REQUEST['which'];

	$file = query("select filename from susek_file where id='".$ID."' limit 1");
	$fileCard = mysql_fetch_assoc($file);
	//echo DIR_STORAGE.$fileCard['filename'];
	//print_r($fileCard);
	if(unlink(DIR_STORAGE.$fileCard['filename']) && query("delete from susek_file where id='".$ID."'")) echo $ID,' deleted and purged';
	
?>