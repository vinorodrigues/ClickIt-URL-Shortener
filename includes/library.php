<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 * @version    $Id$
 */

/* ----- Application related ----- */

define('IN_CLICKIT', TRUE);
define('CLICKIT_VER', '0.5.2&beta;');
define('TEMPLATE', 'template');

define('CHART_API_SERVER', 'http://chart.apis.google.com/chart');

global $phpEx;
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once('lang.' . $phpEx);

@ini_set('session.name', 'C1K_IT_SESSID');  // Session Cookie name

/* ----- Helper function ----- */

function boolval($var) {
	if (empty($var)) return FALSE;
	if (is_bool($var)) return $var;
	return intval($var) > 0;
}

function is_odd($val) {
	return ($val & 1);
}

/* ----- Template Related ----- */

global $page;
$page = array();
$page['head_title'] = 'c1k.it';
$page['full_path'] = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
$page['full_path'] .= $_SERVER['SERVER_NAME'];
$__port = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 443 : 80;
$page['full_path'] .= (intval($_SERVER['SERVER_PORT']) != $__port) ? ':'.$_SERVER['SERVER_PORT'] : '';
unset($__port);
$page['base_path'] = pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);
if (substr($page['base_path'], -1) != '/') $page['base_path'] .= '/';
$page['full_path'] .= $page['base_path'];
$page['self'] = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME) . '.' . $phpEx;
$page['logo'] = 'images/logo.png';
$page['site_name'] = 'c1k.it';
// $page['site_slogan'] = '';
$page['title'] = $page['head_title'];
$page['head'] = '';
$page['content'] = '';

function header_code($code) {
	global $page;
	header('HTTP/1.0 ' . $code . ' ' . T('STATUS_' . $code), TRUE, $code);
	if (($code < 200) || ($code > 299)) $page['title'] = T('STATUS_' . $code);
}

function access_denied($code = 403) {
	global $page;
	header_code($code);
	poke_error(T('ACCESS_DENIED'));
	$page['content'] = T('NO_ACCESS');

	$page['navigation'] = T('HOME', array(
		'url' => $page['base_path'],
		));
	/* $page['navigation'] .= ' <span class="spacer">|</span> ';
	$page['navigation'] .= T('LOGON', array(
		'url' => $page['base_path'] . 'login.' . $phpEx,
		)); */
}



/* ----- phpBB's dbal stuff ----- */

define('IN_PHPBB', TRUE);
global $phpbb_root_path;
$phpbb_root_path = './';



/* ----- Settings array ----- */

global $settings;
$settings = array('version' => CLICKIT_VER);

/**
 * Loads the settings files
 * @param boolean $loaduserset
 */
function initialize_settings($is_install = FALSE) {
	global $__loaded_settings;  // singleton
	if (isset($__loaded_settings) && $__loaded_settings) return FALSE;
	$__loaded_settings = TRUE;

	global $settings, $phpEx;
	require_once('includes/config-default.' . $phpEx);
	if ((!$is_install) && file_exists('config.' . $phpEx))
		include_once('config.' . $phpEx);
	if ((!$is_install) && file_exists('~config.' . $phpEx))
		include_once('~config.' . $phpEx);  // for debugging

	/* -- Tables -- */
	global $SETTINGS_TABLE, $USERS_TABLE, $URLS_TABLE, $LOG_TABLE,
		$EVENTS_TABLE;  // singletons
	$SETTINGS_TABLE = $settings['dbprefix'] . 'settings';
	$USERS_TABLE = $settings['dbprefix'] . 'users';
	$URLS_TABLE = $settings['dbprefix'] . 'urls';
	$LOG_TABLE = $settings['dbprefix'] . 'log';
	$EVENTS_TABLE = $settings['dbprefix'] . 'events';

	/* -- */
	if ((!$is_install) && isset($settings['offline']) && $settings['offline']) :
		global $page;
		header_code(503);  // Service Unavailable
		include('offline.' . $phpEx);
		die();
	endif;

	return $settings;
}

