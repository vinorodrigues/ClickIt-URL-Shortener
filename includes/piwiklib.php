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

// XXX : PIWIKLIB : Support for Attribution Info, '_rcn', '_rck', '_refts', '_ref'
// XXX : PIWIKLIB : Support for visitor Custom Var, '_cvar' (json_encode);
// XXX : PIWIKLIB : Support for Custom Data, 'data' (json_encode);
// XXX : PIWIKLIB : Support for forced Datetime, 'cdt'

define('PIWIK_GIF_FILE', 'piwik.php');
define('PIWIK_TIMEOUT', 10);

if (!isset($GLOBALS['curl_config'])) $GLOBALS['curl_config'] = array();
if (false) $GLOBALS['piwik_host'] = '';  // remain unset, singleton
if (false) $GLOBALS['piwik_site_id'] = '';  // remain unset, singleton
if (false) $GLOBALS['piwik_visitor_id'] = array(); // remain unset, singleton

function piwik_set_host($host_url, $site_id = NULL) {
	global $piwik_host, $piwik_site_id;
	$piwik_host = $host_url;
	if (isset($site_id) && ($site_id > 0)) $piwik_site_id = $site_id;
}

function piwik_set_site_id($site_id) {
	global $piwik_site_id;
	$piwik_site_id = $site_id;
}

function piwik_track_page_view($page_title = NULL) {
	global $piwik_host, $piwik_site_id;
	if (!isset($piwik_host) || empty($piwik_host)) return !trigger_error('piwik_host not set', E_USER_ERROR);
	if (!isset($piwik_site_id) || empty($piwik_site_id)) return !trigger_error('piwik_site_id not set', E_USER_ERROR);

	if (isset($page_title) && (!empty($page_title))) :
		$data = array('action_name' => $page_title);
	else :
		$data = NULL;
	endif;
	return _piwik_http_post($piwik_host, $piwik_site_id, $data);
}

function _piwik_rnd() {
	return rand(1000000000, 0x7fffffff);
}

function _piwik_get_this_server() {
	global $__piwik_this_server;  // singleton
	if (isset($__piwik_this_server)) return $__piwik_this_server;

	$__piwik_this_server = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$__piwik_this_server .= $_SERVER['SERVER_NAME'];
	$__port = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 443 : 80;
	$__piwik_this_server .= (intval($_SERVER['SERVER_PORT']) != $__port) ? ':'.$_SERVER['SERVER_PORT'] : '';
	return $__piwik_this_server;
}

function _piwik_get_cookie($name, $site_id) {
	// Piwik cookie names have dot separators in piwik.js, but PHP uses underscores
	// See: http://www.php.net/manual/en/language.variables.predefined.php#72571
	$pattern = '/^_pk_' . $name . '_' . $site_id . '/';
	foreach ($_COOKIE as $cookie => $value) :
		if (preg_match($pattern, $cookie))
			return $value;
	endforeach;
	return FALSE;
}

function _piwik_get_user_id($site_id = NULL) {
	if (isset($site_id) && (!empty($site_id))) $id = $site_id;
	if (!isset($id) && isset($piwik_site_id)) $id = $piwik_site_id;
	if (!isset($id) || empty($id)) return !trigger_error('piwik_site_id not set', E_USER_ERROR);

	global $piwik_visitor_id;
	if (!isset($piwik_visitor_id)) $piwik_visitor_id = array();  // singleton
	if (isset($piwik_visitor_id[$id])) return $piwik_visitor_id[$id];

	$cookie = _piwik_get_cookie('id', $id);
	if ($cookie !== FALSE) $piwik_visitor_id[$id] = substr($cookie, 0, strpos($cookie, '.'));

	// last resort; make one up, and keep it in session
	if (!isset($piwik_visitor_id[$id])) :
		if (!session_id()) session_start();
		if (!isset($_SESSION['_pk_visitor_id_' . $id]))
			$_SESSION['_pk_visitor_id_' . $id] = substr(_piwik_rnd() . _piwik_rnd(), 0, 16);
		$piwik_visitor_id[$id] = $_SESSION['_pk_visitor_id_' . $id];
	endif;

	return $piwik_visitor_id[$id];
}

