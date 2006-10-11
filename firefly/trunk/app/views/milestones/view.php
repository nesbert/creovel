<?= error_messages_for() ?>

<form action="" method="post">

	<?= $this->milestone->hidden_field_for_id() ?>

	<fieldset>
	
		<legend><strong>Edit Milestone Information</strong></legend>
		
		<div class="required">
			<?= $this->milestone->label_for_name('Name') ?>
			<?= $this->milestone->text_field_for_name(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->milestone->label_for_description('Description') ?>
			<?= $this->milestone->textarea_for_description(array('class' => 'field', 'size' => 20, 'rows' => 10)) ?>
		</div>		

	</fieldset>

	<div class="submit">
		(<a href="/milestones/delete/<?= $this->milestone->id ?>">Delete Milestone</a>)
		<input type="submit" value="Save Milestone" />
	</div>

</form>
