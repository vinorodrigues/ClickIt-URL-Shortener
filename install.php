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
initialize_settings(TRUE);

if (file_exists('config.' . $phpEx)) :
	$e = 403;
	poke_error('File <code>config.' . $phpEx . '</code> already exists');
	include('error.' . $phpEx);
	die();
endif;

$method = 'post';  // set to post forms
global $o;  $o = '';  // Outcome messages

function add_outcome($msg, $success = TRUE) {
	global $o;
	$tf = $success ? 'success' : 'fail';
	$yn = $success ? 'yes' : 'no';
	$o .= "<li style=\"list-style:none;\"><img src=\"images/ico_$tf.png\" alt=\"$yn\" style=\"padding-right:1em;\" />  $msg</li>\n";
}

if (isset($_REQUEST['nextstep'])) :
	$nextstep = $_REQUEST['nextstep'];
else :
	$nextstep = 0;
endif;

$page['head_title'] = 'ClickIt Install';
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
		$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, FALSE, FALSE);

		if ($nextstep > 3) :  // = 4 +
			$dbprefix = $_REQUEST['dbprefix'];

			// redifine table names, ignoring ones set by initialize_settings()
			$SETTINGS_TABLE = $dbprefix . 'settings';
			$USERS_TABLE = $dbprefix . 'users';
			$URLS_TABLE = $dbprefix . 'urls';
			$LOG_TABLE = $dbprefix . 'log';
			$EVENTS_TABLE = $dbprefix . 'events';
		endif;

		if ($nextstep == 4) :  // = 4
			$drop_old = $_REQUEST['drop_old'];
			$adminuser = strtolower( $_REQUEST['adminuser'] );
			$adminpasswd = $_REQUEST['adminpasswd'];
			if (!empty($adminpasswd)) $adminpasswd = md5($adminpasswd);

			include_once('includes/db/db_tools.' . $phpEx);
			$tools = new phpbb_db_tools($db);

			// drop all first in case there are linked tables
			if ($drop_old) :
				$drop_list = array($EVENTS_TABLE, $LOG_TABLE, $URLS_TABLE, $USERS_TABLE, $SETTINGS_TABLE);
				// Drop tables - reverse order
				foreach($drop_list as $Table) :
					if ($tools->sql_table_exists($Table)) :
						$tools->sql_table_drop($Table);
						add_outcome("Drop table <code>$Table</code>");
					endif;
				endforeach;
				unset($drop_list);
			endif;

			// **************************************************
			// ******************** SCHEMAS! ********************
			// **************************************************
			include('includes/schemas.' . $phpEx);
			$schemas = $schema_0_1 + $schema_0_4;
			$inserts = $inserts_0_4;
			// **************************************************
			// **************************************************

			foreach($schemas as $tablename => $schema) :
				if (!empty($messages)) break;
				if (!$tools->sql_table_exists($tablename)) :
					$tools->sql_create_table($tablename, $schema);
					add_outcome("Created table <code>$tablename</code>");
				else :
					add_outcome("Table <code>$tablename</code> already exists", FALSE);
				endif;
			endforeach;

			// ===== Inital required data =====

			include_once('includes/uuid.' . $phpEx);

			$inserts_base = array(

				// ----- Settings Data -----
				array(
					$SETTINGS_TABLE => array(
						'userid' => 0,
						'name' => 'version',
						'value' => str_replace(array('&', ';'), array(' ', ''), CLICKIT_VER),
						),
					),
				array(
					$SETTINGS_TABLE => array(
						'userid' => 0,
						'name' => 'apikey',
						'value' => str_replace('-', '', get_uuid5(NS_URL, $page['full_path'])),
						),
					),

				// ----- Users Data -----
				array(
					$USERS_TABLE => array(
						'username' => $adminuser,
						'realname' => 'Administrator',
						'createdon' => microtime(TRUE),
						'enabled' => TRUE,
						'userlevel' => USER_LEVEL_GD,
						'passwd' => (!empty($adminpasswd) ? $adminpasswd : ''),
						),
					),

				);

			// append schemas.php inserts
			foreach ($inserts as $ins) :
				$inserts_base[] = $ins;
			endforeach;

			foreach ($inserts_base as $ins):
				if (empty($messages))
					foreach ($ins as $tbl => $vars) :
						$sql = "INSERT INTO $tbl " . $db->sql_build_array('INSERT', $vars);
						$db->sql_query($sql);
					endforeach;
			endforeach;

			log_event('INSTALLED');

			// ----- End DB -----
		elseif ($nextstep == 5) :
			$sampledata = $_REQUEST['sampledata'];
			$adminuser = strtolower( $_REQUEST['adminuser'] );

			if (($sampledata == 1) && (strcasecmp($adminuser, 'vino') != 0)) :
				// ----- Users -----
				$data = array(
					'username' => 'vino',
					'passwd' => md5('1'),
					'realname' => 'Vino Rodrigues',
					'email' => 'clickit.source' . '@' . 'tecsmith.com.au',  // just to stop source scrapers
					'createdon' => microtime(TRUE),
					'enabled' => TRUE,
					'userlevel' => USER_LEVEL_GD,
					);
				$sql = "INSERT INTO $USERS_TABLE " . $db->sql_build_array('INSERT', $data);
				$db->sql_query($sql);

				$user_id = $db->sql_nextid();
			else :
				$user_id = 1;  // Let's hope ...
			endif;

			if ($sampledata == 1) :
				// ----- Users -----
				$data = array(
					'username' => 'joe',
					'passwd' => md5('1'),
					'realname' => 'Joe Blow',
					'email' => 'root@localhost',  // hope you have a SMTP server on your PC ;)
					'createdon' => microtime(TRUE),
					'enabled' => TRUE,
					'userlevel' => USER_LEVEL_CR,
					);
				$sql = "INSERT INTO $USERS_TABLE " . $db->sql_build_array('INSERT', $data);
				$db->sql_query($sql);

				add_outcome("Added sample data for table <code>$USERS_TABLE</code>");

				// ----- Urls -----
				$data = array(
					'shorturl' => 'vino',
					'longurl' => 'http://vinorodrigues.com',
					'userid' => $user_id,
					'createdon' => microtime(TRUE),
					'cloak' => 0,
					'title' => 'Vino Rodrigues',
					'log' => 1,
					);
				$sql = "INSERT INTO $URLS_TABLE " . $db->sql_build_array('INSERT', $data);
				$db->sql_query($sql);

				$data = array(
					'shorturl' => 'ts',
					'longurl' => 'http://tecsmith.com.au',
					'userid' => $user_id,
					'createdon' => microtime(TRUE),
					'cloak' => 0,
					'title' => 'Tecsmith',
					'log' => 0,
					);
				$sql = "INSERT INTO $URLS_TABLE " . $db->sql_build_array('INSERT', $data);
				$db->sql_query($sql);

				$data = array(
					'shorturl' => 'test',
					'longurl' => 'http://localhost',
					'userid' => $user_id,
					'createdon' => microtime(TRUE),
					'cloak' => 1,
					'title' => 'LocalHost',
					'metakeyw' => 'Keyword, keyword, keyword, keyword, keyword' ,
					'metadesc' => 'Meta description and meta description and then more meta description' ,
					'log' => 0,
					);
				$sql = "INSERT INTO $URLS_TABLE " . $db->sql_build_array('INSERT', $data);
				$db->sql_query($sql);

				$data = array(
					'shorturl' => 'mail',
					'longurl' => 'mailto:clickit.source@tecsmith.com.au',
					'userid' => $user_id,
					'createdon' => microtime(TRUE),
					'cloak' => 0,
					'title' => 'Send Vino Rodrigues an email',
					'log' => 1,
					);
				$sql = "INSERT INTO $URLS_TABLE " . $db->sql_build_array('INSERT', $data);
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
	if (!empty($messages)) : $e = 500; include('error.' . $phpEx); die(); endif;

	if ($nextstep == 5) :  // = 5
		// Finally - we write the config.php file

		set_error_handler('error_handler');
		set_exception_handler('exception_handler');

		$myFile = "config." . $phpEx;
		$fh = fopen($myFile, 'w');

		$content = '<' . "?php" . PHP_EOL;
		$content .= "if (!defined('IN_CLICKIT')) die('Restricted');" . PHP_EOL . PHP_EOL;

		// $content .= '$' . "settings['offline'] = FALSE;" . PHP_EOL . PHP_EOL;

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

		if (!$fh || !empty($messages)) :
			$content = str_replace('<', '&lt;', $content);
			$content = str_replace('>', '&gt;', $content);
			$content = str_replace('"', '&quot;', $content);
			$content = str_replace('&', '&amp;', $content);
			add_outcome('Cannot create file <code>' . $myFile . '</code>,' .
				' please create it with the following contents:' .
				"<pre>$content</pre>", FALSE);  /* */
		else :
			fwrite($fh, $content);
			fclose($fh);
			add_outcome('Create file <code>' . $myFile . '</code>');  /* */
		endif;

		restore_exception_handler();
		restore_error_handler();
		$messages = array();  // Clear errors
	endif;
