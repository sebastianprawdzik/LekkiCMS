<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');

	//Informations about this module
	function shoutbox_info() {
		return array(
			'name'	=>	'Shoutbox',
			'description'	=>	'Lekki czat na stronę',
			'author'	=>	'MaTvA',
			'version'	=>	'0.3',
			'add2nav'	=>	TRUE
		);
	}
	
	//Installation
	function shoutbox_install() {
		global $db;
		$tablename = 'shoutbox_posts';
		$fields = array(array('name'=>'id','auto_increment'=>true),array('name'=>'content'),array('name'=>'date_added'),array('name'=>'author_name'),array('name'=>'author_ip'),array('name'=>'author_agent'));
		if (!$db->_table_exists('db', $tablename)){
		    $db->create_table($tablename,$fields);
		}
		$tablename = 'shoutbox_settings';
		$fields = array(array('name'=>'id','auto_increment'=>true),array('name'=>'field'),array('name'=>'value'));
		if (!$db->_table_exists('db', $tablename)){
		    if($db->create_table($tablename,$fields)){
		        $newRecord = array(NULL,'nick_min_chars','3');
		        $db->insert($tablename, $newRecord);
		        $newRecord = array(NULL,'nick_max_chars','35');
		        $db->insert($tablename, $newRecord);
		        $newRecord = array(NULL,'post_max_chars','400');
		        $db->insert($tablename, $newRecord);
		        $newRecord = array(NULL,'posts_refresh_time','1,5');
		        $db->insert($tablename, $newRecord);
		        $newRecord = array(NULL,'posts_limit','10');
		        $db->insert($tablename, $newRecord);
		        $newRecord = array(NULL,'time_anti_flood','2');
		        $db->insert($tablename, $newRecord);
		    }
		}
	}
	
	//Uninstallation
	function shoutbox_uninstall() {
		global $db;
		$db->drop_table('shoutbox_posts');
		$db->drop_table('shoutbox_settings');
	}

?>
