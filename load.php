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
	// header('HTTP/1.1 503 Service Unavailable');
	header_code(503);
	include('offline.' . $phpEx);
	die();
endif;

/* ----- Get URL ----- */

if (isset($_REQUEST['url'])) :
	$expectedURL = $_REQUEST['url'];
elseif (isset($_REQUEST['q'])) :
	$expectedURL = $_REQUEST['q'];
else :
	$e = 404;
	include('error.' . $phpEx);
	die();
endif;

$action = preg_replace("/[a-z0-9_]+/i", "", $expectedURL, 1);
$shortURL = strtolower( str_replace($action, "", $expectedURL) );
// test for file existance of urls like "about" and redirect to 'about'
if (file_exists($shortURL . '.' . $phpEx)) :
	// make sure it's not this file or else we'll go into a infinite loop
	if ( strcasecmp($shortURL, pathinfo(__FILE__, PATHINFO_FILENAME)) != 0 ) :
		// include($shortURL . '.' . $phpEx);
		redirect($shortURL . '.' . $phpEx);
		die();
	endif;
endif;
$actionBit = substr($action, 0, 1);
$actionData = substr($action, 1);
unset($action);

/* ---- find url ----- */
$isShortURL = FALSE;

set_error_handler('error_handler');
set_exception_handler('exception_handler');

$db = initialize_db();

/* ----- Offline (from DB) ----- */

if ($settings['offline']) :
	restore_exception_handler();
	restore_error_handler();
	// header('HTTP/1.1 503 Service Unavailable');
	header_code(503);
	include('offline.' . $phpEx);
	die();
endif;

/* ----- Find Short Bit ----- */

if (empty($messages)) :  // was there an error connecting
	$sql = 	"SELECT * FROM $URLS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array(
			'shorturl' => $shortURL,
			'enabled' => 1,  // TRUE,
			));
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	if ($row) :
		$p_cloak = (boolean) $row['cloak'];
		switch ($actionBit) :
			case '-' : $p_action = 'preview'; break;
			case '@' : $p_action = 'mobile'; break;
			case ' ' : $p_action = 'info'; break;  // use '+' in the url
			case '^' : $p_action = 'cloak'; break;  // force a cloak
			default : $p_action = $p_cloak ? 'cloak' : 'redir';
		endswitch;

		$id = (int) $row['id'];
		$p_title = !empty($row['title']) ? $row['title'] : $row['longurl'];
		$p_url = $row['longurl'];
		if ($p_cloak && !empty($row['metakeyw'])) $p_metakeyw = $row['metakeyw'];
		if ($p_cloak && !empty($row['metadesc'])) $p_metadesc = $row['metadesc'];
		$p_log = (boolean) $row['log'];

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
				if (strcmp('unknown', $data['browser']) === 0) :
					$data['version'] = '';
					$data['platform'] = '';
					// We'd like to know so that we can update the browser table
					log_event('Unknown browser; urlid=' . $id, $_SERVER['HTTP_USER_AGENT'], $data['accessedon']);
				else :
					$ver = explode('.', $brwsr['version'], 3);
					$verstr = $ver[0];
					if (isset($ver[1])) $verstr .= '.' . $ver[1];
					$data['version'] = $db->sql_escape($verstr);

					$data['platform'] = $db->sql_escape($brwsr['platform']);
				endif;
				unset($brwsr);
			endif;
			unset($bf);

			$sql = "INSERT INTO $LOG_TABLE " . $db->sql_build_array('INSERT', $data);
			$db->sql_query($sql);
		endif;

		/* ----- Piwik integration ----- */
		$use_pk = isset($settings['piwik_site']) &&
			(!empty($settings['piwik_site'])) &&
			isset($settings['piwik_id']) &&
			(!empty($settings['piwik_id']));
		if ($use_pk) :
			include_once('includes/piwiklib.' . $phpEx);
			piwik_set_host($settings['piwik_site'], $settings['piwik_id']);
			piwik_track_page_view($p_title);
		endif;
	else :
		$db->sql_freeresult($result);
		$e = 404;  // Not found
	endif;
endif;

$db->sql_close();

restore_exception_handler();
restore_error_handler();

if (!empty($messages) || (isset($e))) :  // Opps!  Something went wrong
	if (!isset($e)) $e = 500;
	include('error.' . $phpEx);
	die();
endif;

$p_short = $page['full_path'] . $row['shorturl'];

switch($p_action) :
	case 'preview' :  // '-'
		initialize_settings();
		initialize_lang();

		$p_delay = isset($settings['preview_delay']) ? intval($settings['preview_delay']) : 60;
		if ($p_delay > 0) :
			$page['head_prefix'] = "\t<meta http-equiv=\"refresh\" content=\"$p_delay;$p_url\">\n";

			$_s = "var seconds = " . $p_delay . ";
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
			$page['scripts'] = loadscript($_s);
		endif;

		$page['head_title'] = "$p_title ($p_url)";
		$page['title'] = T('PREVIEW') . ' ' . $p_title;
		$page['content'] = '<p>' . T('REDIRECTING_TO', array('url' => $p_url));
		if ($p_delay > 0) $page['content'] .= " <small><span id=\"countdown\"></span></small>";
		$page['content'] .= '</p><hr /><p>';

		include_once('includes/clippy/clippy.' . $phpEx);
		$page['content'] .= T('LINK', array(
			'url' => $p_short,
			'copy' => clippy_get_html($p_short),
			));
		$page['content'] .= '</p><p>';
		$page['content'] .= T('LINK_M', array(
			'url' => $p_short . '@',
			'copy' => clippy_get_html($p_short . '@'),
			));
		$page['content'] .= '<br /><img src="' . CHART_API_SERVER . '?cht=qr&chs=300x300&chl=' . urlencode($p_short) . '" alt="' . $p_title . '" style="display:block;margin:auto" />';

		$page['content'] .= '</p>';
		include('includes/' . TEMPLATE . '.' . $phpEx);
		break;

	case 'cloak' :  // in db or '^'
		include('cloak.' . $phpEx);
		break;

	case 'mobile' :  // '@'
		// requires $p_short
		include('mobile.' . $phpEx);
		break;

	/* case 'info' :  // TODO : LOAD : Info page
		print 'INFO';
		die();
		break; */

	default : // 'redir'
		redirect($p_url, TRUE);
		die();
		break;

endswitch;

?>
