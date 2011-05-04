<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 * @version    $Id$
 */

if (!defined('IN_CLICKIT')) die('Restricted');

define('REGEX_PCRE_URL', '/^(mailto\:|((ht|f)tp(s?))\:\/\/){1}\S+/');
define('REGEX_PCRE_EMAIL', '/^[a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$/i');
define('REGEX_PCRE_DOMAIN', '/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i');
define('REGEX_PCRE_USERNAME', '/\w/');  // '/[a-z0-9_]+/'
define('REGEX_PCRE_PASSWORD', '/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/');

/**
 * Test whether the proposed long url is valid
 * @param string $longURL
 * @param int $userid
 */
function validate_long($longURL, $userid = 0) {
	// if (substr($longURL, -1) == '/') $longURL = substr($longURL, 0, -1);
	$valid = preg_match(REGEX_PCRE_URL, $longURL);
	if ($valid) :
		switch (TRUE) :
			case (strpos($longURL, 'mailto:') === 0) : // starts with "mailto:"
				$s = substr($longURL, 7);
				$valid = preg_match(REGEX_PCRE_EMAIL, $s);
				break;

			case (strpos($longURL, 'http://') === 0) :
			case (strpos($longURL, 'ftp://') === 0) :
			case (strpos($longURL, 'https://') === 0) :
			case (strpos($longURL, 'ftps://') === 0) :
				$s = substr($longURL, strpos($longURL, '/', 1) + 2);
				$p = strpos($s, '/');
				if (!$p) $p = strpos($s, '?');
				if (!$p) $p = strpos($s, '#');
				if ($p) $s = substr($s, 0, $p);
				$valid = preg_match(REGEX_PCRE_DOMAIN, $s);
				break;

			default :
				$valid = FALSE;
		endswitch;
	endif;
	return $valid;
}

/**
 * Test whether the proposed short bit is valid
 * @param unknown_type $shortURL
 * @param unknown_type $userid
 */
function validate_short($shortURL, $userid) {
	global $settings;
	@$length = intval($settings['shortminlength']);
	if ($length <= 0) $length = 4;
	$pattern = str_replace('4', $length, '/^.*(?=.{4,})[a-z0-9_]+$/i');
	$valid = preg_match($pattern, $shortURL);

	// TODO : VALIDATION : Validate against existing filenames, for version 0.9
	// TODO : VALIDATION : Validate against reserved/bad words, for version 0.9

	return $valid;
}

/**
 * Test to see it username complies to format and duplication
 * @param string $username
 */
function check_new_username($username) {
	if (empty($username)) return 412; // precondition failed
	if (!preg_match(REGEX_PCRE_USERNAME, $username)) return 406; // not acceptable

	global $db, $sql, $USERS_TABLE;
	if (!$db) return 500;  // internal error
	$sql = "SELECT COUNT(*) AS cnt FROM $USERS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('username' => $username));
	$result = $db->sql_query($sql);
	$rows = (int) $db->sql_fetchfield('cnt');
	$db->sql_freeresult($result);
	if ($rows > 0) return 409; // conflict

	return 200;
}

/**
 * Test to see if email complies to format and duplication
 * @param string $email
 */
function check_email($email) {
	if (empty($email)) return 412; // precondition failed
	if (!preg_match(REGEX_PCRE_EMAIL, $email)) return 406; // not acceptable

	global $db, $sql, $USERS_TABLE;
	if (!$db) return 500;  // internal error
	$sql = "SELECT COUNT(*) AS cnt FROM $USERS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('email' => $email));
	$result = $db->sql_query($sql);
	$rows = (int) $db->sql_fetchfield('cnt');
	$db->sql_freeresult($result);
	if ($rows > 0) return 409; // conflict

	return 200;
	}

/**
 * Test to see if password complies to format
 * @param string $passwd
 */
function check_password($passwd) {
	if (!preg_match(REGEX_PCRE_PASSWORD, $passwd)) return 406; // not acceptable
	return 200;
}
