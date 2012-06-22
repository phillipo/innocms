<?php
/*
Creado por: Phillipo
Fecha: 15/06/2011 14:38:28
Fichero: module_controller.php
*/

class moduleController extends AppController {

	// Añadir un modulo
	public function create() {

		// Anulamos la vista
		View::select(null,null);

		// Procesamos datos
		if (Input::hasPost('templateId') && Input::hasPost('zoneId')) {
			$data = array();
			$data['template_id'] = (int)Input::post('templateId');
			$data['zone_id'] = (int)Input::post('zoneId');
			$module['id'] = Load::model('pageModules')->createIt($data);
			echo json_encode($module);	
		} else {
			return false;
		}
		
	}
	
	// Editar un modulo
	public function edit($id) {
		
	}
	
	// Borrar un modulo
	public function remove() {
		
		View::select(null,null);		
		Load::model('pageModules')->removeIt(Input::post('moduleId'));
		
	}
	
	
}

?>