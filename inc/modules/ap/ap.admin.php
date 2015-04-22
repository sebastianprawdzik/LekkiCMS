<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');

	//Load lang file of this module
	//require('../'.LANG.'admin/gymprogress.php');

	//Pages of this module
	function ap_pages() {
		$pages[] = array(
			'func'  => 'ap_test',
			'title' => 'TEST'
		);

		return $pages;
	}

	//Your functions --------------------------------------
	function ap_test() {
		global $lang, $db, $core;

		$test = "PANEL ADMINISTRACYJNY PRZYBĘDZIE W WERSJI 0.2!";
		return $test;
	}



?>
