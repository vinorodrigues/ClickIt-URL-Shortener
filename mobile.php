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

$included = strtolower(realpath(__FILE__)) != strtolower(realpath($_SERVER['SCRIPT_FILENAME']));
if (!$included) include_once('includes/library.php');

if (!isset($p_short))
	if (isset($_REQUEST['q'])) :
		$p_short = $page['full_path'] . $_REQUEST['q'];
	else :
		die('Restricted');
	endif;

$p_url = CHART_API_SERVER . '?cht=qr&chs=300x300&chl=' . urlencode($p_short);

if (!isset($p_title)) $p_title = $p_url;

print __('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
print __('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<!DOCTYPE html>') . "\n";
?>
<html<?php
	print __(' xmlns="http://www.w3.org/1999/xhtml"');
?>>
<head>
<title><?php print $p_title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/gif" href="favicon.gif" />
<link rel="apple-touch-icon" href="touchicon.png" />
<meta name="viewport" content="width=device-width, user-scalable=no; initial-scale=1.0; maximum-scale=1.0;" />
</head>
<body style="margin:0;padding:0;">
<a href="<?php print $p_short; ?>"><img src="<?php print $p_url; ?>>" alt="<?php print $p_title; ?>" style="display:block;margin:auto;" /></a>
</body>