function webmaster($full = TRUE) {
	global $settings;
	return $full ?
		$settings['webmaster_name'] . ' <' . $settings['webmaster_email'] . '>' :
		$settings['webmaster_email'];
}



/* ----- Database stuff ----- */

global $db, $sql;
$db = FALSE;
$sql = '';  // persist $sql for error and exception handlers

function initialize_db($load_settings = TRUE) {
	global $db;
	if ($db === FALSE) :
		global $settings, $phpbb_root_path, $phpEx;
		require_once('includes/db/' . $settings['dbms'] . '.' . $phpEx);
		$sql_db = 'dbal_' . $settings['dbms'];  // fix for a bug in dbal where @sql_db not set correctly!

		$db = new $sql_db();
		$db->sql_connect(
			$settings['dbhost'],
			$settings['dbuser'],
			$settings['dbpasswd'],
			$settings['dbname'],
			$settings['dbport'],
			FALSE,
			FALSE
			);
		unset($settings['dbpasswd']);  // hey... why not

		if ($load_settings) :
			global $SETTINGS_TABLE;
			$sql = 	"SELECT name, value FROM $SETTINGS_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array('userid' => 0));
			$result = $db->sql_query($sql);
			if ($result) :
				while ($row = $db->sql_fetchrow($result)) :
					$settings[$row['name']] = $row['value'];
				endwhile;
			endif;
			$db->sql_freeresult($result);

			// lockout if off line
			if (isset($settings['offline']) && $settings['offline']) :
				global $page;
				header_code(503);  // Service Unavailable
				include('offline.' . $phpEx);
				die();
			endif;
		endif;
	endif;

	return $db;
}

function log_event($msg, $data = NULL, $time = NULL) {
	global $db, $sql, $EVENTS_TABLE;
	if (!$db) $db = initialize_db();

	$ins_data = array(
		'eon' => ((isset($time) && !empty($time)) ? $time : microtime(TRUE)),
		'uri' => $db->sql_escape($_SERVER['REQUEST_URI']),
		'ipaddress' => $db->sql_escape($_SERVER['REMOTE_ADDR']),
		'referer' => isset($_SERVER['HTTP_REFERER']) ? $db->sql_escape($_SERVER['HTTP_REFERER']) : '',
		'msg' => $db->sql_escape($msg),
	);

	if (isset($data)) $ins_data['data'] = $db->sql_escape($data);

	$sql = "INSERT INTO $EVENTS_TABLE " .
		$db->sql_build_array('INSERT', $ins_data);
	$db->sql_query($sql);
}

function get_long($shortURL) {
	global $db, $sql, $URLS_TABLE;
	if (!$db) return FALSE;
	$ret = array();
	$sql = "SELECT id, longurl FROM $URLS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('shorturl' => $shortURL));
	$result = $db->sql_query($sql);
	if ($result) :
		$row = $db->sql_fetchrow($result);
		@$fnd = (int) $row['id'] > 0;
		if ($fnd) :
			$ret['code'] = 200;
			$ret['id'] = $row['id'];
			$ret['url'] = $row['longurl'];
		else :
			$ret['code'] = 404;
		endif;
	else :
		$ret['code'] = 500;
	endif;
	$db->sql_freeresult($result);
	return $ret;
}

function get_short($longURL, $userid) {
	global $db, $sql, $URLS_TABLE;
	if (!$db) return FALSE;
	// if (substr($longURL, -1) == '/') $longURL = substr($longURL, 0, -1);
	$sql = "SELECT id, shorturl FROM $URLS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('longurl' => $longURL, 'userid' => $userid));
	$result = $db->sql_query($sql);
	$ret = array();
	if ($result) :
		$row = $db->sql_fetchrow($result);
		@$fnd = (int) $row['id'] > 0;
		if ($fnd) :
			$ret['code'] = 200;
			$ret['id'] = $row['id'];
			$ret['url'] = $row['shorturl'];
		else :
			$ret['code'] = 404;
		endif;
	else :
		$ret['code'] = 500;
	endif;
	$db->sql_freeresult($result);
	return $ret;
}

