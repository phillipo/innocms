<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: categoryAttributes.php
*/

Load::lib('innocms');

class CategoryAttributes extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;
	protected $source = 'content_attributes';
	private $allowedAttributes = array('title','content','seotitle','seodesc','seotags','image');

	
	// Recuperar información de un item por ID
	public function getbyId($id = '') {
		$sqlQuery = 'SELECT * FROM category_attributes WHERE id = "'.$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información de un item por SLUG
	public function getbyKey($categoryId = 0, $pageKey = '') {
		$sqlQuery = 'SELECT * FROM category_attributes WHERE category_attribute_key = "'.$pageKey.'" AND category_id = "'.$categoryId.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperar información del listado de atributos
	public function getListFields($categoryId = 0) {
		
		// Procesamos
		$attributes = array();
		foreach ($this->allowedAttributes as $row) {

			// Consulta
			$attr = self::getbyKey($categoryId, $row);

			if (!empty($attr->id)) {
				$attributes[$row] = $attr->category_attribute_value;
			} else {
				$attributes[$row] = '';
			}
			
							
		}
		
		// Devolvemos resultados
		if (sizeof($attributes) > 0) return Innocms::arrayToObject($attributes);
		else return false;
		
	}
	
	// Recuperar información del listado de atributos
	public function getList($categoryId = 0) {
		$sqlQuery = 'SELECT attr.* FROM category_attributes attr WHERE attr.category_id = "'.(int)$categoryId.'"';
		$results = $this->find_all_by_sql($sqlQuery);
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
						$v = 'Nueva categoria';
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
	public function remove($categoryId) {
		
		$sqlQuery = 'DELETE FROM category_attributes WHERE category_id = "'.(int)$categoryId.'"';
		$this->sql($sqlQuery);
		
	}
	
}

class preferencesSocialnetworks {

}
?>