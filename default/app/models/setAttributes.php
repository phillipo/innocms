<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: content.php
*/

Load::lib('innocms');

class SetAttributes extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = TRUE;
	protected $source = 'content_attributes';
	private $allowedAttributes = array('title','content','files');

	
	// Recuperar información de un item por ID
	public function getbyId($id = '') {
		$sqlQuery = 'SELECT * FROM content_attributes WHERE id = "'.$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información de un item por SLUG
	public function getbyKey($contentId = 0, $pageKey = '') {
		$sqlQuery = 'SELECT * FROM content_attributes WHERE content_attribute_key = "'.$pageKey.'" AND content_id = "'.$contentId.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información del listado de atributos
	public function getListFields($contentId = 0) {
		
		// Procesamos
		$attributes = array();
		foreach ($this->allowedAttributes as $row) {

			// Consulta
			$attr = self::getbyKey($contentId, $row);

			if (!empty($attr->id)) {
				$attributes[$row] = $attr->content_attribute_value;
			} else {
				$attributes[$row] = '';
			}
			
							
		}
		
		// Devolvemos resultados
		if (sizeof($attributes) > 0) return Innocms::arrayToObject($attributes);
		else return false;
		
	}
	
	// Recuperar información del listado de atributos
	public function getList($contentId = 0) {
		$sqlQuery = 'SELECT attr.* FROM content_attributes AS attr WHERE attr.content_id = "'.(int)$contentId.'"';
		return $this->find_all_by_sql($sqlQuery);
	}	

	// Guardar registro
	public function saveIt($relationalId = 0, $data = array()) {		
		
		// Comprobamos que tenemos dato relacional
		if (empty($relationalId)) return false;
		
		// Chequeamos si se envian datos
		if (is_array($data) && sizeof($data) > 0) {
			
			foreach ($this->allowedAttributes as $k) {
				
				// Buscamos un attributos relacionado con el item y su key
				$attribute = $this->getbyKey($relationalId, strtoupper($k));
				$attributeId = (empty($attribute)) ? '' : $attribute->id;				
				
				// Valor del atributo
				if (isset($data[$k]) && !empty($data[$k])) {
					$v = $data[$k];
				} else {
					
					if (strtolower($k) === 'title') {
						$v = 'Nuevo set';
					} else {					
						$v = '';
					}
					
				}
				
				// Preparamos array de datos para insertar
				$fields = array(
						'id' => $attributeId,
						'content_id' => $relationalId,
						'content_attribute_key' => strtoupper($k) ,
						'content_attribute_value' => $v
				);	

				// Guardamos atributo
				if (!$this->save($fields)) return false;
								
			}
			
		}
	
	}	

	// Eliminar registros
	public function remove($contentId) {
		
		$sqlQuery = 'DELETE FROM content_attributes WHERE content_id = "'.(int)$contentId.'"';
		$this->sql($sqlQuery);
		
	}
	
}

?>