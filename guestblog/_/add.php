<?php
/**
 * Susek blog
 * 
 * @todo 
 * nice editor
 * auth
 * 
 */

/**
 * table susek_blog
 * id, date_stamp, private, subject, content
 */

	require_once "../init.inc";

	$TITLE = '���������� � ���� ������';
	$HEAD  = '';
	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$subject = addslashes(strip_tags($_POST['subject']));
		$content = addslashes(strip_tags($_POST['content']));
		$tags = addslashes(strip_tags($_POST['tags']));
		$private = 0;
		
		$SQL = sprintf("INSERT INTO susek_guestblog_entry(date_added, subject, content, tags,private) values(utc_timestamp(),'%s','%s','%s','%s')", $subject, $content, $tags, $private);
		
		query($SQL);
		header("Location: /guestblog/");
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
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a>, <a href="http://susek.ru/guestblog">���������</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<?
	$BODY = ob_get_clean();
	require_once SUSEK_ROOT."design/template/main.inc";
?>