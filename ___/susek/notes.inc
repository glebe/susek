<?
/**
 * Susek notes, docs, and ToDos
 */
$NOTES = <<<NOTES
/**
 * main record id structure:
 * {author_id}-{type}-{date}-{?}-{file}
 */
NOTES;

$TODO = <<<TODO
global:
 + authentication support for closed areas
 + auth session expiration
 multiuser login
 different users get access to diff. parts of the site
 project management from web (todos,tasks, progress)
 user management
 
commenting:
 openid authorization on posting a comment
 delete comments
 dont physically delete comment, only set deleted state
 comment replies
 building comment tree
 
TODO;

$LOG = <<<LOG
2008-01-30 
 + primitive antibot added to blogs
2008-03-27
 + spam comments removing tool (guestblog/rm.php)
2008-03-28
 + antispam integrated for posts (guestblog).
 * some antispam improvements (
blog comments)
LOG;
function print_notes(){
	global $NOTES;
	exit(nl2br($NOTES));
}

function print_todo(){
	global $TODO;
	exit(nl2br($TODO));
}

function print_log(){
	global $LOG;
	exit(nl2br($LOG));
}

if((int)$_REQUEST['__notes']) print_notes();
if((int)$_REQUEST['__todo']) print_todo();
if((int)$_REQUEST['__log']) print_log();
?>