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

if (!defined('IN_CLICKIT')) die('Restricted');

define('AS_TYPE', 0);
define('AS_T_SECTION', 'section');
define('AS_T_SUBSECTION', 'sub-section');
define('AS_T_CONST', '');
define('AS_T_BOOL', 'checkbox');
define('AS_T_EMAIL', 'email');
define('AS_T_URL', 'url');
define('AS_T_TEXT', 'text');
define('AS_T_SELECT', 'select');
define('AS_T_RADIO', 'radio');
define('AS_T_NUM', 'number');
define('AS_T_TAREA', 'textarea');
define('AS_T_CALLBACK', 'callback');
define('AS_DATA', 1);
define('AS_HINT', 2);
define('AS_NOT_FOR_USER', 3);

$settings_array = array(
	'Sitewide Settings' => array(
		AS_TYPE => AS_T_SECTION,
		),
	'version' => array(
		AS_TYPE => AS_T_CONST,
		),
	'General' => array(
		AS_TYPE => AS_T_SUBSECTION,
		AS_NOT_FOR_USER => TRUE,
		),
	'offline' => array(
		AS_TYPE => AS_T_BOOL,
		AS_HINT => 'Set to TRUE to disable site.',
		AS_NOT_FOR_USER => TRUE,
		),
	'webmaster_email' => array(
		AS_TYPE => AS_T_EMAIL,
		AS_HINT => 'Send email from this address.',
		AS_NOT_FOR_USER => TRUE,
		),
	'webmaster_name' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'Send email from this name.',
		AS_NOT_FOR_USER => TRUE,
		),
	'Plugins' => array(
		AS_TYPE => AS_T_SUBSECTION,
		),
	'func_getbrowser' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'PHP file that contains <code>function _get_browser()</code>.',
		AS_NOT_FOR_USER => TRUE,
		),
	'func_lang' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'PHP file that contains the default <code>$lang</code> array.',
		AS_NOT_FOR_USER => TRUE,
		),
	'apikey' => array(
		AS_TYPE => AS_T_CALLBACK,
		AS_HINT => 'Key used to verify c1k.it API calls.',
		AS_DATA => array(
			'label' => 'Generate a new key',
			'callback' => 'get_new_apikey',
			),
		),
	'Overrides' => array(
		AS_TYPE => AS_T_SUBSECTION,
		AS_NOT_FOR_USER => TRUE,
		),
	'force302' => array(
		AS_TYPE => AS_T_BOOL,
		AS_HINT => 'Use older HTTP/1.1 302 for redirects. 303 (internal) and' .
			' 307 (external) are the prefered methods.',
		AS_NOT_FOR_USER => TRUE,
		),
	'URLs' => array(
		AS_TYPE => AS_T_SUBSECTION,
		),
	'shortminlength' => array(
		AS_TYPE => AS_T_NUM,
		AS_DATA => array(
			'min' => 1,
			'max' => 16,
			),
		AS_HINT => 'Minimum length of the Short Bit in the Short URL.',
		),


	'Template Settings' => array(
		AS_TYPE => AS_T_SECTION,
		AS_NOT_FOR_USER => TRUE,
		),
	'cdn' => array(
		AS_TYPE => AS_T_RADIO,
		AS_DATA => array(
			'local' => 'Local server copy',
			'yahoo' => 'Yahoo\'s CDN',
			'google' => 'Google\'s CDN',
			),
		AS_HINT => 'Content Delivery Network: Location of the YUI library.',
		AS_NOT_FOR_USER => TRUE,
		),
	'meta_description' => array(
		AS_TYPE => AS_T_TAREA,
		AS_HINT => 'Content of <code>meta name=description</code> header tag.',
		AS_NOT_FOR_USER => TRUE,
		),
	'meta_keywords' => array(
		AS_TYPE => AS_T_TAREA,
		AS_HINT => 'Content of <code>meta name=keywords</code> header tag.' .
			' Seperated with comma\'s, no spaces after the comma.',
		AS_NOT_FOR_USER => TRUE,
		),
	'piwik_site' => array(
		AS_TYPE => AS_T_URL,
		AS_HINT => 'URL of the Piwik web-site. Exclude the trailing forward-slash (\'/\'). See <a href="http://piwik.org">http://piwik.org</a>',
		AS_NOT_FOR_USER => TRUE,
		),
	'piwik_site_secure' => array(
		AS_TYPE => AS_T_URL,
		AS_HINT => 'URL of the secure Piwik web-site. Exclude the trailing forward-slash (\'/\').',
		AS_NOT_FOR_USER => TRUE,
		),
	'piwik_id' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'ID of this site on the Piwik web-site.',
		AS_NOT_FOR_USER => TRUE,
		),
	'Home Page' => array(
		AS_TYPE => AS_T_SUBSECTION,
		AS_NOT_FOR_USER => TRUE,
		),
	'facebook_id' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'Facebook Application ID, see <a href="http://www.facebook.com/developers/apps.php">http://www.facebook.com/developers/apps.php</a>',
		AS_NOT_FOR_USER => TRUE,
		),
	#'facebook_key',  // UNUSED
	'twitter_key' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'Twitter @Anywhere API Key, see <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a>',
		AS_NOT_FOR_USER => TRUE,
		),
	'twitter_follow_list' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'Comma seperated list of twitter usernames to add follow buttons for',
		AS_NOT_FOR_USER => TRUE,
		),
	'ga_profile' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'Google Analytics profile ID of this site, see <a href="https://www.google.com/analytics/settings/">https://www.google.com/analytics/settings/</a>',
		AS_NOT_FOR_USER => TRUE,
		),


	'Security Settings' => array(
		AS_TYPE => AS_T_SECTION,
		AS_NOT_FOR_USER => TRUE,
		),
	'mustlogin' => array(
		AS_TYPE => AS_T_BOOL,
		AS_HINT => 'User\'s must have an account to create Short URL\'s.' .
			' Setting to OFF enables anonymous use.',
		AS_NOT_FOR_USER => TRUE,
		),
	'badlogonmax' => array(
		AS_TYPE => AS_T_NUM,
		AS_DATA => array(
			'min' => 1,
			'max' => 9,
			),
		AS_HINT => 'Incorrect password attempts before lockout.',
		),
	'default_user_level' => array(
		AS_TYPE => AS_T_SELECT,
		AS_HINT => 'Default userlevel set on signup page',
		AS_NOT_FOR_USER => TRUE,
		AS_DATA => get_user_levels(),
		),
	'default_user_enabled' => array(
		AS_TYPE => AS_T_BOOL,
		AS_HINT => 'If new users are enabled, if not then activation password is sent to the webmaster',
		AS_NOT_FOR_USER => TRUE,
		),
	'recaptcha_public' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'reCAPTCH Public Key, see <a href="https://www.google.com/recaptcha/admin/list">https://www.google.com/recaptcha/admin/list</a>',
		AS_NOT_FOR_USER => TRUE,
		),
	'recaptcha_private' => array(
		AS_TYPE => AS_T_TEXT,
		AS_HINT => 'reCAPTCH Private Key',
		AS_NOT_FOR_USER => TRUE,
		),

	'Page Settings' => array(
		AS_TYPE => AS_T_SECTION,
		),
	'Preview' => array(
		AS_TYPE => AS_T_SUBSECTION,
		),
	'preview_delay' => array(
		AS_TYPE => AS_T_NUM,
		AS_DATA => array(
			'min' => 1,
			),
		AS_HINT => 'Time, in seconds, to delay redirect in preview page',
		),

	);
