<?php
/*
	Layout view used by creovel to display application error information.
*/
?>
<h1 class="top">Application Error</h1>
<p class="top"><?=$this->error->message?></p>

<?php if ( $error_count = count($this->error->traces) ) { ?>
<h1>Debug Trace</h1>
<ul class="debug">
<?php
$trace_count = 0;
$offset = 0;
foreach ( $this->error->traces as $trace ) {

	// skip traces with no file or line number or magic fucntion calls
	if ( !$trace['file'] || !$trace['file'] || in_string('__call', $trace['function']) ) {
		$offset++;
		continue;
	}
	?>
	<li>
		<?php if ( $_ENV['view_source'] && $_ENV['mode'] == 'development' ) { ?>
		#<?=($error_count - $trace_count - $offset)?> <?=$trace['class'] . $trace['type'] . $trace['function'] . str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')") ?> in <strong><a href="javascript:void(0);" onclick="_Toggle('source_<?=$trace_count?>');"><?=$trace['file']?></a></strong> on line <strong><?=$trace['line']?></strong>
		<?php include dirname(__FILE__).DS.'_source_code.php'; ?>
		<?php } else { ?>
		#<?=($error_count - $trace_count - $offset)?> <?=$trace['class'] . $trace['type'] . $trace['function'] . str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')") ?> in <strong><?=$trace['file']?></strong> on line <strong><?=$trace['line']?></strong>
		<?php } ?>
	</li>
	<?
	$trace_count++;
}
?>
</ul>
<?php } ?>

<?php if ( $_ENV['mode'] == 'development' ) { ?>
<p><a href="javascript:void(0);" onclick="var obj = document.getElementById('creoinfo'); if (obj.style.display='none') obj.style.display=''; this.style.display='none';">More info...</a></p>

<div id="creoinfo" style="display:none;">
<?php include dirname(__FILE__).DS.'info.php'; ?>
</div>
<?php } ?>