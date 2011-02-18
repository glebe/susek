<?
/**
 * Susek blog entry.
 * started 12-12-2007
 * 
 * @todo 
 * 
 * +commenting
 * paginating
 * comment replies
 * comment editing
 */
	require_once "init.inc";
	
	if( !($ID = (int)$_REQUEST['item']) ){
//		header("404");
		$TITLE = "������� �� ������";
		echo "<h1>No such entry</h1><a href='$blog_dir'>������� � ��������</a>";
		finish();
	}
	
	$ss_phrase = rand( 1000, 9999 );
	$ss_key = sha1( rand(1,1000000) );
	
	$_SESSION['antispam'][$ss_key] = $ss_phrase;
	
	$T_POST_D = "<div class='entry'>".
		"<div class='subject'>%s</div>".
		"<div class='content'>%s</div>".
		"<div class='sys'>(<a href='%s.html'>������</a>) ".
		"<a href='%s.html?nc=%s'>����������� (%s)</a>".
		"</div>".
		"<div class='timestamp'>%s</div>".
		//"<div class='timestamp'>",$item['date_added'],"</div>".
		"</div>\n";
		
	$T_COMMENTS_D_UNAVALIBLE = "<div id='comments'>����������� �������� ����������</div>";
	
	$T_COMMENTS_D = "<div class='comment'>".
		"<div class='head'>".
		"<b>%s</b><br/>".
		"<font size='-1'>%s</font><br>".
		"<font size='-1'>%s</font>".
		"</div>".
		"<div class='content'>%s</div>".
		"</div>";
		
	
	$SQL_ENTRY = <<<ENTRY
select be.*, unix_timestamp(be.date_added) unix_stamp, count(bc.id) comments_count
from $blog_table_entries be
left join $blog_table_comments bc on bc.entry_id = be.id
WHERE be.id = $ID
group by be.id
order by unix_timestamp(be.date_added) desc
limit 1
ENTRY;

	$SQL_COMMENTS = <<<COMMENTS
select bc.*, unix_timestamp(bc.date_added) unix_stamp
from $blog_table_comments bc
WHERE bc.entry_id = $ID
AND spam=0
order by unix_timestamp(bc.date_added)
COMMENTS;

	$entry    = query($SQL_ENTRY) or die(mysql_error());
	$comments = query($SQL_COMMENTS) or die(mysql_error());
	
	$item = mysql_fetch_assoc($entry);

?>
<h2>����</h2>
<h4><a href='<?=$blog_dir?>'>��������� � �������</a></h4>
<?
	
	printf($T_POST_D, 
		stripslashes($item['subject']), 
		nl2br(stripslashes($item['content'])), 
		$item['id'], 
		$item['id'], 
		$item['comments_count'], 
		$item['comments_count'], 
		date('d/m/Y H:i',$item['unix_stamp']+3*3600)
	);
	
	while( $comment = mysql_fetch_assoc($comments) ){
		
		printf($T_COMMENTS_D, 
			$comment['subject'],
			strlen($comment['author'])>0?$comment['author']:"(��������)", 
			date('d-m-Y H:i',$comment['unix_stamp']+3*3600), 
			nl2br(stripslashes($comment['content']))
		);
	}
	
	//printf($T_COMMENTS_D_UNAVALIBLE);
	//$SS_KEY = $_SESSION['']
	//$SS_PHRASE = crc32($CC);
	echo <<<ADD_COMMENT
<a name="addcomment"></a>
<h4><a href="#" onclick="showreply()">�������� �����������</a></h4>
<div id="replyform" style="display:none;">
<form method="post" action="comment.add.php">
<input type="hidden" name="entry_id" value="$ID" />
<input type="hidden" name="parent_id" value="0" />
<input type="hidden" name="ss_key" value="$ss_key" />
�����<br>
<input type="text" name="author" /><br>
��������: ������� <font size=+1 color="gray">$ss_phrase</font><br>
<input type="text" name="ss_phrase" /><br>
����<br>
<input type="text" name="subject" size="50" maxlength="100"/><br>
����������<br>
<textarea name="content" cols=50 rows=10></textarea><br>

<input type="submit" value="��������">
</form>
</div>
ADD_COMMENT;
?>
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<script language="javascript">
function showreply(){
	obj = document.getElementById('replyform');
	obj.style.display = 'block';
	return true;
}
</script>
<?
	finish();
?>