#!/usr/bin/php -q
<?

require dirname(__FILE__).'/textile.php';

class dokium
{
	static $template;
	static $output;
	static $files;
	static $classes;
	static $methods;

	public function document($app, $files)
	{
		echo "Starting Dokium ...\n";

		@exec('rm -rf '.dokium::$output);
		@exec('mkdir -p '.dokium::$output);
		@exec('mkdir -p '.dokium::$output.'/creovel');
		@exec('mkdir -p '.dokium::$output.'/application');

		$path = ($app == 'creovel') ? dokium::$output.'/creovel/' : dokium::$output.'/application/';

		@exec("cp ".dirname(__FILE__).'/templates/'.dokium::$template."/styles.css {$path}");
		@exec("cp ".dirname(__FILE__).'/templates/'.dokium::$template."/index.html {$path}");

		foreach ($files as $k => $dir)
		{
			@exec("mkdir -p {$path}{$k}");

			foreach (dokium::get_files($dir) as $file)
			{
				require $file['file'];

				// Creovel Classes
				if ($k == 'classes') {
					dokium::document_class("{$file['file']}", "Creovel::".ucwords($k), new ReflectionClass($file['name']));
					dokium::$classes[] = array( 'path' => $file['name'].'.html', 'name' => $file['name'], 'level' => 'Classes' );
				}

				// Creovel Helpers
				if ($k == 'helpers') {
					dokium::document_helper("{$file['file']}", $file['name'], "Helpers::".ucwords($k));
					dokium::$classes[] = array( 'path' => $file['name'].'.html', 'name' => $file['name'], 'level' => 'Helpers' );
				}

				// Creovel Services
				if ($k == 'services') {
				}
			}
		}

		// README File
		$readme = preg_replace("/{{readme}}/i", file_get_contents(dirname(__FILE__).'/../creovel/README'), file_get_contents(dirname(__FILE__).'/templates/default/readme.html'));
		file_put_contents($path.'README.html', $readme);

		// Class Index
		foreach (dokium::$classes as $class) $classes .= "<a href=\"".strtolower($class['level'])."/{$class['path']}\">{$class['level']}::{$class['name']}</a><br />";
		$class_list = preg_replace("/{{classes}}/i", $classes, file_get_contents(dirname(__FILE__).'/templates/default/class_index.html'));
		file_put_contents($path.'class_index.html', $class_list);

		// Method Index
		foreach (dokium::$methods as $method) $methods .= "<a href=\"{$method['path']}\">{$method['name']}</a><br />";
		$method_list = preg_replace("/{{methods}}/i", $methods, file_get_contents(dirname(__FILE__).'/templates/default/method_index.html'));
		file_put_contents($path.'method_index.html', $method_list);

		// File Index
		foreach (dokium::$files as $file) $codefiles .= "<a href=\"{$file['file']}\">{$file['name']}</a><br />";
		$file_list = preg_replace("/{{files}}/i", $codefiles, file_get_contents(dirname(__FILE__).'/templates/default/file_index.html'));
		file_put_contents($path.'file_index.html', $file_list);

		echo "Documentation Complete\n";
	}

	public function get_files($path)
	{
		$d = dir($path);
		while (false !== ($entry = $d->read())) { 
			if ($entry != '..' && $entry != '.' && $entry != '.svn') {
				$files[] = array
				(
					'file' => "{$path}/{$entry}",
					'name' => str_replace('.php', '', $entry)
				);
			}
		}

		return $files;
	}

	public function document_class($file, $type, $reflection)
	{
		if (!is_object($reflection)) return;

		$template_vars['type']				= $type;
		$template_vars['path']				= 'vendor/creovel/classes/'.$reflection->getName().'.php';
		$template_vars['name']				= $reflection->getName();
		$template_vars['parent']			= $reflection->getParentClass();
		$template_vars['documentation']		= dokium::process_documentation($reflection->getDocComment());

		foreach ($reflection->getMethods() as $method)
		{
			if (!$method->isPrivate()) {

				dokium::$methods[] = array( 'path' => 'classes/'.$reflection->getName().'.html#'.$method->getName(), 'name' => $method->getName().' ('.$reflection->getName().')' );

				$template_vars['methods'][$method->getName()]['name']			= $method->getName();
				$template_vars['methods'][$method->getName()]['documentation']	= dokium::process_documentation($method->getDocComment());
				$template_vars['methods'][$method->getName()]['start_line']		= $method->getStartLine();
				$template_vars['methods'][$method->getName()]['end_line']		= $method->getEndLine();
				$template_vars['methods'][$method->getName()]['code']			= dokium::code_block($file, $method->getStartLine(), $method->getEndLine());

				foreach ($method->getParameters() as $i => $param)
				{
					$template_vars['methods'][$method->getName()]['parameters'] .= "$".$param->getName();
					if ($param->isOptional() && $param->isDefaultValueAvailable()) {
						if ($param->getDefaultValue() == '') {
							$template_vars['methods'][$method->getName()]['parameters'] .= " = ''";
						} else {
							$template_vars['methods'][$method->getName()]['parameters'] .= " = ".$param->getDefaultValue();
						}
					}
					$template_vars['methods'][$method->getName()]['parameters'] .= ", ";
				}

				$template_vars['methods'][$method->getName()]['parameters'] = substr($template_vars['methods'][$method->getName()]['parameters'], 0, -2);
			}
		}

		dokium::$files[] = array( 'file' => 'classes/'.$reflection->getName().'.html', 'name' => 'vendor/creovel/classes/'.$reflection->getName().'.php' );
		dokium::write_file(dokium::$output."/creovel/classes/{$template_vars['name']}.html", $template_vars);
	}

