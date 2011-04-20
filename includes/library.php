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

define('IN_CLICKIT', true);
define('CLICKIT_VER', '0.2 Beta');
define('CLICKIT_BUILD', '$Id$');
define('TEMPLATE', 'template');

include_once('lang.php');



/* ----- Helper function ----- */

function boolval($var) {
	if (empty($var)) return false;
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
$page['full_path'] .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];
$page['base_path'] = pathinfo($_SERVER["PHP_SELF"], PATHINFO_DIRNAME);
if (substr($page['base_path'], -1) != '/') $page['base_path'] .= '/';
$page['full_path'] .= $page['base_path']; 
$page['logo'] = 'images/logo.png';
$page['site_name'] = 'c1k.it';
// $page['site_slogan'] = '';
$page['title'] = $page['head_title'];
$page['head'] = '';
$page['content'] = '';

function header_code($code) {
	global $page;
	header('HTTP/1.0 ' . $code . ' ' . T('STATUS_' . $code), true, $code);	
	$page['title'] = T('STATUS_' . $code);
}
 
function access_denied() {
	global $page;
	header_code(403);
	poke_error(T('ACCESS_DENIED'));
	$page['content'] = T('NO_ACCESS');
	
	$page['navigation'] = T('HOME', array(
		'url' => $page['base_path'],
		));
	/* $page['navigation'] .= ' <span class="spacer">|</span> ';
	$page['navigation'] .= T('LOGON', array(
		'url' => $page['base_path'] . 'login.php',
		)); */
}



/* ----- phpBB's dbal stuff ----- */

define('IN_PHPBB', true);
global $phpbb_root_path, $phpEx;   
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);



/* ----- Settings array ----- */

global $settings, $__loaded_settings;
$settings = array('version' => CLICKIT_VER);
$__loaded_settings = false;

/**
 * Loads the settings files
 * @param boolean $loaduserset
 */
function initialize_settings($is_install = false) {
	global $__loaded_settings;
	if ($__loaded_settings) return false;
	$__loaded_settings = true;
	
	global $settings;
	require_once('includes/config-default.php');
	if ((!$is_install) && file_exists('config.php')) include_once('config.php');

	/* -- Tables -- */
	global $SETTINGS_TABLE, $USERS_TABLE, $URLS_TABLE, $LOG_TABLE;
	$SETTINGS_TABLE = $settings['dbprefix'] . 'settings';
	$USERS_TABLE = $settings['dbprefix'] . 'users';
	$URLS_TABLE = $settings['dbprefix'] . 'urls';
	$LOG_TABLE = $settings['dbprefix'] . 'log';
	
	/* -- */
	if ((!$is_install) && isset($settings['offline']) && $settings['offline']) :
		global $page;
		include('offline.php');
		die();
	endif;

	return $settings;
}


/* ----- Database stuff ----- */

global $db, $sql;
$db = false;
$sql = '';  // persist $sql for error and exception handlers

function initialize_db($load_settings = true) {
	global $db;
	if ($db === false) :
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
			false,
			false
			);
		unset($settings['dbpasswd']);  // hey... why not

		if ($load_settings) :
			global $SETTINGS_TABLE;
			$sql = 	"SELECT name, value FROM $SETTINGS_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array('userid' => 0));;
			$result = $db->sql_query($sql);	
			if ($result) :
				while ($row = $db->sql_fetchrow($result)) :
					$settings[$row['name']] = $row['value'];
				endwhile;
			endif;

			if (isset($settings['offline']) && $settings['offline']) :
				global $page;
				include('offline.php');
				die();
			endif;
		endif;
	endif;
	
	return $db;
}

function get_long($shortURL) {
	global $db, $sql, $URLS_TABLE;
	if (!$db) return false;
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
	return $ret;	
}

function get_short($longURL, $userid) {
	global $db, $sql, $URLS_TABLE;
	if (!$db) return false;
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
	return $ret;	
}

define('REGEX_ECMA_URL', '(^(mailto\:|((ht|f)tp(s?))\://){1}\S+)');

define('REGEX_PCRE_URL', '/^(mailto\:|((ht|f)tp(s?))\:\/\/){1}\S+/');
define('REGEX_PCRE_EMAIL', '/^[a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$/i');
define('REGEX_PCRE_EMAIL_RARE', "/^[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$/i");
define('REGEX_PCRE_DOMAIN', '/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i');

