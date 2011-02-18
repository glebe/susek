<?php
/*
 * local dir listing v0.1.1
 * 
 * started: 2008-05-05
 * author:  Gleb Pereyaslavskiy <gleeeeb@gmail.com>
 * 
 */

	$dirname_preview = "pre_jpeg";
	$dirname     = dirname(__FILE__);//'/tmp';
	
	$dirContent = scandir($dirname);
	
	echo <<<HTML
<html>
<head>
 <title>Photos</title>
 <style>body{font-family:tahoma;font-size:8pt}
 .a{}
 .e{margin:5px;padding:1px;float:left;width:240px;height:165px}
 .e img{border:1px solid black}
 #cnote {display:block;margin-top:15px;}
 </style> 
</head>
<body>
<h2>Listing</h2>
<div class='a'>
HTML;
	foreach($dirContent as $key => $content) {
        if ($content == '.' || $content == '..' || $content == $dirname_preview || $content == basename(__FILE__) || $content == ".htaccess" ) continue;
		
        if(file_exists("{$dirname}/{$dirname_preview}/{$content}"))
	echo "<span class='e'><a href='{$content}'><img src='{$dirname_preview}/{$content}' alt='$content'></a></span>";
		else{
	echo "<span class='e'><a href='{$content}'>{$content}</a></span>";
		}
	}
	echo "</div>";
	
	echo <<<FOOT
<br clear="all">
<div id="cnote">
Camera: Canon Prima BF-800<br/>
Film: Fuji Superia 200<br/>
Scanner: Plustek Opticfilm 7200<br/>
Photos by Gleb Pereyaslavskiy, 2008</div>
</body></html>
FOOT;
?>