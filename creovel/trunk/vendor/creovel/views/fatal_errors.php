<div class="block fatal_error">
	<h1>Fatal Error</h1>
	<p><?=$this->message?></p>
</div>

<div class="block debug_trace">
	<h1>Debug Trace</h1>
	<ul>
	<?
	$count = 0;
	$traces = $this->exception->getTrace();
	foreach ( $traces as $trace ) {
		?>
		<li>#<?=(count($traces) - $count)?> <?=$trace['class'] . $trace['type'] . $trace['function'] . str_replace("('')", '()', ("('" . implode("', '", $trace['args'])) . "')") ?> in <strong><?=$trace['file']?></strong> on line <strong><?=$trace['line']?></strong></li>
		<?
		$count++;
	}
	?>
	</ul>
</div>

<? include(dirname(__FILE__).DS.'info.php') ?>