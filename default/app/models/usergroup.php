<?php

/** 
 * @author Phillipo
 * 
 * 
 */

class usergroup extends ActiveRecord {

	protected $debug = FALSE;
	protected $logger = FALSE;
	
	// Recuperamos un usuario
	public function getbyId($id = '') {

		$sqlQuery = 'SELECT usergroup.id as usergroup_id, usergroup.*, definition.* FROM usergroup INNER JOIN definition ON usergroup_key = definition_key WHERE usergroup.id = "'.(int)$id.'" ORDER BY usergroup_level ASC LIMIT 1';		
		return $this->find_by_sql($sqlQuery);
		
	}
	
	// Metodo para recuperar datos por SLUG
	public function getbyKey($pageKey = '') {
		$sqlQuery = 'SELECT * FROM usergroup WHERE usergroup.usergroup_slug = "'.$pageKey.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);
	}	
	
	// Recuperamos un listado de usuarios
	public function getList($conditions = array()) {
				
		// Query
		$sqlQuery = 'SELECT usergroup.id as usergroup_id, usergroup.*, definition.*, (SELECT COUNT(*) FROM users WHERE users.usergroup_id = usergroup.id) as total FROM usergroup INNER JOIN definition ON usergroup_key = definition_key';
		$order = ' ORDER BY definition_value ASC';
		
		// Condiciones
		if (sizeof($conditions) > 0 && is_array($conditions)) {
		
			$sql = '';
		
			// Si filtramos por estado (inactive/active)
			if (isset($conditions['status'])) {
		
				switch ($conditions['status']) {
					case 'inactive': $status = '1'; break;
					case 'active': $status = '0'; break;
					default: $status = '';
				}
		
				$sql .= ($status != '') ? ' AND usergroup_active = "'.$status.'"' : '';
		
			}
		
			// Texto
			if (isset($conditions['text']) && !empty($conditions['text'])) {
		
				$text = '';
				$words = explode(',',$conditions['text']);
				foreach ($words as $word) {
					$text .= addslashes($word);
				}
		
				$sql .= ($text != '') ? ' AND MATCH(definition_value) AGAINST ("'.$text.'" IN BOOLEAN MODE)' : '';
		
			}
		
			// Ordenación
			if (isset($conditions['order'])) $order = ' ORDER BY '.$conditions['order'];

			// Preparamos SQL con parametros
			if (!empty($sql)) {
				$sql = ' WHERE '.ltrim($sql,' AND ');
			}
		
			// Creamos consulta
			$sqlQuery .= $sql;
		
		}
		
		return $this->find_all_by_sql($sqlQuery.$order);		
		
	}
	
	// Metodo para recuperar usuarios no asignados a un grupo
	public function getAssignedUsers($groupId = 0) {
	
		$sqlQuery = 'SELECT users.id as user_id, users.*, usergroup.*
		FROM users INNER JOIN usergroup ON usergroup.id = users.usergroup_id AND users.usergroup_id = "'.$groupId.'"';
		return $this->find_all_by_sql($sqlQuery);
	
	
	}	
	
	// Guardamos información de un usuario	
	public function saveIt($data) {
		
		// Comprobamos si recibimos un array de datos
		if (is_array($data)) {
			
			// Id del registro. Si no esta asignado se presupone que es una inserción nueva
			$id = isset($data['id']) ? $data['id'] : '';
			
			// Analizamos datos
			foreach ($data as $k=>$v) {
				
				// Revisamos fechas
				if (in_array(strtolower($k), array('user_created_at','user_modified_in'))) {
					$data[$k] = (empty($v)) ? date('Y-m-d H:i:s') : Innocms::mysqlDate($v,'d-m-Y H:i:s');
				}
				
			}
			
			// Revisamos estado
			if (!isset($data['usergroup_active'])) {
				$data['usergroup_active'] = '0';
			}			
			
			
			// Guardamos datos procesados
			if ($this->save($data)) {
				return true;
			}
			
		}
		
		return false;
		
	}
	
	// Borramos un usuario
	public function removeIt() {
		
	}
	
	
}

?>