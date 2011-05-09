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

header("Content-Type: text/xml");

define('CF_ALWAYS', 'always');
define('CF_HOURLY', 'hourly');
define('CF_DAILY', 'daily');
define('CF_WEEKLY', 'weekly');
define('CF_MONTHLY', 'monthly');
define('CF_YEARLY', 'yearly');
define('CF_NEVER', 'never');

global $full_path;
$full_path = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
$full_path .= $_SERVER['SERVER_NAME'];
$__port = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 443 : 80;
$full_path .= (intval($_SERVER['SERVER_PORT']) != $__port) ? ':'.$_SERVER['SERVER_PORT'] : '';
unset($__port);
$full_path .= rtrim(pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME), '/');

/**
 * See: http://www.sitemaps.org/protocol.php
 */
function add_page($loc, $change_freq = NULL, $lastmod = NULL, $priority = -1) {
	global $full_path;
	print "<url>";
	print "<loc>$full_path/$loc</loc>";
	if (!empty($change_freq)) print "<changefreq>$change_freq</changefreq>";
	// TODO : SITEMAP : $lastmod, W3C Datetime format, use YYYY-MM-DD
	// TODO : SITEMAP : $priority, range 0.0 to 1.0, default 0.5
	print "</url>" . PHP_EOL;
}

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<urlset' .
	' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
	' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
	' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' .
	' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

add_page('');
add_page('signup.php');
add_page('login.php', CF_NEVER);
add_page('forgot.php', CF_NEVER);
add_page('license.php', CF_YEARLY);
add_page('tos.php', CF_YEARLY);
add_page('privacy.php', CF_YEARLY);
add_page('code', CF_MONTHLY);


print '</urlset>';

?>
