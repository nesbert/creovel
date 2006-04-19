<div class="block general">
	<h1>creovel <?=get_version()?></h1>
</div>

<div class="block environment">
	<h1>Environment</h1>
	<?=print_obj($_ENV)?>
</div>

<div class="block constants">
	<h1>Constants</h1>
	<?=print_obj(get_user_defined_constants())?>
</div>

<div class="block files">
	<h1>Files</h1>
	<?=print_obj(get_included_files())?>
</div>