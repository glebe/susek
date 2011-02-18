<?
/**
 * Susek add blog comment
 * started 11-12-2007
 */
	require "init.inc";

	if($_SERVER['REQUEST_METHOD']!='POST') header("Location: {$blog_dir}");
	
	$parent_id = (int)$_POST['parent_id'];
	$entry_id = (int)$_POST['entry_id'];
	$subject = addslashes( strip_tags( $_POST['subject'] ) );
	$content = addslashes( strip_tags( $_POST['content'], $allowable_tags ) );
	$author	 = addslashes( strip_tags( $_POST['author'] ) );
	$SQL = <<<SQL
insert into $blog_table_comments(entry_id,parent_id,date_added,subject,content,author) values('%s','%s',utc_timestamp(),'%s','%s','%s')
SQL;
	query(sprintf($SQL,$entry_id,$parent_id,$subject,$content,$author)) or die(mysql_error());
	header("Location: {$blog_dir}{$entry_id}.html");
?>