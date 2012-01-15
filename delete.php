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
initialize_security();

if ($userlevel < USER_LEVEL_DS) :
	access_denied();
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

$urlid = isset($_REQUEST['id']) ? $_REQUEST['id'] : FALSE;

if (!$urlid) :
	header_code(405);
	poke_error(T('UNKNOWN_ACTION'));
	poke_validation(T('INAPPROPRIATE'));
	$page['navigation'] = T('HOME', array('url' => $page['base_path']));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

if (!$db) initialize_db(TRUE);

$http_referer = get_referer();

$sql = "SELECT userid, longurl, title FROM $URLS_TABLE" .
	" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
if (!$row) :
	header_code(404);
	poke_error(T('RECORD_NOT_FOUND', array('id' => $urlid)));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

$permited = (($userlevel >= USER_LEVEL_AD) or ($userid == $row['userid']));
if (!$permited) :
	access_denied();
	poke_validation(P('NOT_YOURS'));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

$title = (!$row['title'] ? $row['longurl'] : $row['title']);

if (!isset($delete_action)) $delete_action = '';

// TODO : DELETE : KILL (real deletes) for admins code!

switch ($delete_action) :
	case 'anon' :
		$sql = "UPDATE $URLS_TABLE SET " .
			$db->sql_build_array('UPDATE', array('userid' => 0)) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
		$db->sql_query($sql);
		poke_success(T('ANONYMIZED', array('title' => $title)), TRUE);
		break;
	case 'undo' :
		$sql = "UPDATE $URLS_TABLE SET " .
			$db->sql_build_array('UPDATE', array('enabled' => TRUE)) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
		$db->sql_query($sql);
		poke_success(T('UNDONE', array('title' => $title)), TRUE);
		break;
	default :
		$sql = "UPDATE $URLS_TABLE SET " .
			$db->sql_build_array('UPDATE', array('enabled' => FALSE)) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
		$db->sql_query($sql);
		poke_success(T('DELETED', array('title' => $title)), TRUE);
		break;
endswitch;

redirect($http_referer);
die();

?>
