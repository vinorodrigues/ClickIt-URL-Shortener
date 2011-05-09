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

// TODO : TCAPTCHA : Don't use CAPTCH, read http://www.90percentofeverything.com/2011/03/25/fk-captcha/
// TODO : TCAPTCHA : Replaced the CAPTCHA with honeypot fields and timestamp analysis

include_once('recaptcha-php/recaptchalib.' . $phpEx);
include_once('lang.' . $phpEx);

/**
 * Returns TRUE if the reCAPTCHA settings are set, else FALSE
 * @return bool
 */
function captcha_ready() {
	global $settings;
	return ( isset($settings['recaptcha_public']) &&
		(!empty($settings['recaptcha_public'])) &&
		isset($settings['recaptcha_private']) &&
		(!empty($settings['recaptcha_private'])) );
}

/**
 * Returns iether the captcha html code or FALSE
 * @return string|boolean
*/
function get_captcha($use_noscript = TRUE, $colspan = 2) {
	global $settings, $lang, $row, $tab;
	if (!isset($row)) $row = 0;  if (!isset($tab)) $tab = 0;
	$row++;  $tab++;
	if (!isset($lang)) $lang = 'en';
	if (captcha_ready()) :

		$custom = '<tr class="row_' . ($row) . ' ' . (is_odd($row) ? 'odd' : 'even') . '">
<td colspan="' . $colspan . '" align="center" style="padding:0;background:white">
<js_goes_here />
</td></tr>
';

		$s1 = "var RecaptchaOptions = {
	theme : 'white',
	tabindex : " . $tab . "
};";
		$part1 = loadscript($s1);

		$use_ssl = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'));
		$part2 = recaptcha_get_html(
			$settings['recaptcha_public'],
			'',
			$use_ssl,
			$use_noscript) . PHP_EOL;

		return str_replace('<js_goes_here />', $part1 . $part2, $custom);
	else :
		return FALSE;
	endif;
}

function check_captcha() {
	global $settings;
	if (isset($_REQUEST["recaptcha_response_field"])) :
		if (!captcha_ready()) return 400;

        $resp = recaptcha_check_answer(
        	$settings['recaptcha_private'],
			$_SERVER["REMOTE_ADDR"],
			$_REQUEST["recaptcha_challenge_field"],
			$_REQUEST["recaptcha_response_field"]);

		if ($resp->is_valid) :
			return 200;  // You got it
        else :
        	poke_error($resp->error);
        	return 401;
        endif;
	else :
		// ReCaptcha code not in post, so it was not submited.
		return captcha_ready() ? 406 : 200;
	endif;
}

?>
