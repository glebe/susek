<?php
/**
 * Susek blog
 * 
 * @todo 
 * +posting
 * +commenting
 * paginating
 * comment replies
 * comment editing
 * 
 */

/**
 * table susek_blog
 * id, date_stamp, private, subject, content
 */

	require_once "../init.inc";

	$TITLE = '�����';
	$HEAD  = '';
	
	$T_POST = "<div class='entry'>".
		"<div class='subject'>%s</div>".
		"<div class='content'>%s</div>".
		"<div class='tags'>%s</div>".
		"<div class='sys'>(<a href='?item=%s'>������</a>) ".
		"<a href='?item=%s&nc=%s'>����������� (%s)</a>".
		"</div>".
		"<div class='timestamp'>������������ %s</div>".
		//"<div class='timestamp'>",$item['date_added'],"</div>",
		"</div>\n";
		
	$T_POST_D = "<div class='entry'>".
		"<div class='subject'>%s</div>".
		"<div class='content'>%s</div>".
		"<div class='sys'>(<a href='?item=%s'>������</a>) ".
		"<a href='?item=%s&nc=%s'>����������� (%s)</a>".
		"</div>".
		"<div class='timestamp'>%s</div>".
		//"<div class='timestamp'>",$item['date_added'],"</div>".
		"</div>\n";
		
	$T_COMMENTS_D_UNAVALIBLE = "<div id='comments'>����������� �������� ����������</div>";
	
	$T_COMMENTS_D = "<div class='comment'>".
		"<div class='head'>".
		"<b>%s</b><br/>".
		"<font size='-1'>%s</font><br>".
		"<font size='-1'> %s</font>".
		"</div>".
		"<div class='content'>%s</div>".
		"</div>";
	
?>
<h2>��� ���� ���� :)</h2>
<h4><a href="http://susek.ru/guestblog/add">�����</a></h4>
<?
if( $ID = (int)$_REQUEST['item'] ){
		$SQL_ENTRY = <<<ENTRY
select be.*, unix_timestamp(be.date_added) unix_stamp, count(bc.id) comments_count
from susek_guestblog_entry be
left join susek_guestblog_comment bc on bc.entry_id = be.id
WHERE be.id = $ID
group by be.id
order by unix_timestamp(be.date_added) desc
limit 1
ENTRY;
		$SQL_COMMENTS = <<<COMMENTS
select bc.*, unix_timestamp(bc.date_added) unix_stamp
from susek_guestblog_comment bc
WHERE bc.entry_id = $ID
order by unix_timestamp(bc.date_added)
COMMENTS;

	$entry = query($SQL_ENTRY) or die(mysql_error());
	$comments = query($SQL_COMMENTS) or die(mysql_error());
	
	$item = mysql_fetch_assoc($entry);
	
	echo "<h4><a href='/guestblog/'>��������� � �������</a></h4>";
	
	printf($T_POST_D, stripslashes($item['subject']), stripslashes($item['content']), $item['id'], $item['id'], $item['comments_count'], $item['comments_count'], date('d/m/Y H:i',$item['unix_stamp']+3*3600));
	
	while( $comment = mysql_fetch_assoc($comments) ){
		
		printf($T_COMMENTS_D, $comment['subject'], strlen($comment['author'])>0?$comment['author']:"(��������)", date('d-m-Y H:i',$comment['unix_stamp']+3*3600), nl2br(stripslashes($comment['content'])) );
	}
	
	//printf($T_COMMENTS_D_UNAVALIBLE);
	echo <<<ADD_COMMENT
<a name="addcomment"></a>
<h4><a href="javascript:void(0);return true" onclick="showreply()">�������� �����������</a></h4>
<div id="replyform" style="display:none;">
<form method="post" action="comment.add.php">
<input type="hidden" name="entry_id" value="$ID" />
<input type="hidden" name="parent_id" value="0" />
�����<br>
<input type="text" name="author" /><br>
����<br>
<input type="text" name="subject" size="50" maxlength="100"/><br>
����������<br>
<textarea name="content" cols=50 rows=10></textarea><br>

<input type="submit" value="��������">
</form>
</div>
ADD_COMMENT;

}else{
	
	$SQL_ENTRIES = <<<ENTRIES
select be.*, unix_timestamp(be.date_added) unix_stamp, count(bc.id) comments_count
from susek_guestblog_entry be
left join susek_guestblog_comment bc on bc.entry_id = be.id
group by be.id
order by unix_timestamp(be.date_added) desc
limit 15
ENTRIES;
	$list = query($SQL_ENTRIES) or die(mysql_error());
	
	while( $item = mysql_fetch_assoc($list) ){
        $tags = split(' ', stripslashes($item['tags']));
        
        $tags_f = array();
        foreach($tags as $tag){
        	$tags_f[] = '<a href="#">'.$tag.'</a>';
        }
        
		printf( $T_POST, stripslashes($item['subject']), nl2br(stripslashes($item['content'])),implode(', ',$tags_f), $item['id'], $item['id'], $item['comments_count'], $item['comments_count'], date('d/m/Y H:i',$item['unix_stamp']+3*3600));
    
	}

}

    
?>
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<script language="javascript">
function showreply(){
	obj = document.getElementById('replyform');
//	alert(obj.style.display );
	obj.style.display = 'block';
//	alert(obj.style.display );
return true;
}
</script>
<?
	$BODY = ob_get_clean();
	require_once SUSEK_ROOT."design/template/main.inc";
?>