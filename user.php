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
require_once('includes/validation.' . $phpEx);
require_once('includes/thelper.' . $phpEx);
require_once('includes/lang.' . $phpEx);
initialize_settings();
initialize_db(TRUE);
initialize_lang();
initialize_security($settings['mustlogin']);

global $run, $tab;

$w_userid = $userid;
if (($userlevel >= USER_LEVEL_AD) && isset($_REQUEST['userid']))
	$w_userid = intval($_REQUEST['userid']);
$function = isset($_REQUEST['f']) ? strtolower($_REQUEST['f']) : '';

$http_referer = get_referer();

$sql = "SELECT * FROM $USERS_TABLE" .
	" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
if (!$row) $function = 'not_found';

switch ($function) :
	/* ============================== List ============================== */
	// TODO : USER : List all users with some stats for admins only
	/* case 'list' :
		break; */


	/* ============================== Edit ============================== */
	case 'edit' :
		if ($userlevel >= USER_LEVEL_AD) $userlevels = get_user_levels();
		ob_start();
		?>

<div class="panel"><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></div><br />

<form  action="user.<?php print $phpEx; ?>?f=postedit" method="post" name="f">
<input type="hidden" name="referer" value="<?php print $http_referer;?>" />
<input type="hidden" name="userid" value="<?php print $row['id']; ?>" />
<table class="editlist"><tbody>
<?php
	$run = 0;
	$tab = 0;
	if ($userlevel >= USER_LEVEL_GD)
		output_field('username', $row['username'], 'text', array('maxlength' => 32), TRUE);
	if (($userlevel >= USER_LEVEL_AD) && ($w_userid != $userid)) :
		$userlevels = get_user_levels();
		output_field('userlevel', $row['userlevel'], 'select', $userlevels);
	endif;
	output_field('realname', $row['realname'], 'text', array('maxlength' => 70));
	if ($userlevel >= USER_LEVEL_GD)
		output_field('email', $row['email'], __('text','email',TRUE), array('maxlength' => 150), TRUE);
	if (($userlevel >= USER_LEVEL_AD) && ($w_userid != $userid))
		output_field('enabled', $row['enabled'], 'checkbox');
?>
</tbody><tfoot>
<tr class="<?php print 'row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last' ?>">
<th><button type="reset" class="minibutton danger btn-rset" tabindex="<?php print $tab+2; ?>"><span class="icon"></span><?php P('RESET_EDITS'); ?></button></th>
<td><button type="submit" class="minibutton btn-subm" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('SUBMIT_EDITS'); ?></button></td>
</tr></tfoot></table>
</form>

		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = T('EDIT_ACCOUNT');
		break;


	/* ========================= Post-Edit ========================= */
	case 'postedit' :
		$page['title'] = T('EDIT_ACCOUNT');	// for errors
		$data = array();

		if ($userlevel >= USER_LEVEL_GD) :  // Super Admin only
			$username = $_REQUEST['username'];
			if (strcmp($username, $row['username']) !== 0) :
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
				$data['username'] = $username;
			endif;

			$email = $_REQUEST['email'];
			if (strcmp($email, $row['email']) !== 0) :
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
				$data['email'] = $email;
			endif;
		endif;

		if (($userlevel >= USER_LEVEL_AD) && ($w_userid != $userid)) :
			// Admin only + cannot change self
			$userlevel = intval($_REQUEST['userlevel']);
			if ($userlevel != intval($row['userlevel']))
				$data['userlevel'] = $userlevel;

			$enabled = boolval($_REQUEST['enabled']);
			if ($enabled != boolval($row['enabled']))
				$data['enabled'] = $enabled;
		endif;

		$realname = $_REQUEST['realname'];
		if (strcmp($realname, $row['realname']) !== 0)
			$data['realname'] = $realname;

		if (count($data) > 0) :
			$sql = "UPDATE $USERS_TABLE SET " .
				$db->sql_build_array('UPDATE', $data) .
				" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));;
			$db->sql_query($sql);

			poke_success(T('EDITS_SAVED'), TRUE);
		else :
			poke_warning(T('NO_CHANGES_FOUND'), TRUE);
		endif;

		die( redirect($http_referer) );
		break;


	/* ========================= Email ========================= */
	case 'email' :
		ob_start();

		P('EMAIL_WARNING', NULL, '<p>', '</p>');
		?>

