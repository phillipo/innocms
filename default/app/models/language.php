<?php
/*
Author: Cesar Caballero Gallego
Date: 29/02/2012 - 15:52:46
File: language.php
*/

class language extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;	
	
	public function getList() {
		$sqlQuery = 'SELECT * FROM language';
		return $this->find_all_by_sql($sqlQuery);	
	}
	
	public function getAvailables() {
		
	}
	
	
}




?>