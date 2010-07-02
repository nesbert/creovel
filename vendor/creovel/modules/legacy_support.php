<?php
/**
 * Legacy Support class that hooks old top level functions to
 * object function that it was replaced by.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class LegacySupport extends ModuleBase
{
    /**
     * Dummy function used to call this class file and load top
     * level functions.
     *
     * @return void
     **/
    public function init()
    {}
} // END class Cipher extends ModuleBase


# helpers/datetime.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CDate object. Relying on this feature is highly discouraged.
 *
 * Global date and time functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.3.0
**/

/**
 * Creates/converters time into a MySQL Timestamp. <i>Note: If nothing
 * is passed use current server time</i>.
 *
 * @param mixed $datetime Accepts either an array, unix timestamp or string.
 * @return string Date and time stamp "1979-03-06 05:55:55".
 * @author Nesbert Hidalgo
 **/
function datetime($datetime = null)
{
    return CDate::datetime($datetime);
}

/**
 * Returns the current time measured in the number of seconds since the Unix Epoch
 * (January 1 1970 00:00:00 GMT) in GMT
 *
 * @return integer
 * @author John Faircloth
 **/
function gmtime()
{
    return CDate::gmtime();
}

/**
 * MySQL Timestamp of from current time in GMT.
 *
 * @param mixed $datetime Accepts either an array, unix timestamp or string.
 * @see datetime
 * @return string Date and time stamp.
 * @author Nesbert Hidalgo
 **/
function gmdatetime($datetime = null)
{
    return CDate::gmdatetime($datetime);
}

/**
 * Returns time passed. Latest activity about "8 hours" ago.
 *
 * @param mixed $time Accepts unix timestamp or datetime string.
 * @return string
 * @author Nesbert Hidalgo 
 **/
function time_ago($time)
{ 
    return CDate::time_ago($time);
}

/**
 * Get an array of dates with key as date (Y-m-d) and value as day (D).
 *
 * @param mixed $start
 * @param mixed $end
 * @param string $key_date_format
 * @param string $value_date_format
 * @param mixed $end
 * @return Array
 * @author Nesbert Hidalgo
 **/
function date_range($start, $end = '', $key_date_format = 'Y-m-d', $value_date_format = 'D')
{
    return CDate::date_range($start, $end, $key_date_format, $value_date_format);
}

# helpers/form.php

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


# helpers/html.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CLocale object. Relying on this feature is highly discouraged.
 *
 * HTML/Tag functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
**/

/**
 * Base function used to create the different types of HTML tags.
 *
 * @param string $name Tag name
 * @param array $html_options Associative array of attributes
 * @param string $content
 * @return string
 * @author Nesbert Hidalgo
 **/
function create_html_element($name, $html_options = null, $content = null)
{
    return CTag::create($name, $html_options, $content);
}

/**
 * Creates a string of html tag attributes.
 *
 * @param array $html_options Assoicative array of attributes
 * @return string
 * @author Nesbert Hidalgo
 **/
function html_options_str($html_options)
{
    return CTag::attributes($html_options);
}

/**
 * Returns a stylesheets include tag.
 *
 * @param string $url Relative stylesheet path
 * @param string $media Stylesheet type default set to "screen"
 * @return string
 * @author Nesbert Hidalgo
 **/
function stylesheet_include_tag($url, $media = 'screen')
{
    return CTag::stylesheet_include($url, $media);
}

/**
 * Returns a javascript script tag with $script for contents.
 *
 * @param string $script
 * @return string
 * @author Nesbert Hidalgo
 **/
function javascript_tag($script = '', $html_options = array())
{
    return CTag::javascript($script, $html_options);
}

/**
 * Returns a javascript include script tag.
 *
 * @param string $url Relative stylesheet path
 * @return string
 * @author Nesbert Hidalgo
 **/
function javascript_include_tag($url, $html_options = array())
{
    return CTag::javascript_include($url, $html_options);
}


