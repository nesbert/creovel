<?php
/**
 * Global form functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
**/

/**
 * Formats user[name] to user_name.
 *
 * @param string $name
 * @return string
 * @author Nesbert Hidalgo
 **/
function name_to_id($field_name)
{	
    return str_replace(array('[', ']'), array('_', ''), str_replace('[]', '', $field_name));
}

/**
 * Add error to form errors.
 *
 * @param string $field_name
 * @param string $message
 * @return void
 * @author Nesbert Hidalgo
 **/
function add_form_error($field_name, $message = null)
{
    $GLOBALS['CREOVEL']['VALIDATION_ERRORS'][name_to_id($field_name)] =
        $message ? $message : humanize($field_name) . ' is invalid.';
}

/**
 * Check if a field is has an error.
 *
 * @param string $field_name
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function field_has_error($field_name)
{
    return isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS'][name_to_id($field_name)]);
}

/**
 * Check if form has errors.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function form_has_errors()
{
    return form_errors_count() ? true : false;
}

/**
 * Returns the total number of form errors.
 *
 * @return integer
 * @author Nesbert Hidalgo
 **/
function form_errors_count()
{
    return count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']);
}

/**
 * Prints out a formatted errors message box for an object. Errors
 * styles below: 
 *
 * <code>
 * #errors {} // container div
 * #errors .top {} 
 * #errors .body {}
 * #errors .bottom {}
 * #errors h1 {} // title
 * #errors p {} // description
 * #errors ul {} // errors list
 * #errors li {} // errors list items
 * #errors a {} // errors list items links
 * .errors_field {} // html element with the error
 * </code>
 *
 * @param mixed $errors
 * @param string $title Optional default "{number of errors} errors
 * {have or has} prohibited this {object name} from being saved."
 * @param string $description Pptional default There were problems
 * with the following fields."
 * @return string
 * @author Nesbert Hidalgo
 **/
function error_messages_for($errors = null, $title = null, $description = null)
{
    $errors_count = 0;
    $errors = array();
    
    if (!$description
        && isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS_DESCRIPTION'])) {
        $description = $GLOBALS['CREOVEL']['VALIDATION_ERRORS_DESCRIPTION']
                        ? $GLOBALS['CREOVEL']['VALIDATION_ERRORS_DESCRIPTION']
                        : 'There were problems with the following fields.';
    }
    // if no errors check global variable
    if (!$errors && isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS'])) {
        $errors = $GLOBALS['CREOVEL']['VALIDATION_ERRORS'];
    }
    
    if (is_object($errors)) {
        $model = get_class($errors);
        if (isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS'])) {
            $errors_count = count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']);
            $errors = $GLOBALS['CREOVEL']['VALIDATION_ERRORS'];
        }
    } else {
        $errors_count = count($errors);
    }
    
    $li_str = '';
    
    if ($errors_count) foreach ( $errors as $field => $message ) {
        if ( $message == 'no_message') continue;
        $li_str .= create_html_element('li', null,
                        create_html_element('a',                                            array('href' => "#error_{$field}"), $message)) . "\n";
    }
    
    if ($errors_count) {
        if (isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS_TITLE'])) {
            $default_title =  $GLOBALS['CREOVEL']['VALIDATION_ERRORS_TITLE'];
        } else {
            $default_title = "{$errors_count} error" .
            ($errors_count == 1 ? ' has' : 's have') .
            " prohibited this " . 
            (isset($model) ? humanize($model) : 'Form' ) . 
            " from being saved.";
        }
        $title = $title ? $title : $default_title;
        $title = str_replace(
                    array('@@errors_count@@','@@title@@'),
                    array($errors_count, $title),
                    $title);
        include_once(CREOVEL_PATH . 'views' . DS . 'layouts' . DS .
                    '_form_errors.php');
    }
}

/**
 * Wrap error fields with span and put an anchor next to element.
 *
 * @param string $field_name
 * @param string $html_str
 * @return string
 * @author Nesbert Hidalgo
 **/
