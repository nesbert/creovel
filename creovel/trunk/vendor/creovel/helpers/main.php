<?php
/*
 * General top-level functions.
 */

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @author John Faircloth
 */
 
function print_obj($obj, $kill = false) {

	echo '<pre class="print_obj" style="text-align: left;">'."\n";
	print_r($obj);
	echo '</pre>'."\n";
	if ( $kill ) die;

}


/**
 * Transform text like 'programmers_field' to 'Programmers Field'
 */	

function humanize($lower_case_and_underscored_word) {
	return str_replace(" ", " ", ucwords(str_replace("_", " ", strtolower($lower_case_and_underscored_word))));
} 


/*
 * Transform text like 'programmers_field' to 'ProgrammersField'
 */	

function camelize($lower_case_and_underscored_word)
{
	return str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));
}    


/*
 * Transforms text like 'ProgrammersField' to 'programmers_field'
 */	

function underscore($camel_cased_word) {
	$camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/','\1_\2',$camel_cased_word);
	return strtolower(preg_replace('/([a-z])([A-Z])/','\1_\2',$camel_cased_word));
}

/**
 * Redirects the page. **Note should only be used on the contrller.
 *
 * @params string $controller required
 * @params string $action required
 * @params int $id optional
 */ 

function redirect_to($controller = '', $action = '', $id = '') {

	header('location: ' . url_for($controller, $action, $id));
	die;
	
}


/**
 * Returns a pluralized verision of a word.
 * Based on Pluralize function v1.0 by Paul Wilkins disk@paradise.net.nz
 * http://homepages.paradise.net.nz/~pmw57/code/javascript/pluralize.js
 *
 * @author Nesbert Hidalgo
 * @param string $word required
 * @param int $count optional
 */

function pluralize($word, $count = null) {

	if ( $count == 1 ) return $word;
	if ( $word == 'is' ) return 'are';

	$unpluralized = array('fish', 'sheep', 'deer');	
	for ( $i=0; $i < count($unpluralized); $i++ ) if ( $unpluralized[$i] == $word ) return $word;
	
	switch ( substr($word, -2) ) {
		case 'ch': case 'sh': case 'ss': case 'zz': case 'x': return $word . 'es';
		case 'ey': case 'oy': case 'ay': return $word . 's';
	}	
	
	switch ( substr($word, -1) ) {
		case 's': return substr($word, 0, -1) . 'ses';
		case 'y': return substr($word, 0, -1) . 'ies';
		case 'z': return substr($word, 0, -1) . 'zes';
	}
	
	switch ( true ) {
		case ( preg_match('/person$/i', $word) ): return str_replace('person', 'people', $word);		
		case ( preg_match('/man$/i', $word) ): return str_replace('man', 'men', $word);		
		case ( preg_match('/child$/i', $word) ): return str_replace('child', 'children', $word);		
	}
	
	return $word . 's';
	
}



function in_csv_string($cvs_string, $mixed_needle) {

	if ( in_array($mixed_needle, split(',', $cvs_string)) ) {
	
		return true;
		
	} else {
	
		return false;
	
	}

}


/**
 * Helpful for alternating between between two values during a loop.
 * Ya'll don't want any of this!!!
 *
 * <code>
 *  <tr class="<?=cycle('data_alt1', 'data_alt2')?>">
 *
 *  <tr class="data_alt<?=cycle()?>">
 * </code> 
 *
 * @author Nesbert Hidalgo
 * @return int/string
 */
 
function cycle($var1 = null, $var2 = null) {
	static $return;
	
	$var1 = $var1 ? $var1 : 1;
	$var2 = $var2 ? $var2 : 2;
	
	$return = ( $return == $var2 || !$return ? $var1 : $var2 );
	
	return $return;
}

/**
 * Implode associative arrays.
 *
 * http://us3.php.net/manual/en/function.implode.php
 */
 
function implode_assoc($assoc_arr, $inglue = '=', $outglue = '&'){
   $return = '';
   foreach ($assoc_arr as $key => $val) {
       $return = ($return != '' ? $return . $outglue : '') . $key . $inglue . $val;
   }
   return $return;
}

/**
 * Creates a query string from an associative array;
 *
 * @author Nesbert Hidalgo
 * @param array
 * @return string
 */
 
function array_to_query_string($assoc_arr){
   $return = '?'.implode_assoc($assoc_arr);
   return $return;
}

/* 
 * check if a data is serialized or not
 * 
 * @param mixed $data variable to check
 * @return boolean
*/

function is_serialized($data) {
	if (trim($data) == "" || !is_string($data)) {
		return false;
	}
	
	
	//add the $data == 'a:0:()' for now because the reg expression is not accounting for an empty array.
	// So if your reading this and you are bored, go for it!
	
	if (preg_match("/^(i|s|a|o|d)(.*);/si", $data) || $data == 'a:0:{}') {
		return true;
	}
	return false;
}

/**
 * Check if array is an associative array.
 *
 * http://us3.php.net/manual/en/function.is-array.php#41179
 */

function is_assoc_array($var) {
   return is_array($var) && array_keys($var)!==range(0,sizeof($var)-1);
}

