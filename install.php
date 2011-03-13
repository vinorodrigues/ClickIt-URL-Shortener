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
load_settings(false);

if (file_exists('config.php')) : 
	$e = 403;
	$m = 'File <code>config.php</code> already exists';
	include('error.php');
	die();
endif;

$method = 'post';  // set to post forms
global $o;  $o = '';  // Outcome messages

function add_outcome($msg, $success = true) {
	global $o;
	$tf = $success ? 'true' : 'false';
	$yn = $success ? 'yes' : 'no';
	$o .= "<li style=\"list-style:none;\"><img src=\"images/ico_$tf.png\" alt=\"$yn\" style=\"padding-right:1em;\" />  $msg</li>\n";
}

if (isset($_REQUEST['nextstep'])) :
	$nextstep = $_REQUEST['nextstep'];
else :
	$nextstep = 0;
endif;

$head_title = 'ClickIt Install';
if (($nextstep > 1) && ($nextstep < 6)) :  // = 2
	set_error_handler('error_handler');
	set_exception_handler('exception_handler');	

	$dbms = $_REQUEST['dbms'];
	include_once('includes/db/' . $dbms . '.' . $phpEx);
		
	if ($nextstep > 2) :  // = 3
		$dbhost = $_REQUEST['dbhost'];
		$dbuser = $_REQUEST['dbuser'];
		$dbpasswd = $_REQUEST['dbpasswd'];
		$dbname = $_REQUEST['dbname'];
		$dbport = $_REQUEST['dbport'];
		
		// Connect to database to see if it works
		$db = new $sql_db();
		$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);
		
		if ($nextstep > 3) :  // = 4 +
			$dbprefix = $_REQUEST['dbprefix'];

			// redifine table names, ignoring ones set by load_settings()
			$SETTINGS_TABLE = $dbprefix . 'settings';
			$USERS_TABLE = $dbprefix . 'users';
			$URLS_TABLE = $dbprefix . 'urls';
			$LOG_TABLE = $dbprefix . 'log';
		endif;
		
		if ($nextstep == 4) :  // = 4
			$drop_old = $_REQUEST['drop_old'];
			$adminuser = $_REQUEST['adminuser'];
			$adminpasswd = $_REQUEST['adminpasswd'];
			if (!empty($adminpasswd)) $adminpasswd = md5($adminpasswd);
			
			include_once('includes/db/db_tools.' . $phpEx);
			$tools = new phpbb_db_tools($db);
			
			// drop all first in case there are linked tables
			if ($drop_old) :
				$drop_list = Array($LOG_TABLE, $URLS_TABLE, $USERS_TABLE, $SETTINGS_TABLE);
				// Drop tables - reverse order
				foreach($drop_list as $Table) :
					if ($tools->sql_table_exists($Table)) :
						$tools->sql_table_drop($Table);
						add_outcome("Drop table <code>$Table</code>");
					endif;
				endforeach;
				unset($drop_list);
			endif;
			
			$schemas = Array(

				// ----- Settings -----
				$SETTINGS_TABLE => Array(
					'COLUMNS' => Array(
						'name' => Array('CHAR:70', Null),
						'value' => Array('CHAR:140', ''),
						),
					'PRIMARY_KEY' => 'name',
					),

				// ----- Users -----					
				$USERS_TABLE => Array(
					'COLUMNS' => Array(
						'id' => Array('UINT', Null, 'auto_increment'),
						'username' => Array('CHAR:32', Null),
						'passwd' => Array('CHAR:32', ''),
						'realname' => Array('VCHAR:70', ''),
						'email' => Array('VCHAR:150', ''),
						'createdon' => Array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP 
						'lastvisiton' => Array('TIMESTAMP', 0),
						'enabled' => Array('BOOL', 0),
						'is_admin' => Array('BOOL', 0),
						'analytics' => Array('CHAR:32', ''),
						),
					'PRIMARY_KEY' => 'id',
					'KEYS' => Array(
						'KEY_username' => Array('UNIQUE', 'username'),
						),
					),

				// ----- Urls -----
				$URLS_TABLE => Array(
					'COLUMNS' => Array(
						'id' => Array('UINT', Null, 'auto_increment'),
						'shorturl' => Array('CHAR:25', Null),
						'longurl' => Array('VCHAR:254', Null),
						'userid' => Array('UINT', 0),
						'createdon' => Array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
						'lastvisiton' => Array('TIMESTAMP', 0),
						'cloak' => Array('BOOL', 0),
						'title' => Array('VCHAR:96', ''),
						'metakeyw' => Array('VCHAR:254', ''),
						'metadesc' => Array('VCHAR:254', ''),
						'log' => Array('BOOL', 0),
						'analytics' => Array('BOOL', 0),
						),
					'PRIMARY_KEY' => 'id',
					'KEYS' => Array(
						'KEY_shorturl' => Array('UNIQUE', 'shorturl'),
						),
					),

				// ----- Log -----	
				$LOG_TABLE => Array(
					'COLUMNS' => Array(
						'urlid' => Array('UINT', Null),
						'accessedon' => Array('TIMESTAMP', 0),  // db_tools does't do DEFAULT CURRENT_TIMESTAMP
						'ipaddress' => Array('CHAR:15', ''),
						'referer' => Array('VCHAR:254', ''),
						'browser' => Array('CHAR:45', ''),
						'version' => Array('CHAR:10', ''),
						'platform' => Array('CHAR:45', ''),
						),
					'KEYS' => Array(
						'KEY_urlid' => Array('INDEX', 'urlid'),
						),
				), 
				);

			foreach($schemas as $tablename => $schema) :
				if (!empty($m)) break;
				if (!$tools->sql_table_exists($tablename)) :
					$tools->sql_create_table($tablename, $schema);
					add_outcome("Created table <code>$tablename</code>");
				else :
					add_outcome("Table <code>$tablename</code> already exists", false);
				endif;
			endforeach;
			
			// ===== Inital required data =====
				
			// ----- Settings Data -----
			if (empty($m)) :
				$data = Array(
					'name' => 'version',
					'value' => CLICKIT_VER,
					);
				$sql = "INSERT INTO $SETTINGS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				add_outcome("Populated table <code>$SETTINGS_TABLE</code>");
			endif;

			// ----- Users Data -----
			if (empty($m)) :
				$data = Array(
					'username' => $adminuser,
					'createdon' => microtime(true),
					'enabled' => '1',
					'is_admin' => '1',
					);
				if (!empty($adminpasswd)) $data['passwd'] = $adminpasswd;
				$sql = "INSERT INTO $USERS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				add_outcome("Populated table <code>$USERS_TABLE</code>");
			endif;

			// ----- End DB -----
		elseif ($nextstep == 5) :
			$sampledata = $_REQUEST['sampledata'];
			
			if (($sampledata == 1) && (strcasecmp($adminuser, 'vino') != 0)) :
				// ----- Users -----
				$data = Array(
					'username' => 'vino',
					'passwd' => md5('1'),
					'realname' => 'Vino Rodrigues',
					'email' => 'clickit.source' . '@' . 'tecsmith.com.au',  // just to stop source scrapers
					'createdon' => microtime(true),
					'analytics' => 'UA-00000000-0',  // naa... not mine!
					'enabled' => 1,
					'is_admin' => 1,
					);
				$sql = "INSERT INTO $USERS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				$user_id = $db->sql_nextid();
				add_outcome("Added sample data for table <code>$USERS_TABLE</code>");
			else :
				$user_id = 1;  // Let's hope ...
			endif; 

			if ($sampledata == 1) :
				// ----- Urls -----
				$data = Array(
					'shorturl' => 'vino',
					'longurl' => 'http://vinorodrigues.com',
					'userid' => $user_id,
					'createdon' => microtime(true),
					'cloak' => 0,
					'title' => 'Vino Rodrigues',
					'log' => 1,
					'analytics' => 1,
					);
				$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				
				$data = Array(
					'shorturl' => 'ts',
					'longurl' => 'http://tecsmith.com.au',
					'userid' => $user_id,
					'createdon' => microtime(true),
					'cloak' => 0,
					'title' => 'Tecsmith',
					'log' => 0,
					);
				$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				
				$data = Array(
					'shorturl' => 'test',
					'longurl' => 'http://localhost',
					'userid' => $user_id,
					'createdon' => microtime(true),
					'cloak' => 1,
					'title' => 'LocalHost',
					'metakeyw' => 'Keyword, keyword, keyword, keyword, keyword' ,
					'metadesc' => 'Meta description and meta description and then more meta description' ,
					'log' => 0,
					);
				$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);
				
				$data = Array(
					'shorturl' => 'mail',
					'longurl' => 'mailto:clickit.source@tecsmith.com.au',
					'userid' => $user_id,
					'createdon' => microtime(true),
					'cloak' => 0,
					'title' => 'Send Vino Rodrigues an email',
					'log' => 1,
					);
				$sql = "INSERT INTO $URLS_TABLE" . $db->sql_build_array('INSERT', $data) . ";";
				$db->sql_query($sql);

				add_outcome("Added sample data for table <code>$URLS_TABLE</code>");  /* */
								
				$sql = '';  // clean SQL
			endif;

			// The config.php file we be created now...
		endif;
					
		$db->sql_close();
	endif;
	
	restore_exception_handler();
	restore_error_handler();
	if (!empty($m)) : $e = 500; include('error.php'); die(); endif;
		
	if ($nextstep == 5) :  // = 5
		// Finally - we write the config.php file
		
		set_error_handler('error_handler');
		set_exception_handler('exception_handler');	

		$myFile = "config.php";
		$fh = fopen($myFile, 'w');
		
		$content = '<' . "?php" . PHP_EOL;
		$content .= "if (!defined('IN_CLICKIT')) die('Restricted');" . PHP_EOL . PHP_EOL;

		// $content .= '$' . "settings['offline'] = false;" . PHP_EOL . PHP_EOL;

		$content .= '$' . "settings['dbms'] = '$dbms';" . PHP_EOL;
		$content .= '$' . "settings['dbhost'] = '$dbhost';" . PHP_EOL;
		$content .= '$' . "settings['dbuser'] = '$dbuser';" . PHP_EOL;
		if (!empty($dbpasswd))
			$content .= '$' . "settings['dbpasswd'] = '$dbpasswd';" . PHP_EOL;
		$content .= '$' . "settings['dbname'] = '$dbname';" . PHP_EOL;
		if (!empty($dbport))
			$content .= '$' . "settings['dbport'] = '$dbport';" . PHP_EOL;
		$content .= '$' . "settings['dbprefix'] = '$dbprefix';" . PHP_EOL;

		// $content .= PHP_EOL . '?' . '>';
		
		if (!$fh || !empty($m)) :
			$content = str_replace('<', '&lt;', $content);
			$content = str_replace('>', '&gt;', $content);
			$content = str_replace('"', '&quot;', $content);
			$content = str_replace('&', '&amp;', $content);
			add_outcome('Cannot create file <code>' . $myFile . '</code>,' .
				' please create it with the following contents:' .
				"<pre>$content</pre>", false);  /* */
		else :
			fwrite($fh, $content);
			fclose($fh);
			add_outcome('Create file <code>' . $myFile . '</code>');  /* */
		endif;
		
		restore_exception_handler();
		restore_error_handler();
		$m = '';  // Clear errors
	endif;
