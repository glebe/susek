<?
/**
 * Susek stat library
 *
 */

function stat_susek(){
	
	$track_id = $_COOKIE[SESSION_ID];
	
	$sql = sprintf("insert into susek_stat(track_id,date_stamp,url,referrer,agent,ip,proxy,forwarded,host) values('%s',utc_timestamp(),'%s','%s','%s','%s','%s','%s','%s')", $track_id, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_VIA'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_HOST']
	);
	query($sql);
}
?>