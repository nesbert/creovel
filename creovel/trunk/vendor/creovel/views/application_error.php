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
<h1 class="top">Application Error</h1>
<p class="top"><?=$this->message?></p>

<? if ( count($this->traces) ) { ?>
<h1>Debug Trace</h1>
<ul class="debug">
<?
$trace_count = 0;
$offset = 0;
foreach ( $this->traces as $trace ) {

	// skip traces with no file or line number or magic fucntion calls
	if ( !$trace['file'] || !$trace['file'] || strstr($trace['function'], '__call') ) {
		$offset++;
		continue;
	}
	?>
	<li>
		#<?=( count($this->traces) - $trace_count - $offset)?> <?=$trace['class'] . $trace['type'] . $trace['function'] . str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')") ?> in <strong><a href="javascript:void(0);" onclick="_Toggle('source_<?=$trace_count?>');"><?=$trace['file']?></a></strong> on line <strong><?=$trace['line']?></strong>
		<? include(dirname(__FILE__).DS.'_source_code.php') ?>
	</li>
	<?
	$trace_count++;
}
?>
</ul>
<? } ?>

<p><a href="javascript:void(0);" onclick="var obj = document.getElementById('creoinfo'); if (obj.style.display='none') obj.style.display=''; this.style.display='none';">More info...</a></p>

<div id="creoinfo" style="display:none;">
<? include(dirname(__FILE__).DS.'info.php') ?>
</div>