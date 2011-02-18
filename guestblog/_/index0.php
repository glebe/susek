<?php
/**
 * Susek blog
 * 
 * @todo 
 * posting
 * commenting
 * 
 */

/**
 * table susek_blog
 * id, date_stamp, private, subject, content
 */

	require_once "../init.inc";

	$TITLE = 'скажи';
	$HEAD  = '';
	
?>
<h2>≈ще один блог :)</h2>
<h4><a href="http://susek.ru/guestblog/add">скажи</a></h4>
<?
	$SQL = "select *, unix_timestamp(date_added) unix_stamp from susek_guestblog_entry order by unix_timestamp(date_added) desc";// limit 10";
	$list = query($SQL);
	
	while( $item = mysql_fetch_assoc($list) ){
        	
		echo "<div class='entry'>",
		"<div class='subject'>",stripslashes($item['subject']),"</div>",
		"<div class='content'>",nl2br(stripslashes($item['content'])),"</div>",
		"<div class='timestamp'>",date('d/m/Y H:i',$item['unix_stamp']+3*3600),"<div class='sys'><a href='?item=",$item['id'],"'>ссылка</a></div> </div>",
		//"<div class='timestamp'>",$item['date_added'],"</div>",
		"</div>\n";
    
	}
    
?>
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">набивай</a>, <a href="http://susek.ru/screbi">скреби</a>, <a href="http://susek.ru/blog">следи</a></div>
<div id="cp">&copy; 2007, √леб ѕтчк</div>
</div>
<?
	$BODY = ob_get_clean();
	require_once SUSEK_ROOT."design/template/main.inc";
?>