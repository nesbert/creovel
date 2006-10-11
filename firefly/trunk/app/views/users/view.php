<?= error_messages_for() ?>

<form action="" method="post">

	<?= $this->user->hidden_field_for_id() ?>

	<fieldset>
	
		<legend><strong>Edit User Information</strong></legend>
		
		<div class="required">
			<?= $this->user->label_for_name('Name') ?>
			<?= $this->user->text_field_for_name(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->user->label_for_email('Email') ?>
			<?= $this->user->text_field_for_email(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->user->label_for_password('Password') ?>
			<?= $this->user->password_field_for_password(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->user->label_for_access_level('Access Level') ?>
			<?= $this->user->select_for_access_level(array( 'developer' => 'Developer', 'tester' => 'Tester' ), array('class' => 'field' )) ?>
		</div>		

	</fieldset>

	<div class="submit">
		(<a href="/users/delete/<?= $this->user->id ?>">Delete User</a>)
		<input type="submit" value="Save User" />
	</div>

</form>
