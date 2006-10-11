<?= error_messages_for($this->errors, 'Errors have prohibited you from accessing this area.') ?>

<form action="" method="post">

	<fieldset>
	
		<legend><strong>Login</strong></legend>
		
		<div class="required">
			<?= $this->user->label_for_email('Name') ?>
			<?= $this->user->text_field_for_email(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

		<div class="required">
			<?= $this->user->label_for_password('Password') ?>
			<?= $this->user->password_field_for_password(array('class' => 'field', 'size' => 20, 'maxlength' => 255)) ?>
		</div>		

	</fieldset>

	<div class="submit">
		<input type="submit" value="Login" />
	</div>

</form>
