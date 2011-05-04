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

require_once('includes/library.php');
require_once('includes/lang.' . $phpEx);
initialize_settings();
initialize_db(TRUE);
initialize_lang();
initialize_security($settings['mustlogin']);

/* -------------------- Functions -------------------- */

function get_new_apikey() {
	global $phpEx;
	require_once('includes/uuid.' . $phpEx);
	return str_replace('-', '', get_uuid());
}

function load_file_based_settings(&$settings, &$setstorage) {
	/* Abstracted this out to a function so that loading config.php does not
	 * impact on GLOBAL $settings
	 */
	global $phpEx;

	include('includes/config-default.php');
	include('config.php');

	foreach ($settings as $name => $value) :
		$setstorage[$name] = -1;
	endforeach;
}

function load_db_based_settings($userid, &$settings, &$setstorage) {
	global $db, $sql, $SETTINGS_TABLE;
	$sql = 	"SELECT name, value FROM $SETTINGS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('userid' => $userid));
	$result = $db->sql_query($sql);
	if ($result) :
		while ($row = $db->sql_fetchrow($result)) :
			$settings[$row['name']] = $row['value'];
			$setstorage[$row['name']] = $userid;
		endwhile;
		$db->sql_freeresult($result);
	endif;
}

/* -------------------- Code begins here -------------------- */

if ($userlevel < USER_LEVEL_AD) :
	access_denied();
	poke_info(T('NOT_ADMIN'));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die(401);
endif;

// $settings_array
$fn = 'lang/' . $lang . '_adminset.' . $phpEx;
if (file_exists($fn)) :
	require_once($fn);
else :
	require_once('includes/adminset.' . $phpEx);
endif;

if (isset($_REQUEST['userid'])) :
	$w_userid = intval($_REQUEST['userid']);
else :
	$w_userid = 0;  // all users is the default
endif;

// parse real settings for unknowns
$unknowns = array();
foreach ($settings as $set_name => $value) :
	if (!isset($settings_array[$set_name]))
		$unknowns[$set_name] = $value;
endforeach;
if (count($unknowns) > 0) :
	ksort($unknowns, SORT_STRING);
	$settings_array[T('OTHER_SETTINGS')] = array(
		AS_TYPE => AS_T_SECTION,
		AS_NOT_FOR_USER => TRUE,
		);
	foreach ($unknowns as $set_name => $value) :
		$settings_array[$set_name] = array(
			AS_TYPE => AS_T_CONST,
			AS_NOT_FOR_USER => TRUE,
			);
	endforeach;
endif;

$settings2 = array();  // working version of settings
$set_store = array();  // where the settings are saved
                       // -2=not_set, -1=config.php*, 0=db_user_0, +x=db_user_id

load_file_based_settings($settings2, $set_store);

foreach ($settings_array as $set_name => $info) :
	// fake out @lang elements for each setting used in output_field()
	if (($info[AS_TYPE] != AS_T_SECTION) && ($info[AS_TYPE] != AS_T_SUBSECTION))
		$lang['FIELD_V_' . strtoupper($set_name)] = $set_name;

	if (!isset($settings2[$set_name])) :
		$settings2[$set_name] = FALSE;
		$set_store[$set_name] = -2;
	endif;
endforeach;

load_db_based_settings(0, $settings2, $set_store);
if ($w_userid > 0) load_db_based_settings($w_userid, $settings2, $set_store);

/* ---------- Post code ---------- */

if (isset($_REQUEST['f']) && ($_REQUEST['f'] == 'post')) :
	$delete_set = array();
	$update_set = array();
	$insert_set = array();
	foreach ($_REQUEST as $name => $value) :
		// deletes
		if (preg_match('/^d_/', $name) && (strcasecmp($value, 'DELETE') === 0))
			$delete_set[] = substr($name, 2);  // just insert, don't need value

		// edits
		if (preg_match('/^v_/', $name)) :
			$name = substr($name, 2);
			// only if they've changed
			if (!isset($_REQUEST['d_' . $name]) && ($settings2[$name] != $value)) :
				// update if in users set or insert
				if ($set_store[$name] == $w_userid) :
					$update_set[$name] = $value;
				else :
					$insert_set[$name] = $value;
				endif;
			endif;
		endif;
	endforeach;

	$run = 0;
	$db->sql_transaction('begin');

	// delete set first
	foreach ($delete_set as $name) :
		$sql = "DELETE FROM $SETTINGS_TABLE" .
			" WHERE " . $db->sql_build_array('SELECT', array(
				'userid' => $w_userid,
				'name' => $name,
				));
		$db->sql_query($sql);
		$run++;
		poke_info(T('SETTING_DELETED', array('name' => $name)), TRUE);
	endforeach;

	// then updates
	foreach ($update_set as $name => $value) :
		$sql = "UPDATE $SETTINGS_TABLE SET " .
			$db->sql_build_array('UPDATE', array('value' => $value)) .
			" WHERE " . $db->sql_build_array('SELECT', array(
				'userid' => $w_userid,
				'name' => $name,
				));
		$db->sql_query($sql);
		$run++;
		poke_info(T('SETTING_UPDATED', array('name' => $name, 'value' => $value)), TRUE);
	endforeach;

	// finally inserts
	foreach ($insert_set as $name => $value) :
		$sql = "INSERT INTO $SETTINGS_TABLE " .
			$db->sql_build_array('INSERT', array(
				'userid' => $w_userid,
				'name' => $name,
				'value' => $value,
				));
		$db->sql_query($sql);
		$run++;
		poke_info(T('SETTING_INSERTED', array('name' => $name, 'value' => $value)), TRUE);
	endforeach;

	$db->sql_transaction('commit');

	poke_info(T('CHANGES_MADE', array('cnt' => $run)), TRUE);
	$url = $page['base_path'] . 'admin.' . $phpEx . '?userid=' . $w_userid;

	die( redirect($url) );
