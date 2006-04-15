<h1>Milestones</h1>

<? foreach ($this->milestones as $milestone) { ?>

	<div class="milestone">
		<h4><?= $milestone->name ?></h4>
		<p><?= $milestone->info ?></p>
	</div>

<? } ?>
