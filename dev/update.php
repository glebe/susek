<?
	
	define('STAT_ENABLE',false);
	require "../init.inc";

	
	$dir = DIR_ROOT . "storage/tmp/";

	$files = array();
	$files_sql = query("select * from susek_file");
	
	while($row = mysql_fetch_assoc($files_sql)){
		$files[] = $row['filename'];
	}
	
	if ($handle = opendir($dir)) {
    //$i=0;
		while ( false !== ($file = readdir($handle))) {
        
			if ($file != "." && $file != "..") {
            //$i++;
				$lastmod = gmdate("Y-m-d H:i:s", filemtime($dir.$file));
				
				if( !in_array($file,$files) ){

		$ID = uuid();

		$title		 = $file; //strlen( $_POST['fn'][$key] )>0 ? addslashes($_POST['fn'][$key]) : addslashes( $_FILES['ff']['name'][$key] );
		$description = '';//addslashes($_POST['fd'][$key]);
		$tags 		 = '';//addslashes($_POST['ft'][$key]);
		$filename    = $ID.fileext($file);

		$SQL = sprintf('insert into susek_file(id, title, date_added, filename, description, tags) values( "%s", "%s", "%s", "%s", "%s", "%s" )', $ID, $title,$lastmod, $filename, $description, $tags);		
		
		//if( rename($dir.$file, $dir.$filename) && query($SQL)) echo '* ';
		echo $file."\n<br/>";
		
				}
			}
		}
    
		closedir($handle);
    
	}
?>