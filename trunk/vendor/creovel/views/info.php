<?php
/**
 * Layout view used by creovel to display application information.
 *
 * @author Nesbert Hidalgo
 */
?>
<div class="block title">
	<h1>creovel <?=get_version()?></h1>
</div>

<table cellspacing="0" class="block">
<tr><td class="sub">Release Date</td><td><?=get_release_date()?></td></tr>
<tr>
	<td class="sub">Registered Adapters</td>
	<td>
		<em>
		<?php if ( $adapters = get_creovel_adapters() ) foreach ( $adapters as $name => $file) { ?>
		<a href="<?=$_SERVER['REQUEST_URI']?><?=( strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' )?>view_source=<?=$file?>"><?=$name?></a>
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
		<?php if ( $services = get_creovel_services() ) foreach ( $services as $name => $file) { ?>
		<a href="<?=$_SERVER['REQUEST_URI']?><?=( strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' )?>view_source=<?=$file?>"><?=$name?></a>
		<?php } else { ?>
		Not Available
		<?php } ?>
		</em>
	</td>
</tr>
</table>

<h1>Environment</h1>
<table cellspacing="0" class="block environment">
<tr><td class="sub">Mode</td><td><?=$_ENV['mode']?></td></tr>
<tr>
	<td class="sub">Routes</td>
	<td>
		<?php if ( count($_ENV['routes']) ) foreach($_ENV['routes'] as $route => $value) { ?>
		<dl>
			<dt><?=$route?></dt>
			<?php if ( count($value) ) foreach($value as $key => $val) { ?>
			<dd><?=$key?> => <?=$val?></dd>
			<?php } ?>
		</dl>
		<?php } ?>	
	</td>
</tr>
<tr>
	<td class="sub">Database Settings</td>
	<td>
		<dl>
			<dt>development</dt>
			<?php if ( count($_ENV['development']) ) foreach($_ENV['development'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<?php } ?>
		</dl>
		<dl>
			<dt>test</dt>
			<?php if ( count($_ENV['test']) ) foreach($_ENV['test'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<?php } ?>
		</dl>
		<dl>
			<dt>production</dt>
			<?php if ( count($_ENV['production']) ) foreach($_ENV['production'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<?php } ?>
		</dl>
	</td>
</tr>
</table>

<h1>Constants</h1>
<table cellspacing="0" class="block constants">
<?php foreach(get_user_defined_constants() as $key => $value) { ?>
<tr><td class="sub"><?=$key?></td><td><?=$value?></td></tr>
<?php } ?>
</table>

<h1>Files</h1>
<table cellspacing="0" class="block constants">
<?php foreach(get_included_files() as $file => $value) { ?>
<?php $total_filesize += filesize($value); ?>
<tr>
	<td class="sub"><?=( $file + 1 )?>.</td>
	<td>
		<?php if ( $_ENV['view_source'] ) { ?>
		<a href="<?=$_SERVER['REQUEST_URI']?><?=( strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' )?>view_source=<?=$value?>"><?=$value?> (<?=get_filesize($value)?>)</a>
		<?php } else { ?>
		<?=$value?> (<?=get_filesize($value)?>)
		<?php } ?>
	</td>
</tr>
<?php } ?>
<tr><td class="sub">Total</td><td><?=( $file + 1 )?> Files (<?=get_filesize($total_filesize)?>)</td></tr>
</table>