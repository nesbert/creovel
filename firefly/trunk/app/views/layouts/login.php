<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>FireFly</title>
	<?= stylesheet_include_tag(array( 'main', 'table' )) ?>
	<?=javascript_include_tag(array('prototype'))?>
	<?=javascript_include_tag(array('scriptaculous/scriptaculous'))?>
</head>

<body>

<div id="headerArea">
	<img id="logo" src="/images/firefly.png" />
	<div id="menuArea">
		<a href="/login" class="active">Login</a>
	</div>
</div>

<div id="sideArea">
</div>

<div id="singleColumn">

	<? if (flash_notice()) { ?>
		<div id="notice-wrap"><div id="notice"><?=flash_notice()?></div></div>
	<? } ?>

	@@page_contents@@

	<div id="footer">
		<p><a href="/pages/about">FireFly</a>. A PHP Subversion BugTracker Written in <a href="http://www.creovel.org" target="blank">Creovel</a>.</p>
	</div>

</div> 

</body>
</html>
