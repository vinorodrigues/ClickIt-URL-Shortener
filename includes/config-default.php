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

// Mail from address
$settings['webmaster'] = 'webmaster@c1k.it';

// Set to true to disable site, no... not here! In config.php
$settings['offline'] = false;

// Set this to browscap.php if your server supports it
// See http://php.net/manual/en/function.get-browser.php
$settings['func_getbrowser'] = 'browser/chrisschuld.php';
//$settings['func_getbrowser'] = 'browscap.php';

// Language file to load
$settings['func_lang'] = 'en.php';

// YUI3 CDN - options are yahoo, google and local (default)
$settings['cdn'] = 'local';

// c1k.it use HTTP error 307 (Temporary redirect),
// set this value to true to force HTTP error 302 (Moved temporarily)
$settings['force302'] = false;

// set to true to disable anonymouse user access
$settings['mustlogin'] = true;

// Default minimum length of a short URL
$settings['shortminlength'] = 4;

// mata tags, self explanatory
$settings['meta_description'] = "c1k.it is a URL Shortening service, or URL shortener, hosted by Tecsmith.com.au for it's internet marketing clients";
$settings['meta_keywords'] = "URL shortening,URL shortener,Clean URL,Link rot,Semantic URL,URL redirection,Vanity domain,Vanity URL,internet marketing,online marketing,marketing";

/* ----- Database Settings (overrides set by install.php in /config.php) ----- */

$settings['dbms']     = 'mysqli';
$settings['dbhost']   = 'localhost';
$settings['dbuser']   = 'root';
$settings['dbpasswd'] = '';
$settings['dbname']   = 'clickit';
$settings['dbport']   = '';
$settings['dbprefix'] = 'c1k_';

?>
