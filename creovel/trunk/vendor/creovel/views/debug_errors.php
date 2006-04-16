<h1>Fatal Error</h1>
<p><?=$this->message?></p>

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

<h1>Environment</h1>
<?=print_obj($_ENV)?>

<h1>Constants</h1>

<h1>Files</h1>
<?=print_obj(get_included_files())?>