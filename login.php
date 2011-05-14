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

// determin is called standalone, then initialize
// if called by an include then assume aleady initialized
require_once('includes/library.php');
require_once('includes/lang.' . $phpEx);
initialize_settings();
initialize_db(TRUE);
initialize_lang();
initialize_security(FALSE);

if (!isset($http_referer))
	$http_referer = get_referer();

if ($userid > 0) :

	poke_info( T('ALREADY_SIGNED_IN', array('username' => $username)), TRUE);
	die( redirect($http_referer) );

else :

	ob_start();	?>

<form action="<?php print $_SERVER['REQUEST_URI']; ?>"method="post" name="f">
<input type="hidden" name="referer" value="<?php print $http_referer; ?>" />
<?php __('<table align="center"><tr>', '<table align="center"><tr><td>'); ?>
<?php __('<th><label for="un">' . T('USERNAME') . '</label>:<span class="required"></span></th><td>'); ?>
<input type="text" name="username" id="un" maxlength="32" <?php __('size="15"', 'placeholder="' . T('USERNAME') . '" required="required"'); ?> />
<?php __('</td></tr><tr><th><label for="pw">' . T('PASSWORD') . '</label>:</th><td>'); ?>
<input type="password" name="passwd" id="pw" <?php __('size="15"', 'placeholder="' . T('PASSWORD') . '"'); ?> />
<?php __('</td></tr><tr><td>', '<br />'); ?>
<input type="checkbox" name="remember" id="rm" value="1" checked="checked" /> <label for="rm"><?php P('REMEMBER_ME'); ?></label>
<?php __('</td><td>', ' &nbsp; '); ?>
<input class="minibutton" type="submit" value="<?php P('LOGIN'); ?>" />
<?php __('</td></tr></table>', '</td></tr></table>'); ?>
</form>

<?php
	$page['content'] .= ob_get_clean();
	$page['navigation'] = T('[');
	if (isset($settings['allow_signup']) && $settings['allow_signup']) :
		$page['navigation'] .= T('DONT_HAVE_ACCOUNT', array('url' => 'signup.' . $phpEx));
		$page['navigation'] .= T('|');
	endif;
	$page['navigation'] .= T('FORGOT_PASSWORD', array('url' => 'forgot.' . $phpEx));
	$page['navigation'] .= T(']');
	$included = strtolower(realpath(__FILE__)) != strtolower(realpath($_SERVER['SCRIPT_FILENAME']));
	if (!$included) $page['title'] = T('LOGIN');

	if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
	$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
	$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=minibutton.js');

endif;

include('includes/' . TEMPLATE . '.' . $phpEx);