define('REGEX_ECMA_URL', '(^(mailto\:|((ht|f)tp(s?))\://){1}\S+)');

function generate_short($id) {
	global $settings, $phpEx;
	@$length = intval($settings['shortminlength']);
	if ($length <= 0) $length = 4;

	include_once('hashlib.' . $phpEx);

	$shortURL = hash_numeric($id, $length);
	$ok = FALSE; $loop = 0;
	while ((!$ok) && ($loop < 10)) :
		$loop++;
		$r = get_long($shortURL);
		if (($r === FALSE) || ($r['code'] == 200)) :
			if ($loop < 10) :
				$incr = pow(intval($loop / 4), 2);
				$shortURL = hash_random($length+$incr);
			endif;
		else :
			$ok = TRUE;
		endif;
	endwhile;
	if (!$ok) $shortURL = FALSE;
	return $shortURL;
}

function get_fav_icon($longURL) {
	// TODO : LIBRARY : Make get_fav_icon use a list of providers, like the CDN setting
	@$domain = parse_url($longURL, PHP_URL_HOST);
	return T('URL_ICON_DATA', array('domain' => $domain));
}

function generate_password() {
	$p = md5(rand() . rand());
	return strtolower( substr($p, 0, 4) ) . strtoupper( substr($p, 4, 4) );
}



/* ----- Error handling ----- */

global $messages;
$messages = array();  // Error messages
if (!session_id()) session_start();
if (isset($_SESSION['messages'])) :
	foreach ($_SESSION['messages'] as $msg => $level) $messages[$msg] = $level;
	unset( $_SESSION['messages'] );
endif;

define('MSG_ERROR',      'error');
define('MSG_VALIDATION', 'validation');
define('MSG_WARNING',    'warning');
define('MSG_INFO',       'info');
define('MSG_SUCCESS',    'success');

function poke_message($msg, $level = '', $far = FALSE) {
	if ($far) :
		if (!session_id()) session_start();
		if (!isset($_SESSION['messages'])) $_SESSION['messages'] = array();
		$_SESSION['messages'][$msg] = $level;
	else :
		global $messages;
		$messages[$msg] = $level;
	endif;
}

function poke_error($msg, $far = FALSE) {
	poke_message($msg, MSG_ERROR, $far);
}

function poke_validation($msg, $far = FALSE) {
	poke_message($msg, MSG_VALIDATION, $far);
}

function poke_warning($msg, $far = FALSE) {
	poke_message($msg, MSG_WARNING, $far);
}

function poke_info($msg, $far = FALSE) {
	poke_message($msg, MSG_INFO, $far);
}

function poke_success($msg, $far = FALSE) {
	poke_message($msg, MSG_SUCCESS, $far);
}

function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $messages, $sql;
	$m = "PHP Error $errno: $errstr <i>in line</i> <code>$errline</code> <i>of file</i> <code>$errfile</code>";
	if (!empty($sql)) : $m .= "<br /><b>SQL:</b> <pre>$sql</pre>"; $sql = ''; endif;
	$m .= "\n";
	$messages[$m] = MSG_ERROR;
	return TRUE;
}

function exception_handler($exception) {
	global $messages, $sql;
	$m = "Exception: " . $exception->getMessage();
	if (!empty($sql)) : $m .= "<br /><b>SQL:</b> <pre>$sql</pre>"; $sql = ''; endif;
	$m .= "\n";
	$messages[$m] = MSG_ERROR;
	return TRUE;
}



/* ----- HTML 5 Support ----- */

