<?php
/*
	Script: form
	
	Form helpers go here.	
*/

/*
	Function: name_to_id
	
	Formats user[name] to user_name.
	
	Returns:
	
		String.
*/
 
function name_to_id($name)
{	
	return str_replace(array('[', ']'), array('_', ''), str_replace('[]', '', $name));
}


/*
	Function: add_form_error
	
	Add error to form errors.
	
	Parameters:
	
		field_name - Field name.
		message - Error message.
*/

function add_form_error($field_name, $message = null)
{	
	$_ENV['creovel']['form_errors'][name_to_id($field_name)] = $message ? $message : humanize($field_name) . ' is invalid.';
}

/*
	Function: field_has_error
	
	checkif a field is has an error.
	
	Parameters:
	
		field_name - Field name.
		
	Returns:
	
		Boolean.
*/

function field_has_error($field_name)
{	
	return isset($_ENV['creovel']['form_errors'][name_to_id($field_name)]);
}

/*
	Function: form_has_errors
	
	Check if form has errors.
	
	Returns:
	
		Boolean.
*/

function form_has_errors()
{	
	return form_errors_count() ? true : false;
}

/*
	Function: form_has_errors
	
	Check if form has errors.
	
	Returns:
	
		Integer.
*/

function form_errors_count()
{	
	return count($_ENV['creovel']['form_errors']);
}

/*
	Function: error_messages_for
	
	Prints out a formatted errors message box for an object. Errors styles below: 

	(start code)
		 #errors {} // container div
		 #errors .top {} 
		 #errors .body {}
		 #errors .bottom {}
		 #errors h1 {} // title
		 #errors p {} // description
		 #errors ul {} // errors list
		 #errors li {} // errors list items
		 #errors a {} // errors list items links
		 .errors_field {} // html element with the error
	(end)
	
	Parameters:
		
		errors - optional
		title - optional default is "{number of errors} errors {have or has} prohibited this {object name} from being saved."
		description - optional default is "There were problems with th following fields."
	
	Returns:
	
		String.
*/

function error_messages_for($errors = null, $title = null, $description = 'There were problems with the following fields.')
{
	// if no errors check global variable
	if ( !$errors ) $errors = $_ENV['creovel']['form_errors'];
	
	if ( is_object($errors) ) {
		$model = get_class($errors);
		$errors = $errors->errors;
	}
	
	$errors_count =	count($errors);
	$li_str = '';
	
	if ($errors_count) foreach ( $errors as $field => $message ) {
		if ( $message == 'no_message') continue;
		$li_str .= create_html_element('li', null, create_html_element('a', array('href' => "#error_{$field}"), $message)) . "\n";
	}	
	
	if ( $errors_count ) {
	
		$title = ( $title ? $title : "{$errors_count} error".( $errors_count == 1 ? ' has' : 's have' )." prohibited this ".( $model ? humanize($model) : 'Form' )." from being saved." );
		$title = str_replace(array('@@errors_count@@','@@title@@'), array($errors_count, $title), $title);
		
	?>
<div class="errors">

<div class="top"></div>
	
<div class="body">
<?=( $title ? '<h1 class="error_title">'.$title.'</h1>' : '' )?>
<?=( $description ? '<p>'.$description.'</p>' : '' )?>

<ul>
<?=$li_str?>
</ul>

</div>

<div class="bottom"></div>

</div><?php
	}

}

/*
	Function: error_wrapper
	
	Parameters:
	
		field - Field name.
		html_str - HTML string.
		
	Returns:
	
		String.
*/

function error_wrapper($field_name, $html_str)
{
	return '<a name="error_' . name_to_id($field_name) . '"></a><span class="fieldWithErrors">' . str_replace("\n", '', $html_str) . "</span>\n";
}

/*

Function: start_form_tag
	Creates the start form tag.

Parameters:
	event_options - required
	name_or_obj - required
	value - optional
	method - optional default set to "post"
	html_options - optional

Returns:
	string

*/
 
function start_form_tag($event_options, $name_or_obj = null, $name_value = null, $method = 'post', $html_options= null)
{

	if ( $name_or_obj ) {	
		if ( is_object($name_or_obj) ) {
			$obj_id_str = hidden_field(str_replace('_model', '', get_class($name_or_obj)).'[id]', $name_or_obj->id);
		} else {
			$obj_id_str = hidden_field($name_or_obj, $name_value)."\n";
		}
	}
	
	$event_arr = get_event_params();
	
	if ( !in_array('controller', array_keys($event_options)) ) {
		$event_options['controller'] = $event_arr['controller'];
	}

	if ( !in_array('action', array_keys($event_options)) ) {
		$event_options['action'] = $event_arr['action'];
	}

	if ( !in_array('id', array_keys($event_options)) ) {
		$event_options['id'] = $event_arr['id'];
	}
	
	if ( $event_options['id'] ) {
		$obj_id_str .= hidden_field('id', $event_options['id'])."\n";
	}
	
	return '<form method="'.$method.'" id="form_'.$event_options['controller'].'" name="form_'.$event_options['controller'].'" action="'.url_for($event_options['controller'], $event_options['action'], $event_options['id']).'"'.html_options_str($html_options).'>'."\n".$obj_id_str;
	
}

