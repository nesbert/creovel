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
?>