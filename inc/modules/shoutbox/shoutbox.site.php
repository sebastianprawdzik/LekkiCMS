<?php

	//Make sure the file isn't accessed directly
	defined('IN_LCMS') or exit('Access denied!');
	
	//Load lang file of this module
	require(LANG.'shoutbox.php');
	
	//Replace pattern by function
	$core->replace('{{shoutbox}}', shoutbox());

	//Your functions --------------------------------------
	function shoutbox() {
		global $core, $db, $lang;
		$result = null;
		if($query = $db->select('shoutbox_settings')) foreach((array)$query as $record) $settings[$record['field']] = $record['value'];
		$core->append('
	<script src="http://malsup.github.com/jquery.form.js"></script>'."
	<script type='text/javascript'>
		$(document).ready(function() { 
			$('#sb_send_form').ajaxForm(function() {
				var cont = $('#sb_cont').val();
				$.ajax({
					url: 'sb_send.php',
					type: 'POST',
					data: 'sb_cont='+cont,
					success: function(msg) {
						$('#sb_cont').attr('placeholder', msg);
					}
				});
				$('#sb_cont').val('');
			});
		});
	</script>", 'head');
		$result .= '<div class="sb_box">';
		if(isset($_SESSION['sb_'.$_SERVER['REMOTE_ADDR']])) {
			$result .= '<form action="" method="post" id="sb_send_form">';
			$result .= '<div id="sb_form">';
			$result .= '<input type="text" id="sb_cont" value="" placeholder="'.$lang['shoutbox']['enter message'].'" pattern=".{1,'.$settings['post_max_chars'].'}" autocomplete="off" required>';
			$result .= '<button type="submit" id="sb_send">»</button>';
			$result .= "
			<div id='sb_bbcode'>
				<a onclick=\"sb_bbcode('[b]','[/b]')\">B</a>
				<a onclick=\"sb_bbcode('[i]','[/i]')\">I</a>
				<a onclick=\"sb_bbcode('[u]','[/u]')\">U</a>
				<a onclick=\"sb_bbcode('[s]','[/s]')\">S</a>
				<a onclick=\"sb_bbcode('[url=http://]','[/url]')\">url</a>
			</div>";
			$result .= '</div></form>';
		} else {
			if(isset($_POST['sb_login'])) {
				if(!empty($_POST['sb_nick']) && mb_strlen($_POST['sb_nick']) >= $settings['nick_min_chars'] && mb_strlen($_POST['sb_nick']) <= $settings['nick_max_chars']) {
					if($_SESSION['sb_'.$_SERVER['REMOTE_ADDR']] = array('nick'=>htmlspecialchars($_POST['sb_nick']))) {
						$newRecord = array(NULL, $lang['shoutbox']['login new user'], date('Y-m-d H:i:s'), $_SESSION['sb_'.$_SERVER['REMOTE_ADDR']]['nick'], '127.0.0.1', ' ');
						if($db->insert('shoutbox_posts', $newRecord)) header('refresh: 0;');
					}
				}
			}
			$result .= '<div id="sb_form"><form action="" method="post">';
			$result .= '<input type="text" name="sb_nick" id="sb_nick" value="" placeholder="'.$lang['shoutbox']['enter nick'].'" pattern=".{'.$settings['nick_min_chars'].','.$settings['nick_max_chars'].'}" autocomplete="off" required>';
			$result .= '<button type="submit" name="sb_login" id="sb_login">»</button>';
			$result .= '</form></div>';
		}
		$result .= '<div id="sb_load">';
		if($query = $db->select('shoutbox_posts')) {
			$i = 0;
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
			krsort($query);
			foreach($query as $record) {
				if($posts < $settings['posts_limit']) 
				$result .= '
					<div class="sb_post">
						<div class="sb_post_top">
							<span class="sb_post_nick">'.$record['author_name'].'</span>
							<span class="sb_post_date">'.$record['date_added'].'</span>
						</div>
						<div class="sb_post_cont">'.preg_replace($bbcode[0],$bbcode[1],$record['content']).'</div>
					</div>';
				$posts++;
			}
		} else $result .= '<div class="sb_post">'.$lang['shoutbox']['empty shoutbox'].'</div>';
		$result .= '</div></div>';
		$reftime = $settings['posts_refresh_time']*1000;
		$result .= '
			<script type="text/javascript">
				$("#sb_form").click(function(){$("#sb_bbcode").show(335);});
				setInterval(function() {
					$.get("sb_load.php", function(data) {
						$("#sb_load").html(data);
					});
				}, '.$reftime.');
				
				function sb_bbcode(tag_start,tag_end){
					var okno = document.getElementById("sb_cont");
					if(!okno.setSelectionRange) {
						var selected = document.selection.createRange().text; 
						if(selected.length <= 0) {
							okno.value +=tag_start + tag_end;
						} else {
							document.selection.createRange().text = tag_start + selected + tag_end;
						}
					} else {
						var pretext = okno.value.substring(0, okno.selectionStart);
						var codetext = tag_start + okno.value.substring(okno.selectionStart,
						okno.selectionEnd) + tag_end;
						var posttext = okno.value.substring(okno.selectionEnd, okno.value.length)
						if(codetext == tag_start + tag_end) {
							okno.value +=tag_start + tag_end;
						} else {
							okno.value = pretext + codetext + posttext;
						}
					}
					okno.focus();
					var wartosc = jQuery("#sb_cont").val();
					jQuery("#sb_cont").val().focus().val(wartosc);
				}
			</script>';
		return $result;
	}

?>
