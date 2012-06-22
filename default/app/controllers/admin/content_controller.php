<?php
/*
Creado por: Phillipo
Fecha: 25/05/2011 16:59:36
Fichero: admin_controller.php
*/

Load::lib('innocms');


class ContentController extends AppController {
	
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
	
	// Listado de contenidos
	public function index($category = 'all', $status = 'all', $page = 1, $referer = '') {
		
		/** Procesamos parametros para crear consulta **/
		
		// Creamos array con parametros de busqueda
		$search = array('content_type'=>'post', 'text'=>$this->searchTerm,'category'=> $category, 'status'=>$status, 'order'=>'content_sort_order ASC');				
		// Listado de contenidos
		$results = Load::model('content')->getList($search);
				
		
		/** Paginamos resultados **/
		
		// Numero de pagina actual
		$this->pageId = (int)$page-1;		
		// Paginamos resultados
		$this->pages = array_chunk($results, RESULT_LIMIT);	
		
		/** Datos para partials de la pantalla */

		// Datos para el partial lateral
		$this->lateralData = array('category'=>$category);				
		// Datos para el partial opciones de listado
		$this->optionsListData = array('category'=>$category);				
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$category.'/'.$status, 'page'=>$page, 'total'=>sizeof($this->pages));
		
		// Url de referencia
		$this->referer = (empty($referer)) ? 'admin/category/index' : base64_decode($referer);		
	
	}

	// Crear un nuevo contenido
	public function create($parent = '') {
		
		// Anulamos la vista
		View::select(null,null);
		
		// Parametros
		$error = false;
		
		// Si estamos dentro de una categoria, debemos crear la nueva categoria dentro de esta
		$this->parentCategory = Load::model('content');
		$this->parentCategory->getbyKey($parent);
		
		if (!empty($this->parentCategory)) {
			$parentId = $this->parentCategory->id;
		} else {
			$parentId = 0;
		}
		
		// Modelos
		$this->content = Load::model('content');
		$this->attributes = Load::model('contentAttributes');
		
		// Datos principales
		$contentData = array('content_type'=>'post', 'content_parent_id'=> $parentId, 'content_language_id'=> 1, 'content_slug'=>'nuevo_contenido_'.substr(md5(date('YmdHis')),0,5),'content_status'=>'private','content_sort_order'=>'0');
		if ($this->content->saveIt($contentData)) {
		
			// Atributos
			$attributesData = array('id'=>'','content_id'=>$this->content->id, 'content_attribute_key'=>'TITLE','content_attribute_value'=>'Nuevo contenido');
			if (!$this->attributes->create($attributesData)) {
				$error = true;
			}
		
		} 
		
		// Comprobamos errores
		if ($error == true) {
			// Devolvemos AJAX
			die ( Input::request('jsoncallback') . "({'jResult':'failed','jMessage':" . json_encode('No se ha podido crear el contenido') . "})" );
		
		} else {
			Router::toAction('index/'.$parent);
		}

	}
	
	// Metodo para editar la pagina
	public function edit($id = 0, $referer = '') {	
		
		$error = false;
		
		// Recuperamos datos DB
		$this->content = Load::model('content');
		$this->attributes = Load::model('contentAttributes');
		
		// Filtramos datos
		if (Input::hasPost('content') && Input::hasPost('attributes') && isset($_POST['attributes']['title'])) {
				
			// Parametros de error
			$error = false;
			
			$this->content->id = (int)$id;
		
			// Guardamos datos principales
			if (!$this->content->saveIt(Input::post('content'))) {
				$error = true;
			} else {

				// Guardamos secundarios
				if (Load::model('contentAttributes')->saveIt($this->content->id, Input::post('attributes'))) {
					$error = true;
				}
				
			}

			// Realizamos acción dependiento de los errores
			if ($error) {
				Flash::error('Revise los datos introducidos');
			} else {
				Flash::success('Datos guardados correctamente');
			}
			
		}
		
		// Recuperamos datos DB
		$this->content = $this->content->getbyId($id);
		$this->attributes = $this->attributes->getListFields($this->content->id);
		
		// Imagen asociada
		$this->featuredImage = Load::model('content')->getbyId($this->content->content_image_id);		
		
		// Listado categorias
		$this->categorySelect = new categorySelect();	

		// Referer
		$this->referer = empty($referer) ? 'admin/content/index/' : base64_decode($referer);
		
	}
	
	// Eliminar contenidos
	public function remove() {
	
		// Vista
		View::select(null,null);

		// Recuperamos datos		
		$ids = explode(',',Input::request('ids'));
		
		// Comprobamos tipo
		if (is_array($ids)) {
			// Borramos datos recursivamente
			foreach ($ids as $id) {
				Load::model('contentAttributes')->remove($id);				
				Load::model('content')->delete($id);
			}

		} else if (is_string($ids)) {			
			Load::model('contentAttributes')->remove($ids);			
			Load::model('content')->delete($ids);
		}	
		
		// Devolvemos AJAX
		die ( Input::request('jsoncallback') . "({'jResult':'ok','jMessage':" . json_encode('Elementos eliminados') . "})" );
		
		return false;
		
	}
	
	// Duplicar contenidos
	public function duplicate() {
		
		// Vista
		View::select(null,null);

		// Recuperamos datos		
		$ids = explode(',',Input::request('ids'));
		
		// Actuamos segun tipo
		if (is_array($ids)) {
			
			// Recuperamos datos 
			foreach ($ids as $id) {
				
				$item = Load::model('content')->duplicate($id);
				
			}
			
			// Devolvemos AJAX
			die ( Input::request('jsoncallback') . "({'jResult':'ok','jMessage':" . json_encode('Elementos duplicados') . "})" );
			
		}

	}
	
	
	
	
	
}

?>