<?php

/*
	Function: create_html_element
	
	Base function used to create the different types of HTML tags.
	
	Returns:
	
		String.
*/
 

function create_html_element($name, $html_options = null, $content = null)
{
	$name = strtolower(trim($name));
	$no_end_tag = false;
	
	// set flag for tags with no ends
	switch ($name)
	{
		case 'meta':
		case 'link':
		case 'input':
		case 'br':
		case 'img':
			$no_end_tag = true;
		break;
	}
	
	return "<{$name}" . ( ($attr_str = html_options_str($html_options)) ? ' ' . $attr_str : '' ) . ($no_end_tag ? ' />' : ">{$content}</{$name}>" );
}

/*
	Function: html_options_str
	
	Creates a string of html tag attributes.

	Parameters:
	
		html_options - required
	
	Returns:
		
		String.
*/

function html_options_str($html_options)
{
	$options_str = '';
	if ( is_array($html_options)  && count($html_options) ){
		
		// lowercase all attributes
		foreach ( $html_options as $attribute => $value ) $temp_arr[strtolower($attribute)] = $value;
		

		//print_obj($temp_arr, 1);
		// set valid html attributes
		$html_options = array(
			'rel',
			// form attributes
			'method',
			'action',
			'accept-charset',
			'accept',
			'type',
			// core & form attributes
			'class',
			'id',
			'name',
			'value',
			'style',
			'title',
			'alt',
			'size',
			'maxlength',
			'cols',
			'rows',
			'multiple',
			'nowrap',
			'disabled',
			'readonly',
			// language attributes
			'dir',
			'lang',
			'language',
			// keyboard attributes
			'accesskey',
			'tabindex',
			// window events
			'onload',
			'onunload',
			// form events
			'onchange',
			'onsubmit',
			'onreset',
			'onselect',
			'onblur',
			'onfocus',
			// keyboard events
			'onkeydown',
			'onkeypress',
			'onkeyup',
			// mouse events
			'onclick',
			'ondblclick',
			'onmousedown',
			'onmousemove',
			'onmouseover',
			'onmouseout',
			'onmouseup',
			// misc attributes
			'link',
			'vlink',
			'alink',
			'align',
			'archive',
			'autocomplete',
			'axis',
			'background',
			'bgcolor',
			'border',
			'cellpadding',
			'cellspacing',
			'char',
			'charoff',
			'charset',
			'checked',
			'cite',
			'classid',
			'clear',
			'code',
			'codebase',
			'codetype',
			'color',
			'colspan',
			'rowspan',
			'compact',
			'content',
			'coords',
			'data',
			'datetime',
			'declare',
			'defer',
			'enctype',
			'face',
			'for',
			'frame',
			'frameborder',
			'headers',
			'href',
			'hreflang',
			'hspace',
			'http-equiv',
			'ismap',
			'label',
			'longdesc',
			'media',
			'marginheight',
			'marginwidth',
			'nohref',
			'noresize',
			'noshade',
			'object',
			'profile',
			'prompt',
			'rev',
			'rules',
			'scheme',
			'scope',
			'scrolling',
			'selected',
			'shape',
			'span',
			'src',
			'standby',
			'start',
			'summary',
			'target',
			'text',
			'usemap',
			'valign',
			'valuetype',
			'version',
			'width',
			'height'
		);
		
		// add confirm pop up
		if ( in_array('confirm', array_keys($temp_arr)) ) {
			$msg = str_replace("'", "\'", htmlentities($temp_arr['confirm']));
			$temp_arr['onclick'] = "if ( !window.confirm('{$msg}') ) return false; " . $temp_arr['onclick'];		
		}

		// create options string foreach valid option set
		foreach ( array_unique($html_options) as $attribute ) {
			if (in_array($attribute, array_keys($temp_arr))) $options_str .= ( isset($temp_arr[$attribute]) ? ' ' . $attribute . '="' . $temp_arr[$attribute].'"' : '' );
		}
		
	}
	
	return trim($options_str);
}

/*
	Function: stylesheet_include_tag	
	
	Returns a stylesheets include tag.
	
	Parameters:
		
		url - relative stylesheet path
		media - stylesheet type default set to "screen"
	
	Returns:
	
		String.
*/

