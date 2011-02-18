<?
define(C,'im constant');
function a(){
	b();
}
function b(){
	echo 'im b';
	echo " ".C;
}

a();
?>