<?php
/**
 * Form methods go here.
 * @package Form Helpers
 * @todo
 *  - check/fix start_form_tag() hidden fields
 */
 
/**
 * Prints out a formatted errors message box for an object. Erros
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
 * @author Nesebrt Hidalgo
 * @name_or_object string/object optional
 * @param string $title optional default is "{number of errors} errors {have or has} prohibited this {object name} from being saved."
 * @param string $description optional default is "There were problems with th following fields."
 * @todo make accept arrays and objects
 * @return string
 */

function error_messages_for($errors = null, $title = null, $description = 'There were problems with the following fields.')
{
	// if no errors check global variable
	if ( !$errors ) $errors = $_ENV['model_error'];

	switch ( true ) {
	
		case ( is_object($errors) ):
			if ( $errors_count = $errors->errors->count() ) { 
				$model = get_class($errors);
				$errors = $errors->errors;
			} else {
				return;
			}
		break;
		
		case ( is_array($errors) ):
			$errors_count =	count($errors);
		break;
		
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
				<? foreach ( $errors as $field => $message ) { ?>
					<? if ( $message == 'no_message') continue; ?>
				<li><a href="#error_<?=$error?>"><?=$message?></a></strong></li>
				<? } ?>
			</ul>
		</div>
		
		<div class="bottom"></div>
	
	</div>		
	<?
	}

}

/**
 * Creates an ancohor tag and wraps the form element with an span tag
 * if it has an error.
 *
 * @author Nesebrt Hidalgo
 * @param string $html_str
 * @return string
 */
function error_check($html_str)
{
	if ( is_array($_ENV['model_error']) ) foreach ( $_ENV['model_error'] as $field => $vals ) {
		
		// need to figure out a better way of doing this [NH] 11/9/20005
		if ( strstr($html_str, '['.$field.']') || ( strstr($html_str, '"'.$field.'"') && !strstr($html_str, 'value="'.$field.'"') ) ) {
			$html_str = '<a name="error_'.$field.'"></a><span class="errors_field">'.$html_str.'</span>';
		}
		
	}	
	return $html_str;	
}

/**
 * Creates the start form tag.
 *
 * @author Nesbert Hidalgo
 * @param array $event_options required
 * @param sting/object $name_or_obj required
 * @param mixed $value optional
 * @param string $method optional default set to "post"
 * @param array $html_options optional
 * @return string
 */
 
