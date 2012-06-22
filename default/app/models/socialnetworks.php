<?php

/*
 Author: Cesar Caballero Gallego
Date: 04/04/2012 - 14:30:24
File: preferences_socialnetworks.php
*/

class socialnetworks extends ActiveRecord {
	
	// Params
	protected $debug = TRUE;
	protected $logger = FALSE;
	protected $source = 'preferences_socialnetworks';
	
	// Metodo para recuperar datos por ID
	public function getbyId($id) {
	
		if (empty($id)) return false;
	
		$sqlQuery = 'SELECT * FROM preferences_socialnetworks as snet WHERE snet.id = "'.(int)$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	
	}
	
	// Metodo para recuperar datos por SLUG
	public function getbyKey($pageKey = '') {
		$sqlQuery = 'SELECT * FROM preferences_socialnetworks as snet WHERE snet.snet_slug = "'.$pageKey.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Metodo para seleccionar todas las redes sociales
	public function getList() {
		$sqlQuery = 'SELECT * FROM preferences_socialnetworks as snet ORDER BY snet_type, snet_name';
		return $this->find_all_by_sql($sqlQuery);
	}
	

}

?>