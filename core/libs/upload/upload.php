<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Sube archivos al servidor
 *
 * @category   Kumbia
 * @package    Upload
 * @copyright  Copyright (c) 2005-2010 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
abstract class Upload
{
	/**
	 * Nombre de archivo subido por metodo POST
	 * 
	 * @var string
	 */
	protected $_name;

	/**
	 * Permitir subir archivos de scripts ejecutables
	 * 
	 * @var boolean
	 */
	protected $_allowScripts = FALSE;
	
	/**
	 * Tamaño minimo del archivo
	 * 
	 * @var string
	 */
	protected $_minSize = NULL;

	/**
	 * Tamaño maximo del archivo
	 * 
	 * @var string
	 */
	protected $_maxSize = NULL;

	/**
	 * Tipos de archivo permitidos utilizando mime
	 * 
	 * @var array
	 */
	protected $_types = NULL;
	
	/**
	 * Extension de archivo permitida
	 * 
	 * @var array
	 */
	protected $_extensions = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param string $name nombre de archivo por metodo POST
	 */ 
	public function __construct($name)
	{
		$this->_name = $name;
	}
	
	/**
	 * Indica si se permitirá guardar archivos de scripts ejecutables
	 * 
	 * @param boolean $value
	 */
	public function setAllowScripts($value)
	{
		$this->_allowScripts = $value;
	}
	
	/**
	 * Asigna el tamaño mínimo permitido para el archivo
	 * 
	 * @param string $size
	 */
	public function setMinSize($size)
	{
		$this->_minSize = $size;
	}

	/**
	 * Asigna el tamaño máximo permitido para el archivo
	 * 
	 * @param string $size
	 */
	public function setMaxSize($size)
	{
		$this->_maxSize = $size;
	}
	
	/**
	 * Asigna los tipos de archivos permitido (mime)
	 * 
	 * @param array $value lista de tipos de archivos permitidos (mime) 
	 */
	public function setTypes($value)
	{
		$this->_types = $value;
	}
	
	/**
	 * Asigna las extensiones de archivos permitidas
	 * 
	 * @param array $value lista de extensiones para archivos
	 */
	public function setExtensions($value)
	{
		$this->_extensions = $value;
	}
	
	/**
	 * Acciones antes de guardar
	 * 
	 * @param string $name nombre con el que se va a guardar el archivo
	 * @return boolean
	 */
	protected function _beforeSave($name) 
	{}

	/**
	 * Acciones despues de guardar
	 * 
	 * @param string $name nombre con el que se guardo el archivo
	 */
	protected function _afterSave($name) 
	{}
	
	/**
	 * Guarda el archivo subido
	 *
	 * @param string $name nombre con el que se guardara el archivo
	 * @return boolean
	 */
	public function save($name = NULL)
    {
		if($this->isUploaded()) {	
			
			if(!$name) {
				$name = $_FILES[$this->_name]['name'];
			}

			// Guarda el archivo
			if($this->_beforeSave($name) !== FALSE && $this->_validates() && $this->_saveFile($name)) {
				$this->_afterSave($name);
				return TRUE;
			}
			
		}
		
		return FALSE;
	}
	
	/**
	 * Guarda el archivo con un nombre saneado
	 *
	 * @return string | boolean nombre de archivo generado o FALSE si falla
	 */
	public function saveSlug()
	{
		// Genera el nombre de archivo
		$name = $this->_toSlug($_FILES[$this->_name]['name']);
		
		if (file_exists($this->_path."/".$name)) {
			$tmp_name = explode('.',$name);
			$basename = strrev(strchr(strrev($name),'.') );
			$name = $basename.md5(time()).'.'.end($tmp_name);
		}
	
		// Guarda el archivo
		if($this->save($name)) {
			return $name;
		}
	
		return FALSE;
	}
	
	
	/**
	 * Guarda el archivo con un nombre aleatorio
	 * 
	 * @return string | boolean nombre de archivo generado o FALSE si falla
	 */
	public function saveRandom()
	{
		// Genera el nombre de archivo
// 		$name = md5(time());
		
		// Genera el nombre de archivo
		$tmp_name = explode('.',$_FILES[$this->_name]['name']);
		$name = md5(time()).'.'.end($tmp_name);		
		
		// Guarda el archivo
		if($this->save($name)) {
			return $name;
		}
		
		return FALSE;
	}
	
	/**
	 * Verifica si el archivo esta subido en el servidor y listo para guardarse
	 * 
	 * @return boolean
	 */
	public function isUploaded()
	{
		return isset($_FILES[$this->_name]) && is_uploaded_file($_FILES[$this->_name]['tmp_name']);
	}
	
