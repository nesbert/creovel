<?php
/*
	View used by Creovel to page source.
*/
?>
<div class="code" id="source_<?=$trace_count?>" style="display:<?=( isset($_GET['view_source']) ? 'block' : 'none' )?>;">
	<table cellspacing="0" class="source">
	<tr>
		<td>
			<code>
<?php
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