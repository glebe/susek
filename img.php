<?
/**
 * Susek image parser
 * 
 * @todo 
 * если запрос идет с внешнего адреса - прописать на картинке копирайт сусека
 * gif,png,bmp format support (imageCreateFromGd instead of ImgeCreateFromJpeg... - universal)
 * smart caching
 * not found image processing
 */
	define('SERVICE_MODE', true);
	define('MYSQL_ENABLE', false);
	define('STAT_ENABLE', false);
	include "init.inc";
	
	$expires = 315360000; // 10 years
	$dir  = DIR_ROOT."storage/tmp/";
	$cache_dir  = DIR_ROOT."susek.ru/cache/img/";
	$file = basename($_REQUEST['name']);
	
	if(!$file || !file_exists($dir.$file)){
		header("HTTP/1.0 404 Not Found");
		exit('No such file');
	}
	
	//if(strpos($_SERVER['REFERRER'],'susek.ru') === false){
	// 
	//}
	
	//$mime = mime_content_type($dir.$file);
	
	//switch($mime){	
	//	case "image/jpeg":
	if( is_jpeg($file) ){
		
		if(!file_exists($cache_dir.$file)){
			resize_jpg($dir.$file, 100, $cache_dir.$file, 80);
		}
		
		$image_type = "image/jpeg";
		readfile($cache_dir.$file);
		$image = ob_get_clean();
	}elseif( is_gif($file) ){
		
		$image_type = "image/gif";
		resize_gif($dir.$file, 100, null);	
		$image = ob_get_clean();
	}
	
	if( is_gif($file) || is_jpeg($file) ){

		header('Last-Modified: '.date('r'). ' GMT');
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . strlen($image));
		header('Cache-Control:	max-age='.$expires);
		header("Expires: ".gmdate('D, d M Y H:i:s',time()+$expires)). " GMT";
		header('Content-Type: '.$image_type);
		echo $image;
		
	}
	
function resize_jpg($inputFilename, $new_side, $outputFilename, $Quality){
	$imagedata = getimagesize($inputFilename);
	$w = $imagedata[0];
	$h = $imagedata[1];
	
	if ($h > $w) {
		$new_w = ($new_side / $h) * $w;
		$new_h = $new_side;	
	} else {
		$new_h = ($new_side / $w) * $h;
		$new_w = $new_side;
	}

	$im2 = ImageCreateTrueColor($new_w, $new_h);
	$image = ImageCreateFromJpeg($inputFilename);
	//imageantialias($im2)
	imagecopyResampled($im2, $image, 0, 0, 0, 0, $new_w, $new_h, $imagedata[0], $imagedata[1]);
	return imagejpeg($im2, $outputFilename, $Quality);
}

function resize_gif($inputFilename, $new_side, $outputFilename){
	$imagedata = getimagesize($inputFilename);
	$w = $imagedata[0];
	$h = $imagedata[1];
	
	if ($h > $w) {
		$new_w = ($new_side / $h) * $w;
		$new_h = $new_side;	
	} else {
		$new_h = ($new_side / $w) * $h;
		$new_w = $new_side;
	}

	$im2 = ImageCreateTrueColor($new_w, $new_h);
	$image = ImageCreateFromGif($inputFilename);
	imagecopyResampled($im2, $image, 0, 0, 0, 0, $new_w, $new_h, $imagedata[0], $imagedata[1]);

	return imagegif($im2, $outputFilename);
}
	
?>