<form action="/reports/view" method="post">

	<fieldset>

		<legend><strong>Report Criteria</strong></legend>
		
		<div class="required">
			<label>Start Date</label>
			<?= date_select('start_date', (time() - (60*60*24*7)), array( 'style' => 'width: 75px;' )) ?>
		</div>		

		<div class="required">
			<label>End Date</label>
			<?= date_select('end_date', time(), array( 'style' => 'width: 75px;' )) ?>
		</div>		

	</fieldset>

	<div class="submit">
		<input type="submit" value="Create Report" />
	</div>

</form>
