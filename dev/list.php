<?

	define('STAT_ENABLE', false);
	
	require("../init.inc");
	
	//$files = query("select *, unix_timestamp(date_stamp) unix_stamp from susek_file order by unix_timestamp(date_stamp) desc limit 20") or print(mysql_error());
	$files = query("select * from susek_blog") or print(mysql_error());
	
	while($row = mysql_fetch_assoc($files)){
		print_r($row);	
	}
?>