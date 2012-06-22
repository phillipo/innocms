<?php
/*
Author: Cesar Caballero Gallego
Date: 23/02/2012 - 14:14:52
File: template.php
*/

class Template extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;
	
	// Recuperar un registro 
	public function getbyId($id) {
		
		$sql = 'SELECT * FROM template WHERE id = "'.(int)$id.'" LIMIT 1';
		return $this->find_by_sql($sql);
		
		
	}
	
	// Recuperar listado
	public function getList() {
		
		$sqlQuery = 'SELECT template.id as template_id, template.*, definition.* FROM template INNER JOIN definition ON template_key = definition_key WHERE definition_language_id = "1" AND template_active = "1" ORDER BY template_sort_order ASC';
		return $this->find_all_by_sql($sqlQuery);
		
	}
	
	
	
}



?>
