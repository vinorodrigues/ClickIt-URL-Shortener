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

/**
 * This page has three modes of opperation.
 * 	 a) Creating a URL (as in from the home page)
 *   b) Editing a URL (as in a form)
 *   c) Updating a URL (as in the post from the edit form)
 */

/* This form still needs significan work, but we'll get the basic functionality
 * working and then revisit it on later releases
 */
// TODO : EDIT : Allow admins to change owner
// TODO : EDIT : AJAX validation on ShortURL
// TODO : EDIT : AJAX validation on LongURL
// TODO : EDIT : HTML5 on LongURL
// TODO : EDIT : JS to Disable MetaK and MetaD if Cloak unchecked

require_once('includes/library.php');
require_once('includes/validation.' . $phpEx);
require_once('includes/thelper.' . $phpEx);
require_once('includes/lang.' . $phpEx);

initialize_settings();
initialize_db(TRUE);
initialize_lang();
if (!isset($settings['mustlogin'])) $settings['mustlogin'] = TRUE;
initialize_security($settings['mustlogin']);
define('USER_LEVEL_MAGIC', ($settings['mustlogin'] ? USER_LEVEL_CR : -1));

/* ========================= Functions ========================= */

/**
 *
 */
function load_row($urlid, &$row) {
	global $db, $sql, $page;
	global $URLS_TABLE;
	$sql = "SELECT * FROM $URLS_TABLE" .
		" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if (!$row) :
		header_code(404);
		poke_error(T('RECORD_NOT_FOUND', array('id' => $urlid)));
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die();
	endif;
	return $result;
}

/* ========================= Code begins here ========================= */

if ($userlevel < USER_LEVEL_MAGIC) :  // 1st level check - create rights
	access_denied();
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

$longURL = isset($_REQUEST['longURL']) ? $_REQUEST['longURL'] : FALSE;
$urlid = isset($_REQUEST['id']) ? $_REQUEST['id'] : FALSE;

if ((!$longURL) && (!$urlid)) :  // don't know what to do
	header_code(405);
	poke_error(T('UNKNOWN_ACTION'));
	poke_validation(T('INAPPROPRIATE'));
	$page['navigation'] = T('HOME', array('url' => $page['base_path']));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

if (!$db) initialize_db(TRUE);

