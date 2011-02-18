<?
/**
 * clear cache
 */
	define("STAT_ENABLE", false);
	require "../init.inc";
	$cache_dir = SUSEK_ROOT . "cache/img/";
	$image_dir = DIR_STORAGE . "/storage/tmp/";
	
	if($handle = opendir($cache_dir))
		while (false !== ($file = readdir($handle)))
			if($file !=  '.' && $file != '..')
				if( !file_exists( $image_dir.$file ) )
					unlink($cache_dir.$file);
	
	closedir($handle);
?>