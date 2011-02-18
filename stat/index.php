<?
	require "init.inc";
	
	$TITLE = "Статистика";
?>

<ul>
<li><a href="summary.php">сводная статистика</a></li>
<li><a href="raw.php">необработанная статистика в xml</a></li>
</ul>

<?	
	$BODY = ob_get_clean();
	include "../design/template/main.inc";
?>