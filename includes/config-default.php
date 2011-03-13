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

if (!defined('IN_CLICKIT')) die('Restricted');

/* ----- General settings (comments tell you what they do) ----- */

// Set to true to disable site, no... not here! In config.php
$settings['offline']	= false;

// Set this to browscap.php if your server supports it
// See http://php.net/manual/en/function.get-browser.php
$settings['getbrowser']	= 'browser/chrisschuld.php';
//$settings['getbrowser']	= 'browscap.php';

// c1k.it use HTTP error 307 (Temporary redirect),
// set this value to true to force HTTP error 302 (Moved temporarily)
$settings['force302'] = false;

/* ----- Database Settings (overrides set by install.php in /config.php) ----- */

$settings['dbms']		= 'mysqli';
$settings['dbhost']		= 'localhost';
$settings['dbuser']		= 'root';
$settings['dbpasswd']	= '';
$settings['dbname']		= 'clickit';
$settings['dbport']		= '';
$settings['dbprefix']	= 'c1k_';

?>
