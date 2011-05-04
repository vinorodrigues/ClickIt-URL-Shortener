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

/* ----- Includes ----- */
require_once('includes/library.php');
initialize_settings();

/* ----- Offline ----- */
if ($settings['offline']) :
	header('HTTP/1.1 503 Service Unavailable');
	include('offline.' . $phpEx);
	die(503);
endif;

/* ----- Get URL ----- */
if (isset($_REQUEST['url'])) :
	$expectedURL = $_REQUEST['url'];
else :
	$e = 404;
	include('error.' . $phpEx);
	die(404);
endif;
$shortURL = strtolower( preg_replace("/[^a-z0-9_]+/i", "", $expectedURL) );
// test for file existance of urls like "about" and redirect to 'about'
if (file_exists($shortURL . '.' . $phpEx)) :
	// make sure it's not this file or else we'll go into a infinite loop
	if ( strcasecmp($shortURL, pathinfo(__FILE__, PATHINFO_FILENAME)) != 0 ) :
		// include($shortURL . '.' . $phpEx);
		die( redirect($shortURL . '.' . $phpEx) );
	endif;
endif;
$actionURL = substr( preg_replace("/[a-z0-9_]/i", "", $expectedURL), 0, 1);

/* ---- find url ----- */
$isShortURL = FALSE;

set_error_handler('error_handler');
set_exception_handler('exception_handler');

$db = initialize_db();

if (empty($m)) :  // was there an error connecting
	$sql = 	"SELECT * FROM $URLS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array(
			'shorturl' => $shortURL,
			'enabled' => 1,  // TRUE,
			));
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	if ($row) :
		$p_cloak = (boolean) $row['cloak'];
		switch ($actionURL) :
			case '-' : $p_action = 'preview'; break;
			// case '@' : $p_action = 'data'; break; // TODO : LOAD : for Version 0.3
			default : $p_action = $p_cloak ? 'cloak' : 'redir';
		endswitch;

		$id = (int) $row['id'];
		$p_title = $row['title'];
		$p_url = $row['longurl'];
		if ($p_cloak && !empty($row['metakeyw'])) $p_metakeyw = $row['metakeyw'];
		if ($p_cloak && !empty($row['metadesc'])) $p_metadesc = $row['metadesc'];
		$p_log = (boolean) $row['log'];
		// $p_analytics = (boolean) $row['analytics'];  // TODO : LOAD : for Version 0.3

		$db->sql_freeresult($result);

		// update lastvisiton
		$sql = "UPDATE $URLS_TABLE" .
			" SET " . $db->sql_build_array('UPDATE', array('lastvisiton' => microtime(TRUE))) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $id));
		$db->sql_query($sql);

		// log visit
		if ($p_log) :
			$data = array(
				'urlid' => $id,
				'accessedon' => microtime(TRUE),
				'ipaddress' => $db->sql_escape($_SERVER['REMOTE_ADDR']),
				'referer' => isset($_SERVER['HTTP_REFERER']) ? $db->sql_escape($_SERVER['HTTP_REFERER']) : '',
				);

			$bf = isset($settings['func_getbrowser']) ? 'includes/'.$settings['func_getbrowser'] : FALSE;
			if ($bf && file_exists($bf)) :
				include_once($bf);
				$brwsr = _get_browser($_SERVER['HTTP_USER_AGENT']);
				$data['browser'] = $db->sql_escape($brwsr['browser']);

				$ver = explode('.', $brwsr['version'], 3);
				$verstr = $ver[0];
				if (isset($ver[1])) $verstr .= '.' . $ver[1];
				$data['version'] = $db->sql_escape($verstr);

				$data['platform'] = $db->sql_escape($brwsr['platform']);
				unset($brwsr);
			endif;
			unset($bf);

			$sql = "INSERT INTO $LOG_TABLE " . $db->sql_build_array('INSERT', $data);
			$db->sql_query($sql);
		endif;

		// set visit occurance to google analitics
		// TODO : LOAD : for Version 0.3
		/* if ($p_analytics) :

		endif; */

	else :
		$db->sql_freeresult($result);
		$e = 404;
	endif;
endif;

$db->sql_close();

restore_exception_handler();
restore_error_handler();
if (!empty($m) || (isset($e))) :  // Opps!  Something went wrong
	if (!isset($e)) $e = 500;
	include('error.' . $phpEx);
	die();
endif;

switch($p_action) :
	case 'preview' :
		initialize_settings();
		initialize_lang();

		$p_delay = 60;
		if (isset($settings['preview_delay'])) $p_delay = $settings['preview_delay'];
		if ($p_delay > 0) :
			$page['head_prefix'] = "\t<meta http-equiv=\"refresh\" content=\"$p_delay;$p_url\">\n";

			$s1 = "var seconds = " . $p_delay . ";
var int = window.setInterval('countdown()', 1000);
function countdown() {
	seconds--;
	var count = document.getElementById('countdown');
	count.innerHTML = '" . T('SEC_PREFIX') . "' + seconds + '" . T('SEC_SUFIX') . "';
	if (seconds == 0) {
		window.clearInterval(int);
		count.innerHTML = '';
		window.location = '" . $p_url . "';
	}
}";

			$page['scripts'] = loadscript($s1);
		endif;

		$page['head_title'] = "$p_title ($p_url)";
		$page['title'] = T('PREVIEW') . ' ' . $p_title;
		$page['content'] = T('REDIRECTING_TO', array('url' => $p_url));
		if ($p_delay > 0) $page['content'] .= " <small><span id=\"countdown\"></span></small>";
		include('includes/' . TEMPLATE . '.' . $phpEx);
		break;

	case 'cloak' :
		include('cloak.' . $phpEx);
		break;

	default : // 'redir'
		die( redirect($p_url, TRUE) );
		break;

endswitch;

?>
