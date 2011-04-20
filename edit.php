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
// TODO : Allow admins to change owner
// TODO : AJAX validation on ShortURL
// TODO : AJAX validation on LongURL
// TODO : HTML5 on LongURL
// TODO : Disable MetaK and MetaD if Cloak unchecked

require_once('includes/library.php');
require_once('includes/lang.php');

initialize_settings();
initialize_db(true);
initialize_lang();
initialize_security();

/* ========================= Code begins here ========================= */

if ($userlevel < USER_LEVEL_CR) :  // 1st level check - create rights
	access_denied();
	include('includes/' . TEMPLATE . '.php');
	die(403);
endif;

$longURL = isset($_REQUEST['longURL']) ? $_REQUEST['longURL'] : false;
$urlid = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

if ((!$longURL) && (!$urlid)) :  // don't know what to do
	header_code(405);
	poke_error(T('UNKNOWN_ACTION'));
	poke_validation(T('INAPPROPRIATE'));
	$page['navigation'] = T('HOME', array('url' => $page['base_path']));
	include('includes/' . TEMPLATE . '.php');
	die(405);
endif;

if (!$db) initialize_db(true);

if ($longURL !== false) :  // iether edit or create

	$longURL = str_replace('\\', '/', $longURL);
	if (substr($longURL, -1) == '/') $longURL = substr($longURL, 0, -1);
	
	$shortURL = isset($_REQUEST['shortURL']) ? $_REQUEST['shortURL'] : false;
	$is_custom = ($shortURL !== false);

	if ($urlid !== false) :  // edit only
		// Validate token
		$ftoken = isset($_REQUEST['formtoken']) ? $_REQUEST['formtoken'] : false;
		if (!session_id()) session_start();
		$stoken = isset($_SESSION['ftoken']) ? $_SESSION['ftoken'] : -1; 
		if ($ftoken !== $stoken) :
			header_code(406);  // Not acceptable
			poke_error(T('FORM_TOKEN_MISMATCH'));
			include('includes/' . TEMPLATE . '.php');
			die(406);
		else :
			// clear token to protect against double submit
			if (isset($_SESSION['ftoken'])) unset($_SESSION['ftoken']);			
		endif;
		
		$row = false;
		$result = load_row($urlid, $row);
		
		$act_on_Long = (strcmp($longURL, $row['longurl']) != 0);
		$act_on_Short = ($is_custom && (strcmp($shortURL, $row['shorturl']) != 0));
	else :
		$act_on_Long = true;
		$act_on_Short = $is_custom;
	endif;
	
	/* .................... Edit and Create post common .................... */

	if ($act_on_Long) :
		// Validate LongURL
		if (!validate_long($longURL, $userid)) :
			header_code(412);
			$page['content'] = T('LONGURL_NOT_VALID', array('url' => $longURL));;
			poke_validation(T('VALIDATION_ERROR'));
			include('includes/' . TEMPLATE . '.php');
			die(412);
		endif;

		// Check if LongURL already in use
		$ret = get_short($longURL, $userid);
		if (!$ret) :
			header_code(500);
			poke_error(T('CANNOT_CONNECT'));
			include('includes/' . TEMPLATE . '.php');
			die(500);
		elseif ($ret['code'] == 200) :
			header_code(409);
			poke_validation(T('DUPLICATION_ERROR'));
			$page['content'] = T('LONGURL_ALREADY_SHORT', array(
				'id' => $ret['id'],
				'longurl' => $longURL, 
				'shorturl' => $ret['url'],
				));
			include('includes/' . TEMPLATE . '.php');
			die(409);
		endif;
	endif;
	
	if ($act_on_Short) :
		// Check rights to create or edit custom
		if (
			(($urlid === false) && ($userlevel < USER_LEVEL_CU))
			||
			(($urlid !== false) && ($userlevel < USER_LEVEL_ES)) 
			) : 
			access_denied();
			poke_validation(T('SHORT_NOT_AUTH'));
			include('includes/' . TEMPLATE . '.php');
			die(403);
		endif;

		// Validate ShortURL
		if (!validate_short($shortURL, $userid)) :
			header_code(412);
			$page['content'] = T('SHORTURL_NOT_VALID', array('url' => $shortURL));
			poke_validation(T('VALIDATION_ERROR'));
			include('includes/' . TEMPLATE . '.php');
			die(412);
		endif;

		// Check if short not already used
		$ret = get_long($shortURL);
		if (!$ret) :
			header_code(500);
			poke_error(T('CANNOT_CONNECT'));
			include('includes/' . TEMPLATE . '.php');
			die(500);
		elseif ($ret['code'] == 200) :
			header_code(409);
			poke_validation(T('DUPLICATION_ERROR'));
			$page['content'] = T('SHORT_ALREADY_MAPPED', array(
				'id' => $ret['id'],
				'longurl' => $ret['url'], 
				'shorturl' => $shortURL,
				));	
			include('includes/' . TEMPLATE . '.php');
			die(409);
		endif;
	endif;

	if ($urlid === false) :
		/* ------------------------- Create post ------------------------- */

		// Generate a Short URL if not a custom URL
		if (!$is_custom) :
			$urlid = $db->sql_nextid();
			$shortURL = generate_short($urlid);
			
			if ($shortURL === false) :
				header_code(500);
				poke_error(T('UNABLE_TO_GENERATE_SHORT'));
				include('includes/' . TEMPLATE . '.php');
				die(500);
			endif;
		endif;

		$data = array(
			'shorturl' => $shortURL,
			'longurl' => $longURL,
			'userid' => $userid,
			'createdon' => time(),
			'enabled' => true,
			'log' => true, 
			);
		$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
		$db->sql_query($sql);

		header_code(201);  // Created
		poke_success(T('SHORT_CREATED', array('short' => $shortURL)));
		$page['content'] = T('SHORT_CREATED_DESCRIPTIVE', 
			array('fullshorturl' => $page['full_path'].$shortURL), '<p>', '</p>');
		$page['title'] = T('CREATED');
		include('includes/' . TEMPLATE . '.php');
		die(201);
	
	else :
		/* ------------------------- Edit post ------------------------- */
	
		if ($userlevel < USER_LEVEL_EL) :  // 2st level check - create rights
			access_denied();
			include('includes/' . TEMPLATE . '.php');
			die(403);
		endif;
		
		$userid2 = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : false;
		$enabled = isset($_REQUEST['enabled']) ? $_REQUEST['enabled'] : false;
		$cloak = isset($_REQUEST['cloak']) ? $_REQUEST['cloak'] : false;
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : false;
		$metakeyw = isset($_REQUEST['metakeyw']) ? $_REQUEST['metakeyw'] : false;
		$metadesc = isset($_REQUEST['metadesc']) ? $_REQUEST['metadesc'] : false;
		$log = isset($_REQUEST['log']) ? $_REQUEST['log'] : false;
		$analytics = isset($_REQUEST['analytics']) ? $_REQUEST['analytics'] : false;

		$data = array();
		
		if ($act_on_Long) $data['longurl'] = $longURL;
		if ($act_on_Short) $data['shorturl'] = $shortURL;
		
		if (($userid2 !== false) && ($userid2 != $row['userid']))
			$data['userid'] = $userid2; 
		if (($userlevel >= USER_LEVEL_DS) && ($enabled != boolval($row['enabled'])))
			$data['enabled'] = $enabled; 
		if ($cloak != boolval($row['cloak']))
			$data['cloak'] = $cloak; 
		if (($title !== false) && (strcmp($title, $row['title']) != 0))
			$data['title'] = $title;
		if (($metakeyw !== false) && (strcmp($metakeyw, $row['metakeyw']) != 0))
			$data['metakeyw'] = $metakeyw;
		if (($metadesc !== false) && (strcmp($metadesc, $row['metadesc']) != 0))
			$data['metadesc'] = $metadesc;
		if ($log != boolval($row['log']))
			$data['log'] = $log;
		if ($analytics != boolval($row['analytics']))
			$data['analytics'] = $analytics;
		$db->sql_freeresult($result);
		unset($row);

		$sql = "UPDATE $URLS_TABLE SET " . 
			$db->sql_build_array('UPDATE', $data) .
			" WHERE id = $urlid";
		$db->sql_query($sql);
		
		poke_success(T('EDITS_SAVED'));
		
		if ($userlevel >= USER_LEVEL_AD) :
			$affected = false;
			foreach($data as $f => $v) :
				if (!$affected) : 
					$affected = $f; 
				else : 
					$affected .= ', ' . $f;
				endif;  
			endforeach;
			poke_info(T('ROWS_AFFECTED', array('rows' => $affected)));
		endif;
	endif;
