<?
	require "init.inc";
	
	$TITLE = "����������";
?>

<ul>
<li><a href="summary.php">������� ����������</a></li>
<li><a href="raw.php">�������������� ���������� � xml</a></li>
</ul>

<?	
	$BODY = ob_get_clean();
	include "../design/template/main.inc";
?>