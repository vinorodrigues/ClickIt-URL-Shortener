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

include_once('recaptchalib.' . $phpEx);
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
function get_captcha($use_noscript = TRUE, $row = 1, $odd = TRUE, $colspan = 2) {
	global $settings, $lang;
	if (!isset($lang)) $lang = 'en';
	if (captcha_ready()) :

		$custom = '<tbody id="recaptcha_widget" style="display:none">
<tr class="row_' . ($row) . ' ' . ($odd ? 'odd' : 'even') . '">
<td colspan="' . $colspan . '" align="center" style="background-color: white;">
	<div id="recaptcha_image"></div>
	<span><a href="javascript:Recaptcha.reload()" class="minibutton silver">' . T('GET_ANOTHER') . '</a></span>
	<span class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')" class="minibutton silver">' . T('GET_AUDIO') . '</a></span>
	<span class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')" class="minibutton silver">' . T('GET_IMAGE') .  '</a></span>
	<span><a href="javascript:Recaptcha.showhelp()" class="minibutton silver">' . T('GET_HELP') . '</a></span>
</td>
</tr>
<tr class="row_' . ($row+1) . ' ' . (!$odd ? 'odd' : 'even') . '">
<th>
	<span class="recaptcha_only_if_incorrect_sol red">' . T('INCORRECT') . '<br /></span>
	<span class="recaptcha_only_if_image">' . T('ENTER_WORDS') . ':<span class="required"></span></span>
	<span class="recaptcha_only_if_audio">' . T('ENTER_NUMBERS') . ':<span class="required"></span></span>
</th>
<td colspan="' . ($colspan-1) . '">
	<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" ' . __('', ' required="required"', TRUE) . ' />
	<js_goes_here />
</td>
</tbody>
';

		$part1 = "<script type=\"text/javascript\">
//<![CDATA[
	var RecaptchaOptions = {
		theme : 'custom',
		lang : '" . $lang . "',
		custom_theme_widget: 'recaptcha_widget'
	};
//]]>
</script>
";

		$use_ssl = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'));
		$part2 = recaptcha_get_html(
			$settings['recaptcha_public'],
			'',
			$use_ssl,
			FALSE) . PHP_EOL;

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
