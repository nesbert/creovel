<h1>Users (<a href="/users/create">Create User</a>)</h1>

<table class="data">
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th>Access Level</th>
		<th>Actions</th>
	</tr>

	<? if ($this->users->row_count() > 0) foreach ($this->users as $user) { ?>
		<tr class="row <?= cycle('odd', 'even') ?>" onclick="window.location = '/users/view/<?= $user->id ?>'">
			<td><?= $user->name ?></td>
			<td><?= mail_to($user->email) ?></td>
			<td><?= ucwords($user->access_level) ?></td>
			<td>
				<a href="/tickets/index?status=1&user=<?= $user->id ?>">Open Tickets</a> |
				<a href="/tickets/index?status=closed&user=<?= $user->id ?>">Closed Tickets</a>
			</td>
		</tr>
	<? } else { ?>
		<tr><td colspan="7">There are no users.</td></tr>
	<? } ?>
</table>
