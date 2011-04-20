<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues 
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 */

if (!isset($enabled_state)) $enabled_state = true;

require_once('includes/library.php');
require_once('includes/lang.php');

initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security();

if ($userlevel >= USER_LEVEL_LS) :  // list
	ob_start();

	if ($userlevel >= USER_LEVEL_AD) :  // admin
		if (isset($_REQUEST['userid'])) :
			$w_userid = intval($_REQUEST['userid']);
		else : 
			$w_userid = $userid;
		endif;
	
		?><form name="f" method="get"><?php P('SHOW_FOR_USER'); ?>
		<select name="userid" id="userid" onchange="document.forms['f'].submit()"><?php
		 
		print "<option ";
		if ($w_userid == 0) print " selected=\"selected\"";
		print "value=\"0\">(" . T('ANON_USERS') . ")</option>";
		
		$sql = "SELECT id, username, realname FROM $USERS_TABLE" .  
			" WHERE userlevel <= $userlevel" .
			" ORDER BY username";
		$result = $db->sql_query($sql);
		if ($result !== false) :
			while ($row = $db->sql_fetchrow($result)) :
				print "<option ";
				if ($row['id'] == $w_userid) print " selected=\"selected\"";
				print "value=\"" . $row['id'] . "\">" . $row['username'];
				if (!empty($row['realname'])) print " (" . $row['realname'] . ')';
				if ($row['id'] == $userid) print " " .  T('YOU'); 
				print "</option>";
			endwhile; 
			$db->sql_freeresult($result);
		endif;
		?></select></form><br /><?php
	endif; ?>
<table class="editlist"><tbody>
<?php
	$data = array(
		'userid' => isset($w_userid) ? $w_userid : $userid,
		'enabled' => $enabled_state,
		);
	$sql = "SELECT COUNT(*) AS cnt FROM $URLS_TABLE" .  
		" WHERE " . $db->sql_build_array('SELECT', $data);
	$result = $db->sql_query($sql);
	$rows = (int) $db->sql_fetchfield('cnt');
	$db->sql_freeresult($result);

	if ($rows == 0) :
		print "<tr class=\"single\"><td>" . T('NO_RECORDS_FOUND') . "</td></tr>";
	else :
		$sql = "SELECT * FROM $URLS_TABLE" .  
			" WHERE " . $db->sql_build_array('SELECT', $data) .
			" ORDER BY title";
		$result = $db->sql_query($sql);
		$run = 0;
		while ($row = $db->sql_fetchrow($result)) :
			$run++;

			$sql = "SELECT COUNT(*) AS cnt, MIN(accessedon) as firstaccess, MAX(accessedon) as lastaccess FROM $LOG_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array(
					'urlid' => $row['id'],
					));	
			$result2 = $db->sql_query($sql);
			$row2 = $db->sql_fetchrow($result2);

			print "<tr class=\"row_$run " . 
				(is_odd($run) ? "odd" : "even") . 
				($enabled_state ? '' : ' arch') .
				(($run == 1) ? ' first' : '') .
				(($run == $rows) ? ' last' : '') . 
				"\"><td>";
			if (boolval($row['log'])) :
				if (!$row2['firstaccess'] || !$row2['lastaccess']) :
					$stats = T('URL_LIST_DATA_NONE');
				else :
					$stats = T('URL_LIST_DATA_STATS', array(
						'datef' => date(T('#DATE_FORMAT'), $row2['firstaccess']),
						'datel' => date(T('#DATE_FORMAT'), $row2['lastaccess']),
						));
				endif;
			else :
				$stats = '';
			endif;
			P(($enabled_state ? 'URL_LIST_DATA' : 'URL_ARCH_DATA'), array(
				'id' => $row['id'],
				'count' => boolval($row['log']) ? $row2['cnt'] : T('NOT_LOGGED'),
				// 'title' => '<a href="info.php?id=' . $row['id'] . '">' . (!$row['title'] ? $row['longurl'] : $row['title']) . '</a>',
				'title' => (!$row['title'] ? $row['longurl'] : $row['title']),
				'icon' => get_fav_icon($row['longurl']),
				'iconarch' => '<img src="' . $page['base_path'] . 'images/ico_arch.png" width="16" height="16" />',
				'longurl' => $row['longurl'],
				'shorturl' => $row['shorturl'],
				'fullshorturl' => $page['full_path'] . $row['shorturl'],
				'date' => date(T('#DATE_FORMAT'), $row['createdon']),
				'stats' => $stats,
				));
			if ($userlevel >= USER_LEVEL_EL) :  // edit long 
				print '<br /><div class="editbuttons">';
				// Edit			
				if ($enabled_state)
					print "<button class=\"minibutton btn-edit\" onclick=\"window.location='edit.php?id=" . $row['id'] . "'\"><span class=\"icon\"></span>" . T('EDIT') . "</button>";
				// Archive	
				if ($userlevel >= USER_LEVEL_DS) :  // disable
					if ($enabled_state) :
						print "<button class=\"minibutton danger btn-arch\" onclick=\"window.location='delete.php?id=" . $row['id'] . "'\"><span class=\"icon\"></span>" . T('ARCHIVE') . "</button>";
					else :
						print "<button class=\"minibutton btn-rstr\" onclick=\"window.location='undelete.php?id=" . $row['id'] . "'\"><span class=\"icon\"></span>" . T('UNARCHIVE') . "</button>";
					endif;
				endif;
				// Anonimize
				if ((isset($w_userid)) && ($w_userid > 0) && ($userlevel >= USER_LEVEL_AD)) :  // admin
					print "<button class=\"minibutton danger btn-anon\" onclick=\"window.location='anonymous.php?id=" . $row['id'] . "'\"><span class=\"icon\"></span>" . T('ANONIMIZE') . "</button>";
				endif;
				print "</div>";
			endif;
			print "</td></tr>\n";

			$db->sql_freeresult($result2); 
		endwhile;
	endif;

	$db->sql_freeresult($result);
?>
</tbody></table>

<?php 
	$page['content'] = ob_get_clean();

	if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
	$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
	if ($userlevel >= USER_LEVEL_EL) : $page['head_suffix'] .= loadjs('includes/loadjs.php?f=minibutton.js'); endif;
else :
	access_denied();
endif;

$page['title'] = $enabled_state ? T('LIST') : T('ARCHIVES');
include('includes/' . TEMPLATE . '.php');
?>