endif;

if (isset($_REQUEST['referer'])) :
	$http_referer = $_REQUEST['referer'];
else :
	if (isset($_SERVER['HTTP_REFERER'])) :
		$http_referer = $_SERVER['HTTP_REFERER'];
	else :
		$http_referer = false;
	endif;
endif;
if ($http_referer !== false)
	$http_referer = str_replace($page['full_path'], '', $http_referer);

$row = false;
$result = load_row($urlid, $row);
$permited = (($userlevel >= USER_LEVEL_AD) || ($userid == $row['userid']));
if (!$permited) :
	access_denied();
	poke_validation(T('NOT_YOURS'));
	include('includes/' . TEMPLATE . '.php');
	die(403);
endif;

/* ------------------------- Edit Form ------------------------- */
	
include_once('includes/uuid.php');

if (!session_id()) session_start();
$ftoken = get_uuid();
$_SESSION['ftoken'] = $ftoken;

ob_start(); ?>

<?php if ($http_referer !== false) : ?><p><button class="minibutton silver btn-back" onclick="window.location='<?php print $http_referer; ?>'"><span class="icon"></span><?php P('BACK'); ?></button></p><?php endif; ?>

<form action="edit.php" method="post" name="f">
<input type="hidden" name="id" value="<?php print $urlid; ?>" />
<?php if ($http_referer !== false) : ?><input type="hidden" name="referer" value="<?php print $http_referer; ?>" /><?php endif; ?>
<input type="hidden" name="formtoken" value="<?php print $ftoken; ?>" />
<table class="editlist"><tbody>
<?php

