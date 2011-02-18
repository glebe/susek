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

	$TITLE = 'добавление в блог сусека';
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
<h2>Блог - добавление </h2>
<form method="post">
<div class="entry_edit">
<div class="subject"> тема <br/><input type="text" name="subject"> </div>
<div class="content"> содержание <br/> <textarea name="content"></textarea> </div>
<div class="tags"> теги <br/> <input type="text" name="tags"> </div>
</div>
<input type="submit" value="Добавить">
</form>
  
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">набивай</a>, <a href="http://susek.ru/screbi">скреби</a>, <a href="http://susek.ru/blog">следи</a>, <a href="http://susek.ru/guestblog">выражайся</a></div>
<div id="cp">&copy; 2007, Глеб Птчк</div>
</div>
<?
	$BODY = ob_get_clean();
	require_once SUSEK_ROOT."design/template/main.inc";
?>