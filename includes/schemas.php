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

if (!isset($USERS_TABLE)) $USERS_TABLE = 'USERS';
if (!isset($SETTINGS_TABLE)) $SETTINGS_TABLE = 'SETTINGS';
if (!isset($URLS_TABLE)) $URLS_TABLE = 'URLS';
if (!isset($LOG_TABLE)) $LOG_TABLE = 'LOG';
if (!isset($EVENTS_TABLE)) $EVENTS_TABLE = 'EVENTS';

$schema_0_1 = array(

	// ----- Users -----
	$USERS_TABLE => array(
		'COLUMNS' => array(
			'id' => array('UINT', NULL, 'auto_increment'),
			'username' => array('CHAR:32', NULL),
			'passwd' => array('CHAR:32', ''),
			'token' => array('CHAR:32', ''),
			'userlevel' => array('TINT:1', 0),
			'realname' => array('VCHAR:70', ''),
			'email' => array('VCHAR:150', ''),
			'createdon' => array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
			'lastvisiton' => array('TIMESTAMP', 0),
			'enabled' => array('BOOL', 0),
			'bad_logon' => array('TINT:1', 0),
			),
		'PRIMARY_KEY' => 'id',
		'KEYS' => array(
			'KEY_username' => array('UNIQUE', 'username'),
			'KEY_token' => array('INDEX', 'token'),
			),
		),

	// ----- Settings -----
	$SETTINGS_TABLE => array(
		'COLUMNS' => array(
			'userid' => array('UINT', 0),
			'name' => array('CHAR:70', NULL),
			'value' => array('CHAR:140', ''),
			),
		'PRIMARY_KEY' => array('userid', 'name'),
		'KEYS' => array(
			'KEY_userid' => array('INDEX', 'userid'),
			),
		),

	// ----- Urls -----
	$URLS_TABLE => array(
		'COLUMNS' => array(
			'id' => array('UINT', NULL, 'auto_increment'),
			'shorturl' => array('CHAR:25', NULL),
			'longurl' => array('VCHAR:254', NULL),
			'userid' => array('UINT', 0),
			'createdon' => array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
			'lastvisiton' => array('TIMESTAMP', 0),
			'enabled' => array('BOOL', 1),
			'cloak' => array('BOOL', 0),
			'title' => array('VCHAR:96', ''),
			'metakeyw' => array('VCHAR:254', ''),
			'metadesc' => array('VCHAR:254', ''),
			'log' => array('BOOL', 0),
			),
		'PRIMARY_KEY' => 'id',
		'KEYS' => array(
			'KEY_shorturl' => array('UNIQUE', 'shorturl'),
			),
		),

	// ----- Log -----
	$LOG_TABLE => array(
		'COLUMNS' => array(
			'urlid' => array('UINT', NULL),
			'accessedon' => array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
			'ipaddress' => array('CHAR:15', ''),
			'referer' => array('VCHAR:254', ''),
			'browser' => array('CHAR:45', ''),
			'version' => array('CHAR:10', ''),
			'platform' => array('CHAR:45', ''),
			),
		'KEYS' => array(
			'KEY_urlid' => array('INDEX', 'urlid'),
			),
		),

	);

$schema_0_4 = array(
	// ----- Events -----
	$EVENTS_TABLE => array(
		'COLUMNS' => array(
			'eon' => array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
			'uri' => array('VCHAR:254', ''),
			'ipaddress' => array('CHAR:15', ''),
			'referer' => array('VCHAR:254', ''),
			'msg' => array('VCHAR:254', ''),
			'data' => array('VCHAR:2048', ''),
			),
		),

	);

/**
 * Handle passed database update array.
 * Expected structure...
 * Key being one of the following
 *	change_columns: Column changes (only type, not name)
 *	add_columns: Add columns to a table
 *	drop_keys: Dropping keys
 *	drop_columns: Removing/Dropping columns
 *	add_primary_keys: adding primary keys
 *	add_unique_index: adding an unique index
 *	add_index: adding an index (can be column:index_size if you need to provide size)
 *
 * The values are in this format:
 *		{TABLE NAME}		=> array(
 *			{COLUMN NAME}		=> array({COLUMN TYPE}, {DEFAULT VALUE}, {OPTIONAL VARIABLES}),
 *			{KEY/INDEX NAME}	=> array({COLUMN NAMES}),
 *		)
 */
$changes_0_4 = array(
	'drop_columns' => array(
		$URLS_TABLE => array(
			'analytics',
			),
		$USERS_TABLE => array(
			'analytics',
			),
		),
	);

$inserts_0_4 = array(
	array(
		$URLS_TABLE => array(
			'shorturl' => 'code',
			'longurl' => 'https://github.com/vinorodrigues/ClickIt-URL-Shortener',
			'enabled' => TRUE,
			'userid' => 0,
			'createdon' => microtime(TRUE),
			'title' => 'c1k.it Source Code',
			'log' => TRUE,
			),
		),
	);

?>
