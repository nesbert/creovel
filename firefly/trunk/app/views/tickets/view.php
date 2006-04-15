<h2><?= $this->ticket->summary ?></h2>
<p><?= $this->ticket->content ?></p>

<hr />

<div id="comment_box">

<?= start_form_tag(array( 'action' => 'comment' )) ?>

<table>
	<tr>
		<td class="label">Name or Email</td>
		<td><?= text_field('author', $this->ticket->author) ?></td>
	</tr>
	<tr>
		<td class="label">Comment</td>
		<td><?= text_area('comment', $this->ticket->summary) ?></td>
	</tr>
</table>

<input type="submit" value="Submit Changes" />

<?= end_form_tag() ?>

</div>
