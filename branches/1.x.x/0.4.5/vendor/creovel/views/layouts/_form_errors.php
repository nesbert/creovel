<?php
/**
 * Partial used for displaying form errors views.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @see         CForm::error_messages_for()
 **/
?><div class="errors">
<div class="top"></div>
<div class="body">
<?php echo(!empty($title) ? '<h1 class="error_title">'.$title.'</h1>' : ''); ?>
<?php echo(!empty($description) ? '<p>'.$description.'</p>' : ''); ?>
<ul>
<?php echo(!empty($li_str) ? $li_str : ''); ?>
</ul>
</div>
<div class="bottom"></div>
</div>
