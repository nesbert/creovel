<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>FireFly</title>
	<?= stylesheet_include_tag(array( 'main', 'table' )) ?>
	<link rel="stylesheet" type="text/css" href="/stylesheets/print.css" media="print" />
	<?=javascript_include_tag(array('prototype'))?>
	<?=javascript_include_tag(array('scriptaculous/scriptaculous'))?>
</head>

<body>

<?

$nav = array
(
	array( 'Changesets', '/changesets', 'changesets', 'index' ),
	array( 'Browse Source', '/source', 'source', '*' ),
	array( 'Milestones', '/milestones', 'milestones', 'index' ),
	array( 'Tickets', '/tickets', 'tickets', 'index' ),
	array( 'New Ticket', '/tickets/create', 'tickets', 'create' ),
);

?>

<div id="headerArea">
	<img id="logo" src="/images/firefly.png" />
	<div id="menuArea">
		<? foreach ($nav as $link) { ?>
			<? $active = ($link[2] == get_controller() && ($link[3] == get_action() || $link[3] == '*')) ? ' class="active"' : '' ?>
			<a href="<?= $link[1] ?>"<?= $active ?>><?= $link[0] ?></a>
		<? } ?>
	</div>
</div>

<div id="sideArea">
	<div id="navcontainer">
	  <ul id="navlist">
		<li><a href="/reports/">Reports</a></li>
		<li><a href="/parts/">Parts</a></li>
		<li><a href="/users/">Users</a></li>
		<li><a href="/login/logout">Logout</a></li>
	  </ul>

	  <ul id="navlist">
		<li><a href="/pages/about">About FireFly</a></li>
		<li><a href="http://www.creovel.org" target="blank">Creovel</a></li>
	  </ul>
  </div>
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
