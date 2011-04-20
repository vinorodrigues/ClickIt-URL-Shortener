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

/* ----- Includes ----- */
require_once('includes/library.php');
initialize_settings();

/* ----- Offline ----- */
if ($settings['offline']) :
	include('index-offline.php');
	exit();
endif;

/* ----- Get URL ----- */
if (isset($_REQUEST['url'])) :
	$expectedURL = $_REQUEST['url'];
else :
	$e = 404;
	include('error.php');
	die(404);
endif;
$shortURL = strtolower( preg_replace("/[^a-z0-9_]+/i", "", $expectedURL) );
// test for file existance of urls like "about" and redirect to 'about.php'
if (file_exists($shortURL . '.php')) :
	// make sure it's not this file or else we'll go into a infinite loop
	if ( strcasecmp($shortURL, pathinfo(__FILE__, PATHINFO_FILENAME)) != 0 ) :
		// include($shortURL . '.php');
		$e = (isset($settings['force302']) && $settings['force302']) ? 302 : 303;
		header("Location: $shortURL.php", true, $e);  // 303 See Other - correct way to redirect local URI 
		exit($e);
	endif;
endif;
$actionURL = substr( preg_replace("/[a-z0-9_]/i", "", $expectedURL), 0, 1);

/* ---- find url ----- */
$isShortURL = false;

set_error_handler('error_handler');
set_exception_handler('exception_handler');	

$db = initialize_db();

if (empty($m)) :  // was there an error connecting
	$sql = 	"SELECT * FROM $URLS_TABLE" .  
		" WHERE " . $db->sql_build_array('SELECT', array(
			'shorturl' => $shortURL,
			'enabled' => 1,  // true,
			));
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	if ($row) :	
		$p_cloak = (boolean) $row['cloak']; 
		switch ($actionURL) :
			case '-' : $p_action = 'preview'; break;
			// case '@' : $p_action = 'data'; break; // TODO : for Version 0.3
			default : $p_action = $p_cloak ? 'cloak' : 'redir'; 
		endswitch;

		$id = (int) $row['id'];
		$p_title = $row['title'];
		$p_url = $row['longurl'];
		if ($p_cloak && !empty($row['metakeyw'])) $p_metakeyw = $row['metakeyw'];
		if ($p_cloak && !empty($row['metadesc'])) $p_metadesc = $row['metadesc']; 
		$p_log = (boolean) $row['log'];
		// $p_analytics = (boolean) $row['analytics'];  // TODO : for Version 0.3
		
		$db->sql_freeresult($result);
		
		// update lastvisiton
		$sql = "UPDATE $URLS_TABLE" .
			" SET " . $db->sql_build_array('UPDATE', array('lastvisiton' => microtime(true))) .
			" WHERE " . $db->sql_build_array('SELECT', array('id' => $id));
		$db->sql_query($sql);

		// log visit
		if ($p_log) :
			$data = array(
				'urlid' => $id,
				'accessedon' => microtime(true),
				'ipaddress' => $db->sql_escape($_SERVER['REMOTE_ADDR']),
				'referer' => isset($_SERVER['HTTP_REFERER']) ? $db->sql_escape($_SERVER['HTTP_REFERER']) : '',
				);
				
			$bf = isset($settings['func_getbrowser']) ? 'includes/'.$settings['func_getbrowser'] : false;
			if ($bf && file_exists($bf)) :
				include_once($bf);
				$brwsr = _get_browser($_SERVER['HTTP_USER_AGENT']);
				$data['browser'] = $db->sql_escape($brwsr['browser']);
				
				$ver = explode('.', $brwsr['version'], 3);
				$verstr = $ver[0];
				if (isset($ver[1])) $verstr .= '.' . $ver[1]; 
				$data['version'] = $db->sql_escape($verstr);
				
				$data['platform'] = $db->sql_escape($brwsr['platform']);
				unset($brwsr);
			endif;
			unset($bf);
			
			$sql = "INSERT INTO $LOG_TABLE " . $db->sql_build_array('INSERT', $data);
			$db->sql_query($sql);
		endif;
		
		// set visit occurance to google analitics
		// TODO : for Version 0.3
		/* if ($p_analytics) :
			 
		endif; */
		
	else :
		$db->sql_freeresult($result);
		$e = 404;
	endif;
endif;

$db->sql_close();
	
restore_exception_handler();
restore_error_handler();
if (!empty($m) || (isset($e))) :  // Opps!  Something went wrong
	if (!isset($e)) $e = 500;
	include('error.php');
	die();
endif;

switch($p_action) :
	case 'preview' :
		initialize_settings();
		initialize_lang();
		
		$p_delay = 60;
		if (isset($settings['preview_delay'])) $p_delay = $settings['preview_delay'];
		if ($p_delay > 0) :
			$page['head_prefix'] = "\t<meta http-equiv=\"refresh\" content=\"$p_delay;$p_url\">\n";
			ob_start();
?>
	<script type="text/javascript">
		var seconds=<?php echo $p_delay; ?>; 
		var int = window.setInterval("countdown()",1000);

		function countdown() { 
			seconds--; 
			var count = document.getElementById("countdown"); 
			count.innerHTML = "<?php P('SEC_PREFIX'); ?>"+seconds+"<?php P('SEC_SUFIX'); ?>"; 
			if (seconds == 0) { 
				window.clearInterval(int);
				count.innerHTML = "";
				window.location = "<?php echo $p_url; ?>"
			} 
		}
	</script>
<?php
			$page['head_suffix'] = ob_get_clean();			
		endif;

		$page['head_title'] = "$p_title ($p_url)";
		$page['title'] = $p_title;
		$page['content'] = T('REDIRECTING_TO', array('url' => $p_url)); 
		if ($p_delay > 0) $page['content'] .= " <small><span id=\"countdown\"></span></small>";
		include('includes/' . TEMPLATE . '.php');
		break;

	case 'cloak' :
		include('cloak.php');
		break;
		
	default : // 'redir'
		$ref_path = '';
		$ref_path .= (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
		$ref_path .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];
		$ref_path .= $_SERVER["REQUEST_URI"];
		header("Referer: $ref_path");  // be nice and tell the other server where you came from
		$e = (isset($settings['force302']) && $settings['force302']) ? 302 : 307;
		header("Location: $p_url", true, $e);
		break;
		
endswitch;

?>
