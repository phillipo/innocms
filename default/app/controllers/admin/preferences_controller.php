<?php

/** 
 * @author Phillipo
 * 
 * 
 */
class PreferencesController extends AppController {
		
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
		
		$this->domain = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'] : "http://".$_SERVER['SERVER_NAME'];	
		
	}
	
	public function edit($id) {
		
		View::select(null,null);
		
		if (Input::hasPost('preferences')) {
			
			$prefs = Load::model('preferences');
			
			if ($prefs->update(Input::post('preferences'))) {
				
				Flash::success('Preferencias guardadas');
				
			} else {
				Flash::error('Error al guardar preferencias');
			}
			
			
		}
		
		return Router::redirect();
		
		
	}
	
}

?>