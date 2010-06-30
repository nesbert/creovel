<?php
/**
 * Base CFile class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class CFile extends CObject
{
    /**
     * Returns a human readable size or a file or a size
     *
     * @param string $file_or_size File path or size.
     * @link http://us2.php.net/manual/hk/function.filesize.php#64387
     * @return string
     **/
    public static function size($file_or_size)
    {
        $iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        $size = is_numeric($file_or_size) ? $file_or_size : @filesize($file_or_size);
        $i = 0;
        while ( ($size/1024) > 1 ) {
            $size = $size / 1024;
            $i++;
        }
        return substr($size, 0, strpos($size,'.') + 4).' '.$iec[$i];
    }

    /**
     * Get the mime type of a file.
     *
     * @param string $filepath
     * @link http://us.php.net/manual/en/function.finfo-open.php#78927
     * @return string
     **/
    public static function mime_type($filepath)
    {
        ob_start();
        system("file -i -b {$filepath}");
        $output = ob_get_clean();
        $output = explode("; ",$output);
        if ( is_array($output) ) {
            $output = $output[0];
        }
        return str_replace("\n", '', $output);
    }
} // END class CFile extends CObject