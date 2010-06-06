<?php
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