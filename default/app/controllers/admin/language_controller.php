<?php
/*
Author: Cesar Caballero Gallego
Date: 29/02/2012 - 15:29:10
File: languages_controller.php
*/


class languageController extends AppController {
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){

		// Template
		if (in_array(Router::get('action'),array('edit','create'))) {
			View::template('admin/oneColumn');
		} else {
			View::template('admin/oneColumn');
		}
		
		//** DATOS GLOBALES
		
		// Datos generales
		$this->prefs = Load::model('preferences')->getPreference(0,0);
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';		
		// Datos del partial
		$this->partialData = array();
				
	}	
	
	public function index() {

		if (Input::hasPost('language_default') && Input::hasPost('language_availables')) {
			
			
			
		}
		
		
		// Listado de idiomas disponibles
		$langs = Load::model('language')->getList();
		
		// Preparamos datos
		$this->availableLangs = array();
		foreach ($langs as $lang) {
			$this->availableLangs[] = $lang; 
		} 
		
				
	}
	
	
}



?>