function __get_htmlver() {
	// User-Agent strings start with "Mozilla/5.0 {...}",
	// the version string after '/' seems to be the HTML version supported
	// {unverified assumption}
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$i = strpos($ua, '/');
	if (!$i) return 1;
	$j = strpos($ua, ' ', $i);
	if (!$j) return 1;
	$u = substr($ua, $i+1, $j-$i-1 );
	// IE has crap support for HTML 5, so revert to HTML 4
	if (($u >= 5) and (strpos($ua, 'MSIE ') !== FALSE)) $u = 4;
	return intval($u);
}

/**
 * prints string based on HTML5 support
 * @param string $str4 Fallback string
 * @param string $str5 String if User-Agent supports HTML 5.0 +
 */
function __($str4, $str5 = '', $return_only = FALSE) {
	global $__htmlver;  // singleton
	if (!isset($__htmlver)) $__htmlver = __get_htmlver();
	if ($return_only) :
		return ($__htmlver >= 5) ? $str5 : $str4;
	elseif ($__htmlver >= 5) :
		print $str5;
	else :
		print $str4;
	endif;
}



/* ----- User Management ----- */


global $userid, $userlevel, $username;
$userid = 0;
$userlevel = -1;  // invalid
$username = '';

/**
 * $userlevel - User Levels Caoabilities
 * User Level 0  = Login
 * User Level 1  + Create ShortURL
 * User Level 2  + List URL's
 * User Level 3  + Edit LongURL's
 * User Level 4  + Edit ShortURL's
 * User Level 5  + Delete/Disable ShortURL's
 * User Level 6  + Create and edit Customised ShortURL's
 * User Level 7  +
 * User Level 8  +
 * User Level 9  + Site Admin
 * User Level 10 +
 */

define('USER_LEVEL_BS', 0);
define('USER_LEVEL_CR', 1);
define('USER_LEVEL_LS', 2);
define('USER_LEVEL_EL', 3);
//                      4
define('USER_LEVEL_DS', 5);
define('USER_LEVEL_CU', 6);
define('USER_LEVEL_ES', 7);
define('USER_LEVEL_DL', 8);
define('USER_LEVEL_AD', 9);
define('USER_LEVEL_GD', 10);

function get_user_levels() {
	// do this on demand as lang may not have been loaded prior
	return array(
		USER_LEVEL_BS => T('USER_LEVEL_BS'),
		USER_LEVEL_CR => T('USER_LEVEL_CR'),
		USER_LEVEL_LS => T('USER_LEVEL_LS'),
		USER_LEVEL_EL => T('USER_LEVEL_EL'),
		USER_LEVEL_DS => T('USER_LEVEL_DS'),
		USER_LEVEL_CU => T('USER_LEVEL_CU'),
		USER_LEVEL_ES => T('USER_LEVEL_ES'),
		USER_LEVEL_DL => T('USER_LEVEL_DL'),
		USER_LEVEL_AD => T('USER_LEVEL_AD'),
		USER_LEVEL_GD => T('USER_LEVEL_GD'),
		);
}

