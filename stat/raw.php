<?
	require "init.inc";
	
	$day = $_REQUEST['day'];
	$url = $_REQUEST['url'] ? $_REQUEST['url']:'%';
	
	$usage_help = <<<MSG
<html>
<head><title>Raw stats usage</title></head>
<body>
<h4>Usage:</h4>
<i>day</i> specifies the day of stat
<i>url</i> specifies the mask for the url

<h4>Examples:</h4>
<em>raw.php?day=2007-12-12</em> will give stats for the December, 12th"
<em>raw.php?url=/blog/</em> will show entries of /blog/ url"
<em>raw.php?url=/blog/&day=2007-12-12</em> will show entries of /blog/ url for the December, 12th"
</body>
</html>
MSG;
	if(!$day and !$url ) exit(nl2br($usage_help));
	$stat_sql = "SELECT * FROM susek_stat ss where SUBSTRING( ss.date_stamp, 1, 10 ) = '$day' and url like '$url' order by date_stamp desc,url";
	
	$stat = query($stat_sql) or die(mysql_error());
	$xml = "";
	
	while( $s_row = mysql_fetch_assoc($stat) ){
 		
		$visit = "";
 		
		foreach($s_row as $s_name => $s_val){
 			$visit .= "<$s_name>".htmlspecialchars($s_val)."</$s_name>";
 		}
 		
 		$xml .= "<visit>$visit</visit>\n";

	}

/**
 * <?xml-stylesheet type="text/xsl" href="simple.xsl"?>
 */

	header("Content-type: text/xml");

	echo <<<EOF
<?xml version="1.0" encoding="ISO-8859-1"?>
<stat>
$xml
</stat>
EOF;
?>