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

global $lang;
$lang = array();

function initialize_lang() {
	global $settings, $lang;
	if (isset($settings['func_lang']) && (!empty($settings['func_lang'])) && file_exists('lang/' . $settings['func_lang']))
		include('lang/' . $settings['func_lang']);
}

function T($str, $params = NULL, $prefix = '', $suffix = '') {
	global $lang;
	if (isset($lang[$str])) :
		if (is_array($lang[$str])) :
			$txt = '';
			if (($prefix == '') && ($suffix == '')) $suffix = ' ';
			foreach ( $lang[$str] as $s ) $txt .= $prefix . $s . $suffix;
		else :
			$txt = $prefix . $lang[$str] . $suffix; 
		endif;
	else :
		$txt = $prefix . str_replace('_', ' ', $str) . $suffix;
	endif;

	if (is_array($params)) :
		if (isset($lang[$str])) :
			$ss = array();
			$rr = array();
			foreach ( $params as $s => $r ) :
				$ss[] = '*|' . $s . '|*';
				$rr[] = $r;
			endforeach;
			$txt = str_replace($ss, $rr, $txt);
		else :
			foreach ( $params as $s => $r ) :
				$txt .= ', ' . $s . ' => ' . $r;
			endforeach;
		endif;  
	endif;

	return $txt;
}

function P($str, $params = NULL, $prefix = '', $suffix = '') {
	print T($str, $params, $prefix, $suffix);
}

?>
