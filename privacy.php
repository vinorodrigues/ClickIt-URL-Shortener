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
 
function innerHTML($node) {
	$doc = new DOMDocument();
	foreach ($node->childNodes as $child)
		$doc->appendChild($doc->importNode($child, true));
	return $doc->saveHTML();
} 

require_once('includes/library.php');
require_once('includes/lang.' . $phpEx);
initialize_settings();
$db = initialize_db(TRUE);
initialize_lang();

if (file_exists($settings['file_privacy'])) {
	libxml_use_internal_errors(TRUE);
	$doc = new DOMDocument();
	$doc->loadHTMLFile($settings['file_privacy']);
	$node = $doc->getElementsByTagName('body')->item(0);  // extract the body
	$title = $doc->getElementsByTagName('h1')->item(0);  // extract the title, should be the first <h1>
	if ($title) {
		$node->removeChild($title);
		$page['title'] = $title->nodeValue;
	} else {
		$title = $doc->getElementsByTagName('title')->item(0);  // okay, then lets try <TITLE>
		$page['title'] = $title ? $title->nodeValue : T('PRIVACY_POLICY');
	}

	ob_start();
	echo innerHTML($node);
	$page['content'] = ob_get_clean();
	libxml_clear_errors(); libxml_use_internal_errors(FALSE);
}

include('includes/' . TEMPLATE . '.' . $phpEx);
