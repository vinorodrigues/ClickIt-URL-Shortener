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

$included = strtolower(realpath(__FILE__)) != strtolower(realpath($_SERVER['SCRIPT_FILENAME']));

/**
 * Sequence of updates seperated by commas, no spaces.  Do not skip versions.
 */
define('CLICKIT_UPDATES', '0.2,0.3,0.4,0.5');

/**
 * Parse the CLICKIT_UPDATES const and run the independent update functions
 */
function perform_updates() {
	global $settings;

	$versions = explode(',', CLICKIT_UPDATES);
	sort($versions, SORT_NUMERIC);  // just in case

	$cnt = 0;
	foreach ($versions as $ver) :
		if (floatval($ver) > floatval($settings['version'])) :
			$fn = 'perform_update_' .  str_replace('.', '_', $ver);
			if (function_exists($fn)) :
				if (call_user_func($fn)) : $cnt++; endif;
			else :
				poke_warning("Function <code>$fn</code> does not exist");
			endif;
		endif;
	endforeach;

	if ($cnt > 0) poke_success('<b><i>Updated:</i></b> Was ' .
		$settings['version'] . ', now ' . CLICKIT_VER);

	return ($cnt > 0) ? $cnt : FALSE;
}

function perform_update_meta_version($ver_str) {
	global $settings, $db, $sql, $SETTINGS_TABLE;
	$sql = "UPDATE $SETTINGS_TABLE SET " .
		$db->sql_build_array('UPDATE', array('value' => $ver_str)) .
		" WHERE " . $db->sql_build_array('SELECT', array(
			'userid' => 0,
			'name' => 'version',
			) );
	$db->sql_query($sql);
	poke_info('<b><i>Updated</i></b> metadata to ' . $ver_str);
}

function perform_update_0_2() {
	perform_update_meta_version('0.2 beta');
	return TRUE;
}

function perform_update_0_3() {
	perform_update_meta_version('0.3 beta');
	return TRUE;
}

function perform_update_0_4() {
	global $phpEx, $db, $settings, $messages;
	global $EVENTS_TABLE, $URLS_TABLE, $USERS_TABLE;

	initialize_settings();
	$db = initialize_db();

	set_error_handler('error_handler');
	set_exception_handler('exception_handler');

	include_once('includes/db/db_tools.' . $phpEx);
	$tools = new phpbb_db_tools($db);

	include('includes/schemas.' . $phpEx);

	foreach($schema_0_4 as $tablename => $schema) :
		if (!$tools->sql_table_exists($tablename)) :
			$tools->sql_create_table($tablename, $schema);
			poke_info("Created table <code>$tablename</code>");
		else :
			poke_info("Table <code>$tablename</code> already exists", FALSE);
		endif;
	endforeach;

	$tools->perform_schema_changes($changes_0_4);

	foreach ($inserts_0_4 as $ins)
		foreach ($ins as $tbl => $vars) :
			$sql = "INSERT INTO $tbl " . $db->sql_build_array('INSERT', $vars);
			$db->sql_query($sql);
		endforeach;

	perform_update_meta_version('0.4 beta');

	restore_exception_handler();
	restore_error_handler();

	if (!empty($messages)) :
		log_event('Updated metadata to 0.4 beta - with Errors');

		foreach ($messages as $msg => $type) :
			if (empty($type)) $type = 'message';
			log_event('Update 0.4 - ' . $type, $msg);
		endforeach;
	else :
		log_event('Updated metadata to 0.4 beta - OK');
	endif;


	return TRUE;
}

function perform_update_0_5() {
	perform_update_meta_version('0.5 beta');
	return TRUE;
}

/* ============================== Main code ============================== */

if (!$included) :
	require_once('includes/library.php');
	require_once('includes/library.' . $phpEx);

	initialize_settings();
	$db = initialize_db(TRUE);
	initialize_lang();

	$ret = (floatval($settings['version']) < floatval(CLICKIT_VER)) ?
		perform_updates(TRUE) : FALSE;

	$page['content'] = 'Status: ' . ($ret ?
		'<span class="green">OK</span>' : '<span class="red">Not Required</span>');

	$page['title'] = 'Update';
	include('includes/' . TEMPLATE . '.' . $phpEx);
endif;

?>
