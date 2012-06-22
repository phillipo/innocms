<?php
/*
Author: Cesar Caballero Gallego
Date: 18/04/2012 - 20:40:10
File: page_tree.php
*/

class pageTree {

	private $value;
	private $lista;
	private $type;
	private $properties;
	private $iteract;
	
	public function __construct() {	
		$this->iteract = 0;
	}
	
	// Crear el arbol
	private function _draw($id = 0) {

		// Parametros
		$hasChilds = false;
		$lista = '';
		$tab = "\t";
				
		// Listado de hijos
		$listado = Load::model('page')->getList(array('parentId'=>$id,'order'=>'page_sort_order ASC'));
		
		// Recorremos listado
		foreach ($listado as $item) {
		
			// Extraemos hijos del item
			$children = Load::model('page')->getList(array('parentId'=>$item->page_id,'order'=>'page_attribute_value'));
					
			// Input fields
			$input = '<div>Pagina Indice '.Html::linkAction('edit/'.$item->page_id.'/'.Innocms::refererEncode(), $item->page_attribute_value).'&nbsp;&nbsp;&nbsp;'.Html::linkAction('remove/'.$item->page_id, '<i class="icon-trash">&nbsp;</i>','class="ui-removeIt"').'</div>';
			
			// Si hay hijos hacemos recursion
			if (sizeof($children) > 0) {
				$lista .= '<li id="item-'.$item->page_id.'" class="expanded">'.$input.''.EOL;
				$lista .= '<ol>';
				$lista .= self::_draw($item->page_id).EOL;
				$lista .= '</ol>';
				$lista .= '</li>'.EOL;
			} else {
				$lista .= '<li id="item-'.$item->page_id.'" class="isPage">'.$input.'</li>'.EOL;
			}
		
		}
		
		// Devolvemos lista
		return $lista;		
		
	}
	

	// Muestra el resultado de un arbol segun parametros
	public function show($id = 0) {

		// Draw
		return '<ol class="iTree">'.self::_draw($id).'</ul>';
			
	}
	
	
	//**************************** PRIVATE METHODS */
		

	
	
	
	
	
	
	
	
	
	
	
	
	// Crear el arbol
	private function _drawOld($id = 0) {
	
		// Parametros
		$hasChilds = false;
		$lista = '';
		$n = 0;
		$tab = "\t";
	
		// Listado de hijos
		$listado = Load::model('page')->getList(array('parentId'=>$id,'order'=>'page_sort_order ASC'));
	
		// Comprobamos hijos
		if (sizeof($listado) > 0 && !$hasChilds) {
			$lista .= $tab.'<ul class="unstyled">'.EOL;
			$hasChilds = true;
		}
	
		// Recorremos listado
		foreach ($listado as $item) {
	
			// Extraemos hijos del item
			$children = Load::model('page')->getList(array('parentId'=>$item->page_id,'order'=>'page_attribute_value'));
	
			// Input fields
			$input = '<div>'.Html::linkAction('edit/'.$item->page_id.'/'.Innocms::refererEncode(), $item->page_attribute_value).'</div>'; //.' '.Html::linkAction('remove/'.$item->page_id, '<i class="icon-remove">&nbsp;</i>','class="ui-removeIt btn btn-mini"');
	
			// Si hay hijos hacemos recursion
			if (sizeof($children) > 0) {
				$lista .= '<li id="item-'.$item->page_id.'" class="isFolder">'.$input.''.EOL;
				$lista .= self::_draw($item->page_id).EOL;
				$lista .= '</li>'.EOL;
			} else {
				$lista .= '<li id="item-'.$item->page_id.'" class="isPage">'.$input.'</li>'.EOL;
			}
	
			$n++;
	
		}
	
		// Cerramos si no hay más hijos
		if ($hasChilds)	$lista .= '</ul>'.EOL;
	
		// Devolvemos lista
		return $lista;
	
	}
	
	
	
	
}

?>	