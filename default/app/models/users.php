<?php

/** 
 * @author Phillipo
 * 
 * 
 */

class users extends ActiveRecord {

	protected $debug = FALSE;
	protected $logger = TRUE;
	
	// Recuperamos un usuario
	public function getUser($id) {
	
		return $this->find_first($id);
		
	}
	
	// Recuperamos un listado de usuarios
	public function getList($conditions = array()) {

		// Query
		$sql = '';
		$order = ' ORDER BY user_name ASC';
		
		// Condiciones
		if (sizeof($conditions) > 0 && is_array($conditions)) {

			// Si filtramos por estado (inactive/active)
			if (isset($conditions['status'])) {
			
				switch ($conditions['status']) {
					case 'inactive': $status = '1'; break;
					case 'active': $status = '0'; break;
					default: $status = '';					
				}
			
				$sql .= ($status != '') ? ' AND user_active = "'.$status.'"' : '';

			}	

			if (isset($conditions['group']) && !empty($conditions['group'])) {
				
				$usergroup = Load::model('usergroup')->getbyKey($conditions['group']);
				
				if (!empty($usergroup)) {
					$sql .= ' AND usergroup_id = "'.$usergroup->id.'"';
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
					$sql .= ($text != '') ? ' AND MATCH(user_name, user_alias, user_email) AGAINST ("'.$text.'" IN BOOLEAN MODE)' : '';
				
				} else {			
					$sql .= ($text != '') ? ' AND (user_name LIKE "%'.addslashes($text).'%" OR user_alias LIKE "%'.addslashes($text).'%"  OR user_email LIKE "%'.addslashes($text).'%")' : '';				
				}

			} 
			
			// Ordenación
			if (isset($conditions['order'])) $order = ' ORDER BY '.$conditions['order'];

			// Preparamos sentencia SQL con parametros
			if (!empty($sql)) {
				$sql = ' WHERE '.ltrim($sql,' AND ');
			}
			
		}	

		$sqlQuery = 'SELECT users.id as user_id, users.*, usergroup.* 
		FROM users INNER JOIN usergroup ON usergroup.id = users.usergroup_id'.$sql.$order;				
		
		
		return $this->find_all_by_sql($sqlQuery);
		
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
			if (!isset($data['user_active'])) {
				$data['user_active'] = '0';
			}			
			
			
			// Guardamos datos procesados
			if ($this->save($data)) {
				return true;
			}
			
		}
		
		return false;
		
	}
	
	// Borramos un usuario
	public function removeIt($id = '') {
		
		// Recuperamos información del usuario
		$user = $this->getUser($id);
		
		// Verificamos id
		if (empty($user->id)) return false;
		
		// Borramos
		if ($this->delete($user->id)) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
}

?>