<h1 class="top">Application Error</h1>
<p class="top"><?=$this->message?></p>

<? if ( count($this->traces) ) { ?>
<h1>Debug Trace</h1>
<ul class="debug">
<?
$trace_count = 0;
foreach ( $this->traces as $trace ) {
	?>
	<li>
		#<?=(count($this->traces) - $trace_count)?> <?=$trace['class'] . $trace['type'] . $trace['function'] . str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')") ?> in <strong><a href="javascript:void(0);" onclick="_Toggle('source_<?=$trace_count?>');"><?=$trace['file']?></a></strong> on line <strong><?=$trace['line']?></strong>
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