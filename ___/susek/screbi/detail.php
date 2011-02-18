<?
/**
 * Susek show file
 * started 12-12-2007
 * 
 * @todo 
 * tags processing
 * +comments
 * author (*author:...) can edit file
 */
	require "init.inc";
	
	$TITLE = "Файл из сусека";
	$HEAD = '<script language="javascript" src="/design/js/main.js"></script>';
	
	if(! ($ID = $_REQUEST['id'])) {
		//header('');
		echo "Файл не найден";
		finish();	
		exit();
	}
	
	//echo $ID;
	$Obj = new Object();
	
	$item = $Obj->Find($ID);
		
	$TITLE = $Obj->v['title'] . " в сусеке";
	
	$fattr = array (
		'url'	  => $ext_link . $Obj->v['filename'],
		'size'	  => sprintf("%.2f kb", filesize( DIR_STORAGE.$Obj->v['filename'] )/1024 ),
		'lastmod' => date("d/m/Y H:i", $Obj->v['unix_stamp'] + 3*60*60),
	);	
	
	if(is_jpeg($Obj->v['filename'])){
		$img_preview = "<img class='pre' src='" . LINK_STORAGE . basename($Obj->v['filename']) . "' alt='{$fattr['size']}'>";
	//$img_preview = "<img class='pre' src='/img.php?name=".basename($file)."' alt='{$fattr['size']}'>";
	}
	elseif(is_mp3($Obj->v['filename'])){
		$img_preview = "<img src='/design/img/mp3.gif'>";
	}else{
		$img_preview = "<img src='/design/img/misc.gif'>";
	}
?>
<h3><?=$Obj->v['title']?> details</h3>
<?=$img_preview?>
<div> <b>title:</b> <?=$Obj->v['title']?></div>
<div> <b>description:</b> <?=$Obj->v['description']?></div>
<div> <b>tags:</b> <?=$Obj->v['tags']?></div>
<div> <b>date uploaded:</b> <?=date('d-m-Y H:i:s',$item['unixtime'])?></div>
<div> <a href="<?=LINK_STORAGE.$Obj->v['filename']?>">(link)</a></div>

<div class="file_featured">

</div>

<h3>Comments</h3>
<?
	$T_COMMENTS_D = "<div class='filecomment'>".
		"<p><b>%s</b> says:</p>".
		"<p>%s</p>".
		"<font size='-2'>Posted %s</font>".
		"</div>";
		
	$SQL_COMMENTS = <<<COMMENTS
select fc.*, unix_timestamp(fc.date_added) unix_stamp
from $table_file_comments fc
WHERE fc.file_id = $ID
order by unix_timestamp(fc.date_added)
COMMENTS;

	$comments = query($SQL_COMMENTS) or die(mysql_error());
	
	while( $comment = mysql_fetch_assoc($comments) ){
		
		printf($T_COMMENTS_D, 
			strlen($comment['author'])>0?$comment['author']:"аноним", 
			nl2br(stripslashes($comment['content'])),
			date('d-m-Y H:i',$comment['unix_stamp']+3*3600)			
		);
	}


?>
<br>
<a name="addcomment"></a>
<h4><a href="#" onclick="showreply()">Добавить комментарий</a></h4>
<div id="replyform" style="display:none;">
<form method="post" action="comment.add.php">
<input type="hidden" name="file_id" value="<?=$ID?>" />
<input type="hidden" name="parent_id" value="0" />
автор<br>
<input type="text" name="author" /><br>
<!--тема<br>
<input type="text" name="subject" size="50" maxlength="100"/><br>
-->
<textarea name="content" cols=50 rows=10></textarea><br>

<input type="submit" value="Добавить">
</form>
</div>
<p><a class="perm" href="http://susek.ru/screbi">back</a></p>
<?
	finish();
//00000000-27ac-ab34-01d8-6e908b2b5b67
?>