<div class="panel"><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></div><br />

<form  action="user.<?php print $phpEx; ?>?f=postemail" method="post" name="f">
<input type="hidden" name="referer" value="<?php print $http_referer;?>" />
<input type="hidden" name="userid" value="<?php print $row['id']; ?>" />
<table class="editlist"><tbody>
<?php
	$run = 0;
	$tab = 0;
	output_field('email', $row['email'], __('text', 'email', TRUE), array('maxlength' => 150), TRUE);
	output_field('email2', '', __('text', 'email', TRUE), array('maxlength' => 150), TRUE);
?>
</tbody><tfoot>
<tr class="<?php print 'row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last' ?>">
<th><button type="reset" class="minibutton danger btn-rset" tabindex="<?php print $tab+2; ?>"><span class="icon"></span><?php P('RESET_EDITS'); ?></button></th>
<td><button type="submit" class="minibutton btn-subm" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('SUBMIT_EDITS'); ?></button></td>
</tr></tfoot></table>
</form>

		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = T('CHANGE_EMAIL');
		break;


	/* ========================= Post-Email ========================= */
	case 'postemail' :
		$page['title'] = T('CHANGE_EMAIL');

		$email = $_REQUEST['email'];
		$email2 = $_REQUEST['email2'];

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
			'passwd' => md5($password),
			'email' => $email,
			'bad_logon' => -1,  // force change password
			);
		$sql = "UPDATE $USERS_TABLE SET " .
			$db->sql_build_array('UPDATE', $data) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));
		$db->sql_query($sql);

		include_once('includes/sendmail.' . $phpEx);

		$url = $page['full_path'] . 'login.' . $phpEx;

		$body = T('EMAIL_CHANGED_EMAIL', array(
			'username' => $row['username'],
			'realname' => $row['realname'],
			'url' => $url,
			'password' => $password,
			), '', PHP_EOL);

		if (send_mail(webmaster(), $email, T('EMAIL_CHANGED') , $body)) :
			poke_success(T('EMAIL_CHANGED_EMAIL_SENT'), TRUE);
		else :
			poke_error(T('EMAIL_CHANGED_EMAIL_NOT_SENT', array('email' => webmaster(FALSE))), TRUE);
		endif;

		// Logout
		if (session_id()) session_destroy();
		setcookie('token', '', 0);

		die( redirect($url) );

		break;


	/* ========================= Password ========================= */
	case 'password' :
		ob_start();

		P('PASSWORD_WARNING', NULL, '<p>', '</p>');
		?>

<div class="panel"><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></div><br />

<form  action="user.<?php print $phpEx; ?>?f=postpasswd" method="post" name="f">
<input type="hidden" name="referer" value="<?php print $http_referer;?>" />
<input type="hidden" name="userid" value="<?php print $row['id']; ?>" />
<table class="editlist"><tbody>
<?php
	$run = 0;
	$tab = 0;
	output_field('oldpasswd', '', 'password');
?>
<tr class="row_2 even"><td colspan="2">
<?php P('PASSWORD_CONDITIONS'); ?>
</td></tr>
<?php
	$run++;
	output_field('passwd', '', 'password');
	output_field('passwd2', '', 'password');
