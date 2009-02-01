<?php
/**
 * Partial view used by Creovel to page source.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
?><div class="code" id="source_<?=$trace_count?>" style="display:<?=( isset($_GET['view_source']) ? 'block' : 'none' )?>;">
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
		
		if (isset($trace['line']) && $count == $trace['line']) {
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