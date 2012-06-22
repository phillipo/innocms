<?php
/*
Author: Cesar Caballero Gallego
Date: 04/04/2012 - 14:30:24
File: category.php
*/

class Category extends ActiveRecord {
	
	// Params
	protected $debug = FALSE;
	protected $logger = FALSE;	
	
	// Metodo para recuperar datos por ID
	public function getbyId($id) {
		
		if (empty($id)) return false;
		
		$sqlQuery = 'SELECT * FROM category WHERE category.id = "'.(int)$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);	
		
	}
	
	// Metodo para recuperar datos por SLUG
	public function getbyKey($pageKey = '') {
		$sqlQuery = 'SELECT * FROM category WHERE category.category_slug = "'.$pageKey.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}
	
	// Recuperar listado de todos los items
	public function getList($page = 0, $limit = 20, $conditions = array()) {
	
		$sql = '';
		$order = 'category_sort_order ASC';
		$parentId = 0;
	
		// Condiciones
		if (sizeof($conditions) > 0 && is_array($conditions)) {
				
			// Si filtramos por estado
			if (isset($conditions['filter'])) {
				
				switch (strtolower($conditions['filter'])) {
	
					case 'public':
					case 'private':
						$status = trim($conditions['filter']);
						$sql .= ($status != '') ? ' AND category_status = "'.addslashes($status).'"' : '';						
					break;
					
					case 'havecontents':						
						$sql .= ' AND (SELECT count(*) FROM content WHERE content.category_id = category.id) > 0';
					break;
	
				}
	
			}
			
			// Filtramos por Parent id
			if (isset($conditions['parentId'])) {
				$sql .= ' AND category_parent_id = "'.(int)$conditions['parentId'].'"';
// 				$parentId = $category->id;				
			}
			
			// Filtramos por padre
			if (isset($conditions['parentSlug'])) {
				
				// Recuperamos padre según slug
				$category = self::getbyKey($conditions['parentSlug']);
				
				if (!empty($category)) {
					$sql .= ' AND category_parent_id = "'.$category->id.'"';
					$parentId = $category->id;
				} else {
					$sql .= ' AND category_parent_id = "0"';
				}
				
			}
	
			// Texto
			if (isset($conditions['text']) && !empty($conditions['text'])) {
	
							$text = trim($conditions['text']);
				$words = explode(' ',$text);
				
				// Tipo de búsqueda
				if (sizeof($words) > 1) {
					
					$text = '';
					foreach ($words as $w) {
						
						$text .= '+'.$w.' '; 
						
					}
					
					$text = rtrim($text,' ');
					$sql .= ($text != '') ? ' AND MATCH(category_attribute_value) AGAINST ("'.$text.'")' : '';
				
				} else {					
					
					$sql .= ($text != '') ? ' AND category_attribute_value LIKE "%'.addslashes($text).'%"' : '';
					
				}
		
			}
	
			// Orden
			if (isset($conditions['order'])) $order = $conditions['order'];	
			
		}
		
		// Limit
		if ($limit > 0) $sqlLimit = ' LIMIT '.$page.','.$limit;	
		else $sqlLimit = '';	
	
// 		
		
		// Creamos consulta
		$sqlQuery = '
		SELECT category.*, attr.category_attribute_key, attr.category_attribute_value, 
			(SELECT count(*) FROM content WHERE content.category_id = category.id) as totalContents
		FROM category INNER JOIN category_attributes as attr ON category.id = attr.category_id
		WHERE category_attribute_key = "TITLE" AND category_language_id = 1'.$sql.' ORDER BY '.$order.$sqlLimit;
	
		return $this->find_all_by_sql($sqlQuery);
	
	}
	

	// Guardar registro
	public function saveIt($data) {
		
		// Chequeamos si se envian datos
		if (is_array($data)) {
	
			// Id de inserción para UPDATE
			$id = isset($data['id']) ? $data['id'] : '';
	
			// Analizamos datos
			foreach ($data as $k=>$v) {
	
				// Revisamos fechas
				if (in_array(strtolower($k), array('category_created_at','category_modified_in'))) {
					$data[$k] = (empty($v)) ? date('Y-m-d H:i:s') : Innocms::mysqlDate($v,'d-m-Y H:i:s');
				}
	
			}
	
			// Procesamos información
			if ($this->save($data)) {
				return true;
			}
	
		}
	
		return false;
	
	}
	
	
	
}


?>