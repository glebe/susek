<?php
/**
 * Main storage class 
 * @version 0.1 prototype ; date: 11 03 2008
 * -- 08 05 2008 start to code
 * TODO: write bodies for functions
 *
 */
	class Object{
		
		public $table = "susek_file";
		
		function __constructor(){
			
		}
		
		function Save($id=null){
			if(is_null($id)){
			 //insert sql
			 //create previews
				
			}else{
			 //update sql

			}
		}
		function Drop(){
			//delete sql
			//delete files
		}
		function ListAll(){
			
		}
		function Find($id){
			if(!$id) return array();
			$SQL = "SELECT *,unix_timestamp(date_added) unixtime from {$this->table} where id='$id'";
			$item = mysql_fetch_assoc(query($SQL));
			array_walk($item,'stripslashes_ex');
			$this->v = $item;
			return $item;
		}
		function Count(){
			
		}
	}
?>