function initialize_security($must_login = FALSE) {
	global $__loaded_security;  // singleton
	if (isset($__loaded_security) && $__loaded_security) return TRUE;  // already initialized
	$__loaded_security = TRUE;

	global $db, $userid, $userlevel, $username, $settings, $phpEx;
	global $USERS_TABLE;
	if (!$db) $db = initialize_db();

	// find out if these is a TOKEN cookie
	if (isset($_COOKIE['token'])) :
		$token = str_replace('-', '', $_COOKIE['token']);

		$sql = "SELECT id, enabled, userlevel, username, realname FROM $USERS_TABLE" .
			" WHERE " . $db->sql_build_array('SELECT', array('token' => $token));
		$result = $db->sql_query($sql);
		if ($result) :
			$row = $db->sql_fetchrow($result);
			if ($row && $row['enabled']) :
				$userid = (int) $row['id'];
				$userlevel = (int) $row['userlevel'];
				$username = empty($row['realname']) ? $row['username'] : $row['realname'];

				$sql = "UPDATE $USERS_TABLE" .
					" SET " . $db->sql_build_array('UPDATE', array(
						'lastvisiton' => microtime(TRUE),
						)) .
					" WHERE " . $db->sql_build_array('SELECT', array('id' => $userid));
				$db->sql_query($sql);

				global $SETTINGS_TABLE;
				$sql = 	"SELECT name, value FROM $SETTINGS_TABLE" .
					" WHERE " . $db->sql_build_array('SELECT', array('userid' => $userid));;
				$result = $db->sql_query($sql);
				if ($result) :
					while ($row = $db->sql_fetchrow($result)) :
						$settings[$row['name']] = $row['value'];
					endwhile;
				endif;

				if (!isset($page['navigation']) && ($userid > 0)) :
					global $page;
					$page['navigation'] = T('[');
					$page['navigation'] .= T('HELLO', array(
						'url' => $page['base_path'] . 'user.' . $phpEx,
						'username' => $username,
						));
					$page['navigation'] .= T('|');
					$page['navigation'] .= T('LOGOFF', array(
						'url' => $page['base_path'] . 'logout.' . $phpEx,
						));
					if ($userlevel >= USER_LEVEL_LS) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('LIST_PAGE', array(
							'url' => $page['base_path'] . 'list.' . $phpEx,
							));
					endif;
					if ($userlevel >= USER_LEVEL_DS) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('ARCH_PAGE', array(
							'url' => $page['base_path'] . 'archives.' . $phpEx,
							));
					endif;
					if ($userlevel >= USER_LEVEL_AD) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('ADMIN_PAGE', array(
							'url' => $page['base_path'] . 'admin.' . $phpEx,
							));
					endif;
					$page['navigation'] .= T(']');
				endif;

				return TRUE;
			else :
				// ignore TOKEN
			endif;
			$db->sql_freeresult($result);
		else :
			// ignore TOKEN
		endif;
	endif;

	// find out if this is a submission from the login form
	if (isset($_REQUEST['username'])) :
		$username = strtolower( $_REQUEST['username'] );
		if (empty($username)) :
			poke_validation(T('PROVIDE_USERNAME'));
		else :
			$passwd = (isset($_REQUEST['passwd']) && (!empty($_REQUEST['passwd']))) ? md5( $_REQUEST['passwd'] ) : '';
			$remember = isset($_REQUEST['remember']) ? ($_REQUEST['remember'] == 1) : $remember = FALSE;

			if (!$db) $db = initialize_db();

			$sql = "SELECT id, passwd, token, enabled, userlevel, realname, bad_logon FROM $USERS_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array('username' => $username));
			$result = $db->sql_query($sql);
			if ($result) :
				$row = $db->sql_fetchrow($result);
				if ($row) :
					if ($row['enabled'] == 1) :
						$blc = intval($row['bad_logon']);
						if (strcasecmp($passwd, $row['passwd']) == 0) :
							$userid = intval($row['id']);
							$userlevel = intval($row['userlevel']);
							$username = empty($row['realname']) ? $username : $row['realname'];
							if ($row['token'] != '') :
								// Rebuild GUID
								// 0000000001111111111222222222233333333334
								// 1234567890123456789012345678901234567890
								// 4d95547f-5320-4b67-8d18-2415735f6398
								$guid = substr_replace($row['token'], '-', 8, 0);
								$guid = substr_replace($guid, '-', 13, 0);
								$guid = substr_replace($guid, '-', 18, 0);
								$guid = substr_replace($guid, '-', 23, 0);
							else :
								include_once('includes/uuid.' . $phpEx);
								$guid = get_uuid();
							endif;
							$exp = $remember ? time() + 60*60*24*30 : 0;
							setcookie('token', $guid, $exp);

							$data = array(
								'lastvisiton' => microtime(TRUE),
								'token' => str_replace('-', '', $guid),
								);
							if ($blc != 0) $data['bad_logon'] = 0;
							$sql = "UPDATE $USERS_TABLE" .
								" SET " . $db->sql_build_array('UPDATE', $data) .
								" WHERE " . $db->sql_build_array('SELECT', array('id' => $userid));
							$db->sql_query($sql);
							poke_success(T('LOGIN_SUCCESSFUL'), TRUE);
							// if ($row['token'] != '')
							//	poke_warning(T('LOGGED_OUT_OTHER_SESSIONS'), TRUE);

							if (isset($_REQUEST['referer'])) :
								$nextpage = $_REQUEST['referer'];
							else :
								$nextpage = $_SERVER['REQUEST_URI'];
							endif;

							if ($blc < 0) :
								$nextpage = $page['basepath'] . 'user.' . $phpEx .
									'?f=password&referer=' . urlencode($nextpage);
								poke_info(T('MUST_CHANGE_PASSWORD'), TRUE);
							endif;

							redirect($nextpage);
							die();
						else :
							$blc++;
							$max = isset($settings['badlogonmax']) ? intval($settings['badlogonmax']) : 3;
							if ($blc >= $max) :
								$data = array(
									'enabled' => FALSE,
									'lastvisiton' => microtime(TRUE),
									'bad_logon' => $blc,
									);
							else :
								$data = array(
									'bad_logon' => $blc,
									);
							endif;

							$sql = "UPDATE $USERS_TABLE" .
								" SET " . $db->sql_build_array('UPDATE', $data) .
								" WHERE " . $db->sql_build_array('SELECT', array('id' => intval($row['id'])));
							$db->sql_query($sql);

							poke_validation(T('PASSWORD_MISMATCH'));
							if ($blc >= $max) poke_warning(T('ACCOUNT_LOCKED_OUT'));
						endif;
					else :
						poke_error(T('ACCESS_DENIED'));
					endif;
				else :
					poke_validation(T('USER_NOT_FOUND'));
				endif;
				$db->sql_freeresult($result);
			else :
				poke_error('DATABASE_ERROR');
			endif;
		endif;
	endif;

	if ($must_login) :
		global $page, $messages, $phpEx;
		$page['content'] .= T('YOU_MUST_LOGIN', NULL, '<p class="loginrequired">', '</p>' . PHP_EOL);
		$http_referer = $_SERVER['REQUEST_URI'];
		header_code(203);  // Non-Authoritative Information
		include('login.' . $phpEx);
		die();
	endif;

	return FALSE;
}

