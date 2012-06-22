<?php
/*
Proyecto: InnoCMS 5.x
Creado por: Phillipo
Fecha: 23/07/2011 13:53:52
Fichero: HierticalSelect.php
*/

class contentSelect {

	private $value;
	private $lista;
	
	public function __construct($parent_id = 0, $value = 0, $key = '-') {
		$this->value = $value;
		$this->lista = self::draw($parent_id, $key);
		
	}
	
	// Generar el desplegable
	public function draw($id = 0, $key = '-') {
		
		$content = Load::model('content');
		$listado = $content->getContents('page',$id);
		
		$lista = '';
		foreach ($listado as $item) {
			$lista .= '<option value="'.$item->id.'"'.($this->value == $item->content_parent_id ? ' selected="selected"' : '' ).'>'.$key.' '.$item->definition_title.'</option>';
			$lista .= self::draw($item->id, $key.$key);
		}
		
		return $lista;
		
	}
	
	public function show($id = '',$name = '') {
		return '<select id="'.$id.'" name="'.$name.'">'.$this->lista.'</select>';
	}
		
}

?>	