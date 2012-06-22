<?php

/** 
 * @author Phillipo
 * 
 * 
 */
class innocms {

	/**
	 * Metodo para convertir una fecha en formato humano
	 * @param String $date
	 * @param String $format
	 * @return String
	 */
	public static function humanDate($date, $format = 'd-m-Y H:i:s') {
		
		if (empty($date)) return;
		
		$date = strtotime($date);
		return date($format, $date);
		
	}

	
	/**
	 * Metodo para convertir una fecha en formato mySQL
	 * @param String $humanDate
	 * @param String $format
	 * @return Object
	 */	
	public static function mysqlDate($humanDate, $format = 'Y-m-d H:i:s') {

		if (empty($humanDate)) return;		
		$date = DateTime::createFromFormat($format, $humanDate);
		return $date->format('Y-m-d H:i:s');
		
	}

	
	/**
	 * Metodo para convertir parametros en un objeto
	 * @param String $params
	 * @return Object
	 */
	public static function getParams($string) {
		
		if (preg_match_all('/[.*]/i',$string,$matches)) {
			
			foreach ($matches as $match) {
				
				echo $match.'<br>';
				
			}
			
			
		}
		
		
		// Devolvemos informacion
		if (sizeof($params) > 0) return self::arrayToObject($params);
		else return FALSE; 
		
	}	
	
	/**
	 * Metodo para convertir un array en un objeto
	 * @param Array $array
	 * @return Object
	 */
	public static function arrayToObject($array) {
		if (! is_array ( $array )) {
			return $array;
		}
	
		$object = new stdClass ();
		if (is_array ( $array ) && sizeof ( $array ) > 0) {
			foreach ( $array as $name => $value ) {
				$name = strtolower ( trim ( $name ) );
				if (! empty ( $name )) {
					$object->$name = self::arrayToObject( $value );
				}
			}
			return $object;
		} else {
			return FALSE;
		}
		
	}	

	
	/**
	 * Metodo para extraer información de una tabla
	 * @param Array $array
	 * @return Object
	 */	
	public static function metaColumns($fieldsdb) {
	
		$fields = array();		
		foreach ($fieldsdb as $k=>$v) {
	
			// Tipo de campo
			if (preg_match('/^[\w]+/i',$v,$matches)) $type = $matches[0];
			else $type = 'unknow';
	
			// Longitud de campo
			if (strtoupper($type) == 'TEXT') $max_length = 2000000;
			else if (preg_match('/[\d]+/',$v,$matches)) $max_length = (int)$matches[0];
			
			// Parseamos resultado en formato array
			$field = array(
					'name'=>$k,
					'type' => $type,
					'max_length'=> $max_length,
			);
	
			$fields[strtolower($k)] = self::arrayToObject($field);
			
		}
	
		return $fields;
	
	}
	
	/**
	 * Metodo para verificar datos de un array para su uso en BD
	 * @param Array $array
	 * @return Object
	 */
	public static function filterData($db, $table = '', $data = array()) {
	
		if (is_array($data) && sizeof($data) > 0 && $db->table_exists($table) == true) {
	
			// Datos de las columnas de la tabla
			$tableFields = $db->metaColumnsObject($table);
	
			// recorremos el array a procesar
			foreach ($data as $k=>$v) {
	
				// Comprobamos si el campo existe en la BD
				if (isset($tableFields[$k])) {
	
					// Revisamos el tipo de campo
					switch ($tableFields[$k]->type) {
						case 'int':	$v = (int)$v;
						case 'text':
						case 'varchar':
						case 'char': $v = addslashes(trim($v));
					}
	
					$data[$k] = $v;
	
				}
	
			}
	
			return $data;
	
		} else {
			return array();
		}
	
	}
	
	
	/**
	 * Convert bytes to human readable format
	 *
	 * @param integer bytes Size in bytes to convert
	 * @return string
	 */
	public static function bytesToSize($bytes, $precision = 2) {
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;
	
		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';
	
		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KB';
	
		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MB';
	
		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GB';
	
		} elseif ($bytes >= $terabyte) {
			return round($bytes / $terabyte, $precision) . ' TB';
		} else {
			return $bytes . ' B';
		}
	}	
	
	/**
	 * trims text to a space then adds ellipses if desired
	 * @param string $input text to trim
	 * @param int $length in characters to trim to
	 * @param bool $ellipses if ellipses (...) are to be added
	 * @param bool $strip_html if html tags are to be stripped
	 * @return string
	 */
	public static function trimText($input, $length, $ellipses = true, $strip_html = true) {
		
		//strip tags, if desired
		if ($strip_html) {
			$input = strip_tags($input);
		}
	
		//no need to trim, already shorter than trim length
		if (strlen($input) <= $length) {
			return $input;
		}
	
		//find last space within length
		//$last_space = strrpos(substr($input, 0, $length), ' ');
		$trimmed_text = substr($input, 0, $length);
	
		//add ellipses (...)
		if ($ellipses) {
			$trimmed_text .= '...';
		}
	
		return $trimmed_text;
		
	}	
	
	
	/**
	 * encode base64 uri for referer pages 
	 * @return string
	 */	
	
	public static function refererEncode() {
		return base64_encode(Router::get('controller_path').'/'.Router::get('action').'/'.implode('/',Router::get('parameters')));
	}
	
	
		
}

?>