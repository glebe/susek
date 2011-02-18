<?
/**
 * Susek common library
 *
 */

function finish($design = NULL){
	global $TITLE,$HEAD,$BODY;
	
	$BODY = ob_get_clean();
	
	if(!$design) $design = SUSEK_ROOT . "design/template/main.inc";
	
	$PAGE_IN_TIME = sprintf("%.3f",microtime(true) - $start_time);
	$PAGE_AT_TIME = gmdate('d/m/y H:i:s', time()+3*3600);
	require_once $design;
	
}

function is_jpeg($name){
	return ((strpos($name,'.jpg') !== false) || (strpos($name,'.jpeg') !== false) );
}
function is_gif($name){
	return ((strpos($name,'.gif') !== false) );
}
function is_mp3($name){
	return ((strpos($name,'.mp3') !== false) );
}

function uuid() {
   
    // The field names refer to RFC 4122 section 4.1.2
    // http://www.ietf.org/rfc/rfc4122.txt

    return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
        //mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
        0,0,
        mt_rand(0, 65535), // 16 bits for "time_mid"
        mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
    ); 
}

	function query($sql){
		return mysql_query($sql);
	}

	function fileext($filename){
		return strtolower(strrchr($filename,"."));

	}

	function stripslashes_ex(&$param, $key){
		$param = stripslashes($param);
	}
	
?>