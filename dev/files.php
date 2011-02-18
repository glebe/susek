<?

	define('STAT_ENABLE', false);
	
	require("../init.inc");
	
	//$files = query("select title from susek_file order by title");
	query("delete from susek_file where description='false'");
	query("update susek_file set description='' where description='true'");
	
	exit();
	while($row = mysql_fetch_assoc($files)){
		echo $row['title'],"\n<br/>";	
	}
?>