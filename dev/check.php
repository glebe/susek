<?
	
	define('STAT_ENABLE',false);
	require "../init.inc";

	
	$dir = DIR_ROOT . "storage/tmp/";

	$files = array();
	$storage_files = array();
	
	//$files_sql = query("select * from susek_file");
	
	//while($row = mysql_fetch_assoc($files_sql)){
	//	$files[] = $row['filename'];
	//}
	
	if ($handle = opendir($dir)) {
    //$i=0;
		while ( false !== ($file = readdir($handle))) {
        
			if ($file != "." && $file != "..") {
            //$i++;
				$lastmod = gmdate("Y-m-d H:i:s", filemtime($dir.$file));
				
/*				if( !in_array($file,$files) ){

		$ID = uuid();

		$title		 = $file; //strlen( $_POST['fn'][$key] )>0 ? addslashes($_POST['fn'][$key]) : addslashes( $_FILES['ff']['name'][$key] );
		$description = '';//addslashes($_POST['fd'][$key]);
		$tags 		 = '';//addslashes($_POST['ft'][$key]);
		$filename    = $ID.fileext($file);

		$SQL = sprintf('insert into susek_file(id, title, date_added, filename, description, tags) values( "%s", "%s", "%s", "%s", "%s", "%s" )', $ID, $title,$lastmod, $filename, $description, $tags);		
		
		//if( rename($dir.$file, $dir.$filename) && query($SQL)) echo '* ';
		//echo $file."\n<br/>";
				}*/
		$storage_files[] = $file;
			}
		}
    
		closedir($handle);
    
	}
	$wtitles = array();
	
	//$diff_sql = query(sprintf("select * from susek_file where filename in('%s')", implode("','",$storage_files)) ) or die(mysql_error());
	
	//$diff_sql = query(" select sf1.id sf1id, sf2.id sf2id, sf1.title t1 from susek_file sf1 inner join susek_file sf2 on (sf1.filename=sf2.title)" ) or die(mysql_error());
	$diff_sql = query("select count(*) a from susek_file where description='true'" ) or die(mysql_error());
	while($row = mysql_fetch_assoc($diff_sql)){
		//print_r($row);
		//echo $row['sf1id'],' ',$row['sf2id'],' [',$row['t1'],']',"<br/>";
		echo $row['a'],"<br/>";
//$upd1 = sprintf("update susek_file set filename = '%s', description='true' where id='%s'",$row['sf2id'].fileext($row['t1']), $row['sf1id']);
//$upd2 = sprintf("update susek_file set description='false' where id='%s'",$row['sf2id']);
//query($upd1);
//query($upd2);
//		$wtitles[] = $row['title'];
	}

	/*$diff2_sql = query(sprintf("select count(*) from susek_file where filename in('%s')", implode("','",$wtitles)) ) or die(mysql_error());
	
	while($row = mysql_fetch_assoc($diff2_sql)){
		//print_r($row);
		echo $row['title'],"<br/>";
		
		//$wtitles[] = $row['title'];
	}*/
	
	
?>