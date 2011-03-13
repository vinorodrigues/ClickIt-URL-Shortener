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
		
if (!file_exists('config.php')) :
	if (file_exists('install.php')) :	
		include('install.php');
		exit;
	else :
		$e = 501;
		$m = 'Neither configuration nor installation files exist';
		include('error.php'); 
		die();
	endif;
endif;
	
load_settings();

if (isset($settings['offline']) && $settings['offline']) :
	include('index-offline.php');
	exit;
endif;

?>

<?php 
	// TODO : for version 0.2 - show a proper homepage
	include('index-offline.php');