function error_wrapper($field_name, $html_str)
{
    return '<a name="error_' . name_to_id($field_name) .
            '"></a><span class="fieldWithErrors">' .
            str_replace("\n", '', $html_str) . "</span>\n";
}

/**
 * Creates the start form tag.
 *
 * @param array $event_options
 * @param mixed $name_or_obj
 * @param string $value
 * @param string $method Optional default set to "post"
 * @param $html_options
 * @return string
 * @author Nesbert Hidalgo
 **/
function start_form_tag($event_options,
                        $name_or_obj = null,
                        $name_value = null,
                        $method = 'post',
                        $html_options= null)
{
    if ($name_or_obj) {
        if (is_object($name_or_obj)) {
            $obj_id_str = hidden_field(
                            str_replace('_model',
                                        '',
                                        get_class($name_or_obj)) . '[id]',
                                        $name_or_obj->id);
        } else {
            $obj_id_str = hidden_field($name_or_obj, $name_value)."\n";
        }
    }
    
    $event_arr = get_event_params();
    
    if (!in_array('controller', array_keys($event_options))) {
        $event_options['controller'] = $event_arr['controller'];
    }
    
    if (!in_array('action', array_keys($event_options))) {
        $event_options['action'] = $event_arr['action'];
    }
    
    if (!in_array('id', array_keys($event_options))) {
        $event_options['id'] = $event_arr['id'];
    }
    
    if ( $event_options['id'] ) {
        $obj_id_str .= hidden_field('id', $event_options['id'])."\n";
    }
    
    return '<form method="' . $method . '" id="form_' .
            $event_options['controller'] . '" name="form_' .
            $event_options['controller'] . '" action="' .
            url_for(
                $event_options['controller'],
                $event_options['action'],
                $event_options['id']
                ) . '"' . html_options_str($html_options) .
                '>' . "\n" . $obj_id_str;
}

/**
 * Creates the end form tag for lazy programmers or anal ones!
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function end_form_tag()
{
    return "</form>\n";
}

/**
 * Base function used to create the different types of input tags.
 *
 * @param string $type Input type 'text', 'password', 'submit', etc.
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @param string $tag_value
 * @param string $text
 * @return string
 * @author Nesbert Hidalgo
 **/
function create_input_tag($type, $name, $value = null, $html_options = array(), $tag_value = null, $text = null)
{
    $input = array();
    if (isset($type)) $input['type'] = $type;
    if (!isset($html_options['id'])) $html_options['id'] = name_to_id($name).( $type == 'radio' | $type == 'checkbox' ? '_'.str_replace(' ', '', $tag_value) : '' );
    $input['id'] = $html_options['id'];
    if (isset($name)) $input['name'] = $name;
    $input['value'] = $value;
    if ($type == 'radio' || $type == 'checkbox') {
        $input['value'] = $tag_value;
        if ( $value == $tag_value ) $html_options['checked'] = 'checked';
    }
    return create_html_element('input', array_merge($input, $html_options)) . ($text ? ' ' . $text : '') . "\n";
}