/**
 * Creates a anchor link for lazy programmers.
 *
 * <code>
 * <?=link_to('Edit', 'agent', 'edit', $this->agent->id, array('class' => 'classname', 'target' => '_blank'))?>
 * </code>
 *
 * @param string $link_title Defaults to "Goto"
 * @param string $controller
 * @param string $action Optional
 * @param mixed $id Optional ID or an associative array of parameters
 * @param array $html_options
 * @return void
 * @author Nesbert Hidalgo
 **/
function link_to($link_title = 'Goto', $controller = '', $action = '', $id = '', $html_options = null)
{
    return CTag::link_to($link_title, $controller,
            $action, $id, $html_options);
}

/**
 * Creates a anchor link for lazy programmers.
 *
 * <code>
 * <?=link_to_url('Edit', 'http://creovel.org', array('class' => 'classname', 'target' => '_blank'))?>
 * </code>
 *
 * @param string $link_title Defaults to "Goto"
 * @param string $url
 * @param array $html_options
 * @return void
 * @author Nesbert Hidalgo
 **/
function link_to_url($link_title = 'Goto', $url = '#', $html_options = null)
{
    return CTag::link_to_url($link_title, $url, $html_options);
}

/**
 * Creates a anchor link for lazy programmers.
 *
 * <code>
 * <?=link_to_google_maps('Directions', '21 Jump Street Los Angeles, CA 90001', array( 'class' => 'classname', 'name' => 'top'))?>
 * </code>
 *
 * @param string $link_title Defaults to "Google Maps&trade;"
 * @param string $address
 * @param array $html_options
 * @return void
 * @author Nesbert Hidalgo
 **/
function link_to_google_maps($link_title = 'Google Maps&trade;', $address, $html_options = null)
{
    return CTag::link_to_google_maps($link_title, $address, $html_options);
}

/**
 * Creates an email link.
 *
 * @param string $email Email address
 * @param string $link_title
 * @param array $html_options
 * @param boolean $amphersand_encode
 * @return string
 * @author Nesbert Hidalgo
 **/
function mail_to($email, $link_title = null, $html_options = null, $amphersand_encode = false)
{
    return CTag::mail_to($email, $link_title,
            $html_options, $amphersand_encode);
}

# helpers/locale.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CLocale object. Relying on this feature is highly discouraged.
 * 
 * Language and location functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
**/

/**
 * Returns an array of countries and states. Only US and Canada
 * states/provinces for now.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function countries_array($more_states = false)
{
    return CLocale::countries_array($more_states);
}

/**
 * Returns an array of countries.
 *
 * @param boolean $us_first
 * @param boolean $show_abbr
 * @return array
 * @author Nesbert Hidalgo
 **/
function countries($us_first = false, $show_abbr = false)
{
    return CLocale::countries($us_first, $show_abbr);
}

/**
 * Returns an array of states/provinces.
 *
 * @param boolean $country Default is 'US'
 * @param boolean $show_abbr
 * @param boolean $more_states
 * @return array
 * @author Nesbert Hidalgo
 **/
function states($country = 'US', $show_abbr = false, $more_states = false)
{
    return CLocale::states($country, $show_abbr, $more_states);
}

/**
 * Returns an array of timezone with GMT labels for keys and
 * timezone name as value.
 *
 * @return void
 * @author Nesbert Hidalgo
 **/
function timezones()
{
    return CLocale::timezones();
}

# helpers/server.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CNetwork object. Relying on this feature is highly discouraged.
 * 
 * General server/networking functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
 **/