/*

Function: form_tag
	Creates the start form tag.

Parameters:
	options - required

Returns:
	string

*/
 
function form_tag($options)
{
	return '<form method="'.( $options['method'] ? $options['method'] : 'post' ).'"'.( $options['controller'] ? ' id="form_'.$options['controller'].( $options['action'] ? '_'.$options['action'] : '' ).'"' : '' ).( $options['controller'] ? ' name="form_'.$options['controller'].( $options['action'] ? '_'.$options['action'] : '' ).'"' : '' ).' action="'.url_for($options['controller'], $options['action'], $options['id']).'"'.html_options_str($options).'>'."\n";
}

/*

Function: end_form_tag
	Creates the end form tag for lazy programmers or anal ones!.

Returns
	string

*/
 
function end_form_tag()
{
	return "</form>\n";
}

/*
	Function: create_input_tag
	
	Base function used to create the different types of input tags.
	
	Returns:
	
		String.
*/
 
function create_input_tag($type, $name, $value = null, $html_options = null, $tag_value = null, $text = null)
{
	if (isset($type)) $html_options['type'] = $type;
	if (!isset($html_options['id'])) $html_options['id'] = name_to_id($name).( $type == 'radio' | $type == 'checkbox' ? '_'.str_replace(' ', '', $tag_value) : '' );
	if (isset($name)) $html_options['name'] = $name;
	$html_options['value'] = $value;
	if ($type == 'radio' || $type == 'checkbox') {
		$html_options['value'] = $tag_value;
		if ( $value == $tag_value ) $html_options['checked'] = 'checked';
	}
	return create_html_element('input', $html_options) . ( $text ? ' ' . $text : '' ) . "\n";
}

/*
	Function: text_field
	
	Creates a text input tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
		
	Returns:
	
		String.
*/ 

function text_field($name, $value = '', $html_options = null, $text = null)
{
	return create_input_tag('text', $name, $value, $html_options, null, $text);
}

/*
	Function: hidden_field
	
	Creates a hidden text input tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
		
	Returns:
	
		String.
*/ 

function hidden_field($name, $value = '', $html_options = null)
{
	return create_input_tag('hidden', $name, $value, $html_options);
}

/*
	Function: password_field
	
	Creates a password text input tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
	
	Returns:
	
		String.
*/ 

function password_field($name, $value = '', $html_options = null, $text = null)
{
	return create_input_tag('password', $name, $value, $html_options, $text);
}

/*
	Function: radio_button
	
	Creates a radio button input tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
		on_value - optional
	
	Returns:
	
		String.
*/

function radio_button($name, $value = '', $html_options = null, $on_value = null, $text = null)
{
	return create_input_tag('radio', $name, $value, $html_options, $on_value, $text);
}

/*
	Function: check_box
	
	Creates a checkbox input tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
		on_value - optional
	
	Returns:
	
		String.
*/ 

function check_box($name, $value = '', $html_options = null, $on_value = null, $text = null)
{
	return create_input_tag('checkbox', $name, $value, $html_options, $on_value, $text);
}

/*
	Function: submit_tag
	
	Creates a submit tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
	
	Returns:
	
		String.
*/ 

function submit_tag($value = 'Submit', $html_options = null)
{
	return create_input_tag('submit', $html_options['name'], $value, $html_options);
}

/*
	Function: button_tag
	
	Creates a button tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
	
	Returns:
	
		String.
*/ 

function button_tag($value = 'Button', $html_options = null)
{
	return create_input_tag('button', $html_options['name'], $value, $html_options);
}

/*
	Function: textarea
	
	Creates a textarea tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
		
	Returns:
	
		String.
*/ 

function textarea($name, $value = '', $html_options = null)
{
	$html_options['id'] = name_to_id($name);
	$html_options['name'] = $name;
	return create_html_element('textarea', $html_options, $value);
}

/*
	Function: text_area
	
	Creates a textarea tag.
	
	Parameters:
	
		name - required
		value - optional
		html_options - optional
	
	Returns:
	
		String.
*/ 

function text_area($name, $value = '', $html_options = null)
{
	return textarea($name, $value, $html_options);
}

