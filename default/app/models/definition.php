<?php
/*
Author: Cesar Caballero Gallego
Date: 06/03/2012 - 13:33:45
File: definition.php
*/

class Definition extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;
		
	// Obtener datos por ID
	public function getbyId($id = '') {
		
	}
	
	// Obtener datos por Key
	public function getbyKey($key = '') {
	
		$sqlQuery = 'SELECT * FROM definition WHERE definition_key = "'.addslashes($key).'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
		
	}
	
	// Obtener un listado
	public function getList($id = '') {
	
	}
	
	// Guardar información
	public function saveIt($data = array()) {
		
		if (is_array($data) && sizeof($data) > 0) {	
			
			if (isset($data['definition_key'])) {
				
				$definition = $this->getbyKey($data['definition_key']);
			
				if ($definition->id != '') {
					
					$data['id'] = $definition->id;				
					$definition->save($data);
					return true;
					
				}
				
			}
		
		}
		
		return false;
		
	}
	
	// Borrar un registro
	public function deleteIt() {
		
	}	
	
	
}


?>