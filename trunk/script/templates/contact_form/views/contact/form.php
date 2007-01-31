<h2>Contact Form</h2>
<style>

form { width:540px; }
	fieldset { clear:both; margin:0 0 10px 0; }
		fieldset div { clear:left; margin:6px 4px; }
			fieldset label { float:left; display:block; width:180px; margin:2px 0 6px 0; padding:0 6px 0 0; text-align:right; }
			fieldset input.field,
			fieldset select,
			fieldset textarea { width:200px; margin:0; padding:1px 2px; font-family:"Times New Roman", Times, serif; }
			fieldset select { width:208px; }
			fieldset small { display:block; }
		fieldset div.required {}
			fieldset div.required label { font-weight:bold; }
		fieldset div.submit, fieldset small { margin-left:190px; }
		
	.errors { margin:0 0 10px 0; background:#ffc; border:1px double #6a6a6a; }
		.error_title { margin:0; padding:6px; background-color:#c00; color:white; font-size:100%; }
		.errors p { margin:6px }
		.errors li a { color:#000; text-decoration:none; }
		
	.errors_field label { color:#c00; }
	.errors_field input,
	.errors_field select,
	.errors_field textarea { background-color:#ffc; }		
		
</style>
<form method="post">

	<p><strong>Bold</strong> fields are required.</p>
	
	<?=error_messages_for($this->contact, 'Errors have prohibited the message from being sent.')?>

	<fieldset>
	
		<legend>Personal Information</legend>
		
		<div class="required">
			<?=$this->contact->label_for_name()?>
			<?=$this->contact->text_field_for_name(array('class' => 'field', 'size' => 20, 'maxlength' => 100))?>
		</div>
		
		<div>
			<?=$this->contact->label_for_address_1('Address')?>
			<?=$this->contact->text_field_for_address_1(array('class' => 'field', 'size' => 10, 'maxlength' => 100))?>
			<?=$this->contact->text_field_for_address_2(array('class' => 'field left-margin', 'size' => 10, 'maxlength' => 100))?>
		</div>
		
		<div>
			<?=$this->contact->label_for_city()?>
			<?=$this->contact->text_field_for_city(array('class' => 'field', 'size' => 10, 'maxlength' => 100))?>
		</div>
		
		<div>
			<?=$this->contact->label_for_state()?>
			<?=$this->contact->select_for_state($this->contact->states)?>
		</div>
		
		<div>
			<?=$this->contact->label_for_zip('Zip/Postal Code')?>
			<?=$this->contact->text_field_for_zip(array('class' => 'field', 'size' => 10, 'maxlength' => 10))?>
		</div>
		
		<div>
			<?=$this->contact->label_for_phone()?>
			<?=$this->contact->text_field_for_phone(array('class' => 'field', 'size' => 10, 'maxlength' => 20))?>
		</div>
		
		<div>
			<?=$this->contact->label_for_fax()?>
			<?=$this->contact->text_field_for_fax(array('class' => 'field', 'size' => 10, 'maxlength' => 20))?>
		</div>
		
		<div class="required">
			<?=$this->contact->label_for_email('Email')?>
			<?=$this->contact->text_field_for_email(array('class' => 'field', 'size' => 10, 'maxlength' => 150))?>
		</div>
		
	</fieldset>

	<fieldset>
	
		<legend>Contact Information</legend>
		
		<div class="required">
			<?=$this->contact->label_for_subject()?>
			<?=$this->contact->text_field_for_subject(array('class' => 'field', 'size' => 20, 'maxlength' => 100))?>
		</div>
		
		<div class="required">
			<?=$this->contact->label_for_body('Comment')?>
			<?=$this->contact->textarea_for_body(array('rows' => 10, 'cols' => 20, 'maxlength' => 255))?>
			<small>Must be 255 characters or less.</small>
		</div>		
		
	</fieldset>

	<fieldset>
	
		<div class="submit">
		  <input type="submit" value="Submit" />
		  <input type="submit" value="Cancel" />
		</div>
		
	</fieldset>

</form>