<h1>Search</h1>

<?= start_form_tag(array( 'action' => 'index' )) ?>

	<table>
		<tr>
			<td class="label">Search</td>
			<td>
				<?= text_field('query', $this->params['query']) ?>
				<input type="submit" value="Submit" />
			</td>
		</tr>
	</table>

<?= end_form_tag() ?>
