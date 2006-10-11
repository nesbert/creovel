<h1>Changeset <?= $this->changeset->revision ?></h1>

<div id="changeset_log" style="background: #ffffaa; border: 1px solid #ccc; padding: 0px 10px;">
	<?= $this->textile->textilethis($this->changeset->log) ?>
</div>

<p>
	<b>Committed By: <?= $this->changeset->author ?><br />
	<b>Date: <?= display_date($this->changeset->created_at) ?><br />
</p>

<h2>Affected Files:</h2>
<div id="changeset_files">
	<?= $this->textile->textilethis($this->changeset->changed_files) ?>
</div>
<br />

<?= $this->render_partial('diff', array( 'changeset' => $this->changeset )) ?>
