<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */
?>
<div class="block title">
	<h1>creovel <?=get_version()?></h1>
</div>

<table cellspacing="0" class="block">
<tr><td class="sub">Release Date</td><td><?=get_release_date()?></td></tr>
<tr><td class="sub">Registered Adapters</td><td>coming soon...</td></tr>
<tr><td class="sub">Registered Services</td><td>coming soon...</td></tr>
</table>


<h1>Environment</h1>
<table cellspacing="0" class="block environment">
<tr><td class="sub">Mode</td><td><?=$_ENV['mode']?></td></tr>
<tr>
	<td class="sub">Routes</td>
	<td>
		<? if ( count($_ENV['routes']) ) foreach($_ENV['routes'] as $route => $value) { ?>
		<dl>
			<dt><?=$route?></dt>
			<? if ( count($value) ) foreach($value as $key => $val) { ?>
			<dd><?=$key?> => <?=$val?></dd>
			<? } ?>
		</dl>
		<? } ?>	
	</td>
</tr>
<tr>
	<td class="sub">Database Settings</td>
	<td>
		<dl>
			<dt>development</dt>
			<? if ( count($_ENV['development']) ) foreach($_ENV['development'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<? } ?>
		</dl>
		<dl>
			<dt>test</dt>
			<? if ( count($_ENV['test']) ) foreach($_ENV['test'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<? } ?>
		</dl>
		<dl>
			<dt>production</dt>
			<? if ( count($_ENV['production']) ) foreach($_ENV['production'] as $key => $val) { ?>
			<dd><?=$key?> => <?=( $key == 'password' ? mask($val) : $val )?></dd>
			<? } ?>
		</dl>
	</td>
</tr>
</table>

<h1>Constants</h1>
<table cellspacing="0" class="block constants">
<? foreach(get_user_defined_constants() as $key => $value) { ?>
<tr><td class="sub"><?=$key?></td><td><?=$value?></td></tr>
<? } ?>
</table>

<h1>Files</h1>
<table cellspacing="0" class="block constants">
<? foreach(get_included_files() as $file => $value) { ?>
<? $total_filesize += filesize($value);?>
<tr><td class="sub"><?=( $file + 1 )?>.</td><td><a href="<?=$_SERVER['REQUEST_URI']?><?=( strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' )?>view_source=<?=$value?>"><?=$value?> (<?=get_filesize($value)?>)</a></td></tr>
<? } ?>
<tr><td class="sub">Total</td><td><?=( $file + 1 )?> Files (<?=get_filesize($total_filesize)?>)</td></tr>
</table>