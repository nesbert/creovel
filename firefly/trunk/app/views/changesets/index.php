<div id="changesets">
<? foreach ($this->changesets as $changeset) { ?>
	<div class="changeset">
		<table>
			<tr>
				<td width="200">
						<h2><a href="changesets/view/<?= $changeset->id ?>">Changeset <?= $changeset->revision ?></a></h2>
						<p><b>Author:</b> <?= $changeset->author ?><br /><b>Date:</b> <?= display_date($changeset->created_at) ?></p>
				</td>
				<td>
					<?= $this->textile->textilethis($changeset->log) ?>
					<?= $this->textile->textilethis($changeset->changed_files) ?>
				</td>
			</tr>
		</table>
	</div>
<? } ?>
</div>
