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

/**
 * Returns array of browser information
 */
function _get_browser($user_agent = '') {
	$old_error_reporting = error_reporting();
	error_reporting(E_ERROR);
	
	$data = get_browser($user_agent, true);
	
	error_reporting($old_error_reporting);
	
	// Some host providers do not switch on their BROWSCAP support on,
	// so this will not work,
	// in thoes cases report that there is an error.
	if (!$data) :
		$data['browser'] = 'BROWSCAP';
		$data['version'] = '';
		$data['platform'] = 'ERROR';
	endif;
	
	return $data;
}

?>