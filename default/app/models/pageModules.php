<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: module.php
*/

Load::lib('innocms');

class pageModules extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;	

	// Obtener un modulo
	public function getbyId($id) {
		$sqlQuery = 'SELECT * FROM page_modules as module WHERE module.id = "'.(int)$id.'" LIMIT 1';
		return $this->find_by_sql($sqlQuery);	
	}
	
	// Obtener un listado de modulos por plantilla
	public function getList($template_id) {
		$sqlQuery = 'SELECT * FROM page_modules as module WHERE module.template_id = "'.(int)$template_id.'"';
		return $this->find_all_by_sql($sqlQuery);		
	}
	
	// Obtener un listado de modulos por zona
	public function getListByZone($zone_id) {
		$sqlQuery = 'SELECT * FROM page_modules as module WHERE module.zone_id = "'.(int)$zone_id.'"';
		return $this->find_all_by_sql($sqlQuery);
	}
			
	// Crear un modulo 
	public function createIt($data) {
		
		// Guardamos y devolvemos el ID
		if (!$this->create($data)) return false;
		else {
			$this->updateSortOrder($this->template_id);
			return $this->id; 	
		}
				
	}
	
	// Guardar un modulo
	public function saveIt($data) {
		
		if (is_array($data)) {
			
			$id = isset($data['id']) ? $data['id'] : '';
			
			if (!$this->save($data)) {
				return false;
			} else {
				return $this->find_first((int)$id);
			}
			
		}
		
		return false;
		
	}
	
	// Borrar un modulo
	public function removeIt($id) {
		$zone = $this->find_first($id);
		$this->delete($id);
		$this->updateSortOrderOnDelete($zone->template_id);
	}
	
	// Actualizar indice de ordenacion
	public function updateSortOrder($template_id) {
		
		$list = $this->getList($template_id);
		foreach ($list as $row) {
			$row->update(array('module_sort_order'=>($row->module_sort_order * 10)));
			$n++;
		}
		
	}
	
	// Actualizar indice de ordenacion
	public function updateSortOrderOnDelete($template_id) {
	
		$list = $this->getList($template_id);
		$n = 1;
		foreach ($list as $row) {
			$row->update(array('module_sort_order'=>($n * 10)));
			$n++;
		}
	
	}	
	
}

?>