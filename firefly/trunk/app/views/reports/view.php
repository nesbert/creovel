<h1>Report for <?= $this->start_date ?> to <?= $this->end_date ?></h1>
<br />

<h2>Tickets</h2>
<? if ($this->tickets->row_count() > 0) foreach ($this->tickets as $ticket) { ?>
	<table border="0" cellpadding="0" cellspacing="0" style="padding-top: 20px; border-top: 1px solid #ccc;">
		<tr>
			<td width="75" valign="top">#<?= $ticket->id ?></td>
			<td valign="top">
				<strong>Summary</strong>
				<p><?= $ticket->title ?></p>
				<strong>Description</strong>
				<?= $this->textile->TextileThis($ticket->description) ?>
			</td>
		</tr>
	</table>
	<br />
<? } else { ?>
	<p>No Tickets</p>
<? } ?>
<br />

<h2>Changesets</h2>
<? if ($this->changesets->row_count() > 0) foreach ($this->changesets as $changeset) { ?>
	<div style="border-top: 1px solid #ccc;">
		<h3>Revision <?= $changeset->revision ?></h3>
		<?= ($changeset->log == '') ? 'No description entered.' : $this->textile->TextileThis($changeset->log) ?>
	</div>
<? } else { ?>
	<p>No New Changesets</p>
<? } ?>
<br />
