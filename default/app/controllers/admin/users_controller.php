<?php
/*
Creado por: Phillipo
Fecha: 25/05/2011 16:59:36
Fichero: users_controller.php
*/

Load::lib('innocms');

class UsersController extends AppController {
	
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
		// Datos del partial
		$this->partialData = array();							
		
	}	
	
	// Metodo para listar todos los usuarios
	public function index($status = 'all', $page = 1) {		
		
		/** Procesamos consulta **/
		
		// Creamos parametros de busqueda para el modelo
		$search = array('text'=>$this->searchTerm,'status'=>$status, 'order'=>'user_name ASC');
		
		// Extraemos listado de usuarios segun criterios
		$this->listadoUsuarios = Load::model('users')->getList($search);
		
		/** Paginamos resultados **/
		
		// Pagina actual
		$this->pageId = (int)$page-1;		
		// Paginamos resultados
		$this->pages = array_chunk($this->listadoUsuarios, RESULT_LIMIT);
		
		/** Datos para partials de la pantalla */
		
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$status, 'page'=>$page, 'total'=>sizeof($this->pages));
		
		// Datos para el partial lateral
		$this->lateralData = array('category'=>'');		
						
	}

	// Metodo para filtrar por tipo de usuario
	public function group($grouptag = '', $page = 1) {
		
		// Vista listado
		View::select('index');
		
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		// Busqueda por URL
		$this->searchFilter = ($grouptag != '') ? $grouptag : '';		

		// Creamos parametros de busqueda para el modelo
		$search = array('text'=>$this->searchTerm,'group'=>$this->searchFilter, 'order'=>'user_name ASC');

		// Extraemos todos los usuario de tipo selecionado		
		$this->listadoUsuarios = Load::model('users')->getList($search);

		// Preparamos páginas
		$this->pageId = (int)$page-1;
		
		// Pages
		$this->pages = array_chunk($this->listadoUsuarios, RESULT_LIMIT);
		
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$this->searchFilter, 'page'=>$page, 'total'=>sizeof($this->pages));	

		// Datos para el partial lateral
		$this->lateralData = array('category'=>'');		
		
	}	
	
	// Metodo para insertar un nuevo usuario
	public function create() {

		// Comprobamos si se envian datos
		if (Input::hasPost('user')) {
			
			$error = false;
		
			// Creamos el modelo
			$this->user = Load::model('users');
			
			// Insertamos datos en DB
			if (!$this->user->saveIt(Input::post('user'))) {
				$error = true;			
			} else {
				Flash::success('Usuario guardado con éxito');
			}	
			
			// Verificamos si hay errores
			if ($error) {
				$this->user = Input::post('user');
			} else {
				Input::delete();
				Router::toAction('edit/'.$this->user->id);				
			}
			
		}
		
	}
	
	// Metodo para actualizar un usuario
	public function edit($id = '') {
		
		// Recuperamos datos del usuario
		$this->user = Load::model('users')->getUser($id);
		$this->usergroupList = Load::model('usergroup')->getList();
		
		// Si no se procesa un ID o no existe el usuario redirigimos
		if (empty($id) || $this->user->id == '') {
			Router::toAction('index');
		}
		
		// Si se envian datos debemos procesar para la BD
		if (Input::hasPost('user')) {
			
			$error = false;
			
			// Proceso de guardar datos
			if (!$this->user->saveIt(Input::post('user'))) {
				Flash::error('Error al guardar datos de usuario');
				$error = true;				
			} else {
				Flash::valid('Usuario actualizado con éxito');
			}

			// Verificamos errores
			if ($error) {
				$this->user = Input::post('user');
			} else {
				// Volvemos a recuperar los datos para mostrar en los campos
				$this->user = Load::model('users')->getUser($id);
			}			
			
		}
		
	}
	
	// Metodo para borrar un usuario
	public function remove() {
		
		// Anulamos vista
		View::select(null,'json');
		
		// Datos
		$ids = explode(',', Input::post('ids'));
		
		// TODO: No se puede borrar a sí mismo. Ver como vincular el usuario de acceso AUTH con usuario a borrar
		
		// Procesamos borrado
		if (is_array($ids)) {
			foreach ($ids as $id) {
				$error = (Load::model('users')->removeIt($id)) ? false : true;
			}
		} else if (is_string($ids)) {
			$error = (Load::model('users')->removeIt($ids)) ? false : true;
		} else {
			$error = true;
		}

		// Verificamos errores
		if ($error) {
			$this->json = array('jResult'=>'error','jMsg'=>'Se ha producido un error al borrar usuario/s');
		} else {
			$this->json = array('jResult'=>'valid','jMsg'=>'Usuario/s borrado/s correctamente');
		}				
		
	}

	
}

?>