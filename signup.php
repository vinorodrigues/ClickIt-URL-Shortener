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

require_once('includes/library.php');
require_once('includes/lang.' . $phpEx);
require_once('includes/validation.' . $phpEx);
require_once('includes/tcaptcha.' . $phpEx);

initialize_settings();
initialize_lang();
initialize_db(TRUE);

$username = isset($_REQUEST['username']) ? strtolower($_REQUEST['username']) : FALSE;
if ($username !== FALSE) :
	$check = check_captcha();
	if ($check != 200) :
		header_code($check);
		$page['content'] = T('CAPTCHA_FAILED');
		poke_validation(T('VALIDATION_ERROR'));
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die($check);
	endif;

	if (!$db) $db = initialize_db(TRUE);

	$check = check_new_username($username);
	if ($check != 200) :
		header_code($check);
		switch ($check) :
			case 412 : $page['content'] = T('USERNAME_TOO_SHORT'); break;
			case 406 : $page['content'] = T('USERNAME_NOT_VALID'); break;
			case 409 : $page['content'] = T('USERNAME_NOT_AVAIL'); break;
		endswitch;
		poke_validation(T('VALIDATION_ERROR'));
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die($check);
	endif;

	$realname = isset($_REQUEST['realname']) ? $_REQUEST['realname'] : '';
	$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : FALSE;
	$email2 = isset($_REQUEST['email2']) ? $_REQUEST['email2'] : FALSE;

	if (strcmp($email, $email2) != 0) :
		header_code(412);  // Not acceptable
		poke_validation(T('EMAILS_DO_NOT_MATCH'));
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die(412);
	endif;

	$check = check_email($email);
	if ($check != 200) :
		header_code($check);
		switch ($check) :
		    case 412 :
		    case 406 : $page['content'] = T('EMAIL_NOT_VALID'); break;
		    case 409 : $page['content'] = T('EMAIL_NOT_AVAIL'); break;
		endswitch;
		poke_validation(T('VALIDATION_ERROR'));
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die($check);
	endif;

	$password = generate_password();

	$data = array(
		'username' => $username,
		'passwd' => md5($password),
		'userlevel' => $settings['default_user_level'],
		'realname' => $realname,
		'email' => $email,
		'createdon' => time(),
		'enabled' => $settings['default_user_enabled'],
		'bad_logon' => -1,  // force change password
		);
	$sql = "INSERT INTO $USERS_TABLE" . $db->sql_build_array('INSERT', $data);
	$db->sql_query($sql);
	$n_userid = $db->sql_nextid();

	include_once('includes/sendmail.' . $phpEx);

	$url = $page['full_path'] . 'login.' . $phpEx;

	$body = T('NEW_USER_EMAIL', array(
		'username' => $username,
		'realname' => $realname,
		'url' => $url,
		'password' => $password,
		'userid' => $n_userid,
		), '', PHP_EOL);

	if (!$settings['default_user_enabled']) :
		$body .= T('NEW_USER_EMAIL_FOR_ADMIN', array(
			'username' => $username,
			'realname' => $realname,
			'url' => $url,
			'password' => $password,
			'userid' => $n_userid,
			'email' => $email,
			'delete_url' => $page['full_path'] . '/user.' . $phpEx . '?userid=' . $n_userid . '&f=delete',
			), '', PHP_EOL);
		$email = webmaster();  // send to webmaster, not user
	else :
		// TODO : SIGNUP : reformat $email with $realname included
	endif;

	if (send_mail(webmaster(), $email, T('NEW_USER_CREATED') , $body)) :
		if ($settings['default_user_enabled']) :
			poke_success(T('NEW_USER_EMAIL_SENT'));
		else :
			poke_success(T('NEW_USER_EMAIL_SENT_TO_ADMIN'));
		endif;
	else :
		poke_error(T('NEW_USER_EMAIL_NOT_SENT', array('email' => webmaster(FALSE))));
	endif;

	header_code(201);  // Created
	poke_success(T('USER_CREATED_OK', array('username' => $username)));
	// $page['content'] = T('USER_CREATED_DESCRIPTIVE', NULL, '<p>', '</p>');
	$page['content'] = '<p>' . T('RETURN_TO_HOMEPAGE', array('url' => $page['base_path'])) . '</p>';
	$page['title'] = T('USER_CREATED');
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die(201);

else :

	require_once('includes/thelper.' . $phpEx);

	ob_start();

	P('SIGNUP_HERE', array('url' => 'login.' . $phpEx), '<p>', '</p>');

	global $run, $tab;
	$run = 0;
	$tab = 0;
	?>

<form action="signup.<?php print $phpEx; ?>" method="get" name="f">
<table class="editlist"><tbody>
<?php
	output_field('username', '', 'text', NULL, TRUE, FALSE,  // TODO : SIGNUP : HTML5 REGEX
		'<img src="images/ico_user_add.png" alt="" />');  // TODO : SIGNUP : AJAX VERIFICATION
	output_field('realname', '', 'text', NULL, FALSE, FALSE, '');
	output_field('email', '', 'text', NULL, TRUE, FALSE, '');  // TODO : SIGNUP : AJAX VERIFICATION
	output_field('email2', '', 'text', NULL, TRUE, FALSE, '');
?>


<tr class="row_1 odd first">
<?php
	$captcha_code = get_captcha(TRUE, 3);
	if ($captcha_code) print $captcha_code;
?>
</tbody>
<tfoot><tr class="row_<?php print $run+1 . ' ' . (is_odd($run+1) ? 'odd' : 'even'); ?> last"><th></th>
<td colspan="2"><button type="submit" class="minibutton btn-subm" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('SUBMIT_USER'); ?></button></td>
</tr></tfoot></table>
</form>

<?php
	$page['content'] = ob_get_clean();

	if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
	$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
	$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=minibutton.js');
	/* if ($captcha_code)
		$page['head_suffix'] .= "\t" . '<link rel="stylesheet" href="' .
			$page['base_path'] . 'css/captcha.css" type="text/css" />' . PHP_EOL; */

	$page['title'] = 'Create an Account';
endif;

include('includes/' . TEMPLATE . '.' . $phpEx);
