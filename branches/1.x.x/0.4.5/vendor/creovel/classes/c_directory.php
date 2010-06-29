<?php
/**
 * Base Directory class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class CDirectory extends CObject
{
    /**
     * Get an array of directory listings with $options for flexibility.
     * Options for $options array are as follows:
     *
     * 'recursive': default false
     * 'show_dirs': default false, add directory to array
     * 'show_invisibles': default false, add .* files to array
     * 'group': default false, Group files by directory
     * 'filter': default '', use a regex to filter array, example '/.php$/'
     *
     * @param string $path
     * @param array Assoctive array of options
     * @return array
     * @author Nesbert Hidalgo
     * @link http://snippets.dzone.com/posts/show/155
     **/
    public static function ls($path, $options = array())
    {
        // set default options
        if (!is_array($options)) $options = array();
        if (!isset($options['recursive'])) $options['recursive'] = false;
        if (!isset($options['show_dirs'])) $options['show_dirs'] = false;
        if (!isset($options['show_invisibles'])) $options['show_invisibles'] = false;
        if (!isset($options['group'])) $options['group'] = false;
        if (!isset($options['filter'])) $options['filter'] = '';

        $array_items = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {

                if (!$options['show_invisibles'] && $file{0} == '.') continue;

                if ($file != '.' && $file != '..') {

                    $filepath = $path . DS . $file;

                    // be nice to non *nix machines
                    $dir_regex = DS == '/' ? '/\/\//si' : '/\\\\/si';

                    if (is_dir($filepath)) {

                        if ($options['show_dirs']) {
                            if ($options['group']) {
                                $array_items[dirname($filepath)][] = preg_replace($dir_regex, DS, $filepath);
                            } else {
                                $array_items[] = preg_replace($dir_regex, DS, $filepath);
                            }
                        }
                        if ($options['recursive']) {
                            $array_items = array_merge($array_items, self::ls($filepath, $options));
                        }

                    } else {

                        if ($options['filter'] && !preg_match($options['filter'], $file)) continue;

                        if ($options['group']) {
                            $array_items[dirname($filepath)][] = preg_replace($dir_regex, DS, $filepath);
                        } else {
                            $array_items[] = preg_replace($dir_regex, DS, $filepath);
                        }

                    }

                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    /**
     * Gets a directories files in a directory by file type. Returns an
     * associative array with the file_name as key and file_path as value.
     *
     * @param string $dir_path
     * @param string $file_type Optional default set to 'php'
     * @return array
     * @author Nesbert Hidalgo
     **/
    public static function ls_with_file_name($path, $file_type = 'php', $show_invisibles = false)
    {
        $files = self::ls($path, array(
            'filter' => '/.'.$file_type.'$/',
            'show_invisibles' => $show_invisibles
            ));
        foreach ($files as $k => $file) {
            if ( CString::ends_with('.'.$file_type, $file) ) {
                $files[basename($file, '.'.$file_type)] = $path.DS.$file;
            }
            unset($files[$k]);
        }
        asort($files);
        return $files;
    }
} // END class CDirectory extends CObject