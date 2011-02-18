<?
/**
 * Susek auth library
 * 
 * handling users, 
 * resticted areas
 * 
 */

	function auth(){
		
		$auth_login = 'gleb';
		$auth_pass = 'sususek';
		
		$currtime = time();
		$auth_expire = 3600 + $currtime;
		
		// если сессия истекла - на выход с вещами
		if( $_SESSION['auth_expires'] < $currtime  || (bool)$_REQUEST['logout']){
			unset( $_SESSION['user'] );
		}
		
		// свои продолжают колбаситься
		if( $_SESSION['user'] == $auth_login){
			$_SESSION['auth_expires'] = $auth_expire;
			return true;
		}

		// кто там! свои? ну заходи
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if($_POST['usr']==$auth_login && $_POST['passwd']==$auth_pass){
				
				$_SESSION['user'] = $auth_login;
				$_SESSION['auth_expires'] = $auth_expire;
				header("Location: ". $_SERVER['REQUEST_URI']);
			}
		}
		
		global $TITLE;
		//$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
		//header("Location: /login.html");
		$TITLE = "Посторонним В.";
		$expired = gmdate('d-m-Y H:i:s', $_SESSION['auth_expires']+2*3600 );
		echo <<<AUTH
<h3>Вход только для зарегистрированных сусектантов</h3>
<h4>Последнее посещение: $expired</h4>
<form name="auth" method="post">
<input type="text" name="usr"> <input type="password" name="passwd"> <input type="submit" value="я свой">
</form>
AUTH;
		finish();
		exit();
	}
?>