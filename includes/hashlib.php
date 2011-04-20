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

global $hashbase, $hashhash;

$hashbase = '0123456789cdfghkmnpqrtvwxy';  // BASE26 :)

# Do not remove the comment lines below with sqare braces,
# they act as find markers in the __self_generate() function
# [[[[[
$hashhash = array(
	'7cw3gvxrqp024fntm6ky89d15h',
	'mrc02ftw7xk614phyqdgv835n9',
	'8324795c0ypngfm1xwtvhkr6qd',
	'x20vcydt59m1fkp7rwng634h8q',
	'mntqyr0xhp89cv2d64g13k57wf',
	'g7v8620cpfdqwrmy39h41kn5tx',
	'p4wk0xvnt8hgq391crmy67f5d2',
	'58wvx497k23p16yncfgmhqr0td',
	'dpw2cgn14r7mxyhq9k0v8356ft',
	'xmk0ftqv38crh54ny9gdwp7216',
	);
# ]]]]]

/*
Why some letter are omitted
---------------------------
A vowel 
B looks like 8
E vowel
I vowel, looks like L(l), or 1
J looks like I(i) or G
L looks like 1 or i
O vowel
S looks like 5
U vowel, looks like V
Z looks like 2
*/
	
function hash_numeric($in, $length = 0) {
	if (!is_numeric($in)) trigger_error('Non numeric value ' . $in, E_USER_ERROR);

	global $hashhash;
	$index = $hashhash[$in % 10];
	if ($length > 1) $in = $in + pow(strlen($index), $length-1); 
	
	if ($in === 0) :
		$out = substr($index, 0, 1);
	else :
		$base  = strlen($index);
		$out = "";
		for ($t = floor(log($in, $base)); $t >= 0; $t--) :
			$bcp = bcpow($base, $t);
			$a   = floor($in / $bcp) % $base;
			$out = $out . substr($index, $a, 1);
			$in  = $in - ($a * $bcp);
    	endfor;
    endif;
    if ($length > 0) $out = str_pad($out, $length, substr($index, 0, 1), STR_PAD_LEFT);
	return strrev($out);  // reverse
}

function hash_random($length) {
	global $hashbase;
	if ($length < 1) $length = 1;
	$out = '';
	for ($i = 0; $i < $length; $i++ ) $out .= substr($hashbase, rand(0, strlen($hashbase)-1), 1);
	return $out;  
}
	
function __self_generate($hashbase) {
	$len = strlen($hashbase);
	$s = "\n\$hashhash = array(\n";

	for ($d = 1; $d <= 10; $d++) :
		for ($i = 0; $i <= rand(10000, 100000); $i++) :
			$a = rand(0, $len-1);
			$b = rand(0, $len-1);
			if ($a != $b) :
				$aa = substr($hashbase, $a, 1);
				$bb = substr($hashbase, $b, 1);
				$hashbase = substr_replace( substr_replace($hashbase, $bb, $a, 1), $aa, $b, 1);
			endif;
		endfor;
		$s .= "\t'$hashbase',\n";
	endfor;

	$s .= "\t);\n";
	
	print "<pre>$s</pre>";
	
	$handle = fopen(__FILE__, "r");
	$contents = fread($handle, filesize(__FILE__));
	fclose($handle);
	
	$a = strpos($contents, str_pad('', 5, '[')) + 5;
	$b = strpos($contents, str_pad('', 5, ']')) - 2;
	
	if (($a > 0) && ($b > $a)) :
	
		$aa = substr($contents, 0, $a);
		$bb = substr($contents, $b);
	
		$handle = fopen(__FILE__, "w+");
		fwrite($handle, $aa);
		fwrite($handle, $s);
		fwrite($handle, $bb);
		fclose($handle);
	
		print 'OK';
	else :
		print 'ERROR';
	endif;
}	

/**
 * If you run this file standalone it will regenerate it's own hash table.
 * (You must have local write access to this file.)
 * To run execute from the command line:  php haslib.php
 */
$included = strtolower(realpath(__FILE__)) != strtolower(realpath($_SERVER['SCRIPT_FILENAME']));
if (!$included) __self_generate($hashbase);
