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

/**
 * Returns array of browser information
 */
function _get_browser($user_agent = '') {
	include_once('browser.php');
		
	$browser = new Browser();
	if (!empty($user_agent))
		$browser->setUserAgent($user_agent);
		
	$ret = array();
	$ret['browser'] = $browser->getBrowser();
	$ret['version'] = $browser->getVersion();
	$ret['platform'] = $browser->getPlatform();
	
	return $ret;
}	
