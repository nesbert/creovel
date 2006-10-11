<div id="changessets">
<? foreach ($this->changessets as $changesset) { ?>
	<div class="changeset">
		<table>
			<tr>
				<td width="300">
						<h2>Changeset <?= $changesset->revision ?></h2>
						<p><b>Author:</b> <?= $changesset->author ?><br /><b>Date:</b> <?= display_date($changesset->created_at) ?></p>
				</td>
				<td>
					<?= $this->textile->TextileThis($changesset->log) ?>
					<?= $this->textile->TextileThis($changesset->changed_files) ?>
				</td>
			</tr>
		</table>
	</div>
<? } ?>
</div>
