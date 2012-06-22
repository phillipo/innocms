<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: page.php
*/

Load::lib('innocms');

class Page extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = TRUE;
	
	// Recuperar información de un item por ID
	public function getbyId($pageId = 0) {
		$sqlQuery = 'SELECT * FROM page WHERE page.id = "'.(int)$pageId.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);		
	}

	// Recuperar información de un item por SLUG
	public function getbyKey($pageKey = '') {
		$sqlQuery = 'SELECT * FROM page WHERE page.page_slug = "'.$pageKey.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}
	
	// Recuperar listado de todos los items
	public function getList($conditions = array()) {

		$sql = '';
		$order = 'page_sort_order ASC';		
		
		// Condiciones
		if (sizeof($conditions) > 0 && is_array($conditions)) {
			
			// Si filtramos por estado
			if (isset($conditions['filter'])) {
				
				switch (strtolower($conditions['filter'])) {
					
					case 'expires':
						$sql .= ' AND page_publish_expires = "1"';						
					break;
					
					case 'redirect':
						$sql .= ' AND page_attribute_key LIKE "%REDIRECT%"';
					break;
					
					case 'public':
					case 'private':
					case 'password':
					case 'draft':
						$status = trim($conditions['filter']);
						$sql .= ($status != '') ? ' AND page_status = "'.addslashes($status).'"' : '';
					break;

				}
				
			}
			
			// Padre
			if (isset($conditions['parentId'])) {
				$sql .= ' AND page_parent_id = "'.(int)$conditions['parentId'].'"';
			}
			
			// Texto de búsqueda
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
					$sql .= ($text != '') ? ' AND MATCH(page_author, page_attribute_value) AGAINST ("'.$text.'")' : '';
			
				} else {
			
					$sql .= ($text != '') ? ' AND page_author LIKE "%'.addslashes($text).'%" OR page_attribute_value LIKE "%'.addslashes($text).'%" ' : '';
			
				}
			
			}
			
			
			// Orden
			if (isset($conditions['order'])) $order = $conditions['order'];		
			
		}

		// Creamos consulta
		$sqlQuery = '
				SELECT page.id as page_id, page.*, attr.* FROM page INNER JOIN page_attributes as attr ON page.id = attr.page_id 
				WHERE page_attribute_key = "TITLE" AND page_language_id = "1"'.$sql.' ORDER BY '.$order;
				
		return $this->find_all_by_sql($sqlQuery);
								      
	}
		
	// Guardar registro
	public function saveIt($data) {
		
		$date_keys = array('page_publish_up','page_publish_down'); //,'page_created_at','page_modified_in');
		
		// Chequeamos si se envian datos
		if (is_array($data)) {

			// Id de inserción para UPDATE
			$id = isset($data['id']) ? $data['id'] : '';						
			
			// Analizamos datos
			foreach ($data as $k=>$v) {
				
				if ($k == 'page_parent_id') {
					$data[$k] = ($data[$k] == $id) ? '0' : $v;
				}				
			
				// Revisamos fechas
				if (in_array(strtolower($k), $date_keys)) {
					$data[$k] = (empty($v)) ? date('d-m-Y H:i:s') : Innocms::mysqlDate($v,'d-m-Y H:i:s');
				}	
			
			}

			// Comprobamos ordenación
			if (!isset($data['page_sort_order'])) {							
				$data['page_sort_order'] = 10;	
			}
			
			// Comprobamos caducacion fechas
			if (!isset($data['page_publish_expires'])) $data['page_publish_expires'] = 0;			
			
			// Comprobamos fechas ( es un proceso de automatización )
			foreach ($date_keys as $key) {				
				if (!isset($data[$key])) $data[$key] = date('d-m-Y H:i:s');				
			}
			
			// Ordenamos el resto de los elementos antes de insertar/guardar uno nuevo
			//self::sort();
						
			// Procesamos información
			return $this->save($data);
			
		}
		
		return false;
	
	}
	
	// Metodo para reordenar elementos
	public function sort() {
		
		$items = self::find_all_by_sql('SELECT id, page_sort_order FROM page ORDER BY page_sort_order ASC');
		
		$n = 1;
		foreach ($items as $item) {			
			$item->page_sort_order = ($n+1)*10;
			$item->save();
			$n++;			
		}
		
		
		
		
	} 
	
	
}

?>