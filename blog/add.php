<?php
/**
 * Susek add blog entry
 */
	define('AUTH_REQUIRED',true);
	require "init.inc";

	$TITLE = '���������� � ���� ������';
	$HEAD  = '';
	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$subject = strip_tags(addslashes($_POST['subject']));
		
		$content = addslashes($_POST['content']);
		if(BLOG_STRICT_MODE) $content = strip_tags($content, $allowable_tags);
		
		$tags = addslashes(strip_tags($_POST['tags']));
		$private = 0;
		
		$SQL = sprintf("INSERT INTO $blog_table_entries(date_added, subject, content, tags,private) values(utc_timestamp(),'%s','%s','%s','%s')", $subject, $content, $tags, $private);
		
		query($SQL);
		header("Location: $blog_dir");
		exit();
	}
?>
<h2>���� - ���������� </h2>
<form method="post">
<div class="entry_edit">
<div class="subject"> ���� <br/><input type="text" name="subject"> </div>
<div class="content"> ���������� <br/> <textarea name="content"></textarea> </div>
<div class="tags"> ���� <br/> <input type="text" name="tags"> </div>
</div>
<input type="submit" value="��������">
</form>
  
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<?
	finish();
?>