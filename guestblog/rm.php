<?php
/**
 * Susek add blog entry
 * HISTORY:
 *  * created 27/03/2008
 *  + delete multiple entries
 * TODO: user interface
 * TODO: delete comments associated with entries
 * TODO: mark as spam to investigave the source of atack
 */
	define('AUTH_REQUIRED',true);
	require "init.inc";
	
	$TITLE = '������ ������';
	$HEAD  = '';
	
	if(!($IDS = $_REQUEST['id'])){
		//header("Location: $blog_dir");
		echo "�������� ��������: <b>id</b>";
		finish();
		exit();
	}

	$SQL = "DELETE FROM {$blog_table_entries} WHERE id in({$IDS})";
		
	query($SQL);
	//header("Location: $blog_dir");
	//exit();
	
?>
�������: <?=$IDS ?>
<a href="<?=$blog_dir ?>">������� � ������ �������</a>
<?
	finish();