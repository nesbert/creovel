<h4>Contact Request</h4>
<hr />
<strong><?=$this->contact->name?></strong><br />
<?=$this->contact->address_1?><br />
<? if ( $this->contact->address_2 ) { ?>
<?=$this->contact->address_2?><br />
<? } ?>
<?=$this->contact->city?>, <?=$this->contact->state?> <?=$this->contact->zip?><br /><br />

Phone: <?=$this->contact->phone?><br />
Fax: <?=$this->contact->fax?><br />
Email: <?=$this->contact->email?><br />

<hr />
Comments:<br />

<?=stripslashes($this->contact->body)?>

<hr />