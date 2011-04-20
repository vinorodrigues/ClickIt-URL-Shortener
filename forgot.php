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
require_once('includes/lang.php');
initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security(false);

if ($userid > 0) :
	ob_start();
	?>
	
<p>You are already signed in as <b><?php print $username; ?></b>.</p>

	<?php
	$page['content'] = ob_get_clean();

elseif (isset($_REQUEST['nameoremail'])) :

	$nameoremail = strtolower( $_REQUEST['nameoremail'] );
	
	$sql = "SELECT id, email, realname FROM $USERS_TABLE" .  
		" WHERE (" . $db->sql_build_array('SELECT', array(
			'username' => $nameoremail,
			'enabled' => 1,
			)) . ")" .
		" OR (" . $db->sql_build_array('SELECT', array(
			'email' => $nameoremail,
			'enabled' => 1,
			)) . ")";
	$result = $db->sql_query($sql);	
	if ($result) :
		$row = $db->sql_fetchrow($result);
		if ($row && (!empty($row['email']))) :
		
			include_once('includes/uuid.php');
			
			$token = get_uuid4();
			$check = intval(microtime(true));
			
			$sql = "UPDATE $USERS_TABLE" .
				" SET " . $db->sql_build_array('UPDATE', array(
					'token' => str_replace('-', '', $token),
					'lastvisiton' => $check,
					)) .
				" WHERE " . $db->sql_build_array('SELECT', array('id' => $row['id']));
			$db->sql_query($sql);
			
			include_once('includes/sendmail.php');
			$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
			$url .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];
			$url .= $_SERVER['PHP_SELF'];
			
			$body = T('FORGOT_EMAIL', array(
				'realname' => $row['realname'],
				'fullurl' => $url . '?token=' . $token . '&check=' . dechex($check), 
				'url' => $url,
				'token' => $token,
				'check' => dechex($check),
				),
				'',
				PHP_EOL);

			if (send_mail($settings['webmaster'], $row['email'], T('FORGOTTEN_PASSWORD') , $body)) :
				poke_success(T('FORGOT_EMAIL_SENT'));
			else :
				poke_error(T('FORGOT_EMAIL_NOT_SENT', array('email' => $settings['webmaster'])));
			endif;
			
			$page['content'] = T('RETURN_TO_HOMEPAGE', array('url' => $page['base_path']));
		else :
			poke_validation(T('USER_NAME_OR_EMAIL_NOT_FOUND'));
			$page['content'] = T('TRY_AGAIN', array('url' => basename(__FILE__)));
		endif;
	else :
		poke_error(T('DATABASE_ERROR'));
	endif;

elseif (isset($_REQUEST['token'])) :

	$token = str_replace('-', '', $_REQUEST['token']);
	@$check = hexdec( $_REQUEST['check'] );
	
	$sql = "SELECT id, username, userlevel, realname FROM $USERS_TABLE" .  
		" WHERE " . $db->sql_build_array('SELECT', array(
			'token' => $token,
			'lastvisiton' => $check, 
			));
	$result = $db->sql_query($sql);	
	if ($result) :
		$row = $db->sql_fetchrow($result);
		if ($row) :
			global $userid, $userlevel;
			$userid = (int) $row['id'];
			$userlevel = (int) $row['userlevel'];
			$username = empty($row['realname']) ? $row['username'] : $row['realname'];
			include_once('includes/uuid.php');
			$guid = get_uuid();
			setcookie('token', $guid, 0);  // expire after session
					
			$sql = "UPDATE $USERS_TABLE" .
				" SET " . $db->sql_build_array('UPDATE', array(
					'lastvisiton' => microtime(true),
					'token' => str_replace('-', '', $guid),
					'bad_logon' => -1,  // force new password
					)) .
				" WHERE " . $db->sql_build_array('SELECT', array('id' => $userid));
			$db->sql_query($sql);
			poke_success(T('LOGIN_SUCCESSFUL'));
			poke_warning(T('LOGGED_OUT_OTHER_SESSIONS'));
			$page['content'] = T('RETURN_TO_HOMEPAGE', array('url' => $page['base_path']));
		else :
			poke_validation(T('TOKEN_NOT_FOUND'));
			$page['content'] = T('TRY_AGAIN', array('url' => basename(__FILE__)));
		endif;
	else :
		poke_error(T('DATABASE_ERROR'));
	endif;

else :

	ob_start(); ?>
	
<?php P('YOU_FORGOT', null, '<p>', '</p>'); ?>

<form method="post">
<?php __('<table><tr>'); ?>
<?php __('<td><label for="ne">' . T('USERNAME_OR_EMAIL') . '</label>:</td><td>'); ?>
<input type="text" name="nameoremail" id="ne" maxlength="128" <?php __('', 'placeholder="' . T('USERNAME_OR_EMAIL') . '" required="required"'); ?> size="30" />
<?php __('</td></tr><tr><td></td><td>'); ?>
<input type="submit" value="<?php P('SEND_REMINDER'); ?>" />
<?php __('</td></tr></table>'); ?>
</form>

<?php P('GOT_FORGOT_KEY', null, '<p>', '</p>'); ?>

<form method="post">
<?php __('<table><tr>'); ?>
<?php __('<td><label for="tk">' . T('TOKEN') . '</label>:</td><td>'); ?>
<input type="text" name="token" id="tk" maxlength="128" <?php __('', 'placeholder="' . T('TOKEN') . '" required="required"'); ?> size="20" />
<?php __('</td></tr><tr><td><label for="ch">' . T('CHECK') . '</label>:</td><td>'); ?>
<input type="text" name="check" id="ch" maxlength="128" <?php __('', 'placeholder="' . T('CHECK') . '" required="required"'); ?> size="10" />
<?php __('</td></tr><tr><td></td><td>'); ?>
<input type="submit" value="<?php P('VALIDATE'); ?>" />
<?php __('</td></tr></table>'); ?>
</form>

	<?php
	$page['content'] = ob_get_clean();
endif;

$page['title'] = T('FORGOTTEN_PASSWORD');
include('includes/' . TEMPLATE . '.php');
