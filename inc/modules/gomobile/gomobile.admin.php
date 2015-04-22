<?php
	defined('IN_LCMS') or exit();
	function gomobile_pages() {
		$p[] = array(
			'func'  => 'gomobile',
			'title' => 'GoMobile'
		);
		return $p;
	}
	function gomobile() {
		global $db, $core;
		if(isset($_POST['submit'])) {
			if(!empty($_POST['phone']) && !empty($_POST['tablet'])) {
				$db->update('gomobile',array('field'=>'phone'),array(1,'phone',$_POST['phone']));
				$db->update('gomobile',array('field'=>'tablet'),array(2,'tablet',$_POST['tablet']));
				$core->notify('Ustawienia zostały zapisane',1);
			} else $core->notify('Ustawienia nie zostały zapisane',2);
		}
		if($q=$db->select('gomobile',array('field'=>'phone'))) {
			$phone=$q[0]['value'];
		}
		if($q=$db->select('gomobile',array('field'=>'tablet'))) {
			$tablet=$q[0]['value'];
		}
		return '<form method="post" action="">
		<label>Szablon dla telefonów</label>
		<select name="phone">'.getTPL('<option value="{{value}}" {{selected}}>{{file}}</option>', $phone).'</select>
		<label>Szablon dla tabletów</label>
		<select name="tablet">'.getTPL('<option value="{{value}}" {{selected}}>{{file}}</option>', $tablet).'</select>
		<button type="submit" name="submit">Zapisz</button></form>';
	}
	function getTPL($pattern, $prefTpl = NULL) {
		global $core;
		$result = NULL;
		$dir = '../'.THEMES.$core->getSettings('theme');
		
		if($array = glob($dir.'/*.html')) {
			foreach($array as $file) {
				$fileInfo = pathinfo($file);
				$replaced = str_replace('{{file}}', $fileInfo['basename'], $pattern);
				$replaced = str_replace('{{value}}', $fileInfo['basename'], $replaced);
				if(!empty($prefTpl) && $prefTpl==$fileInfo['basename']) $replaced = str_replace('{{selected}}', 'selected', $replaced);
				else $replaced = str_replace('{{selected}}', '', $replaced);
				$result .= $replaced;
			}
		}
		return $result;
	}

?>
