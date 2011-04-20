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
require_once('includes/lang.php');

initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security();

if ($userlevel < USER_LEVEL_DS) :
	access_denied();
	include('includes/' . TEMPLATE . '.php');
	die(403);
endif;

$urlid = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

if (!$urlid) :
	header_code(405);
	poke_error(T('UNKNOWN_ACTION'));
	poke_validation(T('INAPPROPRIATE'));
	$page['navigation'] = T('HOME', array('url' => $page['base_path']));
	include('includes/' . TEMPLATE . '.php');
	die(405);
endif;

if (!$db) initialize_db(true);

$http_referer = (isset($_REQUEST['referer'])) ? $_REQUEST['referer'] : $_SERVER['HTTP_REFERER'];
$http_referer = str_replace($page['full_path'], '', $http_referer);

$result = load_row($urlid);
$permited = (($userlevel >= USER_LEVEL_AD) or ($userid == $row['userid']));
if (!$permited) :
	access_denied();
	poke_validation(P('NOT_YOURS'));
	include('includes/' . TEMPLATE . '.php');
	die(403);
endif;
$title = (!$row['title'] ? $row['longurl'] : $row['title']);

if (!isset($delete_action)) $delete_action = '';

switch ($delete_action) :
	case 'anon' :
		$sql = "UPDATE $URLS_TABLE SET " . 
			$db->sql_build_array('UPDATE', array('userid' => 0)) .
			" WHERE id = $urlid";
		$db->sql_query($sql);
		poke_success(T('ANONYMIZED', array('title' => $title)), true);
		break;
	case 'undo' :
		$sql = "UPDATE $URLS_TABLE SET " . 
			$db->sql_build_array('UPDATE', array('enabled' => true)) .
			" WHERE id = $urlid";
		$db->sql_query($sql);
		poke_success(T('UNDONE', array('title' => $title)), true);
		break;
	default :
		$sql = "UPDATE $URLS_TABLE SET " . 
			$db->sql_build_array('UPDATE', array('enabled' => false)) .
			" WHERE id = $urlid";
		$db->sql_query($sql);
		poke_success(T('DELETED', array('title' => $title)), true);
		break;
endswitch;

header('Location: ' . $http_referer, true, 302);
exit(302);

// include('includes/' . TEMPLATE . '.php');
// exit();



/* ========================= Functions ========================= */

/**
 * 
 */
function load_row($urlid) {
	global $db, $sql, $row, $page;
	global $URLS_TABLE;
	$sql = "SELECT userid, longurl, title FROM $URLS_TABLE" .  
		" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	if (!$row) :
		header_code(404);
		poke_error(T('RECORD_NOT_FOUND', array('id' => $urlid)));
		include('includes/' . TEMPLATE . '.php');
		die(404);
	endif;
	return $result;
}




?>