/*
	Function: label	
	
	Creates a label tag.
	
	Parameters:
	
		name - required
		title - optional
		required - optional
		
	Returns:
	
		String.
*/ 

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

	$return = '<select name="'.$name.'" id="'.name_to_id($name).'" '.html_options_str($html_options).">\n\r";
	
	if ( count($choices) ) {
		
		if ($have_none) {
			$return .= "<option value=\"\">{$none_title}</option>\n\r";
		}	
		foreach ( $choices as $value => $description ) {
			if (!is_array($description)) {
				//$value = ( is_assoc_array($choices) ? $value : $description );
				if (is_array($selected)) {
					$select_str = ( in_array($value, $selected) ? ' selected="selected"' : '' );
				} else {
					if (!$value) {
						$select_str = ( $selected === $value ? ' selected="selected"' : '' );
					} else {
						$select_str = ( $selected == $value ? ' selected="selected"' : '' );
					}
				}
				$return .= '<option value="'.$value.'"'.$select_str.'>'.($description?$description:$value)."</option>\n\r";
			} else {
				$value = ( is_assoc_array($choices) ? $value : $description['name'] );
				if (is_array($selected)) {
					$select_str = ( in_array($value, $selected) ? ' selected="selected"' : '' );
				} else {
					if (!$value) {
						$select_str = ( $selected === $value ? ' selected="selected"' : '' );
					} else {
						$select_str = ( $selected == $value ? ' selected="selected"' : '' );
					}
				}
				
				$return .= '<option value="'.$value.'"'.$select_str.'>'.($description['name']?$description['name']:$value)."</option>\n\r";
				
				if (is_array($description['children'])) {
					foreach($description['children'] as $value2 => $description2) {
						$value2 = ( is_assoc_array($choices) ? $value2 : $description2 );
						if (is_array($selected)) {
							$select_str = ( in_array($value2, $selected) ? ' selected="selected"' : '' );
						} else {
							if (!$value) {
								$select_str = ( $selected === $value2 ? ' selected="selected"' : '' );
							} else {
								$select_str = ( $selected == $value2 ? ' selected="selected"' : '' );
							}
						}
						
						$return .= '<option value="'.$value2.'"'.$select_str.'> - '.($description2?$description2:$value2)."</option>\n\r";
					}
				}
			
			}
		}
	
	} else {
	
		$return .= "<option value=\"\">{$none_title}</option>\n\r";
		
	}

	$return .= "</select>\n\r";

	return $return;

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
 
