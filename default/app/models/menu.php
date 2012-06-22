<?php
/*
Proyecto: InnoCMS 5.x
Creado por: Phillipo
Fecha: 16/07/2011 13:19:54
Fichero: menu.php
*/

class Menu extends ActiveRecord {
	
	protected $debug = TRUE;
	protected $logger = TRUE;
	
	public function getMenus($parent_id = 0) {
		
		$sql = 'SELECT * FROM menu WHERE menu_parent_id = '.$parent_id.' ORDER BY menu_sort_order ASC';
		return $this->find_all_by_sql($sql);
		
	}
	
	
	
}


?>