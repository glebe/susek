<?
/**
 * @name    Simple blog engine
 * @author  Gleb Pereyaslavskiy
 * @version 0.3.2a
 * @todo
 * 
 * -combine blogs from multiple addresses:
 *   redirect to 1 engine thru mod_rewrite
 *   set table names and other vars for each blog separately
 *   separate level of auth for each blog/action
 * -reply to comment (implement nested sets to DB)
 * -list by tags
 * -list by date
 * -auth, openid auth
 * -comment collapsing if more then N+ totally
 * -comment editing
 * -comment paginating
 * -nice editor
 * 
 */
	require_once "../init.inc";

	define ('BLOG_STRICT_MODE', false);
	
	$blog_dir 	    	 = "/blog/";
	$blog_table_entries  = "susek_blog_entry";
	$blog_table_comments = "susek_blog_comment";
	
?>