function set_unique_id() {

	$id = uniqid('id', true);
	$_SESSION[$id];
	return  $id;		
		
}

function clear_unique_id($id) {

	unset($_SESSION[$id]);
		
}

function set_current(&$array, $key) {

	while ( current($array) !== false ) {
		if ( key($array) == $key ) break;
		next($array);
	}
	
}

/*
 * Get the mime type of a file (http://www.duke.edu/websrv/file-extensions.html).
 *
 * @author Nesbert Hidalgo
 * @param string $file_name required
 * @return string
 */
 
function get_mime_type($file_name) {

	$mime_types = array(
		'.ai' => 'application/postscript',
		'.aif' => 'audio/x-aiff',
		'.aifc' => 'audio/x-aiff',
		'.aiff' => 'audio/x-aiff',
		'.asc' => 'text/plain',
		'.au' => 'audio/basic',
		'.avi' => 'video/x-msvideo',
		'.bcpio' => 'application/x-bcpio',
		'.bin' => 'application/octet-stream',
		'.c' => 'text/plain',
		'.cc' => 'text/plain',
		'.ccad' => 'application/clariscad',
		'.cdf' => 'application/x-netcdf',
		'.class' => 'application/octet-stream',
		'.cpio' => 'application/x-cpio',
		'.cpt' => 'application/mac-compactpro',
		'.csh' => 'application/x-csh',
		'.css' => 'text/css',
		'.dcr' => 'application/x-director',
		'.dir' => 'application/x-director',
		'.dms' => 'application/octet-stream',
		'.doc' => 'application/msword',
		'.drw' => 'application/drafting',
		'.dvi' => 'application/x-dvi',
		'.dwg' => 'application/acad',
		'.dxf' => 'application/dxf',
		'.dxr' => 'application/x-director',
		'.eps' => 'application/postscript',
		'.etx' => 'text/x-setext',
		'.exe' => 'application/octet-stream',
		'.ez' => 'application/andrew-inset',
		'.f' => 'text/plain',
		'.f90' => 'text/plain',
		'.fli' => 'video/x-fli',
		'.gif' => 'image/gif',
		'.gtar' => 'application/x-gtar',
		'.gz' => 'application/x-gzip',
		'.h' => 'text/plain',
		'.hdf' => 'application/x-hdf',
		'.hh' => 'text/plain',
		'.hqx' => 'application/mac-binhex40',
		'.htm' => 'text/html',
		'.html' => 'text/html',
		'.ice' => 'x-conference/x-cooltalk',
		'.ief' => 'image/ief',
		'.iges' => 'model/iges',
		'.igs' => 'model/iges',
		'.ips' => 'application/x-ipscript',
		'.ipx' => 'application/x-ipix',
		'.jpe' => 'image/jpeg',
		'.jpeg' => 'image/jpeg',
		'.jpg' => 'image/jpeg',
		'.js' => 'application/x-javascript',
		'.kar' => 'audio/midi',
		'.latex' => 'application/x-latex',
		'.lha' => 'application/octet-stream',
		'.lsp' => 'application/x-lisp',
		'.lzh' => 'application/octet-stream',
		'.m' => 'text/plain',
		'.man' => 'application/x-troff-man',
		'.me' => 'application/x-troff-me',
		'.mesh' => 'model/mesh',
		'.mid' => 'audio/midi',
		'.midi' => 'audio/midi',
		'.mif' => 'application/vnd.mif',
		'.mime' => 'www/mime',
		'.mov' => 'video/quicktime',
		'.movie' => 'video/x-sgi-movie',
		'.mp2' => 'audio/mpeg',
		'.mp3' => 'audio/mpeg',
		'.mpe' => 'video/mpeg',
		'.mpeg' => 'video/mpeg',
		'.mpg' => 'video/mpeg',
		'.mpga' => 'audio/mpeg',
		'.ms' => 'application/x-troff-ms',
		'.msh' => 'model/mesh',
		'.nc' => 'application/x-netcdf',
		'.oda' => 'application/oda',
		'.pbm' => 'image/x-portable-bitmap',
		'.pdb' => 'chemical/x-pdb',
		'.pdf' => 'application/pdf',
		'.pgm' => 'image/x-portable-graymap',
		'.pgn' => 'application/x-chess-pgn',
		'.png' => 'image/png',
		'.pnm' => 'image/x-portable-anymap',
		'.pot' => 'application/mspowerpoint',
		'.ppm' => 'image/x-portable-pixmap',
		'.pps' => 'application/mspowerpoint',
		'.ppt' => 'application/mspowerpoint',
		'.ppz' => 'application/mspowerpoint',
		'.pre' => 'application/x-freelance',
		'.prt' => 'application/pro_eng',
		'.ps' => 'application/postscript',
		'.qt' => 'video/quicktime',
		'.ra' => 'audio/x-realaudio',
		'.ram' => 'audio/x-pn-realaudio',
		'.ras' => 'image/cmu-raster',
		'.rgb' => 'image/x-rgb',
		'.rm' => 'audio/x-pn-realaudio',
		'.roff' => 'application/x-troff',
		'.rpm' => 'audio/x-pn-realaudio-plugin',
		'.rtf' => 'text/rtf',
		'.rtx' => 'text/richtext',
		'.scm' => 'application/x-lotusscreencam',
		'.set' => 'application/set',
		'.sgm' => 'text/sgml',
		'.sgml' => 'text/sgml',
		'.sh' => 'application/x-sh',
		'.shar' => 'application/x-shar',
		'.silo' => 'model/mesh',
		'.sit' => 'application/x-stuffit',
		'.skd' => 'application/x-koan',
		'.skm' => 'application/x-koan',
		'.skp' => 'application/x-koan',
		'.skt' => 'application/x-koan',
		'.smi' => 'application/smil',
		'.smil' => 'application/smil',
		'.snd' => 'audio/basic',
		'.sol' => 'application/solids',
		'.spl' => 'application/x-futuresplash',
		'.src' => 'application/x-wais-source',
		'.step' => 'application/STEP',
		'.stl' => 'application/SLA',
		'.stp' => 'application/STEP',
		'.sv4cpio' => 'application/x-sv4cpio',
		'.sv4crc' => 'application/x-sv4crc',
		'.swf' => 'application/x-shockwave-flash',
		'.t' => 'application/x-troff',
		'.tar' => 'application/x-tar',
		'.tcl' => 'application/x-tcl',
		'.tex' => 'application/x-tex',
		'.texi' => 'application/x-texinfo',
		'.texinfo' => 'application/x-texinfo',
		'.tif' => 'image/tiff',
		'.tiff' => 'image/tiff',
		'.tr' => 'application/x-troff',
		'.tsi' => 'audio/TSP-audio',
		'.tsp' => 'application/dsptype',
		'.tsv' => 'text/tab-separated-values',
		'.txt' => 'text/plain',
		'.unv' => 'application/i-deas',
		'.ustar' => 'application/x-ustar',
		'.vcd' => 'application/x-cdlink',
		'.vda' => 'application/vda',
		'.viv' => 'video/vnd.vivo',
		'.vivo' => 'video/vnd.vivo',
		'.vrml' => 'model/vrml',
		'.wav' => 'audio/x-wav',
		'.wrl' => 'model/vrml',
		'.xbm' => 'image/x-xbitmap',
		'.xlc' => 'application/vnd.ms-excel',
		'.xll' => 'application/vnd.ms-excel',
		'.xlm' => 'application/vnd.ms-excel',
		'.xls' => 'application/vnd.ms-excel',
		'.xlw' => 'application/vnd.ms-excel',
		'.xml' => 'text/xml',
		'.xpm' => 'image/x-xpixmap',
		'.xwd' => 'image/x-xwindowdump',
		'.xyz' => 'chemical/x-pdb',
		'.zip' => 'application/zip',
	);
	
	$file_name = ( strstr($file_name, '/') ? basename($file_name) : $file_name );
	
	$extension = explode('.', $file_name);
	
	return ( in_array('.'.$extension[1], array_keys($mime_types)) ? $mime_types['.'.$extension[1]] : false );

}