function validate_long($longURL, $userid = 0) {
	// if (substr($longURL, -1) == '/') $longURL = substr($longURL, 0, -1);
	$valid = preg_match(REGEX_PCRE_URL, $longURL); 
	if ($valid) :
		switch (true) :
			case (strpos($longURL, 'mailto:') === 0) : // starts with "mailto:"
				$s = substr($longURL, 7);
				$valid = preg_match(REGEX_PCRE_EMAIL, $s);
				if (!$valid) $valid = preg_match(REGEX_PCRE_EMAIL_RARE, $s);
				break;
				
			case (strpos($longURL, 'http://') === 0) : 
			case (strpos($longURL, 'ftp://') === 0) : 
			case (strpos($longURL, 'https://') === 0) : 
			case (strpos($longURL, 'ftps://') === 0) :
				$s = substr($longURL, strpos($longURL, '/', 1) + 2);
				$p = strpos($s, '/');
				if (!$p) $p = strpos($s, '?');
				if (!$p) $p = strpos($s, '#');
				if ($p) $s = substr($s, 0, $p);
				$valid = preg_match(REGEX_PCRE_DOMAIN, $s); 
				break;
				
			default :
				$valid = false;
		endswitch;
	endif;
	return $valid;
}

function validate_short($shortURL, $userid) {
	global $settings;
	@$length = intval($settings['shortminlength']);
	if ($length <= 0) $length = 4;
	$pattern = str_replace('4', $length, '/^.*(?=.{4,})[a-z0-9_]+$/i');
	$valid = preg_match($pattern, $shortURL);
	
	// TODO : Validate against reserved words, for version 0.9
	
	return $valid;
}

function generate_short($id) {
	global $settings;
	@$length = intval($settings['shortminlength']);
	if ($length <= 0) $length = 4;
	
	include_once('hashlib.php');
	
	$shortURL = hash_numeric($id, $length);
	$ok = false; $loop = 0;
	while ((!$ok) && ($loop < 10)) :
		$loop++;
		$r = get_long($shortURL);
		if (($r === false) || ($r['code'] == 200)) :
			if ($loop < 10) :
				$incr = pow(intval($loop / 4), 2);
				$shortURL = hash_random($length+$incr);
			endif;
		else : 
			$ok = true;
		endif;
	endwhile;
	if (!$ok) $shortURL = false;
	return $shortURL;	
}

function get_fav_icon($longURL) {
	@$domain = parse_url($longURL, PHP_URL_HOST);
	return T('URL_ICON_DATA', array('domain' => $domain));
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

function poke_message($msg, $level = '', $far = false) {
	if ($far) :
		if (!session_id()) session_start();
		if (!isset($_SESSION['messages'])) $_SESSION['messages'] = array();
		$_SESSION['messages'][$msg] = $level;
	else :
		global $messages;
		$messages[$msg] = $level;
	endif;
}

function poke_error($msg, $far = false) {
	poke_message($msg, MSG_ERROR, $far);
}

function poke_validation($msg, $far = false) {
	poke_message($msg, MSG_VALIDATION, $far);
}

function poke_warning($msg, $far = false) {
	poke_message($msg, MSG_WARNING, $far);
}

function poke_info($msg, $far = false) {
	poke_message($msg, MSG_INFO, $far);
}

function poke_success($msg, $far = false) {
	poke_message($msg, MSG_SUCCESS, $far);
}

function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $messages, $sql;
	$m = "PHP Error $errno: $errstr <i>in line</i> <code>$errline</code> <i>of file</i> <code>$errfile</code>";
	if (!empty($sql)) : $m .= "<br /><b>SQL:</b> <pre>$sql</pre>"; $sql = ''; endif;
	$m .= "\n";
	$messages[$m] = MSG_ERROR;
	return true;
}

function exception_handler($exception) {
	global $messages, $sql;
	$m = "Exception: " . $exception->getMessage(); 
	if (!empty($sql)) : $m .= "<br /><b>SQL:</b> <pre>$sql</pre>"; $sql = ''; endif;
	$m .= "\n";
	$messages[$m] = MSG_ERROR;
	return true;
}



/* ----- HTML 5 Support ----- */

global $__htmlver;
$__htmlver = false;

function __get_htmlver() {
	// User-Agent strings start with "Mozilla/5.0 {...}", 
	// the version string seems to be the HTML version supported
	// {unverified assumption}
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$i = strpos($ua, '/');
	if (!$i) return 1;
	$j = strpos($ua, ' ', $i);
	if (!$j) return 1;
	$u = substr($ua, $i+1, $j-$i-1 );
	if (($u >= 5) and (strpos($ua, 'MSIE ') !== false)) $u = 4;  // IE has crap support for HTML 5
	return intval($u);
}

/**
 * prints string based on HTML5 support 
 * @param string $str4 Fallback string
 * @param string $str5 String if User-Agent supports HTML 5.0 +
 */
function __($str4, $str5 = '', $return_only = false) {
	global $__htmlver;
	if (!$__htmlver) $__htmlver = __get_htmlver();
	if ($return_only) :
		return ($__htmlver >= 5) ? $str5 : $str4; 
	elseif ($__htmlver >= 5) : 
		print $str5; 
	else : 
		print $str4;
	endif;
}



/* ----- User Management ----- */


global $userid, $userlevel, $username, $__loaded_security;
$userid = 0;
$userlevel = -1;  // invalid
$username = '';
$__loaded_security = false;

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

