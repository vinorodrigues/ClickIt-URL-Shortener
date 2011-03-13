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
?>
<!DOCTYPE html>
<html>
<head>
<?php if (isset($head_prefix)) echo $head_prefix; ?>
	<title><?php echo $head_title; ?></title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<?php echo $head; ?>
	<link rel="stylesheet" type="text/css" href="includes/yui/build/cssbase/base-min.css">
	<link rel="stylesheet" type="text/css" href="includes/yui/build/cssgrids/grids-min.css">
	<!-- <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Ubuntu' type='text/css'> --> 
	<link rel="stylesheet" href="<?php echo $base_path; ?>css/style.css" type="text/css" />	
	<script <?php echo "type=\"text/javascript\"";  /* Eclipse doen't like 'type' attrib */ ?>>
		if (window.screen.colorDepth >= 24)
			document.documentElement.setAttribute('high-color-depth', '');
	</script>
<?php if (isset($head_suffix)) echo $head_suffix; ?>
</head>
<body>
<div id="cell"><div id="box" class="boxed rounded shadow">
<a href="<?php echo $base_path; ?>" id="logo"><img src="<?php echo $base_path . $logo; ?>" alt="<?php echo $site_name; ?>" /></a>
<h1 id="title"><?php echo $title; ?></h1>
<?php print $content; ?>
</div></div>
<div id="footer">Copyleft <small>(CC)</small> 2011 <a href="http://tecsmith.com.au">Tecsmith</a> - 
<a rel="license" href="<?php echo $base_path; ?>license">Some rights reserved</a>.</div>
</body>
</html>		