/**
 * Creates a text input tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @param string $text
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function text_field($name, $value = '', $html_options = null, $text = null)
{
    return create_input_tag('text', $name, $value, $html_options, null, $text);
}

/**
 * Creates a hidden text input tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function hidden_field($name, $value = '', $html_options = null)
{
	return create_input_tag('hidden', $name, $value, $html_options);
}

/**
 * Creates a password text input tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @param string $text
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function password_field($name, $value = '', $html_options = null, $text = null)
{
    return create_input_tag('password', $name, $value, $html_options, $text);
}

/**
 * Creates a radio button input tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @param string $on_value
 * @param string $text
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function radio_button($name, $value = '', $html_options = null, $on_value = null, $text = null)
{
    return create_input_tag('radio', $name, $value, $html_options, $on_value, $text);
}

/**
 * Creates a checkbox input tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @param string $on_value
 * @param string $text
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function check_box($name, $value = '', $html_options = null, $on_value = null, $text = null)
{
    return create_input_tag('checkbox', $name, $value, $html_options, $on_value, $text);
}

/**
 * Creates a checkbox input tag.
 *
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function submit_tag($value = 'Submit', $html_options = null)
{
    return create_input_tag('submit', $html_options['name'], $value, $html_options);
}

/**
 * Creates a button input tag.
 *
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function button_tag($value = 'Button', $html_options = null)
{
	return create_input_tag('button', $html_options['name'], $value, $html_options);
}

/**
 * Creates a textarea tag.
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function textarea($name, $value = '', $html_options = array())
{
    $textarea['id'] = name_to_id($name);
    $textarea['name'] = $name;
    return create_html_element('textarea', array_merge($textarea, $html_options), $value);
}

/**
 * Alias to textarea().
 *
 * @param string $name
 * @param string $value
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see textarea()
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function text_area($name, $value = '', $html_options = null)
{
    return textarea($name, $value, $html_options);
}

/**
 * Creates a label tag.
 *
 * @param string $name
 * @param string $title
 * @param array $html_options Associative array of attributes.
 * @return string
 * @see create_input_tag()
 * @author Nesbert Hidalgo
 **/
function label($name, $title = null, $html_options = null)
{
    if (!$title) {
        $args = explode('[', $name);
        $title = str_replace(']', '', end($args));
        $title = humanize($title);
    }
    $html_options['for'] = name_to_id($name);
    return create_html_element('label', $html_options, $title) . "\n";
}

/*

Function: select
	Creates a select tag (dropdown box). Can't beat this!!!

Parameters:
	name - required
	selected - optional 
	choices - optional
	html_options - optional
	none_title - optional default set to "None Available"

Returns:
	string

*/ 

function select($name, $selected = '', $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false)
{
	$og_options = array('name' => $name, 'id' => name_to_id($name)) + (array) $html_options;
	$content = "\n";
	
	if (count($choices)) {
		
		if ($have_none) {
			$content .= create_html_element('option', array('value' => ''), $none_title)."\n";
		}
		
		foreach ($choices as $value => $description) {
			
			if (!is_array($description)) {
				
				if (is_array($selected)) {
					$select_options = in_array($value, $selected) ? array('selected' => 'selected') : '';
				} else {
					if (!$value) {
						$select_options = $selected === $value ? array('selected' => 'selected') : '';
					} else {
						$select_options = $selected == $value ? array('selected' => 'selected') : '';
					}
				}
				
				$html_options = is_array($select_options) ? array('value' => $value) + (array) $select_options : array('value' => $value);
				$content .= create_html_element('option', $html_options, ($description ? $description : $value))."\n";
				
			} else {
				
				if (in_string('optgroup:', $value)) {
					
					$group = "\n";
					
					foreach($description as $value2 => $description2) {
						if (is_array($selected)) {
							$select_options = in_array($value2, $selected) ? array('selected' => 'selected') : '';
						} else {
							if (!$value) {
								$select_options = $selected === $value2 ? array('selected' => 'selected') : '';
							} else {
								$select_options = $selected == $value2 ? array('selected' => 'selected') : '';
							}
						}
						
						$html_options = is_array($select_options) ? array('value' => $value2) + (array) $select_options : array('value' => $value2);
						$group .= create_html_element('option', $html_options, ($description2 ? $description2 : $value2))."\n";
					}
					
					$content .= create_html_element('optgroup', array('label' => str_replace('optgroup:', '', $value)), $group)."\n";
					
				}
			
			}
		}
	
	} else {
	
		$content .= create_html_element('option', array('value' => ''), $none_title);
		
	}
	
	$out = create_html_element('select', $og_options, $content);
	
	return $out;
}

