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
require_once('includes/lang.php');
initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security(false);

if ($userid > 0) :

	poke_info( T('ALREADY_SIGNED_IN', array('username' => $username)), true);
	
	header('Location: ' . $page['base_path'], true, 302);
	die(302);

else :

	include_once('includes/uuid.php');

	if (!session_id()) session_start();
	$ftoken = get_uuid();
	$_SESSION['ftoken'] = $ftoken;

	ob_start();	?>
	
<form method="post" name="f">
<?php __('<table align="center"><tr>', '<table align="center"><tr><td>'); ?>
<?php __('<th><label for="un">' . T('USERNAME') . '</label>:<span class="required"></span></th><td>'); ?>
<input type="text" name="username" id="un" maxlength="32" <?php __('', 'placeholder="' . T('USERNAME') . '" required="required"'); ?> size="15" />
<?php __('</td></tr><tr><th><label for="pw">' . T('PASSWORD') . '</label>:</th><td>'); ?>
<input type="password" name="passwd" id="pw" maxlength="32" <?php __('', 'placeholder="' . T('PASSWORD') . '"'); ?> size="15" />
<?php __('</td></tr><tr><td>', '<br />'); ?>
<input type="checkbox" name="remember" id="rm" value="1" checked="checked" /> <label for="rm"><?php P('REMEMBER_ME'); ?></label>
<?php __('</td><td>', ' &nbsp; '); ?>
<input class="minibutton" type="submit" value="<?php P('LOGIN'); ?>" />
<?php __('</td></tr></table>', '</td></tr></table>'); ?>

<input type="hidden" name="formtoken" value="<?php print $ftoken; ?>" />
</form>

<?php
	$page['content'] .= ob_get_clean();
	$page['navigation'] = T('DONT_HAVE_ACCOUNT', array('url' => 'signup.php')) . 
		" <span class=\"spacer\">|</span> " . 
		T('FORGOT_PASSWORD', array('url' => 'forgot.php'));
	$included = strtolower(realpath(__FILE__)) != strtolower(realpath($_SERVER['SCRIPT_FILENAME']));		
	if (!$included) $page['title'] = T('LOGIN');
	
	if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
	$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
	$page['head_suffix'] .= loadjs('includes/loadjs.php?f=minibutton.js');
endif;

include('includes/' . TEMPLATE . '.php');