function get_admin_select_user($prompt, $form_action, $selectedid, $extra_options = NULL) {
	global $USERS_TABLE, $sql, $userid, $userlevel, $db;

	$R = "<form action=\"$form_action\" name=\"u\" method=\"get\" style=\"display:inline;margin:auto;\">";
	$R .= "<label for=\"userid\">$prompt</label>";
	$R .= ": <select name=\"userid\" id=\"userid\" onchange=\"document.forms['u'].submit()\">";
	if ($extra_options)
		foreach ($extra_options as $id => $value) :
			$R .= "<option ";
			if ($selectedid == $id) $R .= " selected=\"selected\"";
			$R .= "value=\"$id\">$value</option>";
		endforeach;;

	$sql = "SELECT id, username, realname FROM $USERS_TABLE" .
		" WHERE userlevel <= $userlevel" .
		" ORDER BY username";
	$result = $db->sql_query($sql);
	if ($result !== FALSE) :
		while ($row = $db->sql_fetchrow($result)) :
			$R .= "<option ";
			if ($row['id'] == $selectedid) $R .= " selected=\"selected\"";
			$R .= "value=\"" . $row['id'] . "\">" . $row['username'];
			if (!empty($row['realname'])) $R .= " (" . $row['realname'] . ')';
			if ($row['id'] == $userid) $R .= " " .  T('YOU');
			$R .= "</option>";
		endwhile;
		$db->sql_freeresult($result);
	endif;
	$R .= "</select></form>";
	return $R;
}

