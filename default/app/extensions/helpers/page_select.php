<?php
/*
Author: Cesar Caballero Gallego
Date: 19/04/2012 - 18:29:09
File: category_select.php
*/

class pageSelect  {

	private $items;
	private $iteract;
	
	public function __construct() {
		$this->iteract = 0;		
		$this->items[0] = '.Sin categoria';
	}
	
	/***
	 * PUBLIC METHODS
	*/	
	
	public function show($field, $checked = 0, $key = '.') {
		return Form::select($field,$this->parse(0,$key),$checked);
	}

	/***
	 * PRIVATE METHODS
	 */
	
	private function parse($parentId = 0, $key = '.') {
		
		// parametros
		$hasChilds = false;
		
		// Listado de elementos
		$listado = Load::model('page')->getList(array('parentId'=>$parentId,'order'=>'page_parent_id ASC, page_sort_order ASC'));
		
		//if (sizeof($listado > 0) && !$hasChilds && $this->iteract > 0) $key .= $key;
		
		// Recorremos items
		foreach ($listado as $item) {

			// Extraemos hijos del item
			$children = Load::model('page')->getList(array('parentId'=>$item->page_id,'order'=>'page_parent_id ASC, page_sort_order ASC'));
						
			if (sizeof($children) > 0) {
				$this->items[$item->page_id] = $key.$item->page_attribute_value;
				self::parse($item->page_id, $key.$key);
			} else {
				$this->items[$item->page_id] = $key.$item->page_attribute_value;
			}
			
		}
		
		$this->iteract++;
		
		return $this->items;
		
	}
	
		


}

?>