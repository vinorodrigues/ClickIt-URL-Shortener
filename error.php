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

$errors = Array(
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
  
if (!isset($e)) :
	if (isset($_REQUEST['e'])) :
		$e = intval(trim($_REQUEST['e']));
	else:
		$e = 400;
	endif;
endif;
if (!isset($errors[$e])) $e = 400;

header('HTTP/1.0 ' . $e . ' ' . $errors[$e], true, $e);

$head_title = "Error $e - $errors[$e]";
$title = "<span class=\"red\">HTTP Error:</span> $e";
$content = "<p>$errors[$e]</p>";
if (isset($m) && !empty($m)) $content .= "\n<hr />$m";

include('includes/' . TEMPLATE . '.php');
