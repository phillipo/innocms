<?php
/*
Author: Cesar Caballero Gallego
Date: 05/03/2012 - 17:41:19
File: usergroup.php
*/

class usergroupController extends AppController {
	
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
		// Datos partial
		$this->partialData = array();							
	
	}
	
	// Metodo para mostrar listado de perfiles
	public function index($status = '') {
		
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		// Busqueda por URL
		$this->searchFilter = ($status != '') ? $status : '';
		
		// Creamos parametros de busqueda para el modelo
		$search = array('text'=>$this->searchTerm,'status'=>$this->searchFilter, 'order'=>'definition_value ASC');
		
		// Extraemos listado de usuarios segun criterios
		$this->listadoGrupos = Load::model('usergroup')->getList(0,100,$search);
		
	}
	
	// Metodo para añadir un perfil
	public function create() {
		
		// Parametros internos
		$hasError = true;
		
		// Listado de usuarios
		$this->userList = Load::model('users')->getList(0,10000);
		
		// Comprobamos si se envian datos
		if (Input::hasPost('usergroup') && Input::hasPost('attributes')) {
			$hasError = true;

			// Comprobamos errores
			if ($hasError) {
				Flash::error('Revise los datos introducidos');
			} else {
				Input::delete();
				Flash::success('Datos guardados correctamente');
				Router::toAction('edit/'.$this->usergroup->id);
			}
		
		}
			
	}	
	
	// Metodo para actualizar un perfil
	public function edit($id = '') {
		
		// Recuperamos informacion del grupo
		$this->usergroup = Load::model('usergroup')->getbyId($id);
		$this->definition = Load::model('definition')->getbyKey($this->usergroup->usergroup_key);

		// Comprobar si existe ese grupo		
		if ($this->usergroup->id == '') {
			Router::toAction('index');
		}
		
		// Listado de usuarios
		$this->userList = Load::model('users')->getList();
		$this->assignedUsers = Load::model('users')->getAssignedUsers($this->usergroup->usergroup_id);
				
		// Comprobamos si estamos enviando información para procesar
		if (Input::hasPost('usergroup')) {
			
			$error = false;
			
			// Proceso de guardar datos
			if ($this->usergroup->saveIt(Input::post('usergroup'))) {
				
				if ($this->definition->saveIt(Input::post('definition'))) {
					Flash::success('Grupo actualizado con éxito');
					
					// Asignamos a los usuarios su correspondiente grupo
					if (Input::hasPost('usergroup_users')) {
						
						$usersId = Input::post('usergroup_users');
						$tmp = explode(',',$usersId);
						foreach ($tmp as $uId) {
							$user = Load::model('users')->find_first($uId);
							if ($user) {
								$user->usergroup_id = $this->usergroup->id;
								$user->save();
							}
						}
											
					}
					
					
				} else {
					$error = true;				
				}
 				
			} else {
				$error = true;
			}
			
			// Verificamos errores
			if ($error) {
				$this->usergroup = Input::post('usergroup');
			} else {
				$this->usergroup = Load::model('usergroup')->getbyId($id);
				$this->definition = Load::model('definition')->getbyKey($this->usergroup->usergroup_key);				
			}
			
		}
		
	}
	
	// Metodo para asociar un usuario al grupo
	public function addUser($id) {
		
		// Asignamos template JSON
		View::template(null);
		// Recuperamos datos
		$this->user = Load::model('users')->getUser($id);		
		
	}
	
	
	
	
}


?>