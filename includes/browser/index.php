<!DOCTYPE html>
<html>
<head>
	<title>browser.php Test</title>
</head>
<body>
<h1>browser.php Test</h1>
<p>From <a href="http://chrisschuld.com/projects/browser-php-detecting-a-users-browser-from-php/">Chris Schuld</a>.</p>
<form action="<?php print $_SERVER['REQUEST_URI']; ?>" method="get">
<input type="text" name="ua" />
<input type="submit" />
</form>
<hr />
<pre style="background-color: #EEE">
<?php
	include 'browser.php';
	$browser = new Browser(isset($_GET["ua"]) ? $_GET["ua"] : "");
	var_dump($browser);
?>
</pre>
<hr />

<ul>
<li><b>browser</b> = '<?php print $browser->getBrowser(); ?>'</li>
<li><b>version</b> = '<?php print $browser->getVersion(); ?>'</li>
<li><b>platform</b> = '<?php print $browser->getPlatform(); ?>'</li>
</ul>

</body>
</html>
