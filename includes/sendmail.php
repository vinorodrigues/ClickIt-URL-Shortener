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

$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once('uuid.' . $phpEx);

function Send_Mail($sender, $to, $subject, $body) {
	// Set MIME Boundry
	$mime_boundary = '----=_Part_' . get_uuid() . '_=----';
	$style = 'font-family: Verdana, Geneva, sans-serif; font-size: 10pt; color: #039;';

	// Headers
	$headers = "From: $sender" . PHP_EOL;
	$headers .= "Reply-To: $sender" . PHP_EOL;
	$headers .= "MIME-Version: 1.0" . PHP_EOL;
	$headers .= "Content-Type: multipart/alternative;" . PHP_EOL .
		"        boundary=\"$mime_boundary\"" . PHP_EOL;

	// Text Part

	$message = "--$mime_boundary" . PHP_EOL;
	$message .= "Content-Type: text/plain; charset=UTF-8" . PHP_EOL;
	$message .= "Content-Transfer-Encoding: 8bit" . PHP_EOL . PHP_EOL;

	$message .= $body . PHP_EOL . PHP_EOL;

	// HTML Part

	$htmlbody = str_replace(array('<', '>', PHP_EOL, '  '),
		array('&lt;', '&gt;', '<br />', ' &nbsp;'), $body);

	$message .= "--$mime_boundary" . PHP_EOL;
	$message .= "Content-Type: text/html; charset=UTF-8" . PHP_EOL;
	$message .= "Content-Transfer-Encoding: 8bit" . PHP_EOL . PHP_EOL;

	$message .= "<html><body style=\"$style\">". PHP_EOL;
	$message .= $htmlbody . PHP_EOL;
	$message .= "</body></html>" . PHP_EOL . PHP_EOL;

	// Ending Boundary, treminating '--'

	$message .= "--$mime_boundary--" . PHP_EOL . PHP_EOL;

	// sendit!

	return @mail( $to, $subject, $message, $headers );

	// ===== DEBUG ONLY =====
	/*
	echo "<pre>";
	echo "To: $to" . PHP_EOL;
	echo "Subject: $subject" . PHP_EOL;
	echo "$headers" . PHP_EOL . PHP_EOL;
	echo "$message" . PHP_EOL;
	echo "</pre>";

	return TRUE;
	/* */
}

?>
