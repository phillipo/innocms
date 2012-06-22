<?php

class categoryController extends AppController {

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
							
	}	
		
	// Metodo para mostrar listados de categorias
	public function index($parent = 'all', $status = 'all', $page = 1, $referer = '') {
				
		/** Titulo de la seccion **/
		
		// Datos para el titulo
		$this->category = Load::model('content')->getbyKey($parent);
		$categoryId = (empty($this->category)) ? 0 : $this->category->id;
		$categorySlug = (empty($this->category)) ? '' : $this->category->content_slug;
		
		// Titulo del listado
		$this->attributes = Load::model('contentAttributes')->getbyKey($categoryId,'TITLE');
		$this->nameCat = (empty($this->attributes)) ? '' : $this->attributes->content_attribute_value;				
		
		/** Procesamos consulta **/
		
		// Creamos array con parametros de busqueda
		$search = array('content_type'=>'category', 'text'=>$this->searchTerm, 'category'=>$parent, 'status'=>$status, 'order'=>'content_attribute_value');		
		// Listado de categorias
		$this->contentList = Load::model('content')->getList($search);
	
		/** Paginamos resultados **/
		
		// Pagina actual
		$this->pageId = (int)$page-1;		
		// Paginamos resultados
		$this->pages = array_chunk($this->contentList, RESULT_LIMIT);
		
		/** Datos para partials de la pantalla */
		
		// Datos para el partial lateral
		$this->lateralData = array('category'=>$parent);
		
		// Datos para el partial opciones de listado
		$this->optionsListData = array('category'=>$parent);		
		
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$parent.'/'.$status, 'page'=>$page, 'total'=>sizeof($this->pages));
		
		// Datos parta el partial de creación
		$this->createData = array('category'=>$parent);
		
		// Referer
		$this->referer = empty($referer) ? 'admin/content/index/' : base64_decode($referer);		
		
	}
	
	// Metodo para crear una nueva categoria
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
		$contentData = array('content_type'=>'category', 'content_parent_id'=> $parentId, 'content_language_id'=> 1, 'content_slug'=>'nueva_categoria_'.substr(md5(date('YmdHis')),0,5),'content_status'=>'private','content_sort_order'=>'0');
		if ($this->content->create($contentData)) {
		
			// Atributos
			$attributesData = array('id'=>'','content_id'=>$this->content->id, 'content_attribute_key'=>'TITLE','content_attribute_value'=>'Nueva categoria');
			if (!$this->attributes->create($attributesData)) {
				$error = true;
			}
		
		} else {
			$error = false;
		}
		
		// Comprobamos errores
		if ($error) {
			// Devolvemos AJAX
			die ( Input::request('jsoncallback') . "({'jResult':'failed','jMessage':" . json_encode('No se ha podido crear la categoria') . "})" );
		
		} else {
			Router::toAction('index/'.$parent);
		}		
		
	}
	
	// Metodo para editar una categoria
	public function edit($id = 0, $referer = '') {
		
		// Datos
		$this->category = Load::model('content');
		$this->attributes = Load::model('contentAttributes');		
		
		// Filtramos datos
		if (Input::hasPost('category') && Input::hasPost('attributes') && isset($_POST['attributes']['title'])) {

			// Parametros de error
			$error = false;		
		
			// Asignamos ID
			$this->category->id = (int)$id;
			
			// Guardamos datos principales
			if (!$this->category->saveIt(Input::post('category'))) {				
				$error = true;				
			} else {							
				if ($this->attributes->saveIt($this->category->id, Input::post('attributes'))) {
					$error = true;
				}
			}

			// Verificamos errores
			if ($error) {
				Flash::error('No se han podido guardar los datos');
			} else {
				Flash::success('Datos guardados correctamente');
			}			
			
		} 
		
		// Recuperamos datos DB
		$this->category = $this->category->getbyId($id);
		$this->attributes = $this->attributes->getListFields($this->category->id);
		
		// Imagen asociada
		$this->featuredImage = Load::model('content')->getbyId($this->category->content_image_id);
		
		// Listado categorias
		$this->categorySelect = new categorySelect();
		
		// Referer
		$this->referer = empty($referer) ? 'admin/content/index/' : base64_decode($referer);		
		
	}
		
	
	// Metodo para borrar una categoria	
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
		
	
	// EVENTOS PARA AJAX
	public function getCategories() {
		
		$this->cats = Load::model('content');
		return $this->cats->getList(array('content_sort_order, content_parent_id'));
		
	}
	
	
	
	

}

?>