endif;

switch($nextstep) :
	/* ----- 0 ------------------------------------------------------------- */
	case 0 :
		if (!defined('IN_CLICKIT')) : $e = 403; include('error.' . $phpEx); die(); endif;

	case 1 :
		ob_start();
		?>

<h2>Welcome to the ClickIt installation wizard</h2>
<form action="install.<?php print $phpEx; ?>" method="<?php print $method; ?>" name="step1" id="step1">

<fieldset class="install" title="DBMS">

<p>What type of database system will you be connecting to?
<select name="dbms">
<?php $x = array('mysqli');
	foreach ($x as $s) :
		print "  <option value=\"$s\"";
		if (strcasecmp($settings['dbms'], $s) == 0) print "selected=\"selected\"";
		print ">$s</option>\n";
	endforeach; ?>
</select></p>
<p>

</fieldset>

<input type="hidden" name="nextstep" value="2" />

<div class="panel clearfix"><input type="submit" value="Next >" style="float:right;" /></div>
</form>
		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = $page['head_title'] . ' - Step 1';
		break;

	/* ----- 2 ------------------------------------------------------------- */
	case 2 :
		ob_start();
		?>

<h2>Connect to <?php echo strtoupper($dbms); ?> database</h2>
<form action="install.<?php print $phpEx; ?>" method="<?php print $method; ?>" name="step2" id="step2">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />

