<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');

	//Informations about this module
	function gomobile_info() {
		return array(
			'name'	=>	'GoMobile',
			'description'	=>	'Detekcja urządzeń mobilnych',
			'author'	=>	'<a href="http://matva.one.pl">MaTvA</a>',
			'version'	=>	'0.1',
			'add2nav'	=>	FALSE
		);
	}

	//Installation
	function gomobile_install() {
		global $db;
		$fields = array(array('name'=>'id','auto_increment'=>true),array('name'=>'field'),array('name'=>'value'));
		$tablename = 'gomobile';
		if (!$db->_table_exists('db', $tablename)){
		    if($db->create_table($tablename,$fields)){
		    	$newRecord = array(NULL,'phone','mobile.html');
		        $db->insert($tablename, $newRecord);
		    	$newRecord = array(NULL,'tablet','mobile.html');
		        $db->insert($tablename, $newRecord);
		    }
		}
	}

	//Uninstallation
	function gomobile_uninstall() {
		global $db;
		//Delete the table
		$db->drop_table('gomobile');
	}
	
?>
