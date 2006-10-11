<?= $this->render_partial('revision_form') ?>

<h1>Source "<?= $this->path ?>"</h1>

<? if (preg_match('/.(jpg|gif|png)/i', $this->path) > 0) { ?>

	<img src="/raw_image.php?revision<?= $this->revision?>&path=<?= $this->path ?>" />

<? } else { ?>

	<table class="diff_file" border="0" cellpadding="0" cellspacing="0">
		<? $number = 1 ?>
		<? foreach (preg_split("/<br \/>/", highlight_string($this->file, true)) as $line) { ?>
			<tr style="background: #'.$style.'; border-bottom: 1px solid #ccc;">
				<td class="number"><?= $number ?></td>
				<td class="line" class="<?= cycle('odd', 'even') ?>"><?= $line ?></td>
			</tr>
			<? $number++ ?>
		<? } ?>
	</table>

<? } ?>
