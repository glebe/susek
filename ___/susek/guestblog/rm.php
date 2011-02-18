<?php
/**
 * Susek add blog entry
 * created: 27 03 2008
 * TODO: interface
 */
	define('AUTH_REQUIRED',true);
	require "init.inc";

	if(!($ID = (int)$_REQUEST['id'])){
		header("Location: $blog_dir");
		exit();
	}
	
	$TITLE = 'чистка сусека';
	$HEAD  = '';
	
	$SQL = "DELETE FROM {$blog_table_entries} WHERE id={$ID}";
		
	query($SQL);
	header("Location: $blog_dir");
	exit();
	
?>
удалено
<?
	finish();
?>