?>
</tbody>
<tfoot>
<tr class="<?php print 'row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last' ?>">
<th><button type="reset" class="minibutton danger btn-rset" tabindex="<?php print $tab+2; ?>"><span class="icon"></span><?php P('RESET_EDITS'); ?></button></th>
<td><button type="submit" class="minibutton btn-subm" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('SUBMIT_EDITS'); ?></button></td>
</tr></tfoot></table>
</form>

		<?php
		$page['content'] = ob_get_clean();

		$page['title'] = T('CHANGE_PASSWORD');
		break;


	/* ========================= Post-Password ========================= */
	case 'postpasswd' :
		$page['title'] = T('CHANGE_PASSWORD');  // for errors

		$oldpasswd = md5( $_REQUEST['oldpasswd'] );
		if (strcasecmp($oldpasswd, $row['passwd']) != 0) :
			poke_validation(T('PASSWORD_MISMATCH'));
			$page['content'] = T('OLD_PASSWORD_MISMATCH');
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		$check = check_password( $_REQUEST['passwd'] );
		if ($check != 200) :
			header_code($check);
			$page['content'] = T('PASSWORD_NOT_VALID');
			poke_validation(T('VALIDATION_ERROR'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die($check);
		endif;

		$passwd = md5( $_REQUEST['passwd'] );
		$passwd2 = md5( $_REQUEST['passwd2'] );
		if (strcasecmp($passwd, $passwd2) != 0) :
			poke_validation(T('PASSWORD_MISMATCH'));
			$page['content'] = T('CONFIRM_PASSWORD_MISMATCH');
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		$data = array( 'passwd' => $passwd );
		if (intval($row['bad_logon']) < 0) $data['bad_logon'] = 0;

		$sql = "UPDATE $USERS_TABLE SET " .
			$db->sql_build_array('UPDATE', $data) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));
		$db->sql_query($sql);

		poke_success(T('EDITS_SAVED'), TRUE);

		die( redirect($http_referer) );
		break;


	/* ========================= Delete ========================= */
	case 'delete' :
		ob_start();

		P('DELETE_WARNING', NULL, '<p>', '</p>');
		?>

<div class="panel"><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></div><br />

<form  action="user.<?php print $phpEx; ?>?f=postdelete" method="post" name="f">
<input type="hidden" name="referer" value="<?php print $http_referer;?>" />
<input type="hidden" name="userid" value="<?php print $row['id']; ?>" />
<table class="editlist"><tbody>
<tr class="row_1 first odd"><td colspan="2">
<?php P('YOU_ARE_ABOUT_TO_DELETE', array(
	'userid' => $row['id'],
	'username' => $row['username'],
	'realname' => $row['realname'],
	)) ?>
</td></tr>
<?php
	$run = 1;
	$tab = 0;
	output_field('delete', '', 'text', array('maxlength' => 8));
?>
</tbody>
<tfoot>
<tr class="<?php print 'row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last' ?>">
<th></th>
<td><button type="submit" class="minibutton btn-arch danger" tabindex="<?php print $tab+1; ?>"><span class="icon"></span><?php P('DELETE_ACCOUNT'); ?></button></td>
</tr></tfoot></table>
</form>

		<?php
		$page['content'] = ob_get_clean();

		$page['title'] = T('DELETE_ACCOUNT');
		break;


	/* ========================= Post-Delete ========================= */
	case 'postdelete' :
		$page['title'] = T('DELETE_ACCOUNT');

		$deleteword = $_REQUEST['delete'];
		if (strcasecmp($deleteword, 'DELETE') !== 0) :
			poke_warning(T('USER_NOT_DELETED'), TRUE);
			die( redirect($http_referer) );
		else :
			// wrap in transaction **********
			// TODO : USER : Delete Rollback on error
			$db->sql_transaction('begin');

			// move url's
			$sql = "UPDATE $URLS_TABLE SET " .
				$db->sql_build_array('UPDATE', array('userid' => 0)) .
				" WHERE " . $db->sql_build_array('SELECT', array('userid' => $w_userid));;
			$db->sql_query($sql);

			// delete settings
			$sql = "DELETE FROM $SETTINGS_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array('userid' => $w_userid));;
			$db->sql_query($sql);

			// delete user
			$sql = "DELETE FROM $USERS_TABLE" .
				" WHERE " . $db->sql_build_array('SELECT', array('id' => $w_userid));;
			$db->sql_query($sql);

			// commit transaction **********
			$db->sql_transaction('commit');

			// Logout
			if ($userid != $w_userid) :
				if (session_id()) session_destroy();
				setcookie('token', '', 0);
			endif;

			poke_success(T('USER_DELETED'), TRUE);
			$url = ($userid == $w_userid) ? ($page['base_path'] . 'user.' . $phpEx) : $page['base_path'];
			die( redirect($url) );
		endif;
		break;


	/* ========================= Not Found ========================= */
	case 'not_found' :
		poke_validation(T('USER_NOT_FOUND'));
		$page['title'] = T('EDIT_ACCOUNT');
		$page['content'] = T('RETURN_TO_HOMEPAGE', array('url' => $page['base_path']));
		break;


	/* ========================= View ========================= */
	default :
		ob_start();

		if ($userlevel >= USER_LEVEL_AD)
			print '<div class="panel">' .
				get_admin_select_user(T('SHOW_USER'), $_SERVER['PHP_SELF'], $w_userid) .
				'</div><br />';

		$userlevels = get_user_levels();
		$onclicklocation = $_SERVER['PHP_SELF'] . '?';
		if ($userid != $w_userid) $onclicklocation .= 'userid=' . $w_userid . '&';
		?>