function _piwik_get_http_referer() {
	return (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
}

function _piwik_get_user_agent() {
	return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Piwik/PHP';
}

function _piwik_get_language() {
	return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en';
}

/**
 * Generate GET query string
 * @param string $piwik_host
 * @param int $site_id
 * @param array $data
 *
 * $data in the format:
 * 'idsite' => site id
 * 'rec' => bool, record visit, default = TRUE
 * 'apiv' => always 1, cannot overide
 * 'rand' => random number cache buster, cannot override
 * 'cip' => IP address of caller, default from $_SERVER['REMOTE_ADDR']
 * 'cid' => override visitor id,
 * or '_id' => id recovered from cookie
 * 'token_auth' => auth token for super user, needed for cip, cid, cdt
 * 'url' => URL of tracked page, default from $_SERVER['REQUEST_URI']
 * 'urlref' => Referer, default from $_SERVER['HTTP_SERVER']
 * 'action_name' => Track Page View title
 */
function _piwik_build_query($site_id, $data = NULL) {
	// don't urlencode() anything - http_build_query will do that!
	$query = array(
		'idsite' => $site_id,
		'rec' => 1,
		'apiv' => 1,
		'rand' => '0.' . _piwik_rnd(),
		'_id' => _piwik_get_user_id($site_id),
		'cip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
		'url' => _piwik_get_this_server() .
			(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
		'urlref' => _piwik_get_http_referer(),
		// 'cookie' => 1,  // XXX : PIWIKLIB : Cookies?
		);

	if (isset($data) && is_array($data)) :
		if (isset($data['idsite'])) $query['idsite'] = $data['idsite'];
		if (isset($data['rec']) && $data['rec']) unset($query['rec']);
		if (isset($data['cip'])) $query['cip'] = $data['cip'];
		if (isset($data['cid'])) : $query['cid'] = $data['cid']; unset($query['_id']); endif;
		if (isset($data['token_auth'])) $query['token_auth'] = $data['token_auth'];
		if (isset($data['url'])) $query['url'] = $data['url'];
		if (isset($data['urlref'])) $query['urlref'] = $data['urlref'];
		if (isset($data['action_name'])) $query['action_name'] = $data['action_name'];
	endif;

	return http_build_query($query);
}

function _piwik_http_post($piwik_host, $site_id, $data) {
	$m = 'fsockopen';  // fallback, slowest
	if (function_exists('curl_init') && function_exists('curl_exec')) {
		$m = 'curl';  // fastest
	} elseif (function_exists('fopen') && ini_get('allow_url_fopen'))
		$m = 'fopen';  // okayish

	return call_user_func('_piwik_http_post_' . $m, $piwik_host, $site_id, $data);
}

function _piwik_http_post_curl($piwik_host, $site_id, $data) {
	// This code adapted from "legacy" (J. Esteban Acosta Villafane)
	// http://pldleague.com/code/server-side-coding/recaptcha-php-library-with-curl-support/

	$add_headers = array(
		"Host: " . parse_url($piwik_host, PHP_URL_HOST),
		// "Connection: close",
		"Accept-Language: " . _piwik_get_language(),
		// "Cookie: ",  // XXX : PIWIKLIB : Cookies?
		);

	// merge config array - don't use array_merge as CURLOPT_xx are numeric
	$__config = array(
		// CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_CONNECTTIMEOUT => PIWIK_TIMEOUT,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
		CURLOPT_USERAGENT => _piwik_get_user_agent(),
		CURLOPT_REFERER => isset($data['url']) ? $data('url') : _piwik_get_http_referer(),
		CURLOPT_HEADER => TRUE,
		);
	// with provided overrides
	foreach ($GLOBALS['curl_config'] as $n => $v) :
		$__config[$n] = $v;
	endforeach;

	$url = rtrim($piwik_host, '/') . '/' . PIWIK_GIF_FILE;
	$ch = curl_init( $url . '?' . _piwik_build_query($site_id, $data) );
	curl_setopt_array( $ch , $__config );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $add_headers );

	$response = curl_exec( $ch );
	curl_close($ch);

	if ( $response === false ) {
		trigger_error('Error connecting to ' . $piwik_host . '.', E_USER_ERROR);
		return false;
	}

	$response = explode("\r\n\r\n", $response, 2);  // seperate headers from content

	return $response;
}

function _piwik_http_post_fopen($piwik_host, $site_id, $data) {
	// This code adapted from Wez Furlong
	// http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
	// and PHP Manual
	// http://www.php.net/manual/en/context.http.php

	$optional_headers = "Host: " . parse_url($piwik_host, PHP_URL_HOST) . "\r\n";
	// $optional_headers .= "Connection: close";
	$optional_headers .= "User-Agent: " . _piwik_get_user_agent() . "\r\n";
	$optional_headers .= "Referer: " . (isset($data['url']) ? $data('url') : _piwik_get_http_referer()) . "\r\n";
	$optional_headers .= "Accept-Language: " . _piwik_get_language() . "\r\n";
	// $optional_headers .=  "Cookie: ";  // XXX : PIWIKLIB : Cookies?

	$params = array(
		'http' => array(
			'method' => 'GET',
			'header' => $optional_headers,
			'timout' => PIWIK_TIMEOUT,
            )
		);

	$url = rtrim($piwik_host, '/') . '/' . PIWIK_GIF_FILE;
	$ctx = stream_context_create($params);

	$handle = @fopen($url . '?' . _piwik_build_query($site_id, $data), 'rb', false, $ctx);
	if (!$handle) {
		trigger_error("Problem with $url, $php_errormsg", E_USER_ERROR);
		return false;
	}

	$response = @stream_get_contents($handle);
	@fclose($handle);

	if ($response === false)
		trigger_error("Problem reading data from $url, $php_errormsg", E_USER_ERROR);

	// fopen on returns the content part, so create an array so that the
	// response is at array index [1]
	$response = array('', $response);

	return $response;
}

function _piwik_http_post_fsockopen($piwik_host, $site_id, $data) {
	// breakout host details
	$svr = parse_url($piwik_host);

	$http_request  = "GET " . $svr['path'] . '/' . PIWIK_GIF_FILE . '?' .
		_piwik_build_query($site_id, $data) . " HTTP/1.0\r\n";

	$http_request .= "Host: " . $svr['host'] . "\r\n";
	// $http_request .= "Connection: close";
	$http_request .= "User-Agent: " . _piwik_get_user_agent() . "\r\n";
	$http_request .= "Referer: " . (isset($data['url']) ? $data('url') : _piwik_get_http_referer()) . "\r\n";
	$http_request .= "Accept-Language: " . _piwik_get_language() . "\r\n";
	// $http_request .=  "Cookie: ";  // XXX : PIWIKLIB : Cookies?

	$http_request .= "\r\n";  // double CRLF

	var_dump($http_request);

	$response = '';  $errno = NULL;  $errstr = NULL;
	if ( false == ( $fs = @fsockopen(
			$svr['host'],
			(isset($svr['port']) ? $svr['port'] : 80),  // TODO : PIWIKLIB : Handle SSL / HTTPS
			$errno,
			$errstr,
			PIWIK_TIMEOUT) ) ) {
		trigger_error("Could not open socket: $errno - $errstr", E_USER_ERROR);
		return false;
	}

	fwrite($fs, $http_request);

	while ( !feof($fs) )
		$response .= fgets($fs, 1160); // One TCP-IP packet
	fclose($fs);
	$response = explode("\r\n\r\n", $response, 2);

	return $response;
}

function piwik_get_html($host_url = NULL, $site_id = NULL) {
	global $piwik_host, $piwik_site_id;
	if (isset($host_url) && (!empty($host_url))) $host = $host_url;
	if (!isset($host) && isset($piwik_host)) $host = $piwik_host;
	if (!isset($host) || empty($host)) return !trigger_error('piwik_host not set', E_USER_ERROR);
	if (isset($site_id) && (!empty($site_id))) $id = $site_id;
	if (!isset($id) && isset($piwik_site_id)) $id = $piwik_site_id;
	if (!isset($id) || empty($id)) return !trigger_error('piwik_site_id not set', E_USER_ERROR);

	$host_a = parse_url($host);
	$host = rtrim('http://' . $host_a['host'] .
		(isset($host_a['port']) ? ':' . $host_a['port'] : '') .
		$host_a['path'], '/') . '/';
	$host_s = rtrim('https://' . $host_a['host'] .
		$host_a['path'], '/') . '/';
	unset($host_a);

	return "<!-- Piwik -->
<script type=\"text/javascript\">//<![CDATA[
var pkBaseURL = (('https:' == document.location.protocol) ? '$host_s' : '$host');
document.write(unescape('%3Cscript src=\"' + pkBaseURL + 'piwik.js\" type=\"text/javascript\"%3E%3C/script%3E'));
//]]></script>
<script type=\"text/javascript\">//<![CDATA[
try {
  var piwikTracker = Piwik.getTracker(pkBaseURL + 'piwik.php', $id);
  piwikTracker.trackPageView();
  piwikTracker.enableLinkTracking();
} catch( err ) {}
//]]></script>
<noscript><img src=\"" . $host . "piwik.php?idsite=$id\" style=\"border:0\" alt=\"\" /></noscript>
<!-- End Piwik Tracking Code -->
";
}

?>
