<?php
/*
Author: Cesar Caballero Gallego
Date: 20/04/2012 - 11:25:06
File: tableData.php
*/


class tableData {
	
	private $model;
	private $data;
	
	
	public function __construct($model, $headers) {
		
		$this->model = $model;
		
	}	
	
	public function draw() {
		
		$hasChilds = false;
		
		$list = Load::model($this->model)->getList(0,0);
		
		if (sizeof($list) > 0 && !$hasChilds) {
			$key = '-';
		}
		
		foreach ($list as $item) {
			
			// Extraemos hijos del item
			$children = Load::model($this->model)->getList(0,0,array('parentId'=>$item_id,'order'=>'category_sort_order ASC, category_parent_id ASC'));
			
			// Comprobamos los hijos para repetir busqueda o no
			if (sizeof($children) > 0) {
				
			} else {
					
			}
			
			
		}
		
	}
	
	
	public function tr() {
		
		foreach ($this->fields as $field) {
			
		}
		
	}
	
	public function th() {
		
	}
	
	public function td() {
		
	}
	
	
	
	
}

?>