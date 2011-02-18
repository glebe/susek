<?
/**
 * 
 */
	if($_SERVER['REQUEST_METHOD']!='POST') header("Location: /guestblog/");
	require_once "../init.inc";
	
	$parent_id = (int)$_POST['parent_id'];
	$entry_id = (int)$_POST['entry_id'];
	$subject = strip_tags(addslashes($_POST['subject']));
	$content = strip_tags(addslashes($_POST['content']));
	$author	 = strip_tags(addslashes($_POST['author']));
	$SQL = <<<SQL
insert into susek_guestblog_comment(entry_id,parent_id,date_added,subject,content,author) values('%s','%s',utc_timestamp(),'%s','%s','%s')
SQL;
	query(sprintf($SQL,$entry_id,$parent_id,$subject,$content,$author)) or die(mysql_error());
	header('Location: /guestblog/?item='.$entry_id);
?>