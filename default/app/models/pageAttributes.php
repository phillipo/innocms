<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: content.php
*/

Load::lib('innocms');

class PageAttributes extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;
	private $allowedAttributes = array('title','short_title','seotitle','seodesc','seotags','redirect_external','redirect_internal');

	
	// Recuperar información de un item por ID
	public function getbyId($id = '') {
		$sqlQuery = 'SELECT * FROM page_attributes WHERE id = "'.$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información de un item por SLUG
	public function getbyKey($pageId = 0, $pageKey = '') {
		$sqlQuery = 'SELECT * FROM page_attributes WHERE page_attribute_key = "'.$pageKey.'" AND page_id = "'.$pageId.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información del listado de atributos
	public function getListFields($pageId = 0) {
		
		// Procesamos
		$attributes = array();
		foreach ($this->allowedAttributes as $row) {

			// Consulta
			$attr = self::getbyKey($pageId, $row);

			if (!empty($attr->id)) {
				$attributes[$row] = $attr->page_attribute_value;
			} else {
				$attributes[$row] = '';
			}
			
							
		}
		
		// Devolvemos resultados
		if (sizeof($attributes) > 0) return Innocms::arrayToObject($attributes);
		else return false;
		
	}
	
	// Recuperar información del listado de atributos
	public function getList($pageId = 0) {
		$sqlQuery = 'SELECT attr.* FROM page_attributes attr WHERE attr.page_id = "'.(int)$pageId.'"';
		$results = $this->find_all_by_sql($sqlQuery);
	}	

	// Guardar registro
	public function guardar($data) {		
		
		// Chequeamos si se envian datos
		if (is_array($data)) {		
			
			// Procesamos información
			if (!$this->save($data)) {
				return false;
			} else {
				return true;
			}
			
		}
		
		return false;
	
	}	

	// Eliminar registros
	public function remove($pageId) {
		
		$sqlQuery = 'DELETE FROM page_attributes WHERE page_id = "'.(int)$pageId.'"';
		$this->sql($sqlQuery);
		
	}
	
}

?>