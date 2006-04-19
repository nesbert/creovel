<?php
/**
 * Returns MySQL Timestamp.
 *
 * @author Nesbert Hidalgo
 * @param string/array $url required
 */
 
function db_time($unix_timestamp = null) {

	return date('Y-m-d H:i:s', ( $unix_timestamp ? $unix_timestamp : time() ));

}
?>