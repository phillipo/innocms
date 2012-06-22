<?php
/*
Author: Cesar Caballero Gallego
Date: 29/02/2012 - 15:29:10
File: languages_controller.php
*/


class dashboardController extends AppController {
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){
		// Plantilla
		View::template('admin/oneColumn');
		// Datos generales
		$this->prefs = Load::model('preferences')->getPreference(0,0);
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';	
	}	
	
	// Metodo para mostrar la pantalla general de listado de idiomas
	public function index() {
				
		
	}
	
	
}



?>