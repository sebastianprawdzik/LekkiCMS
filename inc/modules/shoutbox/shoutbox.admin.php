<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');

	//Load lang file of this module
	require('../'.LANG.'admin/shoutbox.php');

	//Pages of this module
	function shoutbox_pages() {
		global $lang;
		$pages[] = array(
			'func'  => 'posts',
			'title' => $lang['shoutbox']['page posts']
		);
		$pages[] = array(
			'func'  => 'clean',
			'title' => $lang['shoutbox']['clean shoutbox']
		);
		$pages[] = array(
			'func'  => 'settings',
			'title' => $lang['shoutbox']['page settings']
		);
		$pages[] = array(
			'func'  => 'check_updates',
			'title' => $lang['shoutbox']['updates']
		);
		return $pages;
	}

	//Your functions --------------------------------------
	function posts() {
		global $core, $db, $lang;
		$result = NULL;
		
		/*--- EDIT SECTION ---------------------*/
		if(isset($_GET['q']) && $_GET['q']=='view' && isset($_GET['id'])) {
			$condtion = array('id'=>$_GET['id']); 
            $query = $db->select('shoutbox_posts', $condtion);
            if($query) {
				$bbcode[0] = array(
					"#\[b\](.*?)\[/b\]#si",
					"#\[i\](.*?)\[/i\]#si",
					"#\[u\](.*?)\[/u\]#si",
					"#\[s\](.*?)\[/s\]#si",
					"#\[url=(.*?)\](.*?)\[/url\]#si"
				);
				
				$bbcode[1] = array(
					'<strong>\\1</strong>',
					'<i>\\1</i>',
					'<u>\\1</u>',
					'<s>\\1</s>',
					'<a href="\\1" target="_blank" rel="nofollow">\\2</a>'
				);
				
				$record = $query[0];
				
				$result .= '<div><h2>'.$lang['shoutbox']['author information'].'</h2>';
				$result .= '<div><strong>Nick:</strong> '.$record['author_name'].'</div>';
				$result .= '<div><strong>Adres IP:</strong> '.$record['author_ip'].'</div>';
				$result .= '<div><strong>User agent:</strong> '.$record['author_agent'].'</div></div>';
				
				$result .= '<div style="margin-top: 25px;"><h2>'.$lang['shoutbox']['posts information'].'</h2>';
				$result .= '<div><strong>'.$lang['shoutbox']['date added'].':</strong> '.$record['date_added'].'</div></div>';
				
				$result .= '<div style="margin-top: 25px;"><h2>'.$lang['shoutbox']['post content'].'</h2>';
				$result .= '<div style="padding: 10px;box-shadow: 0 0 5px #a0a0a0;">'.preg_replace($bbcode[0],$bbcode[1],$record['content']).'</div></div>';
			} else $result .= $lang['shoutbox']['post doesnt exist'];
		} else {
			/*--- DELETE SECTION -------------------*/
			if(isset($_GET['q']) && $_GET['q']=='del' && isset($_GET['id'])) {
				$condtion = array('id'=>$_GET['id']); 
           		$query = $db->select('shoutbox_posts', $condtion);
	            if($query) {
					if($db->delete('shoutbox_posts', $condtion)) $core->notify($lang['shoutbox']['post delete success'],1);
					else $core->notify($lang['shoutbox']['post delete fail'],2);
				} else $core->notify($lang['shoutbox']['post doesnt exist'],2);
			}
			$query = $db->select('shoutbox_posts');
			//Sort
			krsort($query);
			//Segmentation
			$result .= '<table><thead>';
			$result .= '<tr>
				<td>'.$lang['shoutbox']['post content'].'</td>
				<td>'.$lang['shoutbox']['date added'].'</td>
				<td>'.$lang['shoutbox']['author'].'</td>
				<td width="52px">'.$lang['shoutbox']['options'].'</td>
			</tr>';
			$result .= '</thead><tbody>';
			//Get the records
			krsort($query); //Sort
			foreach($query as $record) {
				$result .= '
				<tr>
					<td>'.substr($record['content'], 0, 55).'</td>
					<td>'.$record['date_added'].'</td>
					<td>'.$record['author_name'].'</td>
					<td>
						<a href="?go=shoutbox&q=view&id='.$record['id'].'" class="icon">f</a> 
						<a href="?go=shoutbox&q=del&id='.$record['id'].'" onclick="return confirm(\''.$lang['shoutbox']['delete confirm'].'\')" class="icon">l</a>
					</td>
				</tr>';
			}
			$result .= '</tbody></table>';
			//Display info about news
			$result .= '<span class="info">'.$lang['shoutbox']['info'].'</span>';
		}

		return $result;
	}
	
	function clean() {
		global $db, $lang;
		$result = null;
		
		if(!isset($_POST['continue'])) {
			$result .= '<form action="" method="post">';
			$result .= '<label><h2>Uwaga</h2></label><div class="radio">'.$lang['shoutbox']['clean information'].'</div>';
			$result .= '<button type="submit" name="continue">'.$lang['shoutbox']['continue'].'</button>';
			$result .= '</form>';
		} else {
			if($db->drop_table('shoutbox_posts')) {
				$tablename = 'shoutbox_posts';
				$fields = array(array('name'=>'id','auto_increment'=>true),array('name'=>'content'),array('name'=>'date_added'),array('name'=>'author_name'),array('name'=>'author_ip'),array('name'=>'author_agent'));
				if(!$db->_table_exists('db', $tablename)){
					if($db->create_table($tablename,$fields)) header('Location: index.php?go=shoutbox'); else $result .= $lang['shoutbox']['clean fail'].'3';
				} else $result .= $lang['shoutbox']['clean fail'].'2';
			} else $result .= $lang['shoutbox']['clean fail'].'1';
			
		}
		return $result;
	}

	/* Main function: settings_general() ****/
	function settings() {
		global $core, $db, $lang;
		$result = NULL;

		if(isset($_POST['save'])) {
			$error = 0;
			foreach($_POST as $key => $value) {
				if($key != 'save') {
					$query = $db->select('shoutbox_settings', array('field'=>$key));
					$record = $query[0];
					if(!$db->update('shoutbox_settings', array('field'=>$key), array($record['id'],$key,stripslashes($value)))) $error++;
				}
			}
			if($error) $core->notify($lang['shoutbox']['update fail'],2);
			else $core->notify($lang['shoutbox']['update success'],1);
		}

		//Get settings from DB
		if($query = $db->select('shoutbox_settings')) {
			foreach((array)$query as $record) $settings[$record['field']] = $record['value'];
			//Define all inputs
			$form['top'] = '<form name="settings" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			
			$form['nick_min_chars'] = '<label>'.$lang['shoutbox']['nick min chars'].'
				</label>
				<input type="text" name="nick_min_chars" value="'.$settings['nick_min_chars'].'" />';
				
			$form['nick_max_chars'] = '<label>'.$lang['shoutbox']['nick max chars'].'
				</label>
				<input type="text" name="nick_max_chars" value="'.$settings['nick_max_chars'].'" />';
				
			$form['post_max_chars'] = '<label>'.$lang['shoutbox']['post max chars'].'
				</label>
				<input type="text" name="post_max_chars" value="'.$settings['post_max_chars'].'" />';
				
			$form['posts_refresh_time'] = '<label>'.$lang['shoutbox']['posts reflash time'].'
				</label>
				<input type="text" name="posts_refresh_time" value="'.$settings['posts_refresh_time'].'" />';
				
			$form['posts_limit'] = '<label>'.$lang['shoutbox']['posts limit'].'
				</label>
				<input type="text" name="posts_limit" value="'.$settings['posts_limit'].'" />';

			$form['time_anti_flood'] = '<label>'.$lang['shoutbox']['time_anti_flood'].'
				</label>
				<input type="text" name="time_anti_flood" value="'.$settings['time_anti_flood'].'" />';
			
			$form['save'] = '<button type="submit" name="save" value="save">'.$lang['shoutbox']['save'].'</button>';
			
			$form['bottom'] = '</form>';
			
			//Return form
			foreach($form as $input) $result .= $input."\n";
			return $result;
		}
	}
	
	function check_updates() {
		global $lang;
		$check_load = file_get_contents("http://lekkicms.pl/update.php?module=shoutbox&ver=0.3");
		if($check_load == "0" || $check_load == "1") {
			if($check_load == "0") $result = $lang['shoutbox']['update no'];
			if($check_load == "1") $result = $lang['shoutbox']['update yes'];
		} else $result = $lang['shoutbox']['update not loaded'];
		return $result;
	}

?>