output_field($row, "shortURL", (($userlevel >= USER_LEVEL_ES) ? "text" : ""), null, true);
if ($userlevel >= USER_LEVEL_ES) poke_warning(T('EDITING_SHORTS_DANGEROUS'));
output_field($row, "longURL", (($userlevel >= USER_LEVEL_EL) ? "textarea" : ""), null, true);
// output_field($row, "userid", "select", array(), true);
if ($userlevel >= USER_LEVEL_DS) output_field($row, "enabled", "checkbox");
output_field($row, "cloak", (($userlevel >= USER_LEVEL_EL) ? "checkbox" : "bool"));
output_field($row, "title", (($userlevel >= USER_LEVEL_CR) ? "text" : ""));
output_field($row, "metakeyw", (($userlevel >= USER_LEVEL_EL) ? "textarea" : ""));
output_field($row, "metadesc", (($userlevel >= USER_LEVEL_EL) ? "textarea" : ""));
output_field($row, "log", (($userlevel >= USER_LEVEL_EL) ? "checkbox" : "bool"));
output_field($row, "analytics", (($userlevel >= USER_LEVEL_EL) ? "checkbox" : "bool"));
$run++;
?>
	
<tr class="<?php print 'row_' . $run . ' ' . (is_odd($run) ? 'odd' : 'even') . ' last' ?>">
<th><button type="reset" class="minibutton danger btn-rset" tabindex="<?php print $run+1; ?>"><span class="icon"></span><?php P('RESET'); ?></button></th>
<td><button type="submit" class="minibutton btn-subm" tabindex="<?php print $run; ?>"><span class="icon"></span><?php P('SUBMIT'); ?></button></td>
</tr></tbody></table>
</form>

<?php

$db->sql_freeresult($result);

$page['content'] = ob_get_clean();
$page['title'] = T('EDIT') . ' <span id="title"></span>';

if (!isset($page['head_suffix'])) : $page['head_suffix'] = ''; endif;
$page['head_suffix'] .= ajaxjs('yui/yui-min.js');
$page['head_suffix'] .= loadjs('includes/loadjs.php?f=minibutton.js');
$page['head_suffix'] .= loadjs('includes/loadjs.php?f=titles.js');

include('includes/' . TEMPLATE . '.php');
exit();



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
	if (!$row) :
		header_code(404);
		poke_error(T('RECORD_NOT_FOUND', array('id' => $urlid)));
		include('includes/' . TEMPLATE . '.php');
		die(404);
	endif;
	return $result;
}

/**
 * 
 */
function output_field($row, $field, $type, $data = null, $required = false) {
	global $run;
	if (!isset($run)) $run = 0;
	$run++;
	print "\n<tr class=\"row_" . $run . ' ' .
		(is_odd($run) ? 'odd' : 'even') .
		(($run == 1) ? ' first' : '') . "\"><th><label for=\"f_" . $field .
		"\">" . T('FIELD_' . strtoupper($field)) . "</label>:" .
		(($required && ($type != '')) ? '<span class="required"></span>' : '') .
		"</th>";
	print "<td>";
	
	$value = $row[strtolower($field)];
	switch ($type) :
		case 'text' :
			?><input type="text" id="f_<?php print $field; ?>" name="<?php print $field; ?>" value="<?php print $value; ?>" tabindex="<?php print $run; ?>" /><?php
			break;
		case 'checkbox' :
			?><input type="checkbox" id="f_<?php print $field; ?>" name="<?php print $field; ?>" <?php if (boolval($value)) print "checked=\"checked\""; ?> value="1" tabindex="<?php print $run; ?>" /><?php
			break;
		case "textarea" :
			?><textarea id="f_<?php print $field; ?>" name="<?php print $field; ?>" tabindex="<?php print $run; ?>"><?php print $value; ?></textarea><?php
			break;
		case "bool" :
			if (boolval($value)) :
				P('ON');
			else :
				P('OFF');
			endif;
			break;
		default :
			print $value;
	endswitch; 
	
	print "</td></tr>";
}

?>
