<?php
/**
 * Susek blog
 * 
 * @todo 
 * +posting
 * paginating
 * list by date
 * list by tag
 */
	require_once "init.inc";
	
	$TITLE = 'Колбаса по два двадцать в сусеке';
	$HEAD  = '';
	
	// paging
	$SQL = "select count(*) from $blog_table_entries";
	$total 	= array_pop(mysql_fetch_array(query($SQL)));

	$showOnPage = 10;
	$skip 	= (int)$_REQUEST['skip'];
	//$skip_next = $skip+24;
	
	$showLinkBack 	 = $skip + $showOnPage < $total;
	$showLinkForward = $skip > 0;
	
	$T_POST = "<div class='entry'>".
		"<div class='subject'>%s <b>%s</b></div>".
		"<div class='content'>%s</div>".
		"<div class='tags'>%s</div>".
		"<div class='sys'>(<a href='{$blog_dir}%s.html'>ссылка</a>) ".
		"<a href='{$blog_dir}%s.html?nc=%s'>Комментарии (%s)</a>".
		"</div>".
		"</div>\n";
?>
<h2>Еще один блог :)</h2>
<h4><a href="http://susek.ru/guestblog/add">скажи</a></h4>
<?
	$SQL_ENTRIES = <<<ENTRIES
SELECT be.*, unix_timestamp(be.date_added) unix_stamp, count(bc.id) comments_count
FROM $blog_table_entries be
LEFT JOIN $blog_table_comments bc ON bc.entry_id = be.id
GROUP BY be.id
ORDER BY unix_timestamp(be.date_added) DESC
LIMIT $showOnPage
OFFSET $skip
ENTRIES;
	$list = query($SQL_ENTRIES) or die(mysql_error());
	
	while( $item = mysql_fetch_assoc($list) ){
        $tags = trim(stripslashes($item['tags']));
        $tags = split(' ', $tags );
        
        $tags_f = array();
        foreach($tags as $tag){
        	if(strlen(trim($tag))>0)
        	$tags_f[] = "<a href='{$blog_dir}tag/$tag'>".$tag.'</a>';
        }
        unset($tag_line);
		if(count($tags_f)>0){
			$tag_line = "<b>Теги:</b> " . implode(', ',$tags_f);
		}
		printf( $T_POST, date('d/m/Y H:i',$item['unix_stamp']+3*3600), stripslashes($item['subject']), nl2br(stripslashes($item['content'])), $tag_line, $item['id'], $item['id'], $item['comments_count'], $item['comments_count'] );
    
	}
    
?>
<br clear=all />
<?if($showLinkBack) printf('<a class="perm" href="?skip=%s">предыдущие %s</a>', $skip+$showOnPage, $showOnPage)?>
&nbsp;&nbsp;
<?if($showLinkForward) printf('<a class="perm" href="?skip=%s">следующие %s</a>', max($skip-$showOnPage, 0), $showOnPage)?>
<br clear=all />
<br clear=all />
<div>
<div id="cpl"><a href="http://susek.ru/nabey">набивай</a>, <a href="http://susek.ru/screbi">скреби</a>, <a href="http://susek.ru/blog">следи</a></div>
<div id="cp">&copy; 2007, Глеб Птчк</div>
</div>
<?
	finish();
?>