function stylesheet_include_tag($url, $media = 'screen')
{
	if ( is_array($url) ) foreach ( $url as $path ) {
		$return .= stylesheet_include_tag(CSS_URL . $path . '.css', $media) . "\n";
	}
	return $return ? $return : create_html_element('link', array( 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => $media, 'href' => $url ));
}

/*
	Function: javascript_tag
	
	Returns a javascript script tag.
	
	Parameters:
	
		script - Javascript code.
		
	Returns:
	
		String.
*/

function javascript_tag($script)
{
	return create_html_element('script', array( 'type' => 'text/javascript' ), $script);
}

/*
	Function: javascript_tag
	
	Returns a javascript include script tag.
	
	Parameters:
	
		script - Relative path to javascript file.
		
	Returns:
	
		String.
*/

function javascript_include_tag($url)
{
	if ( is_array($url) ) foreach ( $url as $path ) {
		$return .= javascript_include_tag(JAVASCRIPT_URL . $path . '.js', $media) . "\n";
	}
	return $return ? $return : create_html_element('script', array( 'type' => 'text/javascript', 'src' => $url ));
}


/*
	Function: link_to
	
	Creates a anchor link for lazy programmers. Example:

	(start code)
		link_to('Edit', 'agent', 'edit', $this->agent->id, array( 'class' => 'classname', 'name' => 'top'))
	(end)
	
	Parameters:
	
		link_title - optional defaults to "Goto"
		controller - required
		action - required
		id - optional
		html_options - optional
	
	Returns:
	
		String.
*/

function link_to($link_title = 'Goto', $controller = '', $action = '', $id = '', $html_options = null)
{
	// set href
	$html_options['href'] = $html_options['href'] ? $html_options['href'] : url_for($controller, $action, $id, $html_options['https']);
	// if action is array merge it with html_options
	if (is_array($action)) $html_options = array_merge((array) $action, (array) $html_options);
	return create_html_element('a', $html_options, $link_title);
}


/*
	Function: link_to_url
	
	Creates a anchor link for lazy programmers using a url. Example:

	(start code)
		link_to_url('Edit', 'http://creovel.org', array( 'class' => 'classname', 'name' => 'top'))
	(end)
	
	Parameters:
	
		link_title - optional defaults to "Goto"
		url - required
		html_options - optional
		
	Returns:
	
		String.
*/

function link_to_url($link_title = 'Goto', $url = '#', $html_options = null)
{
	$html_options['href'] = $url;
	return link_to($link_title, null, null, null, $html_options);
}

/*
	Function: link_to_google_maps
	
	Creates a anchor link for lazy programmers using a url. Example:

	(start code)
		link_to_google_maps('Edit', 'http://creovel.org', array( 'class' => 'classname', 'name' => 'top'))
	(end)
	
	Parameters:
	
		link_title - optional defaults to "Goto"
		controller - required
		action - required
		id - optional
		html_options - optional
	
	Returns:
	
		HTML string.
*/

function link_to_google_maps($link_title = 'Google Maps&trade;', $address, $html_options = null)
{
	$url = urlencode(strip_tags(str_replace(array(',', '.', '<br>', '<br />', '<br/>'), array('', '', ' ', ' ', ' '), $address)));
	$url .= ( $html_options['title'] ? '+('.urlencode($html_options['title']).')' : '' );
	return link_to_url($link_title, 'http://maps.google.com/maps?q=' . $url, $html_options);
}

/*
	Function: mail_to
	
	Creates an email link.
	
	Parameters:
	
		email - required
		link_title - optional
		html_options - optional
		amphersand_encode - optional
	
	Returns:
	
		String.
*/

function mail_to($email, $link_title = null, $html_options = null, $amphersand_encode = false)
{
	if ($amphersand_encode) {
		$html_options['href'] = amphersand_encode('mailto:' . $email);
	} else {
		$html_options['href'] = 'mailto:' . $email;
	}
	return link_to(($link_title ? $link_title : $email ), null, null, null, $html_options);
}

?>