<?php

/** 
 * @author Phillipo
 * 
 * 
 */
class preferences extends ActiveRecord {
	
	protected $debug = FALSE;
	protected $logger = FALSE;
	
	public function getPreference($userId = 0, $siteId = 0) {
		
		if (empty($siteId) || empty($userId)) {
			$userId = 8;
			$siteId = 1;
		}
		
		return $this->find_by_sql('SELECT * FROM preferences WHERE user_id = "'.$userId.'" AND site_id = "'.$siteId.'" LIMIT 1');
		
	}
	
	public function saveIt($data) {
		
		$pref = $this->getPreference('1');
		$pref->save($data);
		
	}
	
	
}

?>