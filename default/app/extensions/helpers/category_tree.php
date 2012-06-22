<?php
/*
Author: Cesar Caballero Gallego
Date: 18/04/2012 - 20:40:10
File: category_tree.php
*/

class categoryTree {

	private $value;
	private $lista;
	private $type;
	private $properties;
	
	public function __construct($props = null) {	
		$this->_getProperties($props);		
	}
	
	// Crear el arbol
	private function _draw($id = 0, $checkedId = 0) {

		// Parametros
		$hasChilds = false;
		$lista = '';
		$n = 0;
		$tab = "\t";
		
		// Listado de hijos
		$listado = Load::model('content')->getList(array('parentId'=>$id,'content_type'=>'category','order'=>'content_attribute_value'));
				
		if (!$this->properties['nested']) {
			$classNested = '';
		} else {
			$classNested = 'nested unstyled';
		}
		
		// Comprobamos hijos
		if (sizeof($listado) > 0 && !$hasChilds) {
			$lista .= $tab.'<ul class="'.$classNested.'">'.EOL;
			$hasChilds = true;
		}
		
		// Recorremos listado
		foreach ($listado as $item) {
		
			// Extraemos hijos del item
			$children = Load::model('content')->getList(array('parentId'=>$item->id,'content_type'=>'category','order'=>'content_attribute_value'));
		
			// Tipo de nodo
			$lastClass = ($n == (sizeof($listado)-1)) ? ' class="isLast"' : ' class="isNode"';
			
			// Seleccionado
			if ($checkedId == $item->id) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			
			// Input fields
			if ($this->properties['radio'] == true) $input = '<label for="itemCatList_'.$item->id.'" class="radio inline">'.$item->content_attribute_value.'<input type="radio" name="content[content_parent_id]" id="itemCatList_'.$item->id.'" value="'.$item->id.'"'.$checked.'/></label>';
			else if ($this->properties['checkbox'] == true) $input = '<label for="itemCatList_'.$item->id.'" class="checkbox inline">'.$item->content_attribute_value.'<input type="checkbox" name="content[content_parent_id]" id="itemCatList_'.$item->id.'" value="'.$item->id.'"'.$checked.' /></label>';
			else $input = $item->content_attribute_value;
		
			// Si hay hijos hacemos recursion
			if (sizeof($children) > 0) {
				$lista .= '<li'.$lastClass.'>'.$input.''.EOL;
				$lista .= self::_draw($item->id, $checkedId).EOL;
				$lista .= '</li>'.EOL;
			} else {
				$lista .= '<li'.$lastClass.'>'.$input.'</li>'.EOL;
			}
		
			$n++;
		
		}
		
		// Cerramos si no hay más hijos
		if ($hasChilds)	$lista .= '</ul>'.EOL;
		
		// Devolvemos lista
		return $lista;
		
		
		
		
	}
	

	// Muestra el resultado de un arbol segun parametros
	public function show($id = 0, $checked = 0) {

		// Draw
		return self::_draw($id,$checked);
			
	}
	
	
	//**************************** PRIVATE METHODS */
	
	// Metodo para parsear datos
	private function _getProperties($prop) {
		
		$options = array('nested','collapse','radio','checkbox');
		
		if (isset($prop['options'])){ 
		
			$userOptions = strtolower($prop['options']);
			$userOptions = explode(',', $userOptions);
			
			foreach ($options as $opt) {
				if (in_array($opt,$userOptions)) $this->properties[$opt] = true;
				else $this->properties[$opt] = false;
			}
			
		}
		
	}
	
	
		
}

?>	