/**
 * Returns browser's IP address.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function ip()
{
    return CNetwork::ip();
}

/**
 * Return the http: or https: depending on environment.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http()
{
    return CNetwork::http();
}

/**
 * Returns the current server host.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function host()
{
    return CNetwork::host();
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http_host()
{
    return CNetwork::http_host();
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function url()
{
    return CNetwork::url();
}

/**
 * Returns the current server domain.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function domain()
{
    return CNetwork::domain();
}

/**
 * A top-level domain (TLD), sometimes referred to as a top-level domain name
 * (TLDN), is the last part of an Internet domain name; that is, the letters
 * that follow the final dot of any domain name. For example, in the domain
 * name www.example.com, the top-level domain is "com".
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function tld()
{
    return CNetwork::tld();
}

/**
 * Converts a string IP to and integer and vice versa. If no $ip is passed
 * will convert $_SERVER['REMOTE_ADDR'] to an integer.
 *
 * @return mixed $ip
 * @return integer
 * @author Nesbert Hidalgo
 **/
function int_ip($ip = null)
{
    return CNetwork::int_ip($ip);
}

# helpers/text.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CString object. Relying on this feature is highly discouraged.
 * 
 * Text & String functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

/**
 * Returns a pluralized version of a $word.
 *
 * @param string $word
 * @param integer $count
 * @return string
 * @author Nesbert Hidalgo
 **/
function pluralize($word, $count = null)
{
    return CString::pluralize($word, $count = null);
}

/**
 * Returns a singularized verision of a $word.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function singularize($word)
{
    return CString::singularize($word);
}

/**
 * Transform text like 'programmers_field' to 'Programmers Field'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function humanize($word)
{
    return CString::titleize($word);
}

/**
 * Transform text like 'programmers_field' to 'ProgrammersField'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function camelize($word)
{
    return CString::camelize($word);
}

/**
 * Replaces every instance of the underscore ("_") or space (" ")
 * character by a dash ("-").
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function dasherize($word)
{
    return CString::underscore($word, '-');
}

/**
 * Transforms text like 'ProgrammersField' to 'programmers_field'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function underscore($word)
{
    return CString::underscore($word);
}

/**
 * Transforms text to 'ClassName'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function classify($word)
{
    return CString::classify($word);
}

/**
 * Helpful for alternating between between two values during a loop.
 *
 * <code>
 * <tr class="<?=cycle('data_alt1', 'data_alt2')?>">
 * <tr class="data_alt<?=cycle()?>">
 * </code>
 *
 * @param string $var1
 * @param string $var2
 * @return mixed Returns 1 & 2 in to strings passed
 * @author Nesbert Hidalgo
 **/
function cycle($var1 = '', $var2 = '')
{
    return CString::cycle($var1, $var2);
}

/**
 * Replace every " (quote) with its html equevelant.
 *
 * @param string $str
 * @return string
 * @author Nesbert Hidalgo
 **/
function quote2string($str)
{
    return CString::quote2string($str);
}

/**
 * Replace every charactor of a string with $mask
 *
 * @param string $str
 * @param string $mask Optional default set to '*'
 * @return string
 * @author Nesbert Hidalgo
 **/
function mask($str, $mask = '*')
{
    return CString::mask($str, $mask);
}

/**
 * Truncates a string and adds trailing periods to it. Now handles
 * words better thank you Mel Cruz for the suggestion. By default
 * trucates at end of words.
 *
 * @param string $str
 * @param integer $length Optional default set to 100 characters
 * @param string $tail Optional default set to '...'
 * @param boolean $strict Optional default false truncate at exact $length
 * @return string
 * @author Nesbert Hidalgo
 **/
function truncate($str, $length = 100, $tail = '...', $strict = false)
{
    return CString::masktruncate($str, $length, $tail, $strict);
}

/**
 * Reformats a string to fit within a display with a certain
 * number of columns.  Words are moved between the lines as
 * necessary.  Particularly useful for formatting text to
 * be sent via email (prevents nasty wrap-around problems).
 *
 * Credit: syneryder@namesuppressed.com
 *
 * @param string $s The string to be formatted
 * @param integer $l The maximum length of a line
 * @return string
 * @author Russ Smith
 **/
function wordwrap_line($s, $l)
{
    return CString::wordwrap_line($s, $l);
}

/**
 * Retrieve a number from a string.
 *
 * @param string $str
 * @return float
 * @author Nesbert Hidalgo
 **/
