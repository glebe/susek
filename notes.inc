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
function print_notes(){
	global $NOTES;
	exit(nl2br($NOTES));
}

function print_todo(){
	global $TODO;
	exit(nl2br($NOTES));
}

if((int)$_REQUEST['project_notes']) print_notes();
if((int)$_REQUEST['project_todo']) print_todo();
?>