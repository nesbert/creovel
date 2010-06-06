<?php
/**
 * Base CTag class for HTML/Tag functions.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class CTag extends CObject
{
    /**
     * Base function used to create the different types of HTML tags.
     *
     * @param string $name Tag name
     * @param array $html_options Associative array of attributes
     * @param string $content
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function create($name, $html_options = null, $content = null)
    {
        $name = strtolower(trim($name));
        $no_end_tag = false;

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
                (($attr_str = self::attributes($html_options)) ? ' ' . $attr_str : '') .
                ($no_end_tag ? ' />' : ">{$content}</{$name}>" );
    }

    /**
     * Creates a string of html tag attributes.
     *
     * @param array $html_options Associative array of attributes
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function attributes($html_options)
    {
        $options_str = '';
        if (is_array($html_options)){
            // lowercase all attributes
            foreach ($html_options as $attribute => $value) {
                $html_options[strtolower($attribute)] = $value;
            }

            // add confirm pop up
            if (isset($html_options['confirm'])) {
                $msg = str_replace("'", "\'", htmlentities($html_options['confirm']));
                $onclick = "if (!window.confirm('{$msg}')) return false;";
                if (isset($html_options['onclick'])) {
                    $html_options['onclick'] = $onclick . " " .$html_options['onclick'];
                } else {
                    $html_options['onclick'] = $onclick;
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
    public function stylesheet_include($url, $media = 'screen')
    {
        $html_options = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'media' => $media,
            'href' => $url
            );
        if (is_array($media)) {
            $html_options = array_merge($html_options, $media);
            $html_options['media'] = isset($media['media']) ? $media['media'] : 'screen';
        }

        $return = '';
        if (is_array($url)) foreach ($url as $path) {
            $file = CValidate::in_string('.css', $path) ? $path : CSS_URL . $path . '.css';
            $return .= self::stylesheet_include($file, $media) . "\n";
        }
        return $return ? $return : self::create('link', $html_options);
    }

    /**
     * Returns a javascript script tag with $script for contents.
     *
     * @param string $script
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function javascript($script = '', $html_options = array())
    {
        $html_options['type'] = 'text/javascript';
        return self::create('script', $html_options, $script);
    }

    /**
     * Returns a javascript include script tag.
     *
     * @param string $url Relative stylesheet path
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function javascript_include($url, $html_options = array())
    {
        $return = '';
        if (is_array($url)) foreach ($url as $path) {
            $return .= self::javascript_include(
                        $path ? JAVASCRIPT_URL . $path . '.js' : '',
                        $html_options) . "\n";
        }
        if ($url) $html_options['src'] = $url;
        return $return ? $return : self::javascript('', $html_options);
    }


    /**
     * Creates a anchor link for lazy programmers.
     *
     * <code>
     * <?=CTag::link_to('Edit', 'agent', 'edit', $this->agent->id, array('class' => 'classname', 'target' => '_blank'))?>
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
    public function link_to($link_title = 'Goto', $controller = '',
    $action = '', $id = '', $html_options = null) {
        // set href
        $html_options['href'] = @$html_options['href']
                                ? $html_options['href']
                                : url_for($controller, $action, $id,
                                    @$html_options['https']);
        // if action is array merge it with html_options
        if (is_array($action)) $html_options = array_merge(
            (array) $action, (array) $html_options);
        return self::create('a', $html_options, $link_title);
    }

    /**
     * Creates a anchor link for lazy programmers.
     *
     * <code>
     * <?=CTag::link_to_url('Edit', 'http://creovel.org', array('class' => 'classname', 'target' => '_blank'))?>
     * </code>
     *
     * @param string $link_title Defaults to "Goto"
     * @param string $url
     * @param array $html_options
     * @return void
     * @author Nesbert Hidalgo
     **/
    public function link_to_url($link_title = 'Goto', $url = '#',
    $html_options = null) {
        $html_options['href'] = $url;
        return self::link_to($link_title, null, null, null, $html_options);
    }

    /**
     * Creates a anchor link for lazy programmers.
     *
     * <code>
     * <?=CTag::link_to_google_maps('Directions', '21 Jump Street Los Angeles, CA 90001', array( 'class' => 'classname', 'name' => 'top'))?>
     * </code>
     *
     * @param string $link_title Defaults to "Google Maps&trade;"
     * @param string $address
     * @param array $html_options
     * @return void
     * @author Nesbert Hidalgo
     **/
    public function link_to_google_maps($link_title = 'Google Maps&trade;',
    $address, $html_options = null) {
        $url = urlencode(strip_tags(str_replace(array(',', '.', '<br>', '<br />', '<br/>'), array('', '', ' ', ' ', ' '), $address)));
        $url .= isset($html_options['title']) ? '+('.urlencode($html_options['title']).')' : '';
        return self::link_to_url(
                $link_title,
                'http://maps.google.com/maps?q=' . $url,
                $html_options);
    }

    /**
     * Creates an email link.
     *
     * @param string $email Email address
     * @param string $link_title
     * @param array $html_options
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function mail_to($email, $link_title = null, $html_options = null)
    {
        $html_options['href'] = 'mailto:' . $email;
        return self::link_to(($link_title ? $link_title : $email ),
                null, null, null, $html_options);
    }
} // END class CTag extends CObject