function retrieve_number($str)
{
    return CString::retrieve_number($str);
}

/**
 * Checks if the string starts with $needle.
 *
 * @param string $needle
 * @param string $haystack
 * @return string
 * @author Nesbert Hidalgo
 **/
function starts_with($needle, $haystack)
{
    return CString::starts_with($needle, $haystack);
}

/**
 * Checks if the string ends with $needle.
 *
 * @param string $needle
 * @param string $haystack
 * @return string
 * @author Nesbert Hidalgo
 **/
function ends_with($needle, $haystack)
{
    return CString::ends_with($needle, $haystack);
}

/**
 * Convert a number to word representation.
 *
 * @param integer $num
 * @param boolean $money
 * @param boolean $caps
 * @return string
 * @link http://us.php.net/manual/en/function.number-format.php#66895
 **/
function num2words($num, $money = false, $caps = false, $c = 1)
{
    return CString::num2words($num, $money, $caps, $c);
}

/**
 * Escape a string without connecting to a DB.
 *
 * @return string
 * @link http://www.gamedev.net/community/forums/topic.asp?topic_id=448909
 **/
function escape_string($str)
{
    return CString::escape_string($str);
}

/** 
 * Split a string into groups of words with a line no longer than $max 
 * characters. 
 * 
 * @param string $string 
 * @param integer $max 
 * @return array 
 * @author Nesbert Hidalgo
 * @link http://us.php.net/manual/en/function.preg-split.php#95924
 **/ 
function split_words($string, $max = 1)
{ 
    return CString::split_words($string, $max);
}

# helpers/validation.php

/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CValidate object. Relying on this feature is highly discouraged.
 * 
 * Global validation functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
 **/

/**
 * Check if $var is a valid host name. Hostnames must use a-z,0-9,
 * and '-'. A hostname cannot have any spaces nor can it start
 * with a '-'.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_hostname($var)
{
    return CValidate::hostname($var);
}

/**
 * Checks if $var a variable is a valid email address.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_email($var)
{
    return CValidate::email($var);
}

/**
 * Checks if $var a variable is a valid URL.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_url($var)
{
    return CValidate::url($var);
}

/**
 * Checks if $var a variable only contains characters A-Z or a-z
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_alpha($var)
{
    return CValidate::alpha($var);
}

/**
 * Checks if $var only contains characters A-Z or a-z or 0-9.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_alpha_numeric($var)
{
    return CValidate::alpha_numeric($var);
}

/**
 * Checks if $var is a number.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
if (!function_exists('is_number')) {
    function is_number($var)
    {
        return CValidate::number($var);
    }
}

/**
 * Checks if $var is a positive number.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_positive_number($var)
{
    return CValidate::positive_number($var);
}

/**
 * Checks if $var1 is equal to $var2.
 *
 * @param string $var1 Value to validate
 * @param string $var2 Value to validate against
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_match($var1, $var2)
{
    return CValidate::match($var1, $var2);
}

/**
 * Checks if $var is between $min and $max.
 *
 * @param string $var Value to validate
 * @param integer $min Minimum number
 * @param integer $max Maximum number
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_between($var, $min, $max)
{
    return CValidate::between($var, $min, $max);
}

/**
 * Checks if $var length equals $length.
 *
 * @param string $var Value to validate
 * @param integer $length
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_length($var, $length)
{
    return CValidate::length($var, $length);
}

/**
 * Checks if $var length is between $min and $max.
 *
 * @param string $var Value to validate
 * @param integer $min Minimum length
 * @param integer $max Maximum length
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_length_between($var, $min, $max)
{
    return CValidate::length_between($var, $min, $max);
}

/**
 * Finds whether a $var is a regular expression.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_regex($var)
{
    return CValidate::regex($var);
}

/**
 * Finds whether a $var is an odd number.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_even($var)
{
    return CValidate::even($var);
}

/**
 * Finds whether a $var is an odd number.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_odd($var)
{
    return CValidate::odd($var);
}

/**
 * Check is request is using AJAX by checking headers.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_ajax()
{
    return CValidate::ajax();
}

/**
 * Check if an array is an associative array.
 *
 * @param array $_array
 * @link http://us3.php.net/manual/en/function.is-array.php#85324
 * @return boolean
 **/
