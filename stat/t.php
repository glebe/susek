<?
	include "init.inc";
	
	//mysql_query("delete from susek_stat where date_stamp=''");
	//mysql_query("update susek_stat set date_stamp = from_unixtime( unix_timestamp(date_stamp)+7*60*60 )");
	//$s = "select id,date_stamp from susek_stat order by id desc";
	$s = "select now() server, utc_timestamp() utc, from_unixtime( unix_timestamp(now())+7*60*60 ) corrected";
	$t = mysql_query($s);

	while ($_ = mysql_fetch_assoc($t) ){
		print_r($_);
	}
?>