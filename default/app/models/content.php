<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: content.php
*/

Load::lib('innocms');

class Content extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = TRUE;
	private $date_keys = array();
		
	// Recuperar información de un item
	public function getbyId($content_id = 0) {
		$sqlQuery = 'SELECT * FROM content WHERE content.id = "'.(int)$content_id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);		
	}
	
	// Recuperar el padre de un item
	public function getParentContent($content_id = 0) {
		return $this->find_first('content.id = '.(int)$content_id);		
	}	
	
	// Metodo para recuperar datos por SLUG
	public function getbyKey($pageKey = '') {
		$sqlQuery = 'SELECT * FROM content WHERE content.content_slug = "'.$pageKey.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	

	
	// Recuperar listado de todos los items de un padre
	public function getList($conditions = array()) {	

		$sql = '';
		$order = 'content_sort_order ASC';
		
		// Condiciones
		if (sizeof($conditions) > 0 && is_array($conditions)) {
			
			// Tipologia
			if (isset($conditions['content_type'])) {
				$sql .= ' AND content_type = "'.$conditions['content_type'].'"';				
			}		
			
			// Estado			
			if (isset($conditions['status']) && in_array($conditions['status'], array('draft','private','public'))) {
				$sql .= ' AND content_status = "'.$conditions['status'].'"';
			}

			// Padre basado en ID
			if (isset($conditions['parentId'])) {
				$sql .= ' AND content_parent_id = "'.$conditions['parentId'].'"';	
			}

			// Padre basado en SLUG ( solo las categorias son padre )
			if (isset($conditions['category']) && strtolower($conditions['category']) != 'all') {
			
				// Recuperamos padre según slug
				$category = self::getbyKey($conditions['category']);
			
				if (!empty($category)) {
					$sql .= ' AND content_parent_id = "'.$category->id.'"';
					$parentId = $category->id;
				} else {
					$sql .= ' AND content_parent_id = "0"';
				}
			
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
					$sql .= ($text != '') ? ' AND MATCH(content_slug, content_attribute_value) AGAINST ("'.$text.'")' : '';
				
				} else {					
					
					$sql .= ($text != '') ? ' AND (content_attribute_value LIKE "%'.addslashes($text).'%" OR content_slug LIKE "%'.addslashes($text).'%")' : '';
					
				}
				
			}
			
			// Tipo de mime
			if (isset($conditions['content_mime_type']) && $conditions['content_mime_type'] != 'all') {
				
				// Tipos de archivos
				
				// Textos
				$docMime = array(
						// DOC / DOCX
						'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						// ODT
						'application/vnd.oasis.opendocument.text','application/x-vnd.oasis.opendocument.text',
						// XLS / XLSX
						'application/excel','application/x-excel','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'application/vnd.oasis.opendocument.spreadsheet',
						// ODS
						'application/vnd.oasis.opendocument.spreadsheet','application/x-vnd.oasis.opendocument.spreadsheet',
						// PDF
						'application/pdf','application/x-pdf','application/acrobat','applications/vnd.pdf','text/pdf','text/x-pdf',
						// TEXT
						'text/plain','application/txt','browser/internal','text/anytext','widetext/plain','widetext/paragraph'
				);
				
				// Graficos
				$imgMime = array(
						// JPEG
						'image/jpeg','image/jpg','image/jp_','application/jpg','application/x-jpg','image/pjpeg','image/pipeg','image/vnd.swiftview-jpeg','image/x-xbitmap',
						// PNG
						'image/png','application/png','application/x-png',
						// GIF
						'image/gif','image/x-xbitmap','image/gi_'
				);	

				// Audios
				$audioMime = array(
						// MP3
						'audio/mpeg','audio/x-mpeg','audio/mp3','audio/x-mp3','audio/mpeg3','audio/x-mpeg3','audio/mpg','audio/x-mpg','audio/x-mpegaudio',
						// MP4
						'video/mp4v-es','audio/mp4',
						// WAV
						'audio/wav','audio/x-wav','audio/wave','audio/x-pn-wav',
						// WMA
						'audio/x-ms-wma','video/x-ms-asf'				
				);
				
				// Asociacion
				switch ($conditions['content_mime_type']) {					
					case 'docs': $filterMime = $docMime; break;
					case 'images': $filterMime = $imgMime; break;
					case 'audio': $filterMime = $audioMime; break;
					case 'others': $filterMime = array_merge($docMime,$imgMime,$audioMime); break;
				}
				
				if ($conditions['content_mime_type'] == 'others') {
					$sql .= ' AND content_mime_type NOT IN ("'.implode('","',$filterMime).'")';
				} else {
					$sql .= ' AND content_mime_type IN ("'.implode('","',$filterMime).'")';
				}
				
			}
			
			// Orden
			if (isset($conditions['order'])) {	$order = $conditions['order'];	}			
			
		}
		
		// Creamos consulta
		$sqlQuery = '
				SELECT content.id as id, content.id as rootId, content.*, attr.content_attribute_key, attr.content_attribute_value, (SELECT count(*) FROM content WHERE content_parent_id = rootId) as totalContents
				FROM content INNER JOIN content_attributes as attr ON content.id = attr.content_id 
				WHERE content_language_id = "1" AND content_attribute_key = "TITLE"'.$sql.' ORDER BY '.$order;
		
		return $this->find_all_by_sql($sqlQuery);
	
	}
	
	// Get list of parents
	public function getParents($page = 0, $limit = 1000, $conditions = array()) {
		
		$sql = '';
		
		// Tipologia
		if (isset($conditions['content_type'])) {
			$sql .= ' AND content_type = "'.$conditions['content_type'].'"';
		}		
		
		// Padre basado en ID
		if (isset($conditions['parentId'])) {
			$sql .= ' AND content_parent_id = "'.$conditions['parentId'].'"';
		} else if (isset($conditions['category']) && $conditions['category'] != 'all') {  	// Padre basado en SLUG			
			$category = self::getbyKey($conditions['category']);
			if (!empty($category)) $sql .= ' AND content_parent_id = "'.$category->id.'"';			
		} else {
			$sql .= ' AND content_parent_id = "0"';
		}
		
		
		// Orden
		if (isset($conditions['order'])) {
			$order = $conditions['order'];
		} else {
			$order = 'content_sort_order';
		}
		
		$sqlQuery = '
		SELECT content.id as id, content.id as rootId, content.*, attr.content_attribute_key, attr.content_attribute_value, (SELECT count(*) FROM content WHERE content_parent_id = rootId) as totalContents FROM content INNER JOIN content_attributes as attr ON content.id = attr.content_id
		WHERE content_language_id = "1" AND content_attribute_key = "TITLE"'.$sql.' ORDER BY '.$order.'
		LIMIT '.$page.','.$limit;
		
		return $this->find_all_by_sql($sqlQuery);
		
		
	}
	
	// Guardar registro
	public function saveIt($data = array()) {
		
		$date_keys = array('content_publish_up','content_publish_down','content_created_at','content_modified_in');		
		
		// Chequeamos si se envian datos
		if (is_array($data) && sizeof($data) > 0) {
			
			$id = isset($data['id']) ? $data['id'] : '';
			
			// Analizamos datos y los preparamos para su inserción o actualización
			foreach ($data as $k => $v) {							
				
				if ($k == 'content_parent_id') {
					$data[$k] = ($data[$k] == $id) ? '0' : $v; 
				}
				
				// Revisamos fechas
				if (in_array(strtolower($k), $date_keys)) {
					$data[$k] = (empty($v)) ? date('d-m-Y H:i:s') : Innocms::mysqlDate($v,'d-m-Y H:i:s');
				}	
								
			}
			
			// Comprobamos caducacion fechas
			if (!isset($data['content_publish_expires'])) $data['content_publish_expires'] = 0;
			
			// Comprobamos fechas ( es un proceso de automatización )
			foreach ($date_keys as $key) {				
				if (!isset($data[$key])) $data[$key] = date('d-m-Y H:i:s');				
			}
			
			// Guardamos datos
			return $this->save($data);
			
		}
		
		return false;
	
	}	
	
	// Metodo para duplicar registros
	public function duplicate($id) {
		
		if (empty($id)) return false;
		
		// Recuperamos datos tabla principal
		$item = Load::model('content')->find_first($id);
		// Duplicamos entrada
		$item->content_slug = $item->content_slug.'_copy';
		$item->create($item);
		
		$attributeList = Load::model('contentAttributes')->getList($id);
		foreach ($attributeList as $attr) {
		
			$attribute = Load::model('contentAttributes')->find_first($attr->id);
			$attribute->content_id = $item->id;
			if ($attribute->content_attribute_key == 'TITLE') $attribute->content_attribute_value = $attribute->content_attribute_value.' ( copia )';
			$attribute->create();
		
		}		
		
		
		return true;	
		
	}
	
	
}

?>