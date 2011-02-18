<?
/**
 * Daily Summary
 */
	require "init.inc";
	
	//$stat = mysql_query("SELECT count(id) visits, date FROM susek_stat group by day(from_unixtime(date)) order by id desc");
	$day = $_REQUEST['day'];
	
	$STAT = <<<STAT
SELECT ss.url, count(ss.id) visits, count(distinct ss1.ip) visits_ip, count(distinct ss2.forwarded) visits_ff
FROM susek_stat ss
LEFT JOIN susek_stat ss1 ON ss1.id = ss.id 
LEFT JOIN susek_stat ss2 ON ss2.id = ss.id 
WHERE SUBSTRING( ss.date_stamp, 1, 10 ) = '$day'
and ss.url not like '/img.php?name=%'
GROUP BY ss.url
ORDER BY count(ss.id) DESC, count(distinct ss1.ip) DESC, count(distinct ss2.forwarded) DESC,url
STAT;


	$stat = query( $STAT ) or die(mysql_error());

	while($stat_line = mysql_fetch_assoc($stat) ){
		
		$visit = "";
 		
		foreach($stat_line as $s_name => $s_val){
 			$visit .= "<$s_name>".str_replace('&','&amp;',$s_val)."</$s_name>";
 		}
 		
 		echo "<day>$visit</day>\n";
	
	}
	
	$HEAD = "http://susek.ru/design/xsl/report_day.xsl";
	header("Content-type: text/xml");
	finish(SUSEK_ROOT.'design/template/xsl.inc');
	//finish();
?>