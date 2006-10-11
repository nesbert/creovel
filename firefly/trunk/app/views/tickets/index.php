<h1>Showing <?= $this->tickets->row_count()?> Tickets (<a href="#" onclick="Element.toggle('ticket_filter');">Filter Tickets</a>)</h1>

<table class="data">
	<tr>
		<th>Number</th>
		<th>Title</th>
		<th>Milestone</th>
		<th>Status</th>
		<th>Severity</th>
		<th>Part</th>
		<th>Created At</th>
	</tr>

	<? if (count($this->params) > 0) { ?>
		<tr id="ticket_filter" style="background: #ccc; margin: 0 0 15px; padding: 5px;">
	<? } else { ?>
		<tr id="ticket_filter" style="display: none; background: #ccc; margin: 0 0 15px; padding: 5px;">
	<? } ?>

		<td colspan="7">
			Status: <? foreach ($this->statuses as $status) { ?> <a href="<?= filter_link('status', $status->id, $this->params) ?>"><?= ($this->params['status'] == $status->id) ? "<b>{$status->name}</b>" : $status->name ?></a> &nbsp; <? } ?><br />
			Parts: <? foreach ($this->parts as $part) { ?> <a href="<?= filter_link('part', $part->id, $this->params) ?>"><?= ($this->params['part'] == $part->id) ? "<b>{$part->name}</b>" : $part->name ?></a> &nbsp; <? } ?><br />
			Severities: <? foreach ($this->severities as $severity) { ?> <a href="<?= filter_link('severity', $severity->id, $this->params) ?>"><?= ($this->params['severity'] == $severity->id) ? "<b>{$severity->name}</b>" : $severity->name ?></a> &nbsp; <? } ?><br />
			Milestones: <? foreach ($this->milestones as $milestone) { ?> <a href="<?= filter_link('milestone', $milestone->id, $this->params) ?>"><?= ($this->params['milestone'] == $milestone->id) ? "<b>{$milestone->name}</b>" : $milestone->name ?></a> &nbsp; <? } ?><br />
			Users: <? foreach ($this->users as $user) { ?> <a href="<?= filter_link('user', $user->id, $this->params) ?>"><?= ($this->params['user'] == $user->id) ? "<b>{$user->name}</b>" : $user->name ?></a> &nbsp; <? } ?>
		</td>
	</tr>

	<? if ($this->tickets->row_count() > 0) foreach ($this->tickets as $ticket) { ?>
		<tr class="row <?= cycle('odd', 'even') ?>" onclick="window.location = '/tickets/view/<?= $ticket->id ?>'">
			<td>#<?= $ticket->id ?></td>
			<td><?= $ticket->title ?></td>
			<td><?= $ticket->milestone->name ?></td>
			<td><?= ucwords($ticket->status->name) ?></td>
			<td><?= $ticket->severity->name ?></td>
			<td><?= $ticket->part->name ?></td>
			<td align="right"><?= display_date($ticket->created_at) ?></td>
		</tr>
	<? } else { ?>
		<tr><td colspan="7">There are no tickets for this criteria.</td></tr>
	<? } ?>
</table>


<? if ($this->in_queue_tickets->row_count() > 0) { ?>
	<br /><br />
	<h1>Showing <?= $this->in_queue_tickets->row_count()?> Tickets In Queue</h1>
	<table class="data">
		<tr>
			<th>Number</th>
			<th>Title</th>
			<th>Milestone</th>
			<th>Status</th>
			<th>Severity</th>
			<th>Part</th>
			<th>Created At</th>
		</tr>
		<? foreach ($this->in_queue_tickets as $ticket) { ?>
			<tr class="row <?= cycle('odd', 'even') ?>" onclick="window.location = '/tickets/view/<?= $ticket->id ?>'">
				<td>#<?= $ticket->id ?></td>
				<td><?= $ticket->title ?></td>
				<td><?= $ticket->milestone->name ?></td>
				<td><?= ucwords($ticket->status->name) ?></td>
				<td><?= $ticket->severity->name ?></td>
				<td><?= $ticket->part->name ?></td>
				<td align="right"><?= display_date($ticket->created_at) ?></td>
			</tr>
		<? } ?>
	</table>
<? } ?>
