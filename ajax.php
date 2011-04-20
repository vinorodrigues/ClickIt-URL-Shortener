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

/*
 * Usage:
 *   ?f=get_long&q=shortBit
 *   ?f=get_short&q=longURL
 *   ?f=test_long&q=longURL
 *   ?f=test_short&q=shortBit
 *   
 * You also need to append in a locked system:
 *   &u=username
 *   &k=apikey
 */

@$func = strtolower( $_REQUEST['f'] );
@$qry = $_REQUEST['q'];
/* @$username = strtolower( $_REQUEST['u'] );  
@$key = $_REQUEST['k']; */  // TODO : API KEY  

require_once('includes/library.php');
require_once('includes/lang.php');

define('EMPTY_HTML', '<span class="empty"></span>');

initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security();
// TODO : Parse APIKEY token, for version 0.9

if (empty($qry)) :
	$code = 400;
	$data = T('MISSING_QUERY_STRING');
	$html = EMPTY_HTML;
else :
	switch ($func) :
		case '' :
			$code = 400;
			$data = T('MISSING_FUNCTION_NAME');
			$html = EMPTY_HTML;
			break;

		case 'test_long' :
			if (substr($qry, -1) == '/') $qry = substr($qry, 0, -1);
			if (validate_long($qry, $userid)) :
				$ret = get_short($qry, $userid);
				if (!$ret) :
					$code = 500;  // Internal Server Error
					$data = T('CANNOT_CONNECT');
					$html = EMPTY_HTML;
				elseif ($ret['code'] == 200) :
					$code = 409;  // Conflict
					$data = T('LONGURL_USED', array('id' => $ret['id'], 'url' => $ret['url']));
					$html = T('LONGURL_USED_DESC'); 
				else :
					$code = 202;  // Accepted
					$data = T('LONGURL_VALID', array('url' => $qry));
					$html = T('LONGURL_VALID_DESC'); 
				endif;
			else :
				$code = 412;  // Precondition Failed
				$data = T('LONGURL_NOT_VALID', array('url' => $qry));
				$html = T('LONGURL_NOT_VALID_DESC'); 
			endif;
			break;

		case 'test_short' :
			if (validate_short($qry, $userid)) :
				$ret = get_long($qry);
				if (!$ret) :
					$code = 500;  // Internal Server Error
					$data = T('CANNOT_CONNECT');
					$html = EMPTY_HTML;
				elseif ($ret['code'] == 200) :
					$code = 409;  // Conflict
					$data = T('SHORTURL_TAKEN', array('id' => $ret['id'], 'url' => $ret['url']));
					$html = T('SHORTURL_TAKEN_DESC'); 
				else :  // 404
					$code = 202;  // Accepted
					$data = T('SHORTURL_VALID', array('url' => $qry));
					$html = T('SHORTURL_VALID_DESC'); 
				endif;
			else :
				$code = 412;  // Precondition Failed
				$data = T('SHORTURL_NOT_VALID', array('url' => $qry));
				$html = T('SHORTURL_NOT_VALID_DESC'); 
			endif;
			break;
	
		default :
			$code = 405;  // Method Not Allowed
			$data = T('FUNCTION_NAME', array('function' => $func));
			$html = EMPTY_HTML;
	endswitch;
endif;

if (!isset($status)) $status = T('STATUS_' . $code);

$ts = time();
header("Content-Type: text/xml; charset=utf-8");
header("Expires: " . date(DATE_RFC822, $ts) );
header("Last-Modified: " . date(DATE_RFC822, $ts) );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );

print '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PHP_EOL;
?>
<!DOCTYPE responce SYSTEM "ajax.dtd">
<response>
	<issued><?php print date(DATE_RFC822, $ts); ?></issued>
	<code><?php print $code; ?></code>
	<status><?php print $status; ?></status>
	<data><?php print $data; ?></data>
<?php if(isset($html)) : ?>
	<html><?php if (!empty($html)) print '<![CDATA[' . $html . ']]>'; ?></html>
<?php endif; ?>
</response>
