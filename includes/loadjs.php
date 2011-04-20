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

@$fn = $_REQUEST['f'];
if (file_exists($fn)) :
	$ts = time();
	$inc = 31*24*60*60;  // 31 days

	header('Content-Type: text/javascript');
	header('Content-Disposition: filename="' . basename($fn) . '"');
	
	header("Expires: " . date(DATE_RFC822, $ts+$inc) );
	header("Last-Modified: " . date(DATE_RFC822, $ts) );
	header("Cache-Control: max-age=$inc, must-revalidate" );

	$handle = fopen($fn, "r");
	$contents = fread($handle, filesize($fn));
	fclose($handle);
	
	$needle = array();
	$replace = array();
	foreach ($_REQUEST as $name => $value)
		if ($name != 'f') :
			$needle[] = strtoupper($name);
			$replace[] = $value;
		endif;

	// Minify JS using JSMin - http://code.google.com/p/jsmin-php 
	if ((strpos($fn, '-min') === false) && file_exists('~jsmin.php')) :
		include_once('~jsmin.php');
		$contents = '/* Minified */' . JSMin::minify($contents);
	endif;
		
	print str_replace($needle, $replace, $contents);
else :
	die('Declined');
endif;
