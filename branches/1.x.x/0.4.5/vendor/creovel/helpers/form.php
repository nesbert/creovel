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
    return CForm::name_to_id($field_name);
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
    return CForm::add_error($field_name, $message);
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
    return CForm::field_has_error($field_name);
}

/**
 * Check if form has errors.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function form_has_errors()
{
    return CForm::has_error();
}

/**
 * Returns the total number of form errors.
 *
 * @return integer
 * @author Nesbert Hidalgo
 **/
function form_errors_count()
{
    return CForm::error_count();
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
    return CForm::error_messages_for($errors, $title, $description);
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
    return CForm::start_form($event_options,
                            $name_or_obj,
                            $name_value,
                            $method,
                            $html_options);
}

/**
 * Creates the end form tag for lazy programmers or anal ones!
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function end_form_tag()
{
    return CForm::end_form();
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
    return CForm::input($type, $name, $value, $html_options, $tag_value, $text);
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
    return CForm::text_field($name, $value, $html_options, $text);
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
    return CForm::hidden_field($name, $value, $html_options);
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
    return CForm::password_field($name, $value, $html_options, $text);
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
    return CForm::radio_button($name, $value, $html_options, $on_value, $text);
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
    return CForm::check_box($name, $value, $html_options, $on_value, $text);
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
    return CForm::submit($value, $html_options);
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
    return CForm::button($value, $html_options);
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
    return CForm::textarea($name, $value, $html_options);
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
    return CForm::text_area($name, $value, $html_options);
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
    return CForm::label($name, $title, $html_options);
}

/**
 * Creates a select tag (dropdown box).
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @param string $none_title Default set to "None Available"
 * @return string
 * @author Nesbert Hidalgo
 **/
function select($name, $selected = '', $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false)
{
    return CForm::select($name, $selected, $choices, $html_options, $none_title, $have_none);
}

/**
 * Creates dropdown of states.
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @param string $country Default set to "US"
 * @param boolean $state_input
 * @return string
 * @author Nesbert Hidalgo
 **/
function select_states_tag($name = 'state', $selected = null, $choices = null, $html_options = null, $country = 'US', $state_input = false)
{
    return CForm::select_states($name, $selected, $choices,
            $html_options, $country, $state_input);
}

/**
 * Creates dropdown of countries. if $state_id is it will automatically
 * populate those values depending on the country selected.
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @param string $state_id
 * @return boolean $state_input
 * @author Nesbert Hidalgo
 **/
function select_countries_tag($name = 'country', $selected = null, $choices = null, $html_options = null, $state_id = null, $state_input = false)
{
    return CForm::select_countries($name, $selected, $choices,
            $html_options, $state_id, $state_input);
}

/**
 * Creates dropdown that redirects the page onchange.
 *
 * @param string $name
 * @param string $names_and_urls
 * @param string $html_options
 * @return string
 * @author Nesbert Hidalgo
 **/
function select_redirect($name, $names_and_urls, $html_options = null)
{
    return CForm::select_redirect($name, $names_and_urls, $html_options);
}

/**
 * Create date select boxes.
 *
 * @param string $name
 * @param string $date
 * @param string $html_options
 * @return string
 * @author Nesbert Hidalgo
 **/
function date_select($name, $date = null, $html_options = null)
{
    return CForm::date_select($name, $date, $html_options);
}

/**
 * Create time select boxes.
 *
 * @param string $name
 * @param string $time
 * @param string $html_options
 * @return string
 * @author Nesbert Hidalgo
 **/
function time_select($name, $time = null, $html_options = null)
{
    return CForm::time_select($name, $time, $html_options);
}

/**
 * Create date & time select boxes.
 *
 * @param string $name
 * @param string $time
 * @param string $html_options
 * @return string
 * @author Nesbert Hidalgo
 **/
function date_time_select($name, $datetime = null, $html_options = null)
{
    return CForm::date_time_select($name, $datetime, $html_options);
}

/**
 * Get the timestamp array from post.
 *
 * @param string $key
 * @return string
 * @author Russ Smith
 **/
function get_timestamp_from_post($key)
{
    return CForm::get_timestamp_from_post($key);
}

/**
 * Get the timestamp array from array.
 *
 * @param array $array
 * @return string
 * @author Russ Smith
 **/
function get_timestamp_from_array($array)
{
    return CForm::get_timestamp_from_array($array);
}

/**
 * Create timezone select boxes.
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @return void
 * @author Nesbert Hidalgo
 **/
function select_time_zone_tag($name, $selected = null, $choices = null, $html_options = null)
{
    return CForm::select_time_zone($name, $selected, $choices, $html_options);
}

/**
 * Creates a DIV with a group of checkbox inputs.
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @param string $none_title Default set to "None Available"
 * @param boolean $have_none
 * @return void
 * @author Nesbert Hidalgo
 **/
function checkbox_select($name, $selected = array(), $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false, $type = 'checkbox')
{
    return CForm::checkbox_select($name, $selected, $choices, $html_options, $none_title, $have_none, $type);
}

/**
 * Creates a DIV with a group of radio inputs.
 *
 * @param string $name
 * @param string $selected
 * @param string $choices
 * @param string $html_options
 * @param string $none_title Default set to "None Available"
 * @param boolean $have_none
 * @return void
 * @author Nesbert Hidalgo
 **/
function radio_select($name, $selected = array(), $choices = null, $html_options = null, $none_title = 'None Available', $have_none = false)
{
    return CForm::checkbox_select($name, $selected, $choices, $html_options, $none_title, $have_none);
}
