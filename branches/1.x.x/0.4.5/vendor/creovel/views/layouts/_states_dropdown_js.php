<?php
/**
 * Partial used for Javascript country to state switcher.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 * @see         error_messages_for()
 **/

// create JS objects
$countries = array(
    'US' => CLocale::states('US'),
    'CA' => CLocale::states('CA')
    );
$objects = array();
foreach ($countries as $country => $states) {
    $temp = array();
    foreach ($states as $k => $v) {
        $temp[] = "'{$k}': '{$v}'";
    }
    $objects[$country] = implode(', ', $temp);
}
?><script language="javascript" type="text/javascript">
<!--
<?php foreach ($objects as $country => $object) { ?>
var <?php echo $country; ?> = {<?php echo trim($object); ?>}
<?php } ?>
function updateState(country, state_id, default_value) {
    var state = document.getElementById(state_id);
    var o = '';
    
    if (country == 'US' || country == 'CA') {
        o = eval(country);
    }
    
    if (state.tagName == 'SELECT') {
        state.options.length = 0;
    }
    
    <?php if ($state_input) { ?>var name = state.getAttribute('name');
    var css = state.getAttribute('class');
    var title = state.getAttribute('title');
    var span = document.getElementById('<?php echo $state_id; ?>-wrap');
    // remove current element
    span.removeChild(state);
    <?php } ?>
    
    if (o) {
        <?php if ($state_input) { ?>var input = document.createElement('select');
        input.setAttribute('name', name);
        input.setAttribute('id', state_id);
        if (css) input.setAttribute('class', css);
        if (title) input.setAttribute('title', title);
        span.appendChild(input);
        state = document.getElementById(state_id);
        <?php } ?>state.options[state.options.length] = new Option('Please select...', '');
        for (var k in o) {
            state.options[state.options.length] = new Option(o[k], k);
        }
    } else {
        <?php if ($state_input) { ?>var input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('name', name);
        input.setAttribute('id', state_id);
        if (css) input.setAttribute('class', css);
        if (title) input.setAttribute('title', title);
        span.appendChild(input);
        <?php } else { ?>
        state.options[state.options.length] = new Option("None Available", "");
        <?php } ?>
    }
}
-->
</script>
