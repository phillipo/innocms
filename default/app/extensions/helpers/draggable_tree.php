<?php
/*
Proyecto: InnoCMS 5.x
Creado por: Phillipo
Fecha: 23/07/2011 13:53:52
Fichero: HierticalTree.php
*/

class draggableTree {

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
		
		$lista = "\t";
		if (sizeof($listado) > 0)  {
			$lista .= '<li>'.chr(10).'<ul>'.chr(10);
			foreach ($listado as $item) {
				$lista .= "\t".'<li id="ui-page-'.$item->id.'"><span>'.$item->definition_title.'</span></li>'.chr(10);
				$lista .= self::draw($item->id, $key.$key);
			}
			$lista .= '</ul>'.chr(10).'</li>'.chr(10);
		}
		
		return $lista;
		
	}
	
	public function show($id = '',$name = '') {
		return '<ul>'.chr(10).$this->lista.'</ul>'.chr(10);
	}
		
}

?>	