endif;

switch($nextstep) :
	/* ----- 0 ------------------------------------------------------------- */
	case 0 :
		if (!defined('IN_CLICKIT')) : $e = 403; include('error.php'); die(); endif;
		
	case 1 :
		ob_start();
		?>

<h2>Welcome to the ClickIt installation wizard</h2>
<form action="install.php" method="<?php print $method; ?>" name="step1" id="step1"> 

<fieldset>

<p>What type of database system will you be connecting to?
<select name="dbms">
<?php $x = Array('mysql', 'mysqli');
	foreach ($x as $s) :
		print "  <option value=\"$s\"";
		if (strcasecmp($settings['dbms'], $s) == 0) print "selected=\"selected\"";
		print ">$s</option>\n"; 
	endforeach; ?>
</select></p>
<p>

</fieldset>

<input type="hidden" name="nextstep" value="2" />

<div class="panel clearfix"><input type="submit" value="Next >"  style="float:right;" /></div>
</form>
		<?php
		$content = ob_get_clean();
		$title = $head_title . ' - Step 1';
		break;

	/* ----- 2 ------------------------------------------------------------- */
	case 2 :
		ob_start();
		?>

<h2>Connect to <?php echo strtoupper($dbms); ?> database</h2>
<form action="install.php" method="<?php print $method; ?>" name="step2" id="step2">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />

