<h1 class="top">View Source</h1>
<p class="top"><?=$trace['file'] = $_GET['view_source']?> (<?=get_filesize($_GET['view_source'])?>)</p>
<? include(dirname(__FILE__).DS.'_source_code.php') ?>