<h1>Parts (<a href="/parts/create">Create Part</a>)</h1>

<table class="data">
	<tr>
		<th>Name</th>
		<th>Actions</th>
	</tr>

	<? if ($this->parts->row_count() > 0) foreach ($this->parts as $part) { ?>
		<tr class="row <?= cycle('odd', 'even') ?>" onclick="window.location = '/parts/view/<?= $part->id ?>'">
			<td><?= $part->name ?></td>
			<td>
				<a href="/tickets/index?status=1&part=<?= $part->id ?>">Open Tickets</a> |
				<a href="/tickets/index?status=closed&part=<?= $part->id ?>">Closed Tickets</a>
			</td>
		</tr>
	<? } else { ?>
		<tr><td colspan="7">There are no parts.</td></tr>
	<? } ?>
</table>
