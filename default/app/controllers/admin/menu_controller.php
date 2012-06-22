<?php
/*
 Creado por: Phillipo
Fecha: 12/05/2012 16:59:36
Fichero: menu_controller.php
*/

class MenuController extends AppController {
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){
		
		// Template
		if (in_array(Router::get('action'),array('edit','create'))) {
			View::template('admin/oneColumn');
		} else {
			View::template('admin/leftColumn');
		}
		
		//** DATOS GLOBALES
		
		// Datos generales
		$this->prefs = Load::model('preferences')->getPreference(0,0);
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		// Datos para el Partial
		$this->partialData = array();		
					
	}		
	
	public function index() {

	}
	
	public function create() {
		
	}
	
	public function edit() {
		
	}
	
	
}

?>