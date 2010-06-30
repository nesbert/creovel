<?php
/**
 * Layout view used by Creovel to display application source information.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
?><h1 class="top">View Source</h1>
<p class="top"><?php echo($trace['file'] = $_GET['view_source']); ?> (<?php echo CFile::size($_GET['view_source']); ?>)</p>
<?php include dirname(__FILE__).DS.'_source.php' ?>
<p><a href="javascript: void(0);" onclick="history.back();">Back to Previous</a></p>