<?php
/**
 * Susek add blog entry
 * 
 * @todo 
 * nice editor
 * auth
 */
	require "init.inc";

	if(!($ID = (int)$_REQUEST['id'])){
		header("Location: $blog_dir");
		exit();
	}
	
	$TITLE = '�������������� ����� � ������';
	$HEAD  = '';
	
	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$ID = $_POST['id'];
		$subject = strip_tags(addslashes($_POST['subject']));
		
		$content = addslashes($_POST['content']);
		if(BLOG_STRICT_MODE) $content = strip_tags($content, $allowable_tags);
		
		$tags = addslashes(strip_tags($_POST['tags']));
		$private = 0;
		
		$SQL = sprintf("UPDATE $blog_table_entries SET date_modified=utc_timestamp(), subject='%s',content='%s', tags='%s' WHERE id=$ID", $subject, $content, $tags);
		
		query($SQL);
		header("Location: $blog_dir");
		exit();
	}
	
	$ENTRY = "SELECT * FROM $blog_table_entries WHERE id=$ID";
	$res_entry = mysql_fetch_assoc(query($ENTRY));
	
	$subject = stripslashes($res_entry['subject']);
	$content = stripslashes($res_entry['content']);
	$tags = stripslashes($res_entry['tags']);
	
?>
<h2>���� - �������������� </h2>
<form method="post">
<div class="entry_edit">
<input type="hidden" name="id" value="<?=$ID?>">
<div class="subject"> ���� <br/><input type="text" name="subject" value="<?=$subject?>"> </div>
<div class="content"> ���������� <br/> <textarea name="content"><?=$content?></textarea> </div>
<div class="tags"> ���� <br/> <input type="text" name="tags" value="<?=$tags?>"> </div>
</div>
<input type="submit" value="���������">
</form>
  
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<?
	finish();
?>