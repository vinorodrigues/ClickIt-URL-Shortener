<?php
/**
 * @copyright  Andrew Moore
 */

define('NS_DNS',     '6ba7b810-9dad-11d1-80b4-00c04fd430c8');  // FQDN
define('NS_URL',     '6ba7b811-9dad-11d1-80b4-00c04fd430c8');  // URL
define('NS_ISO_OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8');  // ISO OID
define('NS_X500_DN', '6ba7b814-9dad-11d1-80b4-00c04fd430c8');  // X.500 DN (in DER or a text output format))

function is_valid_uuid($uuid) {
	return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
}

function get_uuid3($namespace = NS_ISO_OID, $name = '') {
	if (!is_valid_uuid($namespace)) return FALSE;

	// Get hexadecimal components of namespace
	$nhex = str_replace(array('-','{','}'), '', $namespace);

	// Binary Value
	$nstr = '';

	// Convert Namespace UUID to bits
	for($i = 0; $i < strlen($nhex); $i+=2) {
		$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
	}

	// Calculate hash value
	$hash = md5($nstr . $name);

	// Format and return UUID
	return sprintf('%08s-%04s-%04x-%04x-%12s',
		// 32 bits for "time_low"
		substr($hash, 0, 8),
		// 16 bits for "time_mid"
		substr($hash, 8, 4),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 3
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
		// 48 bits for "node"
		substr($hash, 20, 12)
		);
}

function get_uuid4() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
}

function get_uuid4alt() {
	$u = uniqid();
	return sprintf('%s-%s-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		substr($u, 0, 8),
		// 16 bits for "time_mid"
		substr($u, -4, 4),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
}

function get_uuid5($namespace = NS_ISO_OID, $name = '') {
	if (!is_valid_uuid($namespace)) return FALSE;

	// Get hexadecimal components of namespace
	$nhex = str_replace(array('-','{','}'), '', $namespace);

	// Binary Value
	$nstr = '';

	// Convert Namespace UUID to bits
	for($i = 0; $i < strlen($nhex); $i+=2) {
		$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
	}

	// Calculate hash value
	$hash = sha1($nstr . $name);

	return sprintf('%08s-%04s-%04x-%04x-%12s',
		// 32 bits for "time_low"
		substr($hash, 0, 8),
		// 16 bits for "time_mid"
		substr($hash, 8, 4),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 5
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
		// 48 bits for "node"
		substr($hash, 20, 12)
		);
}

function get_uuid() {
	global $get_uuid_func;
	if (!isset($get_uuid_func)) $get_uuid_func = 'get_uuid4alt'; 
	return call_user_func($get_uuid_func);
}

global $get_uuid_func;

?>
