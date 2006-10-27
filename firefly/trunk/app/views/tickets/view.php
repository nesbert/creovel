<h1>Ticket #<?= $this->ticket->id ?> (<?= $this->ticket->status->name ?>)</h1>
<div class="ticket_description">
	<h4><?= $this->ticket->title ?></h4>
	<?= $this->textile->textilethis($this->ticket->description) ?>
	<div class="ticket_information">
		<p>Created By: <?= $this->ticket->author ?></p>
		<p>Created At: <?= $this->ticket->created_at ?></p>
	</div>
</div>

<?= error_messages_for() ?>

<form action="" method="post">

	<?= $this->ticket->hidden_field_for_id() ?>

	<? if ($this->user->access_level == 'tester') { ?>
		<?= hidden_field('ticket[in_queue]', 1) ?>
	<? } else { ?>
		<?= hidden_field('ticket[in_queue]', 0) ?>
	<? } ?>

	<fieldset>
	
		<legend><strong>Edit Ticket Information</strong></legend>
		
		<div class="required">
			<?= $this->ticket->label_for_milestone_id('Milestone') ?>
			<?= $this->ticket->select_for_milestone_id($this->milestones, array('class' => 'field' )) ?>
		</div>		
	
		<div class="required">
			<?= $this->ticket->label_for_part_id('Part') ?>
			<?= $this->ticket->select_for_part_id($this->parts, array('class' => 'field' )) ?>
		</div>		

		<div class="required">
			<?= $this->ticket->label_for_severity_id('Severity') ?>
			<?= $this->ticket->select_for_severity_id($this->severities, array('class' => 'field' )) ?>
		</div>		

		<div class="required">
			<?= $this->ticket->label_for_status_id('Status') ?>
			<?= $this->ticket->select_for_status_id($this->statuses, array('class' => 'field' )) ?>
		</div>		

		<div class="required">
			<?= $this->ticket->label_for_user_id('Assigned To') ?>
			<?= $this->ticket->select_for_user_id($this->users, array('class' => 'field' )) ?>
		</div>		

		<input type="hidden" name="comment[ticket_id]" value="<?= $this->ticket->id ?>" />
		<input type="hidden" name="comment[author]" value="<?= $this->user->name ?>" />
		<div class="required">
			<?= $this->comment->label_for_description('Comment') ?>
			<?= $this->comment->textarea_for_description(array('class' => 'field', 'size' => 20, 'rows' => 10)) ?>
		</div>		

	</fieldset>

	<div class="submit">
		(<a href="/tickets/delete/<?= $this->ticket->id ?>">Delete Ticket</a>)
		<input type="submit" value="Save Ticket" />
	</div>

</form>
<br />


<? if ($this->ticket->comments->row_count() > 0) { ?>
	<h1>Comments</h1>
	<? foreach ($this->ticket->comments as $comment) { ?>
		<div class="ticket_description">
			<?= $this->textile->textilethis($comment->description) ?>
			<div class="ticket_information">
				<p>Created By: <?= $comment->author ?></p>
				<p>Created At: <?= $comment->created_at ?></p>
			</div>
		</div>
	<? } ?>
<? } ?>

<form action="/tickets/add_comment" method="post">

	<input type="hidden" name="comment[ticket_id]" value="<?= $this->ticket->id ?>" />
	<input type="hidden" name="comment[author]" value="<?= $this->user->name ?>" />

	<fieldset>
		<legend><strong>New Comment</strong></legend>
		<div class="required">
			<?= $this->comment->label_for_description('Comment') ?>
			<?= $this->comment->textarea_for_description(array('class' => 'field', 'size' => 20, 'rows' => 10)) ?>
		</div>		
	</fieldset>

	<div class="submit">
		<input type="submit" value="Add Comment" />
	</div>

</form>
