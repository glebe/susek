<?php
/**
 * Susek add blog entry
 * 
 * TODO: nice editor
 * TODO: auth
 * 
 * HISTORY:
 *  28/03/2008 + text captcha integrated
 */
	require "init.inc";

	$TITLE = '���������� � ���-����-���� ������';
	$HEAD  = '';
	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$ss_key   = $_POST['ss_key'];
		$ss_check = $_POST['ss_phrase'];
	
		if(!$ss_key or !ss_validate( $ss_key, $ss_check)){
			header("Location: {$blog_dir}");
			exit();
		}
		
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
	
	$ss_phrase = rand( 1000, 9999 );
	$ss_pattern_words = array('���� ','���� ','��� ','��� ','������ ', '���� ','����� ','���� ','������ ','������ ');
	$ss_pattern_nums = array('/0/','/1/','/2/','/3/','/4/','/5/','/6/','/7/','/8/','/9/');
	$ss_word_phrase = preg_replace($ss_pattern_nums, $ss_pattern_words,(string)$ss_phrase);
	$ss_key = sha1( rand(1,1000000) );
	
	$_SESSION['antispam'][$ss_key] = $ss_phrase;
?>
<h2>���� - ���������� </h2>
<form method="post">
<div class="entry_edit">
<div class="subject">��������: ������� ����� <font size=+1 color="gray"><?=$ss_word_phrase ?></font> <input style="width:auto" type="text" name="ss_phrase" /></div>
<div class="subject"> ���� <br/><input type="text" name="subject"> </div>
<div class="content"> ���������� <br/> <textarea name="content"></textarea> </div>
<div class="tags"> ���� <br/> <input type="text" name="tags"> </div>
</div>

<input type="hidden" name="ss_key" value="<?=$ss_key ?>" />

<input type="submit" value="��������">
</form>
  
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">�������</a>, <a href="http://susek.ru/screbi">������</a>, <a href="http://susek.ru/blog">�����</a>, <a href="http://susek.ru/guestblog">���������</a></div>
<div id="cp">&copy; 2007, ���� ����</div>
</div>
<?finish();