if ($longURL !== FALSE) :  // iether edit or create

	$longURL = str_replace('\\', '/', $longURL);
	if (substr($longURL, -1) == '/') $longURL = substr($longURL, 0, -1);

	$shortURL = isset($_REQUEST['shortURL']) ? $_REQUEST['shortURL'] : FALSE;
	if (isset($_REQUEST['shortURL']) && empty($shortURL)) $shortURL = FALSE;
	$is_custom = ($shortURL !== FALSE);

	if ($urlid !== FALSE) :  // edit only
		$row = FALSE;
		$result = load_row($urlid, $row);

		$act_on_Long = (strcmp($longURL, $row['longurl']) != 0);
		$act_on_Short = ($is_custom && (strcmp($shortURL, $row['shorturl']) != 0));
	else :
		$act_on_Long = TRUE;
		$act_on_Short = $is_custom;
	endif;

	/* .................... Edit and Create post common .................... */

	if ($act_on_Long) :
		// Validate LongURL
		if (!validate_long($longURL, $userid)) :
			header_code(412);
			$page['content'] = T('LONGURL_NOT_VALID', array('url' => $longURL));;
			poke_validation(T('VALIDATION_ERROR'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		// Check if LongURL already in use
		$ret = get_short($longURL, $userid);
		if (!$ret) :
			header_code(500);
			poke_error(T('CANNOT_CONNECT'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		elseif ($ret['code'] == 200) :
			header_code(409);
			poke_validation(T('DUPLICATION_ERROR'));
			$page['content'] = T('LONGURL_ALREADY_SHORT', array(
				'id' => $ret['id'],
				'longurl' => $longURL,
				'shorturl' => $ret['url'],
				));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;
	endif;

	if ($act_on_Short) :
		// Check rights to create or edit custom
		if (
			(($urlid === FALSE) && ($userlevel < USER_LEVEL_CU))
			||
			(($urlid !== FALSE) && ($userlevel < USER_LEVEL_ES))
			) :
			access_denied();
			poke_validation(T('SHORT_NOT_AUTH'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		// Validate ShortURL
		if (!validate_short($shortURL, $userid)) :
			header_code(412);
			$page['content'] = T('SHORTURL_NOT_VALID', array('url' => $shortURL));
			poke_validation(T('VALIDATION_ERROR'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		// Check if short not already used
		$ret = get_long($shortURL);
		if (!$ret) :
			header_code(500);
			poke_error(T('CANNOT_CONNECT'));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		elseif ($ret['code'] == 200) :
			header_code(409);
			poke_validation(T('DUPLICATION_ERROR'));
			$page['content'] = T('SHORT_ALREADY_MAPPED', array(
				'id' => $ret['id'],
				'longurl' => $ret['url'],
				'shorturl' => $shortURL,
				));
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;
	endif;

	if ($urlid === FALSE) :
		/* ------------------------- Create post ------------------------- */

		// Generate a Short URL if not a custom URL
		if (!$is_custom) :
			$urlid = $db->sql_nextid();
			$shortURL = generate_short($urlid);

			if ($shortURL === FALSE) :
				header_code(500);
				poke_error(T('UNABLE_TO_GENERATE_SHORT'));
				include('includes/' . TEMPLATE . '.' . $phpEx);
				die();
			endif;
		endif;

		$data = array(
			'shorturl' => $shortURL,
			'longurl' => $longURL,
			'userid' => $userid,
			'createdon' => time(),
			'enabled' => TRUE,
			'log' => TRUE,
			);
		$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data);
		$db->sql_query($sql);

		header_code(201);  // Created
		poke_success(T('SHORT_CREATED_OK', array('short' => $shortURL)));
		include_once('includes/clippy/clippy.' . $phpEx);
		$page['content'] = T('SHORT_CREATED_DESCRIPTIVE', array(
			'fullshorturl' => $page['full_path'] . $shortURL,
			'previewurl' => $page['full_path'] . $shortURL . '-',
			'copy' => clippy_get_html($page['full_path'] . $shortURL),
			), '<p>', '</p>');
		$page['title'] = T('SHORT_CREATED');
		include('includes/' . TEMPLATE . '.' . $phpEx);
		die();

	else :
		/* ------------------------- Edit post ------------------------- */

		if ($userlevel < USER_LEVEL_EL) :  // 2st level check - create rights
			access_denied();
			include('includes/' . TEMPLATE . '.' . $phpEx);
			die();
		endif;

		$userid2 = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : FALSE;
		$enabled = isset($_REQUEST['enabled']) ? $_REQUEST['enabled'] : FALSE;
		$cloak = isset($_REQUEST['cloak']) ? $_REQUEST['cloak'] : FALSE;
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : FALSE;
		$metakeyw = isset($_REQUEST['metakeyw']) ? $_REQUEST['metakeyw'] : FALSE;
		$metadesc = isset($_REQUEST['metadesc']) ? $_REQUEST['metadesc'] : FALSE;
		$log = isset($_REQUEST['log']) ? $_REQUEST['log'] : FALSE;

		$data = array();

		if ($act_on_Long) $data['longurl'] = $longURL;
		if ($act_on_Short) $data['shorturl'] = $shortURL;

		if (($userid2 !== FALSE) && ($userid2 != $row['userid']))
			$data['userid'] = $userid2;
		if (($userlevel >= USER_LEVEL_DS) && ($enabled != boolval($row['enabled'])))
			$data['enabled'] = $enabled;
		if ($cloak != boolval($row['cloak']))
			$data['cloak'] = $cloak;
		if (($title !== FALSE) && (strcmp($title, $row['title']) != 0))
			$data['title'] = $title;
		if (($metakeyw !== FALSE) && (strcmp($metakeyw, $row['metakeyw']) != 0))
			$data['metakeyw'] = $metakeyw;
		if (($metadesc !== FALSE) && (strcmp($metadesc, $row['metadesc']) != 0))
			$data['metadesc'] = $metadesc;
		if ($log != boolval($row['log']))
			$data['log'] = $log;
		unset($row);

		if (count($data) > 0) :
			$sql = "UPDATE $URLS_TABLE SET " .
				$db->sql_build_array('UPDATE', $data) .
				" WHERE " . $db->sql_build_array('SELECT', array('id' => $urlid));
			$db->sql_query($sql);

			poke_success(T('EDITS_SAVED'));

			if ($userlevel >= USER_LEVEL_AD) :
				$affected = FALSE;
				foreach($data as $f => $v) :
					if (!$affected) :
						$affected = $f;
					else :
						$affected .= ', ' . $f;
					endif;
				endforeach;
				poke_info(T('ROWS_AFFECTED', array('rows' => $affected)));
			endif;
		else :
			poke_warning(T('NO_CHANGES_FOUND'));
		endif;
	endif;
endif;

$http_referer = get_referer(FALSE);

$row = FALSE;
$result = load_row($urlid, $row);
$permited = (($userlevel >= USER_LEVEL_AD) || ($userid == $row['userid']));
if (!$permited) :
	access_denied();
	poke_validation(T('NOT_YOURS'));
	include('includes/' . TEMPLATE . '.' . $phpEx);
	die();
endif;

/* ------------------------- Edit Form ------------------------- */

ob_start(); ?>

<?php if ($http_referer !== FALSE) : ?><div class="panel"><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></div><br /><?php endif; ?>

<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" name="f">
<input type="hidden" name="id" value="<?php print $urlid; ?>" />
<?php if ($http_referer !== FALSE) : ?><input type="hidden" name="referer" value="<?php print $http_referer; ?>" /><?php endif; ?>
<table class="editlist"><tbody>
<?php
global $run, $tab;
$run = 0;
$tab = 0;
output_field('shortURL', $row['shorturl'], (($userlevel >= USER_LEVEL_ES) ? 'text' : ''), array('maxlength' => 25), TRUE);
if ($userlevel >= USER_LEVEL_ES) poke_warning(T('EDITING_SHORTS_DANGEROUS'));
output_field('longURL', $row['longurl'], (($userlevel >= USER_LEVEL_EL) ? 'textarea' : ''), NULL, TRUE);

// output_field('userid', $row['userid'], 'select', array(), TRUE);

if ($userlevel >= USER_LEVEL_DS) output_field('enabled', $row['enabled'], 'checkbox');
output_field('cloak', $row['cloak'], (($userlevel >= USER_LEVEL_EL) ? 'checkbox' : 'bool'));
output_field('title', $row['title'], (($userlevel >= USER_LEVEL_CR) ? 'text' : ''));
output_field('metakeyw', $row['metakeyw'], (($userlevel >= USER_LEVEL_EL) ? 'textarea' : ''));
output_field('metadesc', $row['metadesc'], (($userlevel >= USER_LEVEL_EL) ? 'textarea' : ''));
output_field('log', $row['log'], (($userlevel >= USER_LEVEL_EL) ? 'checkbox' : 'bool'));
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
$page['title'] = T('EDIT_URL') . ' <span id="title"></span>';

if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=minibutton.js');
$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=titles.js');

include('includes/' . TEMPLATE . '.' . $phpEx);
