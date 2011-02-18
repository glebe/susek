<?php
/**
 * Susek screblya
 * 
 * @todo 
 * tags
 * rating
 * sorting - by date, tag
 * switch views
 * stars (favs)
 */
	require_once "../init.inc";

	// paging
	
	$SQL = "select count(*) from $table_file";
	$total 	= array_pop(mysql_fetch_array(query($SQL)));

	$showOnPage = 24;
	$skip 	= (int)$_REQUEST['skip'];
	//$skip_next = $skip+24;
	
	$showLinkBack 	 = $skip + $showOnPage < $total;
	$showLinkForward = $skip > 0;
	
	$TITLE = 'сусек скребут здесь';
	$HEAD  = '';
	
?>
<h2><a href="http://susek.ru/screbi/all.html">Всё</a> (<?=$total?> уже в сусеке)</h2>
<?
	$limit = $_REQUEST['filter']=="all"?'':" LIMIT $showOnPage OFFSET ".$skip;
	if($_REQUEST['filter']=="tag"){
		$tag = addslashes($_REQUEST['tag']);
		$filter_by_tag = "WHERE tags like '%{$tag}%'";
	}
	$SQL = <<<FILE
SELECT sf.*, unix_timestamp(sf.date_added) unix_stamp, count(sfc.id) comments_count
FROM $table_file sf 
LEFT JOIN $table_file_comments sfc ON sfc.file_id=sf.id 
$filter_by_tag
GROUP BY sf.id
ORDER BY sf.date_added DESC, sf.id desc
$limit
FILE;

	$list = query($SQL) or die(mysql_error());

		while( $item = mysql_fetch_assoc($list) ){
        	
			$file  = $item['filename'];
        	$title = stripslashes($item['title']);

        	array_walk($item,'stripslashes_ex');
			extract($item,EXTR_PREFIX_ALL,'item');
	
	
				$fattr = array (
					'url'	  => "/screbi/{$item['id']}.html",
					'size'	  => sprintf("%.2f kb", filesize( DIR_STORAGE.$file )/1024 ),
					//'lastmod' => date("d/m/Y H:i", $item['unix_stamp'] + 3*60*60),
				);
				
				if(is_jpeg($file) /*|| is_gif($file)*/){
					$img_preview = "<a class='name' href='{$fattr['url']}'><img class='pre' src='/img-thumb/".basename($file)."' alt='{$item['title']}'></a>";
					//$img_preview = "<img class='pre' src='/img.php?name=".basename($file)."' alt='{$fattr['size']}'>";
				}
				elseif(is_mp3($file)){
					$img_preview = "<div class='pre'><img src='/design/img/mp3.gif'></div>";
				}else{
					$img_preview = "<div class='pre'><img src='/design/img/misc.gif'></div>";
				}

				echo "<div class='file'>",
				$img_preview,
				"<a class='name' href='{$fattr['url']}'>$title</a><br/>",
				"<p class='attr'>{$fattr['size']}</p>",
				//"<p class='attr'>{$fattr['lastmod']}</p>",
				$item['comments_count']>0 ? "<p class='attr'>{$item['comments_count']} comments</p>":"",
				//"<p class='attr'>{$fattr['lastmod']}</p>",
				"</div>\n";
		}
?>
<br clear=all />
<?if($showLinkBack) printf('<a class="perm" href="?skip=%s">предыдущие %s</a>', $skip+$showOnPage, $showOnPage)?>
&nbsp;&nbsp;
<?if($showLinkForward) printf('<a class="perm" href="?skip=%s">следующие %s</a>', max($skip-$showOnPage, 0), $showOnPage)?>

<br clear=all />
<br clear=all />
<div>
<div id="cpl"><a class="perm" href="http://susek.ru/nabey">набивай</a> и <a class="perm" href="http://susek.ru/blog">будь в курсе</a></div>
<div id="cp">&copy; 2007, Глеб Птчк</div>
</div>
<?
/*
javascript:R=0; x1=.1; y1=.05; x2=.25; y2=.24; x3=1.6; y3=.24; x4=300; y4=200; x5=300; y5=200; DI=document.images; DIL=DI.length; function A(){for(i=0; i<DIL; i++){DIS=DI[ i ].style; DIS.position='absolute'; DIS.left=Math.sin(R*x1+i*x2+x3)*x4+x5; DIS.top=Math.cos(R*y1+i*y2+y3)*y4+y5}R++}setInterval('A()',5); void(0)
 */
?>
<?	finish();?>