/*
 * Removes these unwanted charachters
 *
 * @author Nesbert Hidalgo
 * @param string $tempVal string to strip
 * @return string
 */

function remove_char($tempVal) {
	$tempVal = str_replace (",", "", $tempVal);
	$tempVal = str_replace ("$", "", $tempVal);
	$tempVal = str_replace ("-", "", $tempVal);
	$tempVal = str_replace ("<", "", $tempVal);
	$tempVal = str_replace (">", "", $tempVal);
	$tempVal = str_replace ("%", "", $tempVal);
	$tempVal = str_replace ("#", "", $tempVal);
	$tempVal = str_replace ("*", "", $tempVal);
	$tempVal = str_replace ("+", "", $tempVal);
	$tempVal = str_replace ("=", "", $tempVal);
	$tempVal = str_replace ("/", "", $tempVal);
	$tempVal = str_replace ("\\", "", $tempVal);
	return trim($tempVal);
}

/*
 * copies a folder
 *
 * @author Found on internet, hope it works
 * @param string $source 
 * @param string $destination
 * @return string
 */
function copyr($source, $dest) {
	 // Simple copy for a file
	 if (is_file($source)) {
		 return copy($source, $dest);
	 }
  
	 // Make destination directory
	 if (!is_dir($dest)) {
		 mkdir($dest);
	 }
  
	 // Loop through the folder
	 $dir = dir($source);
	 while (false !== $entry = $dir->read()) {
		 // Skip pointers
		 if ($entry == '.' || $entry == '..') {
			 continue;
		 }
  
		 // Deep copy directories
		 if ($dest !== "$source/$entry") {
			 copyr("$source/$entry", "$dest/$entry");
		 }
	 }
  
	 // Clean up
	 $dir->close();
	 return true;
}

/*
 * Return user definde constats
 *
 * @author Nesbert Hidalgo
 * @return array
 */
 function get_user_defined_constants() {
 	$return = get_defined_constants(true);
	return $return['user'];
 }
?>