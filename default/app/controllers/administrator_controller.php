<?php
/*
Creado por: Phillipo
Fecha: 25/05/2011 16:59:36
Fichero: admin_controller.php
*/


class AdministratorController extends AppController {

	public function before_filter(){
		if(!Auth::is_valid()){
			Flash::error("Usuario no logueado");
			Router::route_to('controller: administrator', 'action: index');
		} 
		
		$this->preferences = Load::model('preferences')->getPreference(1);	
		
	}	
	
	public function index() {

		View::template('admin');
		
	}
	

	
}

?>