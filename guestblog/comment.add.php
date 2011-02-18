<?
/**
 * Susek add blog comment
 * started 11-12-2007
 */
	require "init.inc";

	$ss_key   = $_POST['ss_key'];
	$ss_check = $_POST['ss_phrase'];
	
	if($_SERVER['REQUEST_METHOD']!='POST' or !$ss_key or !ss_validate( $ss_key, $ss_check)){
		header("Location: {$blog_dir}");
		exit();
	}
	
	function ss_validate($key, $check){
		/**
		 * delete sskey from session
		 * check validity
		 */
		$ss_check = $_SESSION['antispam'][$key];
		unset($_SESSION['antispam'][$key]);
		
		if(!$check or !$key) return false;
		
		return $ss_check == $check;
	}
	
	$parent_id = (int)$_POST['parent_id'];
	$entry_id = (int)$_POST['entry_id'];
	$subject = addslashes( strip_tags( $_POST['subject'] ) );
	$content = addslashes( strip_tags( $_POST['content'] ) );
	$author	 = addslashes( strip_tags( $_POST['author'] ) );
	$SQL = <<<SQL
insert into $blog_table_comments(entry_id,parent_id,date_added,subject,content,author) values('%s','%s',utc_timestamp(),'%s','%s','%s')
SQL;
	query(sprintf($SQL,$entry_id,$parent_id,$subject,$content,$author)) or die(mysql_error());
	header("Location: {$blog_dir}{$entry_id}.html");
?>