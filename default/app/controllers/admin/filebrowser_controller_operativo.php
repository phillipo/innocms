<?php
/*
Author: Cesar Caballero Gallego
Date: 11/04/2012 - 19:28:08
File: upload_controller.php
*/

Load::lib('innocms');

class filebrowserController extends AppController {
	
	
	// Metodo para recuperar información para la cabecera
	public function before_filter(){
		// Template
		View::template('admin/oneColumn');
		// Datos generales
		$this->prefs = Load::model('preferences')->getPreference(0,0);
		// Vista parcial
		$this->partialData = array();
		
		// Busqueda de texto
		$this->searchTerm = Input::hasPost('searchTerm') ? Input::post('searchTerm') : '';
		
		
	}	

	// Metodo para mostrar un browser de ficheros
	public function index($category = 'all', $type = 'all', $page = 1) {	
		
				
		/** Procesamos parametros para crear la consulta **/
		
		// Creamos array con parametros de busqueda
		$this->search = array('content_type'=>'file', 'text'=>$this->searchTerm, 'content_mime_type'=>$type, 'category'=>$category, 'order'=>'content_attribute_value');		
		// Listado de contenidos
		$results = Load::model('content')->getList($this->search);
		
		/** Parseamos los datos desde el controlador para una vista mas limpia **/
		
		// Preparamos datos para la vista
		$this->fileList = array();
		foreach ($results as $file) {
			
			// Ruta del archivo
			if (preg_match("/(png|gif|jpeg)/", $file->content_mime_type)) {
				$filepath = dirname(APP_PATH) . '/public/img/upload/'.$file->content_slug;
			} else {
				$filepath = dirname(APP_PATH) . '/public/files/upload/'.$file->content_slug;
			}
			
			// Extraemos informacion
			$path = pathinfo($filepath);
			
			// Preparamos array de datos
			$this->fileList[] = array(
					'id' => $file->id,
					'name'=> $path['basename'],
					'dirname' => $path['dirname'],
					'type'=> $path['extension'],
					'size' => Innocms::bytesToSize(filesize($filepath),1),
					'date' => filemtime($filepath),
			);	
					
		}
		
		/** Paginamos resultados **/
		
		// Pagina actual
		$this->pageId = (int)$page-1;			
		// Paginamos resultados
		$this->pages = array_chunk($this->fileList, RESULT_LIMIT);
		
		/** Listado de sets/colecciones disponibles **/
		$this->setList = Load::model('content')->getList(array('content_type'=>'set'));
		
		
		/** Datos para partials de pantalla **/

		// Datos para el partial lateral
		$this->lateralData = array('category'=>$category,'type'=>$type, 'setList'=>$this->setList);		
		// Datos para el partial de paginacion
		$this->paginationData = array('urlParams'=>$category.'/'.$type, 'page'=>$page, 'total'=>sizeof($this->pages));		
		// Listado de Sets
		$this->setList = Load::model('content')->getList(array('content_type'=>'set'));	
		// Datos para el partial de opciones de listado
		$this->optionsListData = array('category'=>$category);

	
	}

	
	// Metodo para mostrar un browser de ficheros simplificado para la asociación remota via AJAX
	public function remote($type = 'all') {
		
		// Vista
		View::select('remote','admin/oneColumn-remote');
		
		// Creamos consulta
		$this->search = array('content_type'=>'file', 'content_mime_type'=>$type, 'order'=>'content_attribute_value');	
	
		// Consulta
		$files = Load::model('content')->getList($this->search);
		
		// Preparamos datos para la vista
		$this->fileList = array();
		foreach ($files as $file) {
			
			// Ruta del archivo
			if (preg_match("/(png|gif|jpeg)/", $file->content_mime_type)) {
				$filepath = dirname(APP_PATH) . '/public/img/upload/'.$file->content_slug;
			} else {
				$filepath = dirname(APP_PATH) . '/public/files/upload/'.$file->content_slug;
			}
			
			// Extraemos informacion
			$path = pathinfo($filepath);
			
			// Preparamos array de datos
			$this->fileList[] = array(
					'id' => $file->id,
					'name'=> $path['basename'],
					'dirname' => $path['dirname'],
					'type'=> $path['extension'],
					'size' => Innocms::bytesToSize(filesize($filepath),1),
					'date' => filemtime($filepath),
			);	
					
		}	
		
		/** Datos para los parciale**/
		$this->optionsListData = array('category'=>'all');
	
	}
	
