<?php
/*
 * List Tags
 * started: 08-05-07
 * author: Gleb Pereyaslavskiy
 */
	require_once "../init.inc";	

	$TITLE = 'сусек по тегам';
	$HEAD  = '';

		$SQL = <<<FILE
SELECT sf.*, unix_timestamp(sf.date_added) unix_stamp, count(sfc.id) comments_count
FROM $table_file sf 
LEFT JOIN $table_file_comments sfc ON sfc.file_id=sf.id 
GROUP BY sf.id
ORDER BY sf.date_added DESC, sf.id desc
$limit
FILE;

	$list = query($SQL) or die(mysql_error());
	
	$tag_list = array();
	//$tags_all  = array();
	$tag_list_freq = array();
	
	while( $item = mysql_fetch_assoc($list) ){
		
		$tags_item = split(' ',trim($item['tags']));//echo "{$item['tags']}<br/>";
		//array_walk($tags_item,'trim');
/*		foreach($tags_item as &$tag){
			addslashes(trim($tag));
		}
*/		
		foreach($tags_item as $_){
			$_ = addslashes(trim($_));
			
			if(strlen($_)==0) continue;
			
			//array_push($tags_all, $_);
			$tag_pntr = array_search($_, $tag_list);
			
			if(!is_numeric( $tag_pntr) ){
				array_push($tag_list, $_);
				array_push($tag_list_freq, 1);//frequency
			}
			else{
				$tag_list_freq[$tag_pntr]++;
			}
		}
	}
	
	//print_r( array(/*'tags_all'=>$tags_all,*/'tags_list'=>$tag_list,'tag_list_freq'=>$tag_list_freq) );
	foreach($tag_list as $tagid=>$tag){
		echo "^$tag ({$tag_list_freq[$tagid]}) ";
	}
	
	finish();
?>