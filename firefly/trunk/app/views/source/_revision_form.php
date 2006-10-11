<form id="revision_form" action="" method="get" style="float: right;">
	Revision
	<select name="revision" onchange="$('revision_form').submit();">
		<? for ($i = $this->youngest; $i > 0; $i--) { ?>
			<option value="<?= $i ?>"<?= ($this->revision == $i) ? ' selected="selected"' : '' ?>><?= $i ?></option>
		<? } ?>
	</select>
	<?= hidden_field('path', $this->path) ?>
</form>