/*

	Function: select_states_tag	
	
	Creates dropdown of states.

	Parameters:
	
		name - required
		selected - optional
		choices - optional
		html_options - optional
	
	Returns:
	
		String.
*/
 
function select_states_tag($name = 'state', $selected = null, $choices = null, $html_options = null, $country = 'US', $select_all = false)
{
	
	if (isset($choices['abbr'])) {
		$abbr = true;
		unset($choices['abbr']);
	} else {
		$abbr = false;
	}
	
	if (isset($choices['select_all'])) {
		$select_all = true;
		unset($choices['select_all']);
	} else {
		$select_all = false;
	}
	
	if ($select_all) {
		$choices = ( isset($choices) ? $choices : array( 'all' => 'All States...' ) );
	} else {
		$choices = ( isset($choices) ? $choices : array( '' => 'Please select...' ) );
	}
	
	// intialize states array
	$state_arr = states($country ? $country : 'US');
	
	if ($abbr) $state_arr = array_combine(array_keys($state_arr), array_keys($state_arr));
	
	$state_arr = array_merge($choices, $state_arr);
	return select($name, $selected, $state_arr, $html_options);
}

/*
	Function: select_countries_tag
	
	Creates dropdown of countries. if $state_id is it will automatically populate those values depending on the contry selected.
	
	Parameters:
	
		name - required
		selected - optional
		choices - optional
		html_options - optional
		state_id - optional will update the states depending on the country selected
		
	Returns:
	
		String.
*/
 
function select_countries_tag($name = 'country', $selected = null, $choices = null, $html_options = null, $state_id = null)
{
	$html_options['us_first'] = isset($html_options['us_first']) ? $html_options['us_first'] : false;
	$html_options['show_abbr'] = isset($html_options['show_abbr']) ? $html_options['show_abbr'] : false;
	
	$choices = $choices ? $choices : array('' => 'Please select...');
	
	$country_arr = countries($html_options['us_first'], $html_options['show_abbr']);
	
	// unset country function vars
	unset($html_options['us_first']);
	unset($html_options['show_abbr']);

	if ($state_id) {
		$state_id = name_to_id($state_id);
		$html_options['onchange'] = (isset($html_options['onchange']) ? trim($html_options['onchange']) : '') . ' set_'.$state_id.'();';
	}
	
	$return = select($name, $selected, array_merge($choices, $country_arr), $html_options);
	
	// automatic state dropdown update
	if ( $state_id ) {
	
		$us_states = states('US');
		$ca_states = states('CA');
		?>
<script language="javascript" type="text/javascript">
<!--
function set_<?=$state_id?>() {
	
	var usaVals = new Array("<?=implode('", "', $us_states)?>");
	var usaIDs = new Array("<?=implode('", "', array_keys($us_states))?>");
	var canadaVals = new Array("<?=implode('", "', $ca_states)?>");
	var canadaIDs = new Array("<?=implode('", "', array_keys($ca_states))?>");
	var countryDrop = document.getElementById("<?=name_to_id($name)?>");
	var selectedCountry = countryDrop.options[countryDrop.selectedIndex].value;
	
	switch ( selectedCountry ) {
		case "United States":
		case "USA":
		case "US":
			update_<?=$state_id?>(usaVals, usaIDs);
		break;
		case "Canada":
		case "CA":
			update_<?=$state_id?>(canadaVals, canadaIDs);
		break;
		default:
			update_<?=$state_id?>();
		break;
	}
}

function update_<?=$state_id?>(stateVals, stateIDs) {
	
	var stateDrop = document.getElementById("<?=$state_id?>");
	stateDrop.options.length = 0;
	stateDrop.options[stateDrop.options.length] = new Option("Please select...", "");
	
	if ( stateVals ) {
		for(var i=0; i<stateVals.length; i++) {
			stateDrop.options[stateDrop.options.length] = new Option(stateVals[i], stateIDs[i]);
			stateDrop.options[0].selected = true;
		}
	} else {
		stateDrop.options.length = 0;
		stateDrop.options[stateDrop.options.length] = new Option("None Available", "");
		stateDrop.options.selected = true;
	}
	
};
-->
</script>
<?
	}
	
	return $return;
	
}