<fieldset>

<p>Database Host: <input type="text" name="dbhost" value="<?php print $settings['dbhost']; ?>" size="15" maxlength="128" /></p>

<p>Database Username: <input type="text" name="dbuser" value="<?php print $settings['dbuser']; ?>" size="15" maxlength="30" /></p>

<p>Database Password: <input type="text" name="dbpasswd" value="<?php print $settings['dbpasswd']; ?>" size="15" maxlength="30" /><br >
<small><b>Warning:</b> This installation script will transmit your password in clear text.</small></p>

<p>Database Name: <input type="text" name="dbname" value="<?php print $settings['dbname']; ?>" size="15" maxlength="30" /><br />
<small><b>Note:</b> The databse must already exist.</small></p>

</fieldset>

<input type="hidden" name="dbport" value="<?php print $settings['dbport']; ?>" />
<input type="hidden" name="nextstep" value="3" />

<div class="panel clearfix"><input type="submit" value="Next >"  style="float:right;" /></div>
</form>
		<?php
		$content = ob_get_clean();
		$title = $head_title . ' - Step 2';

		break;

	/* ----- 3 ------------------------------------------------------------- */
	case 3 :
		ob_start();
		?>
		
<h2>Create tables in <i><?php echo $dbname; ?></i> database</h2>