function initialize_security($must_login = false) {
	global $__loaded_security;
	if ($__loaded_security) return true;  // already initialized
	$__loaded_security = true;
	
	global $db, $userid, $userlevel, $username, $settings;
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
						'lastvisiton' => microtime(true),
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
					$page['navigation'] = T('HELLO', array(
						'url' => $page['base_path'] . 'user.php',
						'username' => $username,
						));
					$page['navigation'] .= T('|');
					$page['navigation'] .= T('NOT_YOU', array(
						'url' => $page['base_path'] . 'logout.php',
						));
					if ($userlevel >= USER_LEVEL_LS) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('LIST_PAGE', array(
							'url' => $page['base_path'] . 'list.php',
							));
					endif;
					if ($userlevel >= USER_LEVEL_DS) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('ARCH_PAGE', array(
							'url' => $page['base_path'] . 'archives.php?',
							));
					endif; 
					if ($userlevel >= USER_LEVEL_AD) :
						$page['navigation'] .= T('|');
						$page['navigation'] .= T('ADMIN_PAGE', array(
							'url' => $page['base_path'] . 'admin.php',
							));
					endif; 
				endif;
				
				return true;
			else :
				// ignore TOKEN
			endif;
		else :
			// ignore TOKEN
		endif;
	endif;
	
	// find out if this is a submission from the login form
	if (isset($_REQUEST['username'])) :
		$ftoken = isset($_REQUEST['formtoken']) ? $_REQUEST['formtoken'] : false;
		if (!session_id()) session_start();
		$stoken = isset($_SESSION['ftoken']) ? $_SESSION['ftoken'] : -1; 
		if ($ftoken !== $stoken) :
			poke_error(T('FORM_TOKEN_MISMATCH'));
			return false;
		else :
			if (isset($_SESSION['ftoken'])) unset($_SESSION['ftoken']);  // clear token to protect against double submit			
		endif;
		
		$username = strtolower( $_REQUEST['username'] );
		if (empty($username)) :
			poke_validation(T('PROVIDE_USERNAME'));
		else :
			$passwd = (isset($_REQUEST['passwd']) && (!empty($_REQUEST['passwd']))) ? md5( $_REQUEST['passwd'] ) : '';
			$remember = isset($_REQUEST['remember']) ? ($_REQUEST['remember'] == 1) : $remember = false;
			
			if (!$db) $db = initialize_db();
		
			$sql = "SELECT id, passwd, token, enabled, userlevel, realname FROM $USERS_TABLE" .  
				" WHERE " . $db->sql_build_array('SELECT', array('username' => $username));
			$result = $db->sql_query($sql);	
			if ($result) :
				$row = $db->sql_fetchrow($result);
				if ($row) :
					if ($row['enabled'] == 1) :
						if (strcasecmp($passwd, $row['passwd']) == 0) :
							$userid = (int) $row['id'];
							$userlevel = (int) $row['userlevel'];
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
								include_once('includes/uuid.php');
								$guid = get_uuid();
							endif;
							$exp = $remember ? time() + 60*60*24*30 : 0;
							setcookie('token', $guid, $exp);
					
							$sql = "UPDATE $USERS_TABLE" .
								" SET " . $db->sql_build_array('UPDATE', array(
									'lastvisiton' => microtime(true),
									'token' => str_replace('-', '', $guid),
									)) .
								" WHERE " . $db->sql_build_array('SELECT', array('id' => $userid));
							$db->sql_query($sql);
							poke_success(T('LOGIN_SUCCESSFUL'), true);
							// if ($row['token'] != '') 
							//	poke_warning(T('LOGGED_OUT_OTHER_SESSIONS'), true);
							
							header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
							header('Expires: ' . date(DATE_RFC822));								
							header('Location: ' . $_SERVER['SCRIPT_NAME'] );
							die();
						else :
							// TODO : Count incorrect passwords and disable user access	
							poke_validation(T('PASSWORD_MISMATCH'));
						endif;
					else :
						poke_error(T('ACCESS_DENIED'));
					endif;
				else :
					poke_validation(T('USER_NOT_FOUND'));
				endif;
			else :
				poke_error('DATABASE_ERROR');
			endif;
		endif;
	endif;
	
	if ($must_login) :
		global $page, $messages;
		$page['content'] .= T('YOU_MUST_LOGIN', null, '<p class="loginrequired">', '</p>' . PHP_EOL);
		include('login.php');
		die();
	endif;

	return false;
}

/* ----- CDN ----- */

global $__yuicdn;
$__yuicdn = '';

function yuicdn($returnvalue = false) {
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
	return "\t<script type=\"text/javascript\" src=\"" . yuicdn(true) . "build/$file\"></script>\n";	
}

function loadjs($file) {
	global $page;
	return "\t<script type=\"text/javascript\" src=\"" . $page['base_path'] . "$file\"></script>\n";	
}
