<h3 style="float: right;"><a href="/milestones/create">Create New Milestone</a></h3>

<? foreach ($this->milestones as $milestone) { ?>

	<div class="milestone">
		<h1><a href="/milestones/view/<?= $milestone->id ?>">Milestone: <?= $milestone->name ?></a></h1>
		<?= $this->textile->TextileThis($milestone->description) ?>
		<div class="progress_bar">
			<div class="text"><?= @($milestone->closed_tickets->row_count() / $milestone->tickets->row_count()) * 100 ?>%</div>
			<div class="progress" style="width: <?= @($milestone->closed_tickets->row_count() / $milestone->tickets->row_count()) * 100 ?>%;"></div>
		</div>
		<p class="grey">
			<a href="/tickets/index?milestone=<?= $milestone->id ?>">Total Tickets: <?= $milestone->tickets->row_count() ?></a> &nbsp; &nbsp;
			<a href="/tickets/index?milestone=<?= $milestone->id ?>&status=1">Open Tickets: <?= $milestone->open_tickets->row_count() ?></a> &nbsp; &nbsp;
			<a href="/tickets/index?milestone=<?= $milestone->id ?>&status=closed">Closed Tickets: <?= $milestone->closed_tickets->row_count() ?></a>
		</p>
	</div>

<? } ?>
