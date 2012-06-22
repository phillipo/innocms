<?php
/*
Author: Cesar Caballero Gallego
Date: 10/05/2012 - 16:25:21
File: sets_controller.php
*/

class SetController extends AppController {
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){
	
		// Template
		View::template('admin/oneColumn');
	
		//** DATOS GLOBALES
	
		// Datos generales
		$this->prefs = Load::model('preferences')->getPreference(0,0);
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		// Datos para el Partial
		$this->partialData = array();
		// Url de referencia
		$this->referer = (empty($referer)) ? 'admin/set/index' : base64_decode($referer);		
	
	}	
	
	// Metodo para mostrar listados
	public function index($status = 'all', $page = 1) {
		
		
		/** Procesamos datos para crear consulta **/
		
		// Datos de consulta
		$search = array('content_type'=>'set', 'text'=>$this->searchTerm, 'status'=>$status);		
		// Consulta SQL
		$this->listado = Load::model('content')->getList($search);
		
		/** Paginamos resultados **/
		
		// Número Pagina
		$this->pageId = (int)$page-1;		
		// Paginas
		$this->pages = array_chunk($this->listado, RESULT_LIMIT);
		
		/** Datos para partials de la pantalla */
		
		// Datos para el partial lateral
		$this->lateralData = array('category'=>$status);
		// Datos para el partial opciones de listado
		$this->optionsListData = array();		
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$status, 'page'=>$page, 'total'=>sizeof($this->pages));
		
	}
	
	// Metodo para crear un set ( AJAX )
	public function create() {
		
		// Anulamos la vista
		View::select(null,null);
		
		// Parametros
		$error = false;

		// Modelos
		$this->content = Load::model('content');
		$this->attributes = Load::model('contentAttributes');
		
		// Datos principales		
		$contentData = array('content_type'=>'set', 'content_language_id'=> 1, 'content_slug'=>'nuevo_set','content_status'=>'private','content_sort_order'=>'0');
		if ($this->content->create($contentData)) {
		
			// Atributos
			$attributesData = array('id'=>'','content_id'=>$this->content->id, 'content_attribute_key'=>'TITLE','content_attribute_value'=>'Nuevo Set');		
			if (!$this->attributes->create($attributesData)) {
				$error = true;
			}	
		
		} else {
			$error = false;
		}
			
		// Comprobamos errores
		if ($error) {
			// Devolvemos AJAX
			die ( Input::request('jsoncallback') . "({'jResult':'failed','jMessage':" . json_encode('No se ha podido crear un set') . "})" );
			
		} else {
			Router::toAction('index');
		}

	}
	
	// Metodo para editar un set
	public function edit($id = 0, $referer = '') {
		
		// Filtramos datos
		if (Input::hasPost('set') && Input::hasPost('attributes') && isset($_POST['attributes']['title'])) {
		
			// Parametros de error
			$error = false;
			
			// Recuperamos datos DB
			$this->content = Load::model('content')->getbyId($id);
			$this->attributes = Load::model('setAttributes')->getListFields($id);			
		
			// Guardamos datos principales
			if (!$this->content->saveIt(Input::post('set'))) {
				$error = true;
			} else {
				
				// Guardamos secundarios
				if (Load::model('setAttributes')->saveIt($this->content->id, Input::post('attributes'))) {
					$error = true;
				}
		
			}
		
			// Realizamos acción dependiento de los errores
			if ($error) {
				Flash::error('Revise los datos introducidos');
			} else {
				Input::delete();
				Flash::success('Datos guardados correctamente');
			}
		
		}
		
		// Recuperamos datos DB
		$this->content = Load::model('content')->getbyId($id);
		$this->attributes = Load::model('setAttributes')->getListFields($this->content->id);		
		
		// Recuperamos listado de ficheros para el slider
		$this->files = Load::model('content')->getList(array('content_type'=>'file','order'=>'content_slug ASC'));
				
	}	
	
	// Eliminar sets
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
	
	
	// Duplicar set
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
	
	// Eliminar items de un SET
	public function addItem($id = 0) {

		// Anulamos vista al tratarse de JSON
		View::template(null);
		
		
		// Datos del fichero
		$file = Load::model('content')->getbyId($id);	
		
		// Datos del fichero para la vista
		$this->itemSet = array(
				'basename'=>'',
				'extension'=>'',
				'size'=>'',
				'preview'=>'',
				'type'=>'',
				'path'=>'');
		
		// ID
		$this->itemSet['id'] = $file->id;		
		
		// Nombre
		$this->itemSet['basename'] = $file->content_slug;
		
		// Ruta
		if (preg_match("/(png|gif|jpg|jpeg)/i", $file->content_slug)) {
			$this->itemSet['type'] = 'image';
			$this->itemSet['path'] = dirname(APP_PATH).'/public/img/upload/'.$file->content_slug;						
		} else {
			$this->itemSet['type'] = 'others';
			$this->itemSet['path'] = dirname(APP_PATH).'/public/files/upload/'.$file->content_slug;
		}
		
		// Peso
		$this->itemSet['size'] = filesize($this->file['path']);
		
		// Datos si el fichero es una imagen
		if (preg_match("/(png|gif|jpg|jpeg)/i", $file->content_slug)) {
			$this->itemSet['preview'] = '/admin/filebrowser/resize/'.$file->content_slug.'/crop/120/135';
		} else if (preg_match("/(doc?|odt|txt|rtf')/i", $file->content_slug)) {
			$this->itemSet['preview'] = 'default/preview_docs_100.png';
		} else if(preg_match("/(xl?|ods')/i", $file->content_slug)) {
			$this->itemSet['preview'] = 'default/preview_spreadsheet_100.png';
		} else  if (preg_match("/(pdf')/i", $file->content_slug)) {
			$this->itemSet['preview'] = 'default/preview_pdf_100.png';
		} else if (preg_match("/(mp3|mp4|wav|wma')/i", $file->content_slug)) {
			$this->itemSet['preview'] = 'default/preview_audio_100.png';
		} else {
			$this->itemSet['preview'] = 'default/preview_others_100.png';
		}
	
	}
	
	
	
}



?>