/*
	Function: select_redirect
	
	Creates dropdown that redirects the page onchange.
	
	Parameters:
	
		name - required
		names_and_urls - required
		html_options - optional
		
	Returns:
	
		String.
*/
 
function select_redirect($name, $names_and_urls, $html_options = null)
{
	$html_options['onchange'] .= 'location.href=this.options[this.selectedIndex].value;';
	return select($name, null, $names_and_urls, $html_options);
}


/*
		
Function: date_select	
	Create date selectboxes

Parameters:
	name - name
	date - date to use

Returns:
	string

*/

function date_select($name, $date = null)
{
	$date = strtotime(datetime($date));
	
	$i = 1;
	$months = array();
	while ($i <= 12) { $months[$i] = $i; $i++; }

	$i = 1;
	$days = array();
	while ($i <= 31) { $days[$i] = $i; $i++; }

	$i = (date('Y') - 3);
	$years = array();
	while ($i <= (date('Y') + 3)) { $years[$i] = $i; $i++; }

	$out = "";
	$out .= select("{$name}[month]", date('m', $date), $months);
	$out .= select("{$name}[day]", date('j', $date), $days);
	$out .= select("{$name}[year]", date('Y', $date), $years);

	return $out;
}

/*
		
Function: time_select	
	Create time selectboxes

Parameters:
	name - name
	date - time to use

Returns:
	string

*/

function time_select($name, $time = null)
{
	switch ( true ) {
	
		case ( !$time  || ($time == '0000-00-00 00:00:00') ):
			$time = time();
		break;
		
		case ( is_array($time) ):
			$time = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);
		break;
		
		case ( is_numeric($time) ):
		break;
		
		case ( is_string($time) ):
			$time = strtotime($time);
		break;	
		
	}
	
	$i = 1;
	$hours = array();
	while ($i <= 12) { $hours[$i] = $i; $i++; }	

	$i = 0;
	$minutes = array();
	while ($i <= 59) { $minutes[sprintf("%02d", $i)] = sprintf("%02d", $i); $i++; }	

	$ampm['AM'] = 'AM';
	$ampm['PM'] = 'PM';

	$out = "";
	$out .= select("{$name}[hour]", date('g', $time), $hours);
	$out .= select("{$name}[minute]", date('i', $time), $minutes);
	$out .= select("{$name}[ampm]", date('A', $time), $ampm);

	return $out;
}

/*
		
Function: date_time_select	
	Create date selectboxes

Parameters:
	name - name
	datetime - datetime to use

Returns:
	string

*/

function date_time_select($name, $datetime = null)
{
	return date_select($name, $datetime)." @ ".time_select($name, $datetime);
}

/*
		
Function: get_timestamp_from_post
	Get the timestamp array from post

Parameters:
	key - key to use

Returns:
	string

*/

function get_timestamp_from_post($key)
{
	$_POST[$key]['hour'] = ($_POST[$key]['ampm'] == 'pm') ? ($_POST[$key]['hour'] + 12) : $_POST[$key]['hour'];
	return mktime($_POST[$key]['hour'], $_POST[$key]['minute'], 0, $_POST[$key]['month'], $_POST[$key]['day'], $_POST[$key]['year']);
}

/*
		
Function: date_select	
	Create date selectboxes

Parameters:
	name - name
	date - date to use

Returns:
	string

*/

function get_timestamp_from_array($array)
{
	$array['hour'] = ($array['ampm'] == 'pm') ? ($array['hour'] + 12) : $array['hour'];
	return mktime($array['hour'], $array['minute'], 0, $array['month'], $array['day'], $array['year']);
}

/*
		
Function: select_time_zone_tag	
	Create time zone selectboxes

Parameters:
	name - name
	selected - item to be selected
	choices - array of choices
	html_options - html options

Returns:
	string

*/

