<table cellspacing="0" class="block constants">
<?php foreach($data as $key => $value) { ?>
<tr><td class="sub"><?php echo $key; ?></td><td><?php echo nl2br(print_r($value, 1)); ?></td></tr>
<?php } ?>
</table>
