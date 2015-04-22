<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');

	//Informations about this module
	function ap_info() {
		return array(
			'name'	=>	'Register and Login',
			'description'	=>	'Rejestracja, Logowanie, Zarządzanie',
			'author'	=>	'Trybun',
			'version'	=>	'0.1',
			'add2nav'	=>	FALSE
		);
	}
        
        function ap_install() {
		global $db;
		$fields = array(array('name'=>'id','auto_increment'=>true),array('name'=>'name'),array('name'=>'password'),array('name'=>'email'),array('name'=>'regdate'),array('name'=>'from'),array('name'=>'website'),array('name'=>'range'),array('name'=>'lastlogoutdate'),array('name'=>'lastlogouttime'),array('name'=>'online'),array('name'=>'field'));
		$tablename = 'accounts';
		if (!$db->_table_exists('db', $tablename)){
		    $db->create_table($tablename,$fields);
                  
                  

        }
        }
	
?>
