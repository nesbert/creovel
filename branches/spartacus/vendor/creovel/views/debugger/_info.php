<?php
/*
	Layout view used by creovel to display application information.
*/
?>
<div class="block title">
	<h1>creovel <?=CREOVEL_VERSION?></h1>
</div>

<table cellspacing="0" class="block">
<tr><td class="sub">Version</td><td><?=CREOVEL_VERSION?></td></tr>
<tr><td class="sub">Release Date</td><td><?=CREOVEL_RELEASE_DATE?></td></tr>
<tr>
	<td class="sub">Registered Adapters</td>
	<td>
		<em>
		<?php if ($adapters = get_creovel_adapters()) foreach ($adapters as $name => $file) { ?>
			<?php if (CREO('show_source')) { ?>
			<a href="<?php echo $_SERVER['REQUEST_URI'].strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?'; ?>view_source=<?php echo $file; ?>"><?php echo $name; ?></a>
			<?php } else { ?>
			<?php echo $name; ?>
			<?php } ?>
		<?php } else { ?>
			Not Available
		<?php } ?>
		</em>
	</td>
</tr>
<tr>
	<td class="sub">Registered Services</td>
	<td>
		<em>
		<?php if ($services = get_creovel_services()) foreach ($services as $name => $file) { ?>
			<?php if (CREO('show_source')) { ?>
			<a href="<?php echo $_SERVER['REQUEST_URI'].strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?'; ?>view_source=<?php echo $file; ?>"><?php echo $name; ?></a>
			<?php } else { ?>
			<?php echo $name; ?>
			<?php } ?>
		<?php } else { ?>
			Not Available
		<?php } ?>
		</em>
	</td>
</tr>
</table>

<h1>Environment</h1>
<table cellspacing="0" class="block environment">
<tr><td class="sub">Mode</td><td><?=CREO('mode')?></td></tr>
<?php if (count(CREO('routing'))) { ?>
	<?php $routing = CREO('routing'); ?>
<tr>
	<td class="sub">Routes</td>
	<td>
		<?php foreach ($routing['ROUTES'] as $name => $route) { ?>
		<dl>
			<dt><?=$route['name']?> (<?=$route['url']?>)</dt>
		</dl>
		<?php } ?>
	</td>
</tr>
<?php } ?>
<?php if (count($GLOBALS['CREOVEL']['DATABASES'])) { ?>
<tr>
	<td class="sub">Database Settings</td>
	<td>
		<?php foreach ($GLOBALS['CREOVEL']['DATABASES'] as $mode => $data) { ?>
		<dl>
			<dt><?=strtolower($mode)?></dt>
			<?php if (count($data)) foreach($data as $key => $val) { ?>
			<dd><?=strtolower($key)?> => <?=( $key == 'PASSWORD' ? mask($val) : $val )?></dd>
			<?php } ?>
		</dl>
		<?php } ?>
	</td>
</tr>
<?php } ?>
</table>

<h1>Constants</h1>
<table cellspacing="0" class="block constants">
<?php foreach(get_user_defined_constants() as $key => $value) { ?>
<tr><td class="sub"><?=$key?></td><td><?=$value?></td></tr>
<?php } ?>
</table>

<h1>Files</h1>
<table cellspacing="0" class="block constants">
<?php $total_filesize = 0; ?>
<?php foreach(get_included_files() as $file => $value) { ?>
<?php $total_filesize += filesize($value); ?>
<tr>
	<td class="sub"><?php print($file + 1); ?>.</td>
	<td>
		<?php if (CREO('show_source')) { ?>
		<a href="<?=$_SERVER['REQUEST_URI']?><?=( strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' )?>view_source=<?=$value?>"><?=$value?> (<?php echo get_filesize($value); ?>)</a>
		<?php } else { ?>
		<?php echo $value; ?> (<?php echo get_filesize($value); ?>)
		<?php } ?>
	</td>
</tr>
<?php } ?>
<tr><td class="sub">Total</td><td><?php print($file + 1); ?> Files (<?php echo get_filesize($total_filesize); ?>)</td></tr>
</table>
