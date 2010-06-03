<?php
/**
 * The *View* class handles all the presentation logic. The Simple Template
 * System (STS) allows an easy way of separating/combining business logic and
 * presentation layers.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 */
class ActionView extends CreovelObject
{
    /**
     * Creates the page to be displayed and sets it to the page property.
     *
     * @param string $view_path Required string of the file path.
     * @param string $layout_path - Required string of the layout path.
     * @param array $options - Optional array of display options.
     * @return string Content/HTML used for output.
     **/
    public function to_str($view_path, $layout_path, $options = null)
    {
        try {
            // set content data
            $content = isset($options['text']) ? $options['text'] : '';
            $options['render'] = isset($options['render'])
                                    ? $options['render']
                                    : '';
            $options['layout'] = isset($options['layout'])
                                    ?
                                    $options['layout']
                                    : '';
            
            // grab and set view content
            if ($options['render'] !== false) {
                
                if (is_file($view_path)) {
                    $content .= self::include_contents($view_path, $options);
                } else {
                    throw new Exception('Unable to render <em>view</em> '.
                        "not found in <strong>{$view_path}</strong>.");
                }
                
            }
            
            // combine content and template. else use content only
            if ($options['layout'] !== false) {
                
                if (is_file($layout_path)) {
                    $layout = self::include_contents($layout_path, $options);
                    // allow inline head decalarations with <!--HEADSPLIT-->
                    // in views
                    $parts = explode('<!--HEADSPLIT-->', $content);
                    if (count($parts) == 2) {
                        $layout = str_replace_array(
                                $layout,
                                array(
                                    '</head>' => $parts[0] . '</head>'
                                    )
                            );
                        $content = $parts[1];
                    }
                    $page = str_replace(
                                $GLOBALS['CREOVEL']['PAGE_CONTENTS'],
                                $content,
                                $layout
                                );
                } else {
                    throw new Exception('Unable to render <em>layout</em> '.
                        "not found in <strong>{$layout_path}</strong>.");
                }
                
            } else {
                $page = $content;
            }
            
            return $page;
            
        } catch ( Exception $e ) {
            CREO('application_error_code', 404);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Using output buffering to include a PHP file into a string. Used to
     * combine coding logic (PHP) and views. The main function used by
     * Creovel's template engine (STS).
     *
     * @param string Required string of the file path.
     * @param array $options - Optional array of display options.
     * @link http://us3.php.net/manual/en/function.include.php Example #6
     * @return string HTML/Text from buffer.
     **/
    public function include_contents($filename, $options = null)
    {
        if (is_file($filename)) {
            ob_start();
            // create a variable foreach option, using keyas the vairable name
            if (count($options)) foreach ($options as $key => $values) {
                $$key = $values;
            }
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
    
    /**
     * Print a page view to screen. A wrapper to ActionView::to_str().
     *
     * @param string $view_path Required string of the file path.
     * @param string $layout_path - Required string of the layout path.
     * @param array $options - Optional array of display options.
     * @return string Content/HTML printed out to screen.
     **/
    public function show($view_path, $layout_path, $options = null)
    {
        print self::to_str($view_path, $layout_path, $options);
    }
} // END class ActionView extends CreovelObject