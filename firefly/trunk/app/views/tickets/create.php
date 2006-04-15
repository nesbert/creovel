<h1>New Ticket</h1>

<?= start_form_tag(array( 'action' => 'create' )) ?>

<table>
	<tr>
		<td class="label">Name or Email</td>
		<td><?= text_field('author', $this->ticket->author) ?></td>
	</tr>
	<tr>
		<td class="label">Summary</td>
		<td><?= text_area('summary', $this->ticket->summary) ?></td>
	</tr>
	<tr>
		<td class="label">Comment</td>
		<td><?= text_area('comment', $this->ticket->summary) ?></td>
	</tr>
	<tr>
		<td class="label">Severity</td>
		<td></td>
	</tr>
</table>

<input type="submit" value="Submit Changes" />

<? foreach ($this->parts as $part) { ?>
	<h1><? echo $part->id; ?></h1>
<? } ?>

<?= end_form_tag() ?> 
