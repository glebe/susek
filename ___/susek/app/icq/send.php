<?
$filename = getcwd()."/send.queue";

if($_SERVER['REQUEST_METHOD']=="POST"){
	$fp = fopen($filename,'a');
	fwrite($fp,"{$_POST['uin']}|{$_POST['msg']}\n");
	fclose($fp);
}
?>
<hmtl>
<head>
<title>Отправить сообщение</title>
<style type="text/css">
body 	 { text-align:center;font-family:'lucida grande', sans serif;font-size:0.8em}
div.form { text-align:right;position:absolute;top:20px;left:100px}
</style>
</head>
<body bgcolor="#cccccc">

<form action="send.php" method=post>

<div class=form>
 <div>
  UIN: <input type="text" name='uin'>
 </div>
 <div>
  Message: <input type="text" name='msg'>
 </div>
 <div>
  <input type="submit" value="send">
 </div>
</div>

</form>
</body>
</html>