	// Metodo para obtener información de un archivo
	public function info($json) {
		
		if (Input::hasPost('file')) {
		
			// Buscamos ID en sistema de archivos
			$filedb = Load::model('content')->getById(Input::post('file'));
	
			if (!empty($filedb)) {
				
				if (in_array($filedb->content_mime_type, array('image/jpeg','image/pjpeg','image/gif','image/png'))) {
					$path = dirname(APP_PATH).'/public/img/upload/'.$filedb->content_slug;
				} else {
					$path = dirname(APP_PATH).'/public/files/upload/'.$filedb->content_slug;
				}
				
				// Información
				$file = pathinfo($path);
				
				// Datos si el fichero es una imagen
				if (in_array($file['extension'], array('jpg','png','gif','jpeg'))) {
					list($width, $height, $type, $attr) = getimagesize($path);
					$output = '<div class="info-filebrowser-preview"><img src="/admin/filebrowser/resize/'.$file['basename'].'/crop/250/100"></div>'.EOL.'<hr>'.EOL;					
				} else {

					$width = 0; $height = 0;					
					
					if (in_array($file['extension'], array('doc','docx','odt','txt','rtf'))) {
						$output = '<div class="info-filebrowser-preview">'.Html::img('default/preview_docs_100.png').'</div>'.EOL.'<hr>'.EOL;
					} else if (in_array($file['extension'], array('xls','xlsx','ods'))) {
						$output = '<div class="info-filebrowser-preview">'.Html::img('default/preview_spreadsheet_100.png').'</div>'.EOL.'<hr>'.EOL;
					} else  if (in_array($file['extension'], array('pdf'))) {
						$output = '<div class="info-filebrowser-preview">'.Html::img('default/preview_pdf_100.png').'</div>'.EOL.'<hr>'.EOL;
					} else  if (in_array($file['extension'], array('mp3','mp4','wav','wma'))) {
						$output = '<div class="info-filebrowser-preview">'.Html::img('default/preview_audio_100.png').'</div>'.EOL.'<hr>'.EOL;					
					} else {
						$output = '<div class="info-filebrowser-preview">'.Html::img('default/preview_others_100.png').'</div>'.EOL.'<hr>'.EOL;
					}
					
				}
				
				// Salida imagen
				
				// Salida datos HTML
				$output .= '
					<dl class="dl-horizontal info-filebrowser-data">
					<dt>Nombre<dt>
					<dd><abbr title="'.$file['basename'].'">'.Innocms::trimText($file['basename'],20).'</abbr></dd>
					<dt>Descripción</dt>
					<dd>--</dd>
					<dt>Tipo<dt>
					<dd>'.strtoupper($file['extension']).'</dd>				
					<dt>Peso<dt>
					<dd>'.Innocms::bytesToSize(@filesize($path),1).'</dd>
					<dt>Dimensiones<dt>
					<dd>'.(empty($width) ? '--' : $width.' x '.$height.' pixels').'</dd>
					</dl>
				';		
				
				// Devolvemos array de datos
				die ( $json . "({'jResult':'ok','jMessage':" . json_encode($output) . "})" );
			
			} else {
				die ( $json . "({'jResult':'failed','jMessage':" . json_encode('No existe información del archivo'.Input::post('file')) . "})" );
			}

		} else {
			die ( $json . "({'jResult':'failed','jMessage':" . json_encode('Paámetros incorrectos') . "})" );
		}
		
	}
	