<div class="panel clearfix">
<button onclick="window.location='<?php print $onclicklocation . 'f=edit'; ?>'" class="minibutton btn-edit"><span class="icon"></span><?php P('EDIT_ACCOUNT'); ?></button>
<?php if ($userlevel < USER_LEVEL_GD) : ?>
<button onclick="window.location='<?php print $onclicklocation . 'f=email'; ?>'" class="minibutton btn-mail"><span class="icon"></span><?php P('CHANGE_EMAIL'); ?></button>
<?php endif;
      if (($userlevel < USER_LEVEL_GD) || ($userid == $w_userid)) : ?>
<button onclick="window.location='<?php print $onclicklocation . 'f=password'; ?>'" class="minibutton btn-lock"><span class="icon"></span><?php P('CHANGE_PASSWORD'); ?></button>
<?php endif; ?>
<button onclick="window.location='<?php print $onclicklocation . 'f=delete'; ?>'" class="minibutton danger btn-arch"><span class="icon"></span><?php P('DELETE_ACCOUNT'); ?></button>
<?php if (($userid != $w_userid) && ($userlevel >= USER_LEVEL_AD)) : ?>
<button onclick="window.location='<?php print $page['base_path'] . 'list.' . $phpEx . '?userid=' . $w_userid; ?>'" class="minibutton green"><?php P('VIEW_URLS'); ?></button>
<button onclick="window.location='<?php print $page['base_path'] . 'archives.' . $phpEx . '?userid=' . $w_userid; ?>'" class="minibutton green"><?php P('VIEW_ARCHIVES'); ?></button>
<?php endif; ?>
</div>
<br />

<table class="editlist"><tbody>
<?php
	$run = 0;
	output_field('id', $row['id']);
	output_field('username', $row['username']);
	output_field('userlevel', ($row['userlevel'] . ' - ' . $userlevels[$row['userlevel']]) );
	output_field('realname', (empty($row['realname']) ? T('NOT_SET') : $row['realname']) );
	output_field('email', $row['email']);
	output_field('createdon', (date(T('#DATE_LONG_FORMAT'), $row['createdon'])) );
	if (($userlevel >= USER_LEVEL_AD) && ($userid != $w_userid)) :
		if ($row['lastvisiton'] == 0) :
			$elapsed = T('NO_VISITS');
		else :
			$elapsed = T('TIME_AGO', array(
				'time' => get_time_ago( $row['lastvisiton'] )));
		endif;
		output_field('lastvisiton', (date(T('#DATE_LONG_FORMAT'), $row['lastvisiton']) .
			' - ' . $elapsed) );
		output_field('enabled', $row['enabled'], 'bool');
	endif;
?>
</tbody></table>

		<?php
		$page['content'] = ob_get_clean();

		$page['title'] = T('YOUR_ACCOUNT');
		break;


	/* ========================= * ========================= */
endswitch;

if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=minibutton.js');

include('includes/' . TEMPLATE . '.' . $phpEx);
