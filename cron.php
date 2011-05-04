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

// TODO : CRON : CRON functions, for Version 1.0

function run_cron() {
	// TODO : CRON : Parse URLs for lastvisit on more than 3 months old, disable
	// TODO : CRON : Parse Users for lastvisit on more that 3 months old, disable & send admin email
	// TODO : CRON : Parse Users that are locked out with lastvisit on more than 1 month ago, send user and admin email
	// TODO : CRON : Clean out logs more than a year old
	return FALSE;
}

if (!$included) :
	$result = run_cron();
	// TODO : CRON : Page can use template
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <title>CRON</title>
</head>
<body>

<h1>CRON</h1>

<p>Status: <b><?php print $result ? 'OK' : 'Fail'; ?></b></p>

</body>
</html>
<?php endif; ?>
