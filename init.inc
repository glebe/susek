<?
/**
 * Susek Project
 * started : 03/12/2007
 * @link    http://susek.ru
 * @author  Gleb Pereyaslavskiy
 * @version 0.1 running beta
 * @todo 
 * 
 *  statistics:
 *   +daily visits
 *   average
 *   person tracking (global id thru cookie)
 *   picture shows tracking
 *   +picture parser
 * 
 *  image listing:
 *   +image cache!
 *   more smart cache
 *   headers for good caching images
 *   gif support
 *   visual directory lister (like win explorer)
 *  
 *  user interactions:
 *   commenting
 *   meta info on uploads
 *   tags
 *   rating
 *   starring + saving fav.list
 * 
 * @link https://p3smysqladmin01.secureserver.net/p50/33/
 * @link http://mya.godaddy.com/
 *
 */
		$start_time = microtime(true);
		
		define ('DIR_ROOT', '/home/content/v/a/s/vashdom/html/');
		define ('SUSEK_ROOT', DIR_ROOT.'susek.ru/');
		define ('DBNAME', 'routine');
		define ('DBUSER', 'routine');
		define ('DBPASS', 'variousP0');
		define ('DBHOST', 'p50mysql33.secureserver.net');
		
		define ('STAT_ENABLE', true);
		define ('AUTH_REQUIRED', false);
		define ('SERVICE_MODE', false);
		define ('MYSQL_ENABLE', true);
		define ('DIR_STORAGE', DIR_ROOT."storage/tmp/");
		define ('DIR_SAVED', DIR_ROOT."storage/misc/wget");
		
		define('LINK_STORAGE', "http://inside.susek.ru/tmp/");
		
		define('SESSION_ID','cube');
		define('SESSION_TTL',60*24*365*2);
		$table_file = "susek_file";
		$table_file_comments = "susek_file_comment";
		
		$lang_count = array('нулевого', 'первого','второго','третьего','четевертого','пятого');

		$allowable_tags = "<a><img><font><p><b><u><i><em><strong>";

		define ('BLOG_STRICT_MODE', false);
		
		//session
		
		session_name(SESSION_ID);
		session_cache_expire(SESSION_TTL);
		session_set_cookie_params(60*SESSION_TTL);

		//temp session lifetime fix
		if(!SERVICE_MODE){		
			setcookie(SESSION_ID, $_COOKIE[SESSION_ID], time()+60*SESSION_TTL, "/");
			session_start();
		}
		
		ob_start();
		
		if(MYSQL_ENABLE or STAT_ENABLE){
			mysql_connect(DBHOST, DBUSER, DBPASS);
			mysql_select_db(DBNAME);	
		}
		
		//includes
		
		require_once SUSEK_ROOT."lib/lib.inc";
		require_once SUSEK_ROOT."lib/stat.inc";
		require_once SUSEK_ROOT."lib/auth.inc";

		if(STAT_ENABLE) stat_susek();
		if(AUTH_REQUIRED) auth();

		require_once SUSEK_ROOT."notes.inc";	
?>