<h1>Tickets</h1>
<table class="data">
	<? foreach ($this->tickets as $ticket) { ?>
		<tr onclick="window.location = '/tickets/view/<?= $ticket->id ?>'">
			<td><?= $ticket->summary ?></td>
			<td><?= $ticket->author ?></td>
			<td align="right"><?= display_date($ticket->created_at) ?></td>
		</tr>
	<? } ?>
</table>