<fieldset class="install" title="Database">

<p>Database Host: <input type="text" name="dbhost" value="<?php print $settings['dbhost']; ?>" size="15" maxlength="128" /></p>

<p>Database Username: <input type="text" name="dbuser" value="<?php print $settings['dbuser']; ?>" size="15" maxlength="30" /></p>

<p>Database Password: <input type="text" name="dbpasswd" value="<?php print $settings['dbpasswd']; ?>" size="15" maxlength="30" /><br >
<small><b>Warning:</b> This installation script will transmit your password in clear text.</small></p>

<p>Database Name: <input type="text" name="dbname" value="<?php print $settings['dbname']; ?>" size="15" maxlength="30" /><br />
<small><b>Note:</b> The databse must already exist. Collation should be case insensitive (<i>xxx</i><b>_ci</b> in MySQL)</small></p>

</fieldset>

<input type="hidden" name="dbport" value="<?php print $settings['dbport']; ?>" />
<input type="hidden" name="nextstep" value="3" />

<div class="panel clearfix"><input type="submit" value="Next >"  style="float:right;" /></div>
</form>
		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = $page['head_title'] . ' - Step 2';

		break;

	/* ----- 3 ------------------------------------------------------------- */
	case 3 :
		ob_start();
		?>

<h2>Create tables in <i><?php echo $dbname; ?></i> database</h2>

<form action="install.<?php print $phpEx; ?>" method="<?php print $method; ?>" name="step3" id="step3">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />
<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>" />
<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>" />
<input type="hidden" name="dbpasswd" value="<?php echo $dbpasswd; ?>" />
<input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
<input type="hidden" name="dbport" value="<?php echo $dbport; ?>" />

<fieldset class="install" title="Tables">

<p>Table name prefix: <input type="text" name="dbprefix" value="<?php print $settings['dbprefix']; ?>" size="5" maxlength="10" /></p>

<p><input type="checkbox" checked="checked" name="drop_old" id="drop_old" />
<label for="drop_old">Drop existing tables if they exist</label></p>

<?php print '</fieldset><fieldset class="install" title="Administrator">'; /* Eclipse parse workaround */ ?>

<p>Admin Username: <input type="text" name="adminuser" value="admin" size="15" maxlength="30" /></p>

<p>Admin Password: <input type="text" name="adminpasswd" value="password" size="15" maxlength="30" /><br >
<small><b>Warning:</b> This installation script will transmit your password in clear text.</small></p>

</fieldset>

<input type="hidden" name="nextstep" value="4" />

<div class="panel clearfix"><input type="submit" value="Next >"  style="float:right;" /></div>
</form>

		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = $page['head_title'] . ' - Step 3';

		break;

	/* ----- 4 ------------------------------------------------------------- */
	case 4 :
		ob_start();
		if (!empty($o)) : print "<ul>$o</ul>"; endif;
		?>

<h2>Create configuration file and optional sample data</h2>

<form action="install.<?php print $phpEx; ?>" method="<?php print $method; ?>" name="step4" id="step4">

<input type="hidden" name="dbms" value="<?php echo $dbms; ?>" />
<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>" />
<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>" />
<input type="hidden" name="dbpasswd" value="<?php echo $dbpasswd; ?>" />
<input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
<input type="hidden" name="dbport" value="<?php echo $dbport; ?>" />
<input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
<input type="hidden" name="adminuser" value="<?php echo $adminuser; ?>" />

<fieldset class="install" title="Sample data">

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
		$page['content'] = ob_get_clean();
		$page['title'] = $page['head_title'] . ' - Step 4';

		break;

	/* ----- 5 ------------------------------------------------------------- */
	case 5 :
		ob_start();
		if (!empty($o)) : print "<ul>$o</ul>"; endif;
		?>

<h2>Installation complete</h2>

<form action="index.<?php print $phpEx; ?>" method="<?php print $method; ?>" name="step5" id="step5">
<div class="panel clearfix">
You will now be directed to the home page.
<input type="submit" id="yes" value="Next >" style="float:right;" />
</div>
</form>

<p><small>It is recomended that in production systems the file <code>install.<?php print $phpEx; ?></code> be deleted.</small></p>

		<?php
		$page['content'] = ob_get_clean();
		$page['title'] = $page['head_title'] . ' - Done!';

		break;

	/* ----- default ------------------------------------------------------- */
	default:
		$e = 405; include('error.' . $phpEx); die();
		break;
endswitch;

include('includes/' . TEMPLATE . '.' . $phpEx);