function is_hash($array)
{
    return CValidate::hash($array);
}

# helpers/general.php

/**
 * General top-level functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
**/

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @param mixed $obj The value to print out
 * @param boolean $kill Die after print out to screen.
 * @return void
 * @author John Faircloth
 **/
function print_obj($obj, $kill = false)
{
    CObject::debug($obj, $kill);
}

/**
 * Returns an array user defined constants.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function get_user_defined_constants()
{
    return CObject::user_defined_constants();
}

/**
 * Get an array of all class parents.
 *
 * @link http://us.php.net/manual/en/function.get-parent-class.php#57548
 * @return array
 **/
function get_ancestors($class)
{
    return CObject::ancestors($class);
}

/**
 * Returns a human readable size or a file or a size
 *
 * @param string $file_or_size File path or size.
 * @link http://us2.php.net/manual/hk/function.filesize.php#64387
 * @return string
 **/
function get_filesize($file_or_size)
{
    return CFile::size($file_or_size);
}

/**
 * Get the mime type of a file.
 *
 * @param string $filepath
 * @link http://us.php.net/manual/en/function.finfo-open.php#78927
 * @return string
 **/
function get_mime_type($filepath)
{
    return CFile::mime_type($filepath);
}

/**
 * Add slashes to arrays, objects, and strings recursively.
 *
 * @param mixed $data
 * @return mixed
 * @author Nesbert Hidalgo
 **/
function add_slashes($data)
{
    return CData::add_slashes($data);
}

/**
 * Strip slashes to arrays, objects, and strings recursively.
 *
 * @param mixed $data
 * @return mixed
 * @author Nesbert Hidalgo
 **/
function strip_slashes($data)
{
    return CData::strip_slashes($data);
}

/**
 * String replaces a string using array keys with array values.
 *
 * @param string $string
 * @param array $array
 * @return string
 * @author Nesbert Hidalgo
 **/
function str_replace_array($string, $array)
{
    return CString::replace_with_array($string, $array);
}

/**
 * A faster/less memory substitute for strstr() used to check the occurrence
 * of a subject in a string.
 *
 * @param string $needle
 * @param array $haystack
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function in_string($needle, $haystack)
{
    return CString::contains($needle, $haystack);
}

/**
 * Get the data type of a variable.
 *
 * @param $var
 * @link http://us3.php.net/manual/en/function.gettype.php#78381
 * @return string
 **/
function get_type($var)
{
    return CData::type($var);
}

/**
 * Returns the raw post from php://input. It is a less memory intensive
 * alternative to $HTTP_RAW_POST_DATA and does not need any special php.ini
 * directives. php://input is not available with enctype="multipart/form-data".
 *
 * @link http://us.php.net/wrappers.php
 * @return string
 * @author Nesbert Hidalgo
 **/
function get_raw_post()
{
    return CData::raw_post();
}

/**
 * Sanitize a string by not allowing HTML, encoding and using HTML Special
 * characters for certain tags. Basic layer for XSS prevention.
 *
 * @param string $str
 * @param string $length
 * @param string $allowed_tags
 * @author Nesbert Hidalgo
 **/
function clean_str($str, $length = 0, $allowed_tags = false)
{
    return CString::clean($str, $length, $allowed_tags);
}

/**
 * Sanitize associative array values.
 *
 * @param array $array
 * @return array
 * @see clean_str()
 * @author Nesbert Hidalgo
 **/
function clean_array($array)
{
    return CArray::clean($array);
}

/**
 * Search a multidimensional array for a certain value and return the
 * array with the match.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function search_array($i, $val, $array)
{
    return CArray::search($i, $val, $array);
}