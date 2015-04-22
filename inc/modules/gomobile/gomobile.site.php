<?php
	defined('IN_LCMS') or exit();
	require_once 'Mobile_Detect.php';
	$detect = new Mobile_Detect;
	if($detect->isMobile()) {
		if($q=$db->select('gomobile',array('field'=>'phone'))) {
			$core->pattern = $core->loadPattern($q[0]['value']);
		}
	}
	elseif($detect->isTablet()) {
		if($q=$db->select('gomobile',array('field'=>'tablet'))) {
			$core->pattern = $core->loadPattern($q[0]['value']);
		}
	}
?>