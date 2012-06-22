<?php
/*
Author: Cesar Caballero Gallego
Date: 10/05/2012 - 13:59:54
File: socialnetwork.php
*/

class SocialnetworkController extends AppController {
	
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
	
	
	// Metodo para mostrar ficha
	public function index() {
		
		if (Input::haspost('socialmedia')) {
			
				
			
		}	
		
		// Recuperamos datos de la BD
		$socialList = Load::model('socialnetworks')->getList();
		
		// Preparamos array para la vista
		$this->socialType = array('social','photo','video','others');
		$this->socialList = array();
		foreach ($socialList as $social) {			
			$this->socialList[$social->snet_type][] = $social;		
		}
					
				
	}
	
	
	
	
	
}






?>