<?
/**
 * Stat Summary
 */
	require "init.inc";
	
	//$stat = mysql_query("SELECT count(id) visits, date FROM susek_stat group by day(from_unixtime(date)) order by id desc");
	$STAT = <<<STAT
SELECT SUBSTRING( ss.date_stamp, 1, 10 ) date, count( ss.id ) summary, count( ss1.id ) blog, count( ss2.id ) img, count( ss3.id ) guestblog, count( ss4.id ) dev
FROM susek_stat ss
LEFT JOIN susek_stat ss1 ON ss1.id = ss.id AND ss1.url like '/blog/%'
LEFT JOIN susek_stat ss3 ON ss3.id = ss.id AND ss3.url like '/guestblog/%'
LEFT JOIN susek_stat ss4 ON ss4.id = ss.id AND ss4.url like '/dev/%'
LEFT JOIN susek_stat ss2 ON ss2.id = ss.id AND ss2.url like '/img.php?%'
WHERE ss.url not like '/img.php?name=%'
GROUP BY SUBSTRING( ss.date_stamp, 1, 10 )
ORDER BY ss.date_stamp DESC
STAT;
	$stat = query( $STAT ) or die(mysql_error());

	while($stat_line = mysql_fetch_assoc($stat) ){
		
		$visit = "";
 		
		foreach($stat_line as $s_name => $s_val){
 			$visit .= "<$s_name>$s_val</$s_name>";
 		}
 		
 		echo "<day>$visit</day>\n";
	
	}
	
	//$TITLE = "Сводная статистика";

	$HEAD = "http://susek.ru/design/xsl/report_summary.xsl";
	header("Content-type: text/xml");
	finish(SUSEK_ROOT.'design/template/xsl.inc');
?>