	public function document_helper($file, $name, $type)
	{
		$template_vars['type']				= $type;
		$template_vars['path']				= 'vendor/creovel/helpers/'.$name.'.php';
		$template_vars['name']				= $name;
		$template_vars['documentation']		= '';

		preg_match_all('/function (.*)\(.*\)/', file_get_contents($file), $matches);

		foreach ($matches[1] as $function)
		{
			if (!function_exists($function)) continue;

			$method = new ReflectionFunction($function);

			$template_vars['methods'][$method->getName()]['name']			= $method->getName();
			$template_vars['methods'][$method->getName()]['documentation']	= dokium::process_documentation($method->getDocComment());
			$template_vars['methods'][$method->getName()]['start_line']		= $method->getStartLine();
			$template_vars['methods'][$method->getName()]['end_line']		= $method->getEndLine();
			$template_vars['methods'][$method->getName()]['code']			= dokium::code_block($file, $method->getStartLine(), $method->getEndLine());

			foreach ($method->getParameters() as $i => $param)
			{
				$template_vars['methods'][$method->getName()]['parameters'] .= "$".$param->getName();
				if ($param->isOptional() && $param->isDefaultValueAvailable()) {
					if ($param->getDefaultValue() == '') {
						$template_vars['methods'][$method->getName()]['parameters'] .= " = ''";
					} else {
						$template_vars['methods'][$method->getName()]['parameters'] .= " = ".$param->getDefaultValue();
					}
				}
				$template_vars['methods'][$method->getName()]['parameters'] .= ", ";
			}

			$template_vars['methods'][$method->getName()]['parameters'] = substr($template_vars['methods'][$method->getName()]['parameters'], 0, -2);
		}

		dokium::$files[] = array( 'file' => 'helpers/'.$name.'.html', 'name' => 'vendor/creovel/helpers/'.$name.'.php' );
		dokium::write_file(dokium::$output."/creovel/helpers/{$name}.html", $template_vars);
	}
	
	public function write_file($file, $vars)
	{
		if (is_array($vars['methods'])) foreach ($vars['methods'] as $method)
		{
			$patterns = array( '/{{name}}/i', '/{{documentation}}/i', '/{{start_line}}/i', '/{{end_line}}/i', '/{{parameters}}/i', '/{{code}}/i' );
			$replace  = array( $method['name'], $method['documentation'], $method['start_line'], $method['end_line'], $method['parameters'], $method['code'] );

			$all_methods .= preg_replace($patterns, $replace, file_get_contents(dirname(__FILE__).'/templates/default/method.html'));
		}

		$patterns = array( '/{{type}}/i', '/{{path}}/i', '/{{name}}/i', '/{{documentation}}/i', '/{{methods}}/i' );
		$replace  = array( $vars['type'], $vars['path'], $vars['name'], $vars['documentation'], $all_methods );

		$content = preg_replace($patterns, $replace, file_get_contents(dirname(__FILE__).'/templates/default/class.html'));

		file_put_contents($file, $content);
	}

	public function process_documentation($doc_block)
	{
		$textile = new Textile();

		foreach (split("\n", $doc_block) as $line)
		{
			$temp = str_replace(array( '/**', ' *', '*/' ), array( '', '', '' ), $line);
			$temp = preg_replace('/@.*/i', '', $temp);

			if (trim($temp) != '/') $block .= $temp;
		}

		return $textile->textilethis($block);
	}

	public function code_block($file, $start_line, $end_line)
	{
		$file = explode("\n" , file_get_contents($file));
		for ($i = ($start_line - 1); $i < $end_line; $i++) $block .= "{$i}: {$file[$i]}\n";

		return $block;
	}
}

?>
