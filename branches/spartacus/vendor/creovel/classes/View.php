<?php
/**
 * The *View* class handles all the presentation logic. The Simple Template
 * System (STS) allows an easy way of separating/combining business logic and
 * presentation layers.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 */
class View
{
	/**
	 * Creates the page to be displayed and sets it to the page property.
	 *
	 * @param string $view_path Required string of the file path.
	 * @param string $layout_path - Required string of the layout path.
	 * @param array $options - Optional array of display options.
	 * @return string Content/HTML used for output.
	 **/
	public function create($view_path, $layout_path, $options = null)
	{
		try {
			// set content data
			$content = isset($options['text']) ? $options['text'] : '';
			$options['render'] = isset($options['render']) ? $options['render'] : '';
			$options['layout'] = isset($options['layout']) ? $options['layout'] : '';
			
			// grab and set view content
			if ($options['render'] !== false) {
				
				if (is_file($view_path)) {
					$content .= self::includeContents($view_path, $options);
				} else {
					throw new Exception('Unable to render <em>view<em> '.
						"not found in <strong>{$view_path}</strong>.");
				}
			
			}
			
			// combine content and template. else use content only
			if ($options['layout'] !== false) {
				
				if (is_file($layout_path)) {
					$page = str_replace(
							$GLOBALS['CREOVEL']['PAGE_CONTENTS'],
							$content,
							self::includeContents($layout_path, $options));
				} else {
					throw new Exception('Unable to render <em>layout<em> '.
						"not found in <strong>{$layout_path}</strong>.");
				}
				
			} else {
				$page = $content;
			}
			
			return $page;
			
		} catch ( Exception $e ) {
			CREO('error_code', 404);
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
	public function includeContents($filename, $options = null)
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
	 * Return a page view as string. A wrapper to View::create().
	 *
	 * @param string $view_path Required string of the file path.
	 * @param string $layout_path - Required string of the layout path.
	 * @param array $options - Optional array of display options.
	 * @return string Content/HTML used for output.
	 **/
	public function toString($view_path, $layout_path, $options = null)
	{
		return self::create($view_path, $layout_path, $options);
	}
	
	/**
	 * Print a page view to screen. A wrapper to View::create().
	 *
	 * @param string $view_path Required string of the file path.
	 * @param string $layout_path - Required string of the layout path.
	 * @param array $options - Optional array of display options.
	 * @return string Content/HTML printed out to screen.
	 **/
	public function show($view_path, $layout_path, $options = null)
	{
		print self::create($view_path, $layout_path, $options);
	}
} // END class View