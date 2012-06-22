<?php
/*
Author: Cesar Caballero Gallego
Date: 11/04/2012 - 19:28:08
File: upload_controller.php
*/

Load::lib('innocms');
Load::lib('upload');

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
						
			if (preg_match("/(png|gif|jpg|jpeg)/i", $file->content_slug)) {
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
			if (preg_match("/(png|gif|jpeg|jpg)/", $file->content_slug)) {
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
				
				if (preg_match("/(png|gif|jpg|jpeg)/i", $filedb->content_slug)) {
					$path = dirname(APP_PATH).'/public/img/upload/'.$filedb->content_slug;
				} else {
					$path = dirname(APP_PATH).'/public/files/upload/'.$filedb->content_slug;
				}
				
				// Información
				$file = pathinfo($path);
				
				// Datos si el fichero es una imagen
				if (preg_match("/(png|gif|jpg|jpeg)/i", $filedb->content_slug)) {
					list($width, $height, $type, $attr) = getimagesize($path);
					$output = '<div class="info-filebrowser-preview"><img src="/admin/filebrowser/resize/'.$file['basename'].'/crop/250/175"></div>'.EOL.'<hr>'.EOL;					
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
				
		// Codigo extraido de plupload y adaptado
		
		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Parametros generales		
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
		
		// 5 minutes execution time
		@set_time_limit(5 * 60);
		
		// Uncomment this one to fake upload time
		// usleep(5000);
		
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		
		// Analizamos tipo de archivo
		if (preg_match("/(png|gif|jpg|jpeg)/i", $fileName)) {
			$targetDir = dirname(APP_PATH).'/public/img/upload/';
		} else {
			$targetDir = dirname(APP_PATH).'/public/files/upload/';
		}
		
		
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
		
			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;
		
			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);
		
		// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
		
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}
		
			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		
		
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");
		
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
		
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		
				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
		}
		
		// Si el archivo se ha copiado lo registramos en la BD
		if (file_exists($filePath)) {
		
			// Tecnicamente si llegamos hasta aquí debemos guardar los datos en la BD
			$error = false;
			
			// Items
			$this->content = Load::model('content');
			$this->attributes = Load::model('contentAttributes');
			
			$data = array(
					'id' => '',
					'content_type' => 'file',
					'content_mime_type' => $_FILES['file']['type'],
					'content_slug' => basename($filePath),
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
				$fields = array('TITLE' => basename($filePath));
			
				// Guardamos atributo
				if (!$this->attributes->saveIt($this->content->id, $fields)) {
					Flash::error('Error al guardar atributos');
					$error = true;
				}
			
			}
			
		}
		
		
		
		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');		
		
		
		
		die ( "({'jResult':'ok','jMessage':" . json_encode('Fichero subido correctamente') . "})" );
						
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
				if (preg_match("/(png|gif|jpg|jpeg)/i", $file->content_slug)) {					
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