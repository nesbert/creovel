<?php
/*
 * Sets and unsets $_SESSION['notice'].
 *
 * @author Nesbert Hidalgo
 * @param string $message optional
 */

function flash_notice($message = null) {

	if ( $message || $_SESSION['notice']['message'] ) {

		if ( $message ) {
		
			$_SESSION['notice']['message'] = $message;
			$_SESSION['notice']['checked'] = 'no';
		
		} elseif ( $_SESSION['notice']['checked'] == 'no' ) {
		
			$_SESSION['notice']['checked'] = 'yes';	
			return true;
		
		} else {
		
			$message = $_SESSION['notice']['message'];
			unset($_SESSION['notice']);
			return $message;
			
		}
		
	} else {

		return false;
	
	}

}

/*
 * Creates the floating tabs. Expects an array, key = url/javascript, value = label
 *
 * @author Nesbert Hidalgo
 * @param array $links required
 * @param string $current optional default set to 1
 * @param bool $use_small_tabs optional default set to false
 * @return string
 */
 
function tabs($links, $current = 1, $use_small_tabs = false) {

	$tabs_id = 'tab'.rand();

	$tabs = '<ul class="tabs'.( $use_small_tabs ? ' small_tabs' : '' ).'">';
	
	$count = 1;
	
	foreach ( $links as $link => $text ) {
		$text_id = (is_numeric($current) ? $count : underscore(strip_tags($text)));
		
		$tabs .= '<li id="'.$tabs_id.'_'.$count.'" '.( $text_id == $current ? ' class="current"' : '').'><a href="'.$link.'" '.( strstr($link, 'javascript:') ? 'onclick="'.$tabs_id.'('.$count.', '.count($links).');"' : '' ).'><span>'.$text.'</span></a></li>';
		$count++;
	}
		
	$tabs .= '</ul>';
	
	?>
	<script language="javascript" type="text/javascript">
	<!--
		function <?=$tabs_id?>(tab_id, tabs_count) {

			for (var i=1; i <= tabs_count; i++) {
			
				if (tab_id == i) {
					document.getElementById('<?=$tabs_id?>_' + i).className = 'current';
				} else {
					document.getElementById('<?=$tabs_id?>_' + i).className = '';
				}
			
			}
			
		}
	-->	
	</script>
	<?
	
	return $tabs;
	
}
?>