function get_time_ago($time) {
	$time = time() - $time;

	$tokens = array (
		31536000 => 'YEAR',
		2592000 => 'MONTH',
		604800 => 'WEEK',
		86400 => 'DAY',
		3600 => 'HOUR',
		60 => 'MINUTE',
		1 => 'SECOND'
		);

    foreach ($tokens as $unit => $text) :
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . T((($numberOfUnits>1)?($text.'S'):$text));
    endforeach;
    return T('NOW');
}



/* ----- CDN ----- */

global $__yuicdn;
$__yuicdn = '';

function yuicdn($returnvalue = FALSE) {
	global $__yuicdn;
	if ($__yuicdn == '') :
		global $settings;
		switch (@$settings['cdn']) :
			case 'yahoo' :
				$__yuicdn = 'http://yui.yahooapis.com/3.3.0/';
				break;
			case 'google' :
				$__yuicdn = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
				$__yuicdn .= 'ajax.googleapis.com/ajax/libs/yui/3.3.0/';
				break;
			case 'google-smart' :
				$__yuicdn = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
				$__yuicdn .= 'ajax.googleapis.com/ajax/libs/yui/3/';
				break;
			default :
				global $page;
				$__yuicdn = $page['base_path'] . 'includes/yui/';
		endswitch;
	endif;

	if ($returnvalue) :
		return $__yuicdn;
	else :
		print $__yuicdn;
	endif;
}

function ajaxjs($file) {
	return "\t<script type=\"text/javascript\" src=\"" . yuicdn(TRUE) . "build/$file\"></script>\n";
}

function loadjs($file) {
	global $page;
	return "\t<script type=\"text/javascript\" src=\"" . $page['base_path'] . "$file\"></script>\n";
}

function loadscript($script) {
	$jsmfn = 'includes/jsmin/jsmin.php';
	if (file_exists($jsmfn)) :
		include_once($jsmfn);
		$script = JSMin::minify($script);
	else :
		$script = str_replace("\r\n", "\n", $script);
		$script = str_replace("\n", PHP_EOL . "\t\t", $script);
		$script = PHP_EOL . $script;
	endif;
	return "\t<script type=\"text/javascript\">//<![CDATA[" .
		"\t\t$script" .
		PHP_EOL . "\t//]]></script>" . PHP_EOL;
}



/* ----- Helper Functions ----- */

function redirect($url, $external = FALSE) {
	global $settings, $page;
	$code = $external ? 307 : 303;
	if (isset($settings['force302'])) :
		switch ($settings['force302']) :
			case 1: $code = 302; break;
			case 2: $code = 301; break;
		endswitch;
	endif;
	header('HTTP/1.1 ' . $code . ' ' . T('STATUS_' .  $code));
	if ($code != 301) :
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1, ask me again next time
		header('Expires: ' . date(DATE_RFC822));  // expire now
	endif;
	if ($external) :
		$ref_path = $page['full_path'] . str_replace($page['base_path'], '', $_SERVER['REQUEST_URI']);
		header("Referer: $ref_path");  // be nice and tell the other server where you came from
	endif;
	header('Location: ' . $url, TRUE, $code);
	echo "Redirecting to <a href=\"" . $url . "\">" . $url . "</a>.";  /// just in case all goes wrong on the browser
	return $code;
}

function get_referer($set_to_self = TRUE) {
	if (isset($_REQUEST['referer']))
		return $_REQUEST['referer'];

	global $page;

	if (isset($_SERVER['HTTP_REFERER'])) :
		$ref = $_SERVER['HTTP_REFERER'];

		$i = strpos($ref, $page['full_path']);

		// test is referer local
		if (($i !== FALSE) && ($i == 0))
			return substr($ref, strlen($page['full_path']));
	endif;

	if ($set_to_self) :
		$ref = $_SERVER['REQUEST_URI'];
		return substr($ref, strpos($ref, $page['base_path']) + strlen($page['base_path']));
	endif;

	return FALSE;
}

?>
