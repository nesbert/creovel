<?php
/* HTML helpers here. */

/**
 * Returns a stylesheets include tag.
 *
 * @author Nesbert Hidalgo
 * @param string/array $url required
 * @mparam string $/array optional default set to "screen"
 */ 
function stylesheet_include_tag($url, $media = 'screen') {
	if ( is_array($url) ) {
	
		foreach ( $url as $path ) {
		
			$str .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"".$media."\" href=\"/stylesheets/".$path.".css\">\n";
		
		}
		
		return $str;
	
	} else {
	
		return sprintf('<link rel="stylesheet" type="text/css" media="'.$media.'" href="%s">', $url);
		
	}
	
}

/**
 * Returns a javascript script tag.
 *
 * @author Nesbert Hidalgo
 * @param string $script required
 */
function javascript_tag($script) {
	return sprintf('<script language="javascript" type="text/javascript">%s</script>'."\n", $script);
}

/**
 * Returns a javascript include tag.
 *
 * @author Nesbert Hidalgo
 * @param string/array $url required
 */ 
function javascript_include_tag($url) {
	if ( is_array($url) ) {
	
		foreach ( $url as $path ) {
		
			$str .= "<script language=\"javascript\" type=\"text/javascript\" src=\"/javascripts/".$path.".js\"></script>\n";
		
		}
		
		return $str;
	
	} else {
	
		return sprintf('<script language="javascript" type="text/javascript" src="%s"></script>', $url);
		
	}
}
?>