	// Reescalado de imagenes
	public function resize($image = '', $mode = "auto", $width = 100, $height = 100, $quality = 100) {
		
		// Anulamos salida
		View::select(null,'admin/empty');
		
		// Si no hay imagen 
		if (empty($image)) return;
		
		// Imagen a convertir
		$image = dirname(APP_PATH).'/public/img/upload/'.$image;
		
		// Parametros
		$this->image = new Resize($image);
		$this->image->resize($width, $height, $mode);
		$this->output = $this->image->onfly('.jpg',$quality);		
		
	}
	
	
	// Upload un archivo fisico
	public function upload() {
		
		// Anulamos vista
		View::select(null,null); //para mostrar siempre la vista con los formularios
		
		$totalFiles = sizeof($_FILES['files']['name']);
		for ($i = 0; $i < $totalFiles; $i++) {
		
			// Detectamos tipo de archivo
			$FILE['type'] = $_FILES['files']['type'][$i];

			// Tipos de uploads
			if (in_array(strtolower($FILE['type']), array('image/jpeg','image/pjpeg','image/gif','image/png'))) {
				
				//llamamos a la libreria y le pasamos el nombre del campo file del formulario
				//el segundo parametro de Upload::factory indica que estamos subiendo una imagen
				//por defecto la libreria Upload trabaja con archivos...
				$archivo = Upload::factory('file', 'image');
				$archivo->setExtensions(array('jpg', 'png', 'gif'));//le asignamos las extensiones a permitir
				
			} else {
					
				//llamamos a la libreria y le pasamos el nombre del campo file del formulario
				//el segundo parametro de Upload::factory indica que estamos subiendo una imagen
				//por defecto la libreria Upload trabaja con archivos...
				$archivo = Upload::factory('file');
				$archivo->setExtensions(array('doc','docx','xls','xlsx','odt','ods','pdf', 'zip', 'rar', 'mp3','txt'));//le asignamos las extensiones a permitir				
				
			}
			
			// Insertamos datos en BD		
			if ($filename = $archivo->saveSlug()) {
				
				$error = false;
				
				// Items
				$this->content = Load::model('content');
				$this->attributes = Load::model('contentAttributes');
				
				$data = array(
					'id' => '',
					'content_type' => 'file',
					'content_mime_type' => $FILE['type'],
					'content_slug' => $filename,
					'content_language_id' => '1',					
				);
				
				// Guardamos datos principales
				if (!$this->content->saveIt($data)) {
					Flash::error('Error al guardar datos principales');
					$error = true;
				} else {
					
					// Asignamos el id principal a sus attributos
					$this->attributes->content_id = $this->content->id;
											
					// Preparamos array de datos para insertar
					$fields = array('DESCRIPTION' => '');		
					$fields = array('TITLE' => $filename);
					
					// Guardamos atributo
					if (!$this->attributes->saveIt($this->content->id, $fields)) {
						Flash::error('Error al guardar atributos');
						$error = true;
					}									
	
				}			

				
			}
			
		}
			
		die ( $json . "({'jResult':'ok','jMessage':" . json_encode('Fichero subido correctamente') . "})" );
			
	//	die ( $json . "({'jResult':'ok','jMessage':" . json_encode('Archivo no se ha podido subir') . "})" );
						
	}	
	
	// Editar datos extra del fichero
	public function edit($slug) {
		
	}
	
	// Cambia el nombre de un archivo ( al estar asociados por BD,
	// debe realizar una busqueda masiva por todas las referencias )
	public function rename() {
		
	}
	
	// Borrar fichero
	public function remove($ids = array()) {
		
		// Vista
		View::select(null,null);
		
		// Recuperamos datos
		$ids = explode(',',Input::request('ids'));
		
		// Comprobamos tipo
		if (is_array($ids)) {
			
			// Borramos datos recursivamente
			foreach ($ids as $id) {

				// Datos de DB
				$file = Load::model('content')->getbyId($id);
				
				// Borramos archivo fisicamente
				if (in_array($file->content_mime_type, array('image/jpeg','image/pjpeg','image/gif','image/png'))) {
					@unlink(dirname(APP_PATH).'/public/img/upload/'.$file->content_slug);
				} else {
					@unlink(dirname(APP_PATH).'/public/files/upload/'.$file->content_slug);
				}

				// Borramos de la BD
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
	
	
	
	
	
}

?>