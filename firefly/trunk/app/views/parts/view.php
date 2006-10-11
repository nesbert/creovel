<?= error_messages_for() ?>

<form action="" method="post">

	<?= $this->part->hidden_field_for_id() ?>

	<fieldset>
	
		<legend><strong>Edit Part Information</strong></legend>
		
		<div class="required">
			<?= $this->part->label_for_name('Name') ?>
			<?= $this->part->text_field_for_name(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

	</fieldset>

	<div class="submit">
		(<a href="/parts/delete/<?= $this->part->id ?>">Delete Part</a>)
		<input type="submit" value="Save Part" />
	</div>

</form>
