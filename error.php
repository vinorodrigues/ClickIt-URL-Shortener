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
include_once('includes/lang.' . $phpEx);

// don't call initialize_settings(), just do the simple config inclusion
require_once('includes/config-default.' . $phpEx);
if (file_exists('config.' . $phpEx)) include_once('config.' . $phpEx);

initialize_lang();

$errors = array(
	300 => "Info Multiple Choices",
	301 => "Moved Permanently",
	302 => "Found",
	303 => "See Other",
	304 => "Not Modified",
	305 => "Use Proxy",
	307 => "Temporary Redirect",
	400 => "Bad Request",
	401 => "Unauthorized",
	402 => "Payment Required",
	403 => "Forbidden",
	404 => "Not Found",
	405 => "Method Not Allowed",
	406 => "Not Acceptable",
	407 => "Proxy Authentication Required",
	408 => "Request Timeout",
	409 => "Conflict",
	410 => "Gone",
	411 => "Length Required",
	412 => "Precondition Failed",
	413 => "Request Entity Too Large",
	414 => "Request-URI Too Large",
	415 => "Unsupported Media Type",
	416 => "Requested Range Not Satisfiable",
	417 => "Expectation Failed",
	500 => "Internal Server Error",
	501 => "Not Implemented",
	502 => "Bad Gateway",
	503 => "Service Unavailable",
	504 => "Gateway Timeout",
	505 => "HTTP Version not supported",
	);
  
if (!isset($e)) :  // may come from include, so $e may already be set
	if (isset($_REQUEST['e'])) :
		$e = intval(trim($_REQUEST['e']));
	else:
		$e = 400;
	endif;
endif;

$err = 'STATUS_' . $e;
if (isset($lang[$err])) :
	if (is_array($lang[$err])) :
		$err = '';
		foreach ( $lang[$err] as $s ) $err .= $s . '<br />';
	else :
		$err = $lang[$err]; 
	endif;
else :
	if (isset($errors[$e])) :
		$err = $errors[$e];
	else :
		$err = '';
	endif;
endif; 

header('HTTP/1.0 ' . $e . ' ' . $err, TRUE, $e);

$page['head_title'] = empty($err) ? "Error $e" : "Error $e - $err";
$page['title'] = "<span class=\"red\">HTTP Error:</span> $e";
if (!empty($err)) $page['content'] = "<p>$err</p>";
include('includes/' . TEMPLATE . '.' . $phpEx);