<form action="install.php" method="<?php print $method; ?>" name="step3" id="step3">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />
<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>" />
<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>" />
<input type="hidden" name="dbpasswd" value="<?php echo $dbpasswd; ?>" />
<input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
<input type="hidden" name="dbport" value="<?php echo $dbport; ?>" />

<fieldset>

<p>Table name prefix: <input type="text" name="dbprefix" value="<?php print $settings['dbprefix']; ?>" size="5" maxlength="10" /></p>

<p><input type="checkbox" checked="checked" name="drop_old" id="drop_old" />
<label for="drop_old">Drop existing tables if they exist</label></p>

</fieldset><fieldset title="Administrator">

<p>Admin Username: <input type="text" name="adminuser" value="admin" size="15" maxlength="30" /></p>

<p>Admin Password: <input type="text" name="adminpasswd" value="password" size="15" maxlength="30" /><br >
<small><b>Warning:</b> This installation script will transmit your password in clear text.</small></p>

</fieldset>

<input type="hidden" name="nextstep" value="4" />

<div class="panel clearfix"><input type="submit" value="Next >"  style="float:right;" /></div>
</form>
		
		<?php
		$content = ob_get_clean();
		$title = $head_title . ' - Step 3';

		break;
		
	/* ----- 4 ------------------------------------------------------------- */
	case 4 :
		ob_start();
		if (!empty($o)) : print "<ul>$o</ul>"; endif;
		?>

<h2>Create configuration file and optional sample data</h2>

<form action="install.php" method="<?php print $method; ?>" name="step4" id="step4">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />
<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>" />
<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>" />
<input type="hidden" name="dbpasswd" value="<?php echo $dbpasswd; ?>" />
<input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
<input type="hidden" name="dbport" value="<?php echo $dbport; ?>" />
<input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />

<fieldset>

<p>
<label for="yesno">Do you wish to add sample data?</label>
&nbsp;
<input type="radio" name="sampledata" value="1" checked="checked" id="yes" /><label for="yes">Yes</label>
&nbsp; | &nbsp;
<input type="radio" name="sampledata" value="0" id="no" /><label for="no">No</label>
</p>

</fieldset>

<input type="hidden" name="nextstep" value="5" />

<div class="panel clearfix">
<input type="submit" id="yes" value="Next >" style="float:right;" />
</div>
</form>

		<?php
		$content = ob_get_clean();
		$title = $head_title . ' - Step 4';

		break;

	/* ----- 5 ------------------------------------------------------------- */
	case 5 :
		ob_start();
		if (!empty($o)) : print "<ul>$o</ul>"; endif;
		?>
		
<h2>Installation complete</h2>

<form action="index.php" method="<?php print $method; ?>" name="step5" id="step5">
<div class="panel clearfix">
You will now be directed to the home page.
<input type="submit" id="yes" value="Next >" style="float:right;" />
</div>
</form>

<p><small>It is recomended that in production systems the file <code>install.php</code> be deleted.</small></p>
		
		<?php
		$content = ob_get_clean();
		$title = $head_title . ' - Done!';

		break;

	/* ----- default ------------------------------------------------------- */
	default:
		$e = 405; include('error.php'); die();
		break;
endswitch;

include('includes/' . TEMPLATE . '.php');