function select_states_tag($name = 'state', $selected = null, $choices = null, $html_options = null, $select_all = false)
{
	
	if ( isset($choices['abbr']) ) {
		$abbr = true;
		unset($choices['abbr']);
	}
		
	if ( isset($choices['select_all']) ) {
		$select_all = true;
		unset($choices['select_all']);
	}
	
	if ($select_all) {
		$choices = ( isset($choices) ? $choices : array( 'all' => 'All States...' ) );
	} else {
		$choices = ( isset($choices) ? $choices : array( '' => 'Please select...' ) );
	}
	
	// intialize states array
	$state_arr = states();
	
	if ( $abbr ) $state_arr = array_combine(array_keys($state_arr), array_keys($state_arr));
	
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

	$choices = ( $choices ? $choices : array('' => 'Please select...') );
	
	$country_arr = countries($html_options['us_first'], $html_options['show_abbr']);
	
	// unset country function vars
	unset($html_options['us_first']);
	unset($html_options['show_abbr']);

	if ($state_id) {
		$state_id = name_to_id($state_id);
		$html_options['onchange'] = trim($html_options['onchange']) . ' set_'.$state_id.'();';
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
	switch ( true ) {
	
		case ( !$date  || ($date == '0000-00-00 00:00:00') ):
			$date = time();
		break;
		
		case ( is_array($date) ):
			$date = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		break;
		
		case ( is_numeric($date) ):
		break;
		
		case ( is_string($date) ):
			$date = strtotime($date);
		break;	
		
	}
	
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
		$time_zones = array(
			"US & Canada" => "US/Pacific",
			"-10:00 Hawaii" => "US/Hawaii",
			"-09:00 Alaska" => "US/Alaska",
			"-08:00 Pacific Time" => "US/Pacific",
			"-08:00 Pacific Time (Yukon)" =>"Canada/Yukon",
			"-07:00 Arizona" => "US/Arizona",
			"-07:00 Mountain Time" => "US/Mountain",
			"-06:00 Central Time" => "US/Central",
			"-06:00 Saskatchewan" => "Canada/Saskatchewan",
			"-06:00 Saskatchewan (East)" => "Canada/East-Saskatchewan",
			"-05:00 Eastern Time" => "US/Eastern",
			"-05:00 Eastern Time (Michigan)" => "US/Michigan",
			"-05:00 Indiana (East)" => "US/East-Indiana",
			"-05:00 Indiana (Starke)" => "US/Indiana-Starke",
			"-04:00 Atlantic Time (Canada)" => "Canada/Atlantic",
			"-03:30 Newfoundland" => "Canada/Newfoundland",
			"International" => "GMT",
			"-12:00 Eniwetok, Kwajalein" => "Pacific/Kwajalein",
			"-11:00 Midway Island, Samoa" => "US/Samoa",
			"-06:00 Central America" => "Etc/GMT-6",
			"-06:00 Mexico City" => "America/Mexico_City",
			"-05:00 Bogota, Lima, Quito" => "America/Bogota",
			"-04:00 Caracas, La Paz" => "America/Caracas",
			"-04:00 Santiago" => "America/Santiago",
			"-03:00 Brasilia" => "Brazil/West",
			"-03:00 Greenland" => "Etc/GMT-3",
			"-02:00 Mid-Atlantic" => "Etc/GMT-2",
			"-01:00 Azores" => "Atlantic/Azores",
			"-01:00 Cape Verde Is." => "Atlantic/Cape_Verde",
			"GMT Casablanca, Monrovia" => "Africa/Casablanca",
			"Greenwich Mean Time GMT: Dublin, Edinburgh, Lisbon, London" => "GMT",
			"+01:00 Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna" => "Etc/GMT+1",
			"+01:00 Belgrade, Bratislava, Budapest, Ljubljana, Prague" => "Etc/GMT+1",
			"+01:00 Brussels, Copenhagen, Madrid, Paris" => "Etc/GMT+1",
			"+01:00 Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb" => "Etc/GMT+1",
			"+01:00 West Central Africa" => "Etc/GMT+1",
			"+02:00 Athens, Istanbul, Minsk" => "Etc/GMT+2",
			"+02:00 Bucharest" => "Etc/GMT+2",
			"+02:00 Cairo" => "Etc/GMT+2",
			"+02:00 Harare, Pretoria" => "Etc/GMT+2",
			"+02:00 Helsinki, Riga, Tallinn" => "Etc/GMT+2",
			"+02:00 Jarusalem" => "Etc/GMT+2",
			"+03:00 Baghdad" => "Etc/GMT+3",
			"+03:00 Kuwait, Riyadh" => "Etc/GMT+3",
			"+03:00 Moscow, St. Peter sburg, Volgograd" => "Etc/GMT+3",
			"+03:00 Nairobi"=> "Etc/GMT+3",
			"+03:30 Tehran" => "Etc/GMT+3",
			"+04:00 Abu Dhabi, Muscat" => "Etc/GMT+4",
			"+04:00 Baku, bilisi, erevan" => "Etc/GMT+4",
			"+04:30 Kabul" => "Asia/Kabul",
			"+05:00 Ekaterinburg" => "Etc/GMT+5",
			"+05:00Islamabad, Karachi, Tashkent" => "Etc/GMT+5",
			"+05:30 Calcutta, Chennai, Mumbai, New Delhi" => "Asia/Calcutta",
			"+05:45 Kathmandu" => "Asia/Katmandu",
			"+06:00 Almatay, Novosibirsk" => "Etc/GMT+6",
			"+06:00Astana, Dhaki" => "Etc/GMT+6",
			"+06:00 Sri Jayawardenepura" => "Etc/GMT+6",
			"+06:30 Rangoon" => "Asia/Rangoon",
			"+07:00 Bangkok, Hanoi, Jakarta" => "Etc/GMT+7",
			"+07:00 Krasnoyarsk" => "Etc/GMT+7",
			"+08:00Beijing, Chongqing, Hong Kong, Urumqi" => "Etc/GMT+8",
			"+08:00 Irkutsk, Ulaan Bataar" => "Etc/GMT+8",
			"+08:00 Kuala Lumpur, Singapore" => "Etc/GMT+8",
			"+08:00 Perth" => "Etc/GMT+8",
			"+08:00Taipei" => "Etc/GMT+8",
			"+09:00 Osaka, Sapporo, Tokyo" => "Etc/GMT+9",
			"+09:00 Seoul" => "Etc/GMT+9",
			"+09:00 Yakutsk" => "Etc/GMT+9",
			"+09:30 Adelaide" => "Etc/GMT+9",
			"+09:30 Darwin" => "Australia/Darwin",
			"+10:00 Brisbane" => "Etc/GMT+10",
			"+10:00 Canberra, Melbourne, Sydney" => "Etc/GMT+10",
			"+10:00 Guam, Port Moresby" => "Etc/GMT+10",
			"+10:00 Hobart" => "Etc/GMT+10",
			"+10:00 Vladivostok" => "Etc/GMT+10",
			"+11:00 Magadan, Solomon Is., New Caledonia" => "Etc/GMT+11",
			"+12:00 Auckland, ellington" => "Etc/GMT+12",
			"+12:00 Fiji, Kamchatka, Marshall Is." => "Etc/GMT+12"
	);

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

?>