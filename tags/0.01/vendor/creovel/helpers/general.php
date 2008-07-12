<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/*
 * General top-level functions.
 */

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @author John Faircloth
 */
 
function print_obj($obj, $kill = false)
{

	echo '<pre class="print_obj" style="text-align: left;">'."\n";
	print_r($obj);
	echo "\n</pre>\n";
	if ( $kill ) die;

}

/*
 * Return user definde constats
 *
 * @author Nesbert Hidalgo
 * @return array
 */
 function get_user_defined_constants()
 {
 	$return = get_defined_constants(true);
	return $return['user'];
 }
 
/*
 * Returns a human readable size or a file or a size
 * http://us2.php.net/manual/hk/function.filesize.php#64387
 *
 * @author Nesbert Hidalgo
 * @param mixed $file_or_size
 * @return string
 */
function get_filesize($file_or_size)
{
	$iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");	
	$size = is_numeric($file_or_size) ? $file_or_size : filesize($file_or_size);
	$i = 0;
	while ( ($size/1024) > 1 ) {
		$size = $size / 1024;
		$i++;
	}
	return substr($size, 0, strpos($size,'.') + 4).' '.$iec[$i];
}

/*
 * Get the mime type of a file (http://www.duke.edu/websrv/file-extensions.html).
 *
 * @author Nesbert Hidalgo
 * @param string $file_name required
 * @return string
 */ 
function get_mime_type($filename)
{
	echo $filename;
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
		'.php' => 'text/html',
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
	
	$filename = basename($filename);	
	$extension = pathinfo($filename, PATHINFO_EXTENSION);	
	return ( in_array('.'.$extension, array_keys($mime_types)) ? $mime_types['.'.$extension] : false );
}

/*
 * Deep cleans arrays, objects, strings
 *
 * @author Nesbert Hidalgo
 * @param mixed $data
 * @return mixed
 */
function strip_slashes($data) {
	
	switch ( true ) {
		
		// clean data array
		case ( is_array($data) ):
			$clean_values = array();				
			foreach ($data as $name => $value) $clean_values[$name] = is_array($value) ? array_map('strip_slashes', $value) : stripslashes(trim($value));
		break;
		
		// get vars from object -> clean data -> update and return object
		case ( is_object($data) ):
			$clean_values = $this->strip_slashes(get_object_vars($data));
			foreach ($clean_values as $name => $value) $data->$name = is_array($value) ? array_map('strip_slashes', $value) : stripslashes(trim($value));
			$clean_values = $data;
		break;
		
		// clean data
		default:
			$clean_values = stripslashes(trim($data));
		break;
		
	}
	
	return $clean_values;
	
}

/*
 * String replaces a string using array_key with array_val
 *
 * @author Nesbert Hidalgo
 * @param string $str
 * @param array $array associative array
 * @return string
 */
function str_replace_array($str, $array)
{
	return str_replace(array_keys($array), array_values($array), $str);
}

?>