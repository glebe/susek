<?php
/**
 * Susek nabivka
 * 
 * @todo 
 * + rename uploaded file if already exist - avoid substitutions [names by unique id]
 * + safe names - remove wrong characters
 * + description handling
 * + tags
 * authoring
 * external urls
 * field in expanding menus (tags, description, name...)
 * dynamically built fields (if 3+ needed)
 */
	define('STAT_ENABLE', false);
	define('DEBUG',false);
	
	if(DEBUG){
		require_once "../../init.inc";
	}else{
		require_once "../init.inc";
	}
	
	$TITLE = '������ ����� ����� �����';
	$HEAD  = '';
	
	//$time_start = mktime();
	session_start();

		$dir = DIR_ROOT."storage/tmp/";
		define('DIR_STORAGE', DIR_ROOT."storage/tmp/");
		$dir_im = DIR_ROOT."storage/im/%s/";

	
	$whos_in = array('goo','semga');

/**
 * 
 * Printing random text
 * $your_ending = array('����','����','��������','������','����������','��������','������');
 * USE:
 * $your_ending[(int)rand(0,count($your_ending))];
 * 
 */
	if($_SERVER['REQUEST_METHOD']=='POST'){

		$msg = $dmsg = array();

		if(in_array($_POST['who'], $whos_in)){
			$dir = sprintf($dir_im, $_POST['who']);
		}

		foreach(array_keys($_FILES['ff']['error']) as $key){
			save_file($key);
		}
	
		$_SESSION['msg'] = $msg;
		$_SESSION['dmsg'] = $dmsg;
	
		header("Location: ".$_SERVER['REQUEST_URI']);
		exit();
	
	}
//print_r($_SESSION);
	if(isset($_SESSION['msg'])){
	
		$msg = $_SESSION['msg'];
		$dmsg = $_SESSION['dmsg'];
		unset($_SESSION['msg']);
		unset($_SESSION['dmsg']);

	}

	$skreb_link = '<a class="perm" href="http://susek.ru/screbi/">��������� ��� �����</a>';

	if(isset($_REQUEST['who']) && in_array($_REQUEST['who'], $whos_in) ){
	
		$personal_who  = sprintf('<input type="hidden" name="who" value="%s">', $_REQUEST['who'] );
		$skreb_link = sprintf('<a class="perm" href="http://inside.susek.ru/im/%s/">��������� ��� ���</a> ( ���� ����� ��������������� ����� �����������, � �� ����� ���� ����� )', $_REQUEST['who']);

	}
?>
<div id="cp">&copy; 2007, ���� ����</div>

<form enctype="multipart/form-data" method="post">
<h2>������� ���� � ��������� ������!</h2>
<p class="notice">
��� ������? ������ ����� ����� ������� ������ ���� ������� ����� <?=$skreb_link?>
</p>
<p class="update">
<b>!!</b> ����� ��������, �������� � ���� (���� ������) � ����� ���� ������� ���� (�� <?echo ini_get('upload_max_filesize');?>) � �����. 
</p>
<p class="update">� ��������� ���������� �� ������� � <a class="perm" href="http://susek.ru/blog">�����</a></p>


<?php
if(count($msg)>0){
	foreach ($msg as $_){
		echo "<p class='msg'>$_</p>";
	}
}if(DEBUG && count($dmsg)>0){
	foreach ($dmsg as $_){
		echo "<p class='dmsg'>$_</p>";
	}
}
?>

<?=$personal_who?>
<span class="fldn">��������</span> <span class="fldn">��������</span> <span class="fldn">���� (����� ������)</span><span class="fldn">����</span> <br/>
<div class=fldline><input type="text" name="fn[1]" class="fldt"> <input type="text" name="fd[1]" class="fldt"> <input type="text" name="ft[1]" class="fldt"> <input type="file" name="ff[1]"></div>
<div class=fldline><input type="text" name="fn[2]" class="fldt"> <input type="text" name="fd[2]" class="fldt"> <input type="text" name="ft[2]" class="fldt"> <input type="file" name="ff[2]"></div>
<div class=fldline><input type="text" name="fn[3]" class="fldt"> <input type="text" name="fd[3]" class="fldt"> <input type="text" name="ft[3]" class="fldt"> <input type="file" name="ff[3]"></div>
<input type="submit" value="������">
</form>
<?
    //$time_finish = time();
	//$time_run = $time_finish - $time_start;
?>
<?
	finish();

function save_file($key){
	
	global $msg, $dmsg;
	
	if( $_FILES['ff']['error'][$key] == UPLOAD_ERR_OK ){
	
		$ID = uuid();

		$title		 = strlen( $_POST['fn'][$key] )>0 ? addslashes($_POST['fn'][$key]) : addslashes( $_FILES['ff']['name'][$key] );
		$description = addslashes($_POST['fd'][$key]);
		$tags 		 = addslashes($_POST['ft'][$key]);
		$filename    = $ID.fileext($_FILES['ff']['name'][$key]);

		if(DEBUG) $dmsg[] = $_FILES['ff']['tmp_name'][$key] . " -> " . DIR_STORAGE . $filename;
		else move_uploaded_file($_FILES['ff']['tmp_name'][$key], DIR_STORAGE . $filename );

		$SQL = sprintf('insert into susek_file(guid, title, date_added, filename, description, tags) values( "%s", "%s", utc_timestamp(), "%s", "%s", "%s" )', $ID, $title, $filename, $description, $tags);
		
		if(DEBUG) $dmsg[] = $SQL; 				
		else query($SQL); 

		$msg[] = "���-�� �� {$lang_count[$key]} ���� ������ � �����.";

	}else{
		$dmsg[] = "��� ������: " . $obj['error'] . ", ��������  )";
		return false;
	}
}
?>