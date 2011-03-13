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
define('CLICKIT_VER', '0.1 Beta');
$clickit_build = '$Id$';
define('TEMPLATE', 'template');

/* ----- Template Related ----- */

global $head_title, $base_path, $logged_in, $logo, $site_name, $site_slogan, $title, $content;
$head_title = 'C1K.IT';
$base_path = '';
// $base_path .= (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
// $base_path .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];
$base_path .= pathinfo($_SERVER["PHP_SELF"], PATHINFO_DIRNAME) . '/' ;
$logged_in = false;
$logo = 'images/logo.png';
$site_name = 'C1K.IT';
$site_slogan = '';
$title = $head_title;
$head = "\t<meta name=\"description\" content=\"C1K.IT (ClickIt) is a URL Shortening service, or URL shortener, hosted by Tecsmith.com.au for it's internet marketing clients\" />\n"; 
$head .= "\t<meta name=\"keywords\" content=\"URL shortening,URL shortener,Clean URL,Link rot,Semantic URL,URL redirection,Vanity domain,Vanity URL,internet marketing\" />\n";

/* ----- phpBB's dbal stuff ----- */

define('IN_PHPBB', true);
global $phpbb_root_path, $phpEx;   
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

/* ----- Settings array ----- */

global $settings;
$settings = Array('version' => CLICKIT_VER);

/**
 * Loads the settings files
 * @param boolean $loaduserset
 */
function load_settings($loaduserset = true) {
	global $settings;
	require_once('includes/config-default.php');
	if ($loaduserset && file_exists('config.php')) include_once('config.php');

	/* -- Tables -- */
	global $SETTINGS_TABLE, $USERS_TABLE, $URLS_TABLE, $LOG_TABLE;
	$SETTINGS_TABLE = $settings['dbprefix'] . 'settings';
	$USERS_TABLE = $settings['dbprefix'] . 'users';
	$URLS_TABLE = $settings['dbprefix'] . 'urls';
	$LOG_TABLE = $settings['dbprefix'] . 'log';
}

/* ----- Error handling ----- */

global $m;  $m = '';  // Error messages
global $sql;  $sql = '';  // persist $sql for error and exception handlers

function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $m, $sql;
	$m .= "PHP Error $errno: $errstr <i>in line</i> <code>$errline</code> <i>of file</i> <code>$errfile</code><br />";
	if (!empty($sql)) $m .= "<b>SQL:</b> <pre>$sql</pre><br />";
	$m .= "<br />\n";
	return true;
}

function exception_handler($exception) {
	global $m, $sql;
	$m .= "Exception: " . $exception->getMessage() . "<br />"; 
	if (!empty($sql)) $m .= "<b>SQL:</b> <pre>$sql</pre><br />";
	$m .= "<br />\n";
	return true;
}
