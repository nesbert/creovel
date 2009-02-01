<?php
/**
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
    $name = strtolower(trim($name));
    $no_end_tag = false;
    
    if ($name == 'state') {
        print_obj($html_options, 1);
    }
    
    // set flag for tags with no ends
    switch ($name) {
        case 'meta':
        case 'link':
        case 'input':
        case 'br':
        case 'img':
            $no_end_tag = true;
            break;
    }
    
    return "<{$name}" .
            (($attr_str = html_options_str($html_options)) ? ' ' . $attr_str : '') .
            ($no_end_tag ? ' />' : ">{$content}</{$name}>" );
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
    $options_str = '';
    if (count($html_options)){
        // lowercase all attributes
        foreach ($html_options as $attribute => $value) {
            $html_options[strtolower($attribute)] = $value;
        }
        
        // add confirm pop up
        if (isset($html_options['confirm'])) {
            $msg = str_replace("'", "\'", htmlentities($html_options['confirm']));
            if (isset($html_options['onclick'])) {
                $html_options['onclick'] =
                    "if ( !window.confirm('{$msg}') ) return false; " .
                    $html_options['onclick'];
            }
            unset($html_options['confirm']);
        }
        
        // create options string foreach valid option set
        foreach ($html_options as $attribute => $value) {
            $options_str .= ' ' . $attribute . '="' . $value .'"';
        }
    }
    
    return trim($options_str);
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
    if ( is_array($url) ) foreach ( $url as $path ) {
        $return .= stylesheet_include_tag(CSS_URL . $path . '.css', $media) . "\n";
    }
    return $return
            ? $return
            : create_html_element('link',
                                    array(
                                        'rel' => 'stylesheet',
                                        'type' => 'text/css',
                                        'media' => $media,
                                        'href' => $url)
                                        );
}

/**
 * Returns a javascript script tag with $script for contents.
 *
 * @param string $script
 * @return string
 * @author Nesbert Hidalgo
 **/
function javascript_tag($script)
{
    return create_html_element('script',
                                array('type' => 'text/javascript'),
                                $script);
}

/**
 * Returns a javascript include script tag.
 *
 * @param string $url Relative stylesheet path
 * @return string
 * @author Nesbert Hidalgo
 **/
function javascript_include_tag($url)
{
    $return = '';
    if (is_array($url)) foreach ($url as $path) {
        $return .= javascript_include_tag(JAVASCRIPT_URL . $path . '.js',
                                            $media) . "\n";
    }
    return $return
            ? $return
            : create_html_element('script',
                                        array(
                                        'type' => 'text/javascript',
                                        'src' => $url)
                                        );
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
    // set href
    $html_options['href'] = $html_options['href']
                            ? $html_options['href']
                            : url_for($controller, $action, $id, $html_options['https']);
    // if action is array merge it with html_options
    if (is_array($action)) $html_options = array_merge((array) $action, (array) $html_options);
    return create_html_element('a', $html_options, $link_title);
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
	$html_options['href'] = $url;
	return link_to($link_title, null, null, null, $html_options);
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
    $url = urlencode(strip_tags(str_replace(array(',', '.', '<br>', '<br />', '<br/>'), array('', '', ' ', ' ', ' '), $address)));
    $url .= ( $html_options['title'] ? '+('.urlencode($html_options['title']).')' : '' );
    return link_to_url($link_title, 'http://maps.google.com/maps?q=' . $url, $html_options);
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
    if ($amphersand_encode) {
        $html_options['href'] = amphersand_encode('mailto:' . $email);
    } else {
        $html_options['href'] = 'mailto:' . $email;
    }
    return link_to(($link_title ? $link_title : $email ), null, null, null, $html_options);
}