	/**
	 * Valida el archivo antes de guardar
	 * 
	 * @return boolean
	 */
	protected function _validates()
	{
		// Verifica si ha ocurrido un error al subir
        if ($_FILES[$this->_name]['error'] > 0) {
			$error = array(
				UPLOAD_ERR_INI_SIZE => 'el archivo excede el tamaño máximo permitido por el servidor',
				UPLOAD_ERR_FORM_SIZE => 'el archivo excede el tamaño máximo permitido',
				UPLOAD_ERR_PARTIAL => 'se ha subido el archivo parcialmente',
				UPLOAD_ERR_NO_FILE => 'no se ha subido ningún archivo',     
				UPLOAD_ERR_NO_TMP_DIR => 'no se encuentra el directorio de archivos temporales',
				UPLOAD_ERR_CANT_WRITE => 'falló al escribir el archivo en disco',
				UPLOAD_ERR_EXTENSION => 'una extensión de php ha detenido la subida del archivo'
			);
			
            Flash::error('Error: ' . $error[$_FILES[$this->_name]['error']]);
            return FALSE;
        }
		
		// Denegar subir archivos de scripts ejecutables
		if(!$this->_allowScripts && preg_match('/\.(php|phtml|php3|php4|js|shtml|pl|py|rb|rhtml)$/i', $_FILES[$this->_name]['name'])) {
			Flash::error('Error: no esta permitido subir scripts ejecutables');
			return FALSE;
		}
		
		// Valida el tipo de archivo
		if($this->_types !== NULL && !$this->_validatesTypes()) {
			Flash::error('Error: el tipo de archivo no es valido');
			return FALSE;
		}
		
		// Valida extension del archivo
		if($this->_extensions !== NULL && !preg_match('/\.(' . implode('|', $this->_extensions) . ')$/i', $_FILES[$this->_name]['name'])) {
			Flash::error('Error: la extensión del archivo no es valida');
            return FALSE;
		}
		
		// Verifica si es superior al tamaño indicado
        if($this->_maxSize !== NULL && $_FILES[$this->_name]['size'] > $this->_toBytes($this->_maxSize)) {
            Flash::error("Error: no se admiten archivos superiores a $this->_maxSize");
            return FALSE;
        }
		
		// Verifica si es inferior al tamaño indicado
        if($this->_minSize !== NULL && $_FILES[$this->_name]['size'] < $this->_toBytes($this->_minSize)) {
            Flash::error("Error: no se admiten archivos inferiores a $this->_minSize");
            return FALSE;
        }
		
		return TRUE;
	}
	
	/**
	 * Valida que el tipo de archivo
	 * 
	 * @return boolean
	 */
	protected function _validatesTypes()
	{
		return in_array($_FILES[$this->_name]['type'], $this->_types);
	}
	
	/**
	 * Convierte de tamaño legible por humanos a bytes
	 * 
	 * @param string $size
	 * @return int
	 */
	protected function _toBytes($size)
	{
		if(preg_match('/([KMGTP]?B)/', $size, $matches)) {
			$bytes_array = array(
				'B' => 1,
				'KB' => 1024,
				'MB' => 1024 * 1024,
				'GB' => 1024 * 1024 * 1024,
				'TB' => 1024 * 1024 * 1024 * 1024,
				'PB' => 1024 * 1024 * 1024 * 1024 * 1024
			);

			$size = floatval($size) * $bytes_array[$matches[1]];
		}
		
		return intval(round($size, 2));
	}
	
	/**
	 * Convierte de el nombre de un archivo en un nombre normalizado y internacional
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function _toSlug($filename)
	{
		
// 		function replace_accents($var){ //replace for accents catalan spanish and more
// 			$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
// 			$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
// 			$var= str_replace($a, $b,$var);
// 			return $var;
// 		}		
		
		// Pasamos a camelcase 
		$filename = ucwords(mb_strtolower($filename,'UTF-8'));
		
		// Rompemos la cadena en nombre y extensión
		$tmp = explode(".", $filename);
		$ext = end($tmp);
		$name = basename($filename,$ext);
		
		// Quitamos espacios
		$name = preg_replace('/\s+/', '_', $name);	
		// Converts CamelCase	
		return iconv('UTF-8', 'ASCII//TRANSLIT', $name.$ext);
		
// 		return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
		
	}	
	
	/**
	 * Guardar el archivo en el servidor
	 * 
	 * @param string $name nombre con el que se guardará el archivo
	 * @return boolean
	 */
	protected abstract function _saveFile($name);
	
	/**
	 * Obtiene el adaptador para Upload
	 *
	 * @param string $name nombre de archivo recibido por POST
	 * @param string $adapter (file, image, model)
	 * @return Upload
	 */
	public static function factory($name, $adapter = 'file')
	{
		require_once CORE_PATH . "libs/upload/adapters/{$adapter}_upload.php";
		$class = $adapter.'upload';
		
		return new $class($name);
	}
	
}
