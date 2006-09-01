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
<div class="code" id="source_<?=$trace_count?>" style="display:<?=( isset($_GET['view_source']) ? 'block' : 'none' )?>;">
	<table cellspacing="0" class="source">
	<tr>
		<td>
			<code>
<?
$handle = @fopen($trace['file'], "r");

if ($handle) {

	$count = 1;

	while (!feof($handle)) {
	
		switch ( true ) {
			
			case ( $count < 10 ):
				$zeros = '000';
			break;
			
			case ( $count < 100 ):
				$zeros = '00';
			break;
			
			default:
				$zeros = '0';
			break;
			
		}
		
		$buffer = fgets($handle, 4096);
		if ( $count == $trace['line'] ) {
			echo "<strong class=\"red\">#{$zeros}{$count}</strong>&nbsp;&nbsp;\n<br />";
		} else {
			echo "#{$zeros}{$count}&nbsp;&nbsp;\n<br />";
		
		}
		$count++;
	}
	
	fclose($handle);
}
?>
			</code>
		</td>
		<td><?=highlight_file($trace['file'], true)?></td>
	</tr>
	</table>
</div>