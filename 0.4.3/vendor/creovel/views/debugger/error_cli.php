<?php
/**
 * Layout view used by Creovel to display CLI error information.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.2
 **/
?>

[Application Error]------------------------------

<?=strip_tags(str_replace(array('<em>', '</em>', '<strong>', '</strong>'), '"', $this->message)) . "\n"?>

<?php if ((is_object($this->exception)) && ($error_count = count($this->exception->getTrace()))) { ?>
[Debug Trace]------------------------------------

<?php
$trace_count = 0;
$offset = 0;
?>
<?php foreach ($this->exception->getTrace() as $trace) {
        
    // skip traces with no file or line number or magic fucntion calls
    if (!isset($trace['file']) || in_string('__call', $trace['function']) ) {
        $offset++;
        continue;
    }
    
    $num = $error_count - $trace_count - $offset;
    ?>
# <?=$num?> <?=(isset($trace['class']) ? $trace['class'] : '') . (isset($trace['type']) ? $trace['type'] : '') . (isset($trace['function']) ? $trace['function'] : '')?> in "<?=$trace['file']?>" on line <?=$trace['line']. "\n"?>
<?php $trace_count++;
    }
}
echo "\n";
?>