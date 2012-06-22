<?php
/*
Author: Cesar Caballero Gallego
Date: 19/04/2012 - 18:29:09
File: category_select.php
*/

class categorySelect  {

	private $categorySelect;
	private $iteract;
	
	public function __construct() {
		$this->iteract = 0;		
		$this->categorySelect[0] = 'Sin categoria';
	}
	
	public function draw($parentId, $key = "-") {
		
		// parametros
		$hasChilds = false;
		
		// Listado de elementos
		$listado = Load::model('content')->getList(array('parentId'=>$parentId,'content_type'=>'category','order'=>'content_sort_order ASC, content_parent_id ASC'));
		
 		if (sizeof($listado > 0) && !$hasChilds) $key = $key.$key;
		
		// Recorremos items
		foreach ($listado as $item) {

			// Extraemos hijos del item
			$children = Load::model('content')->getList(array('parentId'=>$item->id,'content_type'=>'category','order'=>'content_sort_order ASC, content_parent_id ASC'));
						
			if (sizeof($children) > 0) {
				$this->categorySelect[$item->id] = $key.$item->content_attribute_value;
				self::draw($item->id, $key);
			} else {
				$this->categorySelect[$item->id] = $key.$item->content_attribute_value;
			}
			
		}
		
		$this->iteract = 1;
		
		return $this->categorySelect;
		
	}
	
	public function show($id,$key) {
		return $this->draw($id,$key);	
	}

}

?>