function start_form_tag($event_options, $name_or_obj = null, $name_value = null, $method = 'post', $html_options= null) {

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

/**
 * Creates the start form tag.
 *
 * @author Nesbert Hidalgo
 * @param array $options required
 * @return string
 */
 
function form_tag($options) {
	return '<form method="'.( $options['method'] ? $options['method'] : 'post' ).'"'.( $options['controller'] ? ' id="form_'.$options['controller'].( $options['action'] ? '_'.$options['action'] : '' ).'"' : '' ).( $options['controller'] ? ' name="form_'.$options['controller'].( $options['action'] ? '_'.$options['action'] : '' ).'"' : '' ).' action="'.url_for($options['controller'], $options['action'], $options['id']).'"'.html_options_str($options).'>'."\n";
}

/**
 * Creates the end form tag for lazy programmers or anal ones!.
 *
 * @author Nesbert Hidalgo
 */
 
function end_form_tag() {
	return "</form>\n";
}

/**
 * Formats user[name] to user_name.
 *
 * @author Nesbert Hidalgo
 * @return string
 */
 
function name_to_id($name) {
	
	return str_replace(array('[', ']'), array('_', ''), str_replace('[]', '', $name));

}

/**
 * Base function used to create the different types of input tags.
 *
 * @author Nesbert Hidalgo
 * @return string
 */
 
function create_input_tag($type, $name, $value = null, $html_options = null, $on_value = null, $text = null) {

	if (is_string($text)) {
		$append_text = $text;
	} else {
		$prepend_text = $text[0];
		$append_text = $text[1];
	}

	if ( $value == $on_value ) $html_options['checked'] = 'checked';
	$id = name_to_id($name).( $type == 'radio' || $type == 'checkbox' ? '_'.str_replace(' ', '', $value) : '' );
	return error_check($prepend_text.' <input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'"'.html_options_str($html_options).' /> '.$append_text);
	
}

/**
 * Creates a text input tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function text_field($name, $value = '', $html_options = null, $text = null) {

	return create_input_tag('text', $name, $value, $html_options, null, $text);

}

/**
 * Creates a hidden text input tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function hidden_field($name, $value = '', $html_options = null) {

	return create_input_tag('hidden', $name, $value, $html_options);

}

/**
 * Creates a password text input tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function password_field($name, $value = '', $html_options = null, $text = null) {

	return create_input_tag('password', $name, $value, $html_options, $text);

}

/**
 * Creates a radio button input tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @param string $on_value optional
 * @return string
 */ 

function radio_button($name, $value = '', $html_options = null, $on_value = null, $text = null) {

	return create_input_tag('radio', $name, $value, $html_options, $on_value, $text);

}

/**
 * Creates a checkbox input tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @param string $on_value optional
 * @return string
 */ 

function check_box($name, $value = '', $html_options = null, $on_value = null, $text = null) {

	return create_input_tag('checkbox', $name, $value, $html_options, $on_value, $text);

}

/**
 * Creates a submit tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function submit_tag($name = '', $value = 'Submit', $html_options = null) {

	return create_input_tag('submit', $name, $value, $html_options);

}

/**
 * Creates a button tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function button_tag($name = '', $value = 'Button', $html_options = null) {

	return create_input_tag('button', $name, $value, $html_options);

}

/**
 * Creates a textarea tag.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $value optional
 * @param array $html_options optional
 * @return string
 */ 

function text_area($name, $value = '', $html_options = null) {

	return error_check('<textarea id="'.name_to_id($name).'" name="'.$name.'"'.html_options_str($html_options).'>'.$value.'</textarea>');

}

/**
 * Creates a label tag. Modified to accept html_options [NH] 2/3
 *
 * @author Russ Smith
 * @param string $name required
 * @param string $field required
 * @param boolean $required required
 * @return string
 */ 

function label_tag($name, $field, $required = true, $html_options = null)
{

	$html_options['class'] = ( $required ? ' required' : '' ) . ( is_array($GLOBALS['form_errors']) && in_array($field, array_keys($GLOBALS['form_errors'])) ? ' errors_field' : '' ) . ' ' . $html_options['class'];

	return '<label for="'.$field.'"'.html_options_str($html_options).'>'.$name.'</label>';
}

/**
 * Creates a select tag (dropdown box). Can't beat this!!!
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param mix $selected optional 
 * @param array $choices optional
 * @param array $html_options optional
 * @param string $none_title optional default set to "None Available"
 * @return string
 */ 

function select($name, $selected = '', $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false) {

	$return = '<select name="'.$name.'" id="'.name_to_id($name).'"'.html_options_str($html_options).">\n\r";
	
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
					$select_str = ( $selected == $value ? ' selected="selected"' : '' );
				}
				$return .= '<option value="'.$value.'"'.$select_str.'>'.($description?$description:$value)."</option>\n\r";
			} else {
				$value = ( is_assoc_array($choices) ? $value : $description['name'] );
				if (is_array($selected)) {
					$select_str = ( in_array($value, $selected) ? ' selected="selected"' : '' );
				} else {
					$select_str = ( $selected == $value ? ' selected="selected"' : '' );
				}
				
				$return .= '<option value="'.$value.'"'.$select_str.'>'.($description['name']?$description['name']:$value)."</option>\n\r";
				
				if (is_array($description['children'])) {
					foreach($description['children'] as $value2 => $description2) {
						$value2 = ( is_assoc_array($choices) ? $value2 : $description2 );
						if (is_array($selected)) {
							$select_str = ( in_array($value2, $selected) ? ' selected="selected"' : '' );
						} else {
							$select_str = ( $selected == $value2 ? ' selected="selected"' : '' );
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

	return error_check($return);

}

/**
 * Creates a select tag (dropdown box). Can't beat this!!!
 *
 * @author John Faircloth, Nesbert Hidalgo
 * @param string $name required
 * @param mix $selected optional 
 * @param array $choices optional
 * @param array $html_options optional
 * @param string $none_title optional default set to "None Available"
 * @return string
 */ 

function checkbox_select($name, $selected = array(), $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false) {
	
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
			$label_options['class'] = $class_temp . ( strstr($desc, 'class="sub"') ? '_sub' : '' ) . ' row ' . cycle('row-1', 'row-2');
			$label_options['for'] = name_to_id($name) . '_' . $value;
			$return .= "<label ".html_options_str($label_options).">\n";
			$return .= create_input_tag('checkbox', $name, $value, $box_html_options, in_array($value, $selected), $desc)."\n";
			$return .= "<br /></label>\n";		
		}
		
	} else {
		$return .= '<span class="'.underscore($none_title).'">'.$none_title.'</span>';
	}
	
	$return .= "</div>\n";
	
	return $return;
}


/**
 * Creates dropdown of states.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $selected optional
 * @param array $choices optional
 * @param array $html_options optional
 * @return string
 */
 
function select_states_tag($name, $selected = null, $choices = null, $html_options = null, $select_all = false) {

	if (!$select_all) {
		$choices = ( $choices ? $choices : array('' => 'Please select...') );
	} else {
		$choices = ( $choices ? $choices : array('all' => 'All States..') );
	}
	
	$state = new state_country_model();
	
	$state_arr = array_merge($choices, $state->get_states_by_country_code());
	
	return select($name, $selected, $state_arr, $html_options);
	
}

/**
 * Creates dropdown of countries. if $state_id is it will automatically populate those
 * values depending on the contry selected.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param string $selected optional
 * @param array $choices optional
 * @param array $html_options optional
 * @param array $state_id optional will update the states depending on the country selected
 * @return string
 */
 
function select_countries_tag($name, $selected = null, $choices = null, $html_options = null, $state_id = null) {

	$choices = ( $choices ? $choices : array('' => 'Please select...') );
	
	$country = new state_country_model();
	$country_arr = array_merge($choices, $country->get_countries());

	$html_options['onchange'] .= 'set_'.$state_id.'();';	
	
	$return = select($name, $selected, $country_arr, $html_options);
	
	// automatic state dropdown update
	if ( $state_id ) {
	
		$state = new state_country_model();
		$state->load_states_by_country_code();
		
		while ( $state->get_next() ) {
			$usaVals[] = $state->get_description();
			$usaIDs[] = $state->get_state();
		}

		$state->load_states_by_country_code('Canada');
	
		while ( $state->get_next() ) {
			$canadaVals[] = $state->get_description();
			$canadaIDs[] = $state->get_state();
		}
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function set_<?=$state_id?>() {
			
			var usaVals = new Array("<?=implode('", "', $usaVals)?>");
			var usaIDs = new Array("<?=implode('", "', $usaIDs)?>");
			var canadaVals = new Array("<?=implode('", "', $canadaVals)?>");
			var canadaIDs = new Array("<?=implode('", "', $canadaIDs)?>");
			var countryDrop = document.getElementById("<?=name_to_id($name)?>");
			var selectedCountry = countryDrop.options[countryDrop.selectedIndex].value;
			
			switch ( selectedCountry ) {
				case "United States":
				case "USA":
					update_<?=$state_id?>(usaVals, usaIDs);
				break;
				case "Canada":
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

/**
 * Creates dropdown that redirects the page onchange.
 *
 * @author Nesbert Hidalgo
 * @param string $name required
 * @param array $names_and_urls requires
 * @param array $html_options optional
 * @return string
 */
 
function select_redirect($name, $names_and_urls, $html_options = null) {
	
	$html_options['onchange'] .= 'location.href=this.options[this.selectedIndex].value;';	
	
	$return = select($name, null, $names_and_urls, $html_options);
	return $return;
	
}


/**
 * Create date selectboxes
 * 
 * @author Nesbert
 * @access public
 * @param string $name 
 * @param mixed $date
 * @return string
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

function time_select($name, $time = null)
{
	$time = ($time == null) ? mktime() : $time;

	$i = 1;
	$hours = array();
	while ($i <= 12) { $hours[$i] = $i; $i++; }	

	$i = 0;
	$minutes = array();
	while ($i <= 59) { $minutes[sprintf("%02d", $i)] = sprintf("%02d", $i); $i++; }	

	$ampm['AM'] = 'AM';
	$ampm['PM'] = 'PM';

	$out = "";
	$out .= select("{$name}[hour]", date('g', time()), $hours);
	$out .= select("{$name}[minute]", date('i', time()), $minutes);
	$out .= select("{$name}[ampm]", date('A', time()), $ampm);

	return $out;
}

function date_time_select($name, $datetime = null)
{
	return date_select($name, $datetime)." @ ".time_select($name, $datetime);
}

function get_timestamp_from_post($key)
{
	$_POST[$key]['hour'] = ($_POST[$key]['ampm'] == 'pm') ? ($_POST[$key]['hour'] + 12) : $_POST[$key]['hour'];
	return mktime($_POST[$key]['hour'], $_POST[$key]['minute'], 0, $_POST[$key]['month'], $_POST[$key]['day'], $_POST[$key]['year']);
}

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

?>