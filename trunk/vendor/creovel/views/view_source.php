<?php
/**
 * Layout view used by creovel to display application information.
 *
 * @author Nesbert Hidalgo
 */
?>
<h1 class="top">View Source</h1>
<p class="top"><?=$trace['file'] = $_GET['view_source']?> (<?=get_filesize($_GET['view_source'])?>)</p>
<?php include dirname(__FILE__).DS.'_source_code.php' ?>