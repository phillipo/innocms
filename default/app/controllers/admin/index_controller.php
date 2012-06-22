<?php
/*
Creado por: Phillipo
Fecha: 25/05/2011 16:59:36
Fichero: admin_controller.php
*/

class IndexController extends AppController {

	public function before_filter(){
		$this->prefs = Load::model('preferences')->getPreference(0,0);
	}		
	
	public function index() {

		View::template('admin');
		
	}
	

	
}

?>