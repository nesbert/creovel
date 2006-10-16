<?= error_messages_for() ?>

<form action="" method="post">

	<fieldset>

		<? if ($this->user->access_level == 'tester') { ?>
			<?= hidden_field('ticket[in_queue]', 1) ?>
		<? } else { ?>
			<?= hidden_field('ticket[in_queue]', 0) ?>
		<? } ?>
	
		<legend><strong>Create New Ticket Information</strong></legend>
		
		<div class="required">
			<?= $this->ticket->label_for_title('Title') ?>
			<?= $this->ticket->text_field_for_title(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->ticket->label_for_author('Author') ?>
			<?= $this->ticket->text_field_for_author(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->ticket->label_for_description('Description') ?>
			<?= $this->ticket->textarea_for_description(array('class' => 'field', 'size' => 20, 'rows' => 10)) ?>
		</div>		

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
			<?= $this->ticket->label_for_user_id('Assigned To') ?>
			<?= $this->ticket->select_for_user_id($this->users, array('class' => 'field' )) ?>
		</div>		

	</fieldset>

	<div class="submit">
		<input type="submit" value="Create Ticket" />
	</div>

</form>
