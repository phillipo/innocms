<?php
/*
Creado por: Phillipo
Fecha: 25/05/2011 16:59:36
Fichero: admin_controller.php
*/

Load::lib('innocms');

class PageController extends AppController {
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){
		
		// Template
		if (in_array(Router::get('action'),array('edit','create','structure','design'))) {
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
	
	// Listado de contenidos
	public function index($filter = '', $page = 0, $limit = 20) {	
		
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		// Busqueda por URL
		$this->searchFilter = ($filter == '') ? '' : $filter;		
		// Creamos parametros de busqueda
		$search = array('text'=>$this->searchTerm, 'filter'=> $this->searchFilter, 'order'=>'page_sort_order ASC');				
		// Objecto
		$this->contentList = Load::model('page')->getList($page, $limit, $search);
		// Datos para el Partial
		$this->partialData = array();	
						
	}

	// Crear un nuevo contenido
	public function create($referer = '') {
				
		// Anulamos la vista
		View::select(null,null);
		
		// Parametros
		$error = false;
		
		// Modelos
		$this->page = Load::model('page');
		$this->attributes = Load::model('pageAttributes');
		
		// Datos principales
		$contentData = array('page_slug'=>'nuevo_pagina_'.date('YmdHis'), 'page_publish_expires'=>'0', 'page_language_id'=>'1', 'page_status'=>'draft');
		if ($this->page->saveIt($contentData)) {
		
			// Atributos
			$attributesData = array('id'=>'','page_id'=>$this->page->id, 'page_attribute_key'=>'TITLE','page_attribute_value'=>'Nueva página');
			if (!$this->attributes->create($attributesData)) {
				$error = true;
			}
		
		}
		
		// Comprobamos errores
		if ($error == true) {
			// Devolvemos AJAX
			die ( Input::request('jsoncallback') . "({'jResult':'failed','jMessage':" . json_encode('No se ha podido crear la página') . "})" );
		
		} else {
			Router::toAction('edit/'.$this->page->id.'/'.$referer);
		}
	
	}
	
	// Editar un contenido
	public function edit($id = '', $referer = '') {			
		
		// Recuperamos datos DB
		$this->page = Load::model('page')->getbyId($id);
		$this->attributes = Load::model('pageAttributes')->getListFields($id);		
		$this->templateList = Load::model('template')->getList();
			
		// Filtramos datos
		if (Input::hasPost('page') && Input::hasPost('attributes') && isset($_POST['attributes']['title'])) {

			// Parametro de error
			$error = false;									
						
			// Guardamos datos principales
			if (!$this->page->saveIt(Input::post('page'))) {
				Flash::error('Error al saveIt datos principales');
				$error = true;				
			} else {		
				
				// Datos enviados por POST
				$attributesPosted = Input::post('attributes');
				
				// Se hace un bucle, ya que se pueden pasar n Atributos
				foreach ($attributesPosted as $k=>$v) {
				
					if (!empty($v)) {		

						$attribute = Load::model('pageAttributes')->getbyKey($this->page->id,$k);
									
						$attrId = (!empty($attribute->id)) ? $attribute->id : '';
						
						$fields = array(
								'id' => $attrId,
								'page_id' => $this->page->id ,
								'page_attribute_key' => strtoupper($k) ,
								'page_attribute_value' => $v
						);
						
						if (!Load::model('pageAttributes')->save($fields)) {
							$error = true;
						}				
						
					}
				
				}				
								
			}
			
			// Verificamos errores
			if ($error) {
				$this->page = Input::post('page');
				$this->attributes = Input::post('pageAttributes');
			} else {
				Flash::success('Página actualizada');
				Router::toAction('edit/'.$this->page->id.'/'.$referer);
			}
			
		}  
		
		
		// Elementos de la plantilla
		$this->referer = (empty($referer)) ? 'admin/page/index' : base64_decode($referer); 
	
	}
	
	// Metodo para ordenar la estructura de las paginas
	public function structure() {	
		
		// Recuperamos datos
		$this->pageList = Load::model('page')->getList();
		
	}
	
	// Metodo para diseñar el contenido de una página
	public function design($id = 0) {
		
		// Pagina que tenemos que cargar
		if (empty($id)) {
			$this->page = Load::model('page')->getFirst();
		} else {
			$this->page = Load::model('page')->getbyId($id);
		}
		
		// Plantilla que tenemos que cargar
		$this->pagetemplate = Load::model('template')->getbyId($this->page->page_template_id);
		
	}
	
	
	// Metodo para ordenar la estructura de las paginas
	public function sort() {
		
		View::select(null,null);
	
		if (Input::hasPost('pages')) {
			
			$pages = Input::post('pages');
			foreach ($pages as $key => $value) {
				$page = Load::model('page')->getbyId($key);
				if ($page) {
					$page->page_parent_id = ($value == 'root') ? '0' : (int)$value;
					$page->page_sort_order = ($key+1)*10;
					$page->save();
				}

			}

	
		}
	
	}	
	
	// Eliminar un contenido
	public function remove($id = 0) {
	
		// Vista
		View::select(null,null);
		
		
		// Comprobamos metodo de envio
		if (Input::hasPost('ids')) {

			// Recuperamos datos		
			$ids = explode(',',Input::post('ids'));
			
			// Comprobamos tipo
			if (is_array($ids)) {
				// Borramos datos recursivamente
				foreach ($ids as $id) {
					Load::model('pageAttributes')->remove($id);				
					Load::model('page')->delete($id);
				}

			}
				
		} else if (!empty($id)) {					
			Load::model('pageAttributes')->remove($id);			
			Load::model('page')->delete($id);
		}	
		
		// Devolvemos AJAX
		die ( Input::request('jsoncallback') . "({'jResult':'ok','jMessage':" . json_encode('Elemento/s eliminados') . "})" );
		
	}

	
}

?>