function select_time_zone_tag($name, $selected = null, $choices = null, $html_options = null)
{
	$time_zones = timezones();
	$choices = ( $choices ? $choices : array('' => 'Please select...') );
	$time_zones = array_merge($choices, $time_zones);
	
	return select($name, $selected, $time_zones, $html_options);
}

/*

Function: checkbox_select
	Creates a select tag (dropdown box). Can't beat this!!!

Parameters:
	name - required
	selected - optional 
	choices - optional
	html_options - optional
	none_title - optional default set to "None Available"

Returns:
	string

*/ 

function checkbox_select($name, $selected = array(), $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false)
{
	
	if ( !is_array($selected) ) $selected = array();
	
	if ( $html_options['label_options'] ) {
		$label_options = $html_options['label_options'];
		unset($html_options['label_options']);
	}
	
	$box_html_options = array();

	if ( is_array($html_options) && count($html_options) > 0 ) {
	
		foreach ( $html_options as $key=>$value ) {
			if (strtolower(substr(trim($key), 0, 2)) == 'on') {
				$box_html_options[$key] = $value;
			}
		}
		
		foreach ( $box_html_options as $key=>$value ) {
			unset($html_options[$key]);
		}
		
	}

	$return = "<div ". html_options_str($html_options) .">\n";
	
	if ( count($choices) ) {
	
		$class_temp = $label_options['class'];
	
		foreach( $choices as $value => $desc ) {
			$label_options['class'] = $class_temp . ( in_string('class="sub"', $desc) ? '_sub' : '' ) . ' row ' . cycle('row-1', 'row-2');
			$label_options['for'] = name_to_id($name) . '_' . $value;
			$return .= "<label ".html_options_str($label_options).">\n";
			$return .= create_input_tag('checkbox', $name, in_array($value, $selected), $box_html_options, $value, $desc)."\n";
			$return .= "<br /></label>\n";		
		}
		
	} else {
		$return .= '<span class="'.underscore($none_title).'">'.$none_title.'</span>';
	}
	
	$return .= "</div>\n";
	
	return $return;
}

/*

Function: checkbox_select
	Creates a select tag (dropdown box). Can't beat this!!!

Parameters:
	name - required
	selected - optional 
	choices - optional
	html_options - optional
	none_title - optional default set to "None Available"

Returns:
	string

*/ 

function radio_select($name, $selected = array(), $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false)
{
	
	if ( !is_array($selected) ) $selected = array($selected);
	
	if ( $html_options['label_options'] ) {
		$label_options = $html_options['label_options'];
		unset($html_options['label_options']);
	}
	
	$box_html_options = array();

	if ( is_array($html_options) && count($html_options) > 0 ) {
	
		foreach ( $html_options as $key=>$value ) {
			if (strtolower(substr(trim($key), 0, 2)) == 'on') {
				$box_html_options[$key] = $value;
			}
		}
		
		foreach ( $box_html_options as $key=>$value ) {
			unset($html_options[$key]);
		}
		
	}

	$return = "<div ". html_options_str($html_options) .">\n";
	
	if ( count($choices) ) {
	
		$class_temp = $label_options['class'];
	
		foreach( $choices as $value => $desc ) {
			$label_options['class'] = $class_temp . ( in_string('class="sub"', $desc) ? '_sub' : '' ) . ' row ' . cycle('row-1', 'row-2');
			$label_options['for'] = name_to_id($name) . '_' . $value;
			$return .= "<label ".html_options_str($label_options).">\n";
			$return .= create_input_tag('radio', $name, in_array($value, $selected), $box_html_options, $value, $desc)."\n";
			$return .= "<br /></label>\n";		
		}
		
	} else {
		$return .= '<span class="'.underscore($none_title).'">'.$none_title.'</span>';
	}
	
	$return .= "</div>\n";
	
	return $return;
}
