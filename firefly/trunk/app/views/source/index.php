<?= $this->render_partial('revision_form') ?>

<h1>Path: <?= parse_path($this->revision, $this->path) ?></h1>

<table class="data" border="0">
	<? foreach ($this->tree as $file) { ?>
		<tr class="row <?= cycle('odd', 'even') ?>">
			<td width="20"><img src="<?= ($file['type'] == 'dir') ? "http://tango.freedesktop.org/static/cvs/tango-icon-theme/22x22/places/folder.png" : "http://tango.freedesktop.org/static/cvs/tango-icon-theme/22x22/mimetypes/text-x-generic.png" ?>" /></td>

			<? if ($file['type'] == 'dir') { ?>
				<td><a href="?revision=<?= $this->revision ?>&path=<?= $this->path ?>/<?= $file['name'] ?>"><?= $file['name'] ?></a></td>
			<? } else { ?>
				<td><a href="source/view_file?revision=<?= $this->revision ?>&path=<?= urlencode($this->path) ?>/<?= $file['name'] ?>"><?= $file['name'] ?></a></td>
			<? } ?>

		</tr>
	<? } ?>
</table>