endif;

/* ---------- Form code ---------- */

require_once('includes/thelper.' . $phpEx);

ob_start();

print '<div class="panel">' .
	get_admin_select_user(T('SHOW_SETTINGS_FOR'), $_SERVER['PHP_SELF'],
		$w_userid, array(0 => '(' . T('ALL_USERS') . ')')) .
	'</div><br />';

global $run, $tab;
$run = 0;
$tab = 0;
?>

<form action="admin.<?php print $phpEx; ?>" method="post" name="f">
<input type="hidden" name="f" value="post" />
<input type="hidden" name="userid" value="<?php print $w_userid; ?>" />

<table class="editlist">
<thead>
<tr>
	<th class="col_1"><?php P('SETTING'); ?></th>
	<th class="col_2"><?php P('VALUE'); ?></th>
	<th class="col_3"><?php P('INFO'); ?></th>
	<th class="col_4"><?php P('DELETE'); ?></th>
</tr>
</thead>
<tbody>
<?php
foreach ($settings_array as $set_name => $info) :
	if (!(($w_userid > 0) && (isset($info[AS_NOT_FOR_USER])) && ($info[AS_NOT_FOR_USER]))) :
		print PHP_EOL;
		if (($info[AS_TYPE] == AS_T_SECTION) || ($info[AS_TYPE] == AS_T_SUBSECTION)) :
			$run++;
			print '<tr class="row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') .
				(($run == 1) ? ' first' : '') .
				' ' . $info[AS_TYPE] . '">';
			print '<th colspan="4">' . $set_name . '</th>';
			print '</tr>';
		else :
			$col3 = '';

			if (isset($info[AS_HINT])) :
				$col3 .= '<a href="javascript:void(0)" onclick="alert(\'' . strip_tags($info[AS_HINT]) . '\')">';
				$col3 .= '<img src="images/ico_help.png" title="' .
					strip_tags($info[AS_HINT]) . '" alt="' .
					strip_tags($info[AS_HINT]) . '" /><br />';
				$col3 .= '</a>';
			endif;

			if ($set_store[$set_name] < -1) :
				$st = 'no_entry';
			elseif ($set_store[$set_name] == -1) :
				$st = 'php';
			elseif ($set_store[$set_name] == 0) :
				$st = 'database';
			else :
				$st = 'user';
			endif;

			$col3 .= '<img src="images/ico_' . $st . '.png" title="' .
				T('SAVED_IN_' . strtoupper($st)) . '" alt="' .
				T('SAVED_IN_' . strtoupper($st)) . '" />';

			$col3 .= '</td><td class="col_4">';

			if (($set_store[$set_name] == $w_userid) && (!empty($info[AS_TYPE]))) :
				$tab++;
				$col3 .= '<input type="checkbox" name="d_' . $set_name .
					'" value="DELETE" title="' . T('DELETE_ITEM') .
					'" id="f_d_' . $set_name . '" tabindex="' . $tab .  ' "' .
					' onclick="checkboxer(\'f_d_' . $set_name . '\', \'f_v_' . $set_name . '\');' .
					(($info[AS_TYPE] == AS_T_CALLBACK) ? 'checkboxer(\'f_d_' . $set_name . '\', \'f_c_v_' . $set_name . '\');' : '') .
					'" />';
				$tab--;
			endif;

			output_field(
				'v_' . $set_name,
				$settings2[$set_name],
				$info[AS_TYPE],
				(isset($info[AS_DATA]) ? $info[AS_DATA] : NULL),
				FALSE, FALSE,
				$col3);

			if (($set_store[$set_name] >= 0) && (!empty($info[AS_TYPE])))
				$tab++;
		endif;
	endif;
endforeach;
print PHP_EOL . PHP_EOL;
?>
</tbody>
<tfoot>
<tr class="<?php print 'row_' . ($run+1) . ' ' . (is_odd($run) ? 'even' : 'odd'); ?>">
<th><button type="reset" class="minibutton danger btn-rset" tabindex="<?php print $tab+2; ?>"><span class="icon"></span><?php P('RESET_EDITS'); ?></button></th>
<td colspan="3"><button type="submit" class="minibutton btn-subm" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('SUBMIT_EDITS'); ?></button></td>
</tr>
<tr class="<?php print 'row_' . ($run+2) . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last'; ?>">
<th></th>
<td colspan="3">
<?php
	if ($w_userid == 0) :
		print '<img src="images/ico_database.png" /> ';
		P('CHANGE_SITEWIDE');
	else :
		print '<img src="images/ico_user.png" /> ';

		$sql = 	"SELECT id, username, realname FROM $USERS_TABLE" .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		P('CHANGE_FOR_USER', array(
			'userid' => $row['id'],
			'username' => $row['username'],
			'realname' => $row['realname'],
			));
	endif;
?>
</td></tr>
</tfoot></table>
</form>

<?php
$page['content'] = ob_get_clean();

// TODO : ADMIN : Checkbox disbaling with YUI code
$s1 = "function checkboxer(tocheck, todisable) {
	var x = document.getElementById(todisable);
	var y = document.getElementById(tocheck);
	x.disabled = y.checked;
	if (y.checked) {
		x.style.visibility = 'hidden';
	} else {
		x.style.visibility = 'visible';
	}
}";
$page['scripts'] = loadscript($s1);

$page['title'] = T('SITE_ADMIN');
include('includes/' . TEMPLATE . '.' . $phpEx);
