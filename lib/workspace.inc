<?
/**
 * Main entity
 *
 */
class susekFile{
	
	function susekFile(){
		/**
		 * constructor
		 */
		return true;
	}
	
	function loadFile($id){
		/**
		 * load file by id
		 */
		
	}
	
	function loadFiles($params){
		/**
		 * specify misc conditions
		 */
		
	}

	function saveFile($params){
		/**
		 * create file entry or update existent
		 */
		
	}
}

class susekBlog{

}
/**
 * Blog entry
 *
 */
class susekBlogEntry{
	public $table_name;	

	function susekBlogEntry($params){
		/**
		 * constructor
		 */
	}
	
	function loadEntry($id){
		/**
		 * load 1 entry
		 */
		
	}
	
	function loadEntries($params){
		/**
		 * load multiple entries by condition
		 */
		
	}
	
	function saveEntry($id){
		/**
		 * save entry by id or create new
		 */
	}
	
}

/**
 * Blog comment
 *
 */
class susekBlogComment extends susekBlog{
	public $table_name;

	function susekBlogComment(){
		/**
		 * constructor
		 */
	}
	
	function loadComment($id){
		/**
		 * load comment by id
		 */
	}
	
	function loadComments($entry_id){
		/**
		 * load list of comments for an entry
		 */
	}
	
	function saveComment($id){
		/**
		 * save a comment or update existent
		 */
	}
}

/**
 * Statistics
 * 
 */
class susekStat{
	public $table_name;
	
	function susekStat(){
		
	}
	
	function statSummary(){
		
	}
	
	function statByDate($date){
		
	}
	
	function statByUrl($url){
		
	}
	
}
/**
 * Users
 *
 */
class susekUser{
	public $table_name;
	
	function susekUser(){
		
	}
	
	function getUser(){
		
	}
	
	function saveUser(){
		
	}
}

?>