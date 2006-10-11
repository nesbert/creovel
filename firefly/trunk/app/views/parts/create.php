<?= error_messages_for() ?>

<form action="" method="post">

	<fieldset>
	
		<legend><strong>New Part Information</strong></legend>
		
		<div class="required">
			<?= $this->part->label_for_name('Name') ?>
			<?= $this->part->text_field_for_name(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

	</fieldset>

	<div class="submit">
		<input type="submit" value="Save Part" />
	</div>

</form>
