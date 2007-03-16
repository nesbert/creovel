<?php

/*

Class: routing
	Routing class

*/

class routing
{
	public $routes;

	public function __construct()
	{
		$this->routes = array();
	}

	/*

	Function: add_route
		Append a route to the routes array

	Parameters:
		route - route object	
	
	*/

	public function add_route(route $route)
	{
		array_push($this->routes, $route);
	}

	/*

	Function: which_route
		Finds the route that fits best.

	Parameters:
		uri	- server uri

	Returns:
		route object

	*/

	public function which_route($uri, $controllers_path = null)
	{
		if ($uri == '/' || $uri == '') {

			$match = $this->routes[(count($this->routes) - 1)];

		} else {

			foreach ($this->routes as $route) 
			{
				if ($route->match($uri, $controllers_path))
				{
					$match = $route;
					break;
				}
			}

		}

		$match->params['controller'] = ($match->params['controller'] == '') ? 'index' : $match->params['controller'];
		$match->params['action'] = ($match->params['action'] == '') ? 'index' : $match->params['action'];

		return $match;
	}
}

class route
{
	public $prototype;
	public $constraints;
	public $defaults;
	public $params;
	public $segments;

	public function __construct($params = array())
	{
		$this->prototype = $params['prototype'];
		$this->constraints = $params['constraints'];
		$this->defaults = $params['defaults'];
		$this->params = array();
		$this->segments = array();

		$this->set_defaults();
		$this->breakdown();
	}

	public function set_defaults()
	{
		if (is_array($this->defaults)) foreach ($this->defaults as $k => $v) $this->params[$k] = $v;
	}

	public function match($uri, $controllers_path = null)
	{
		if ($uri == '') return false;
		if ($controllers_path == null) CONTROLLERS_PATH;

		$this->params = array();
		$this->set_defaults();

		$uri = preg_replace('/^\//', '', $uri);
		$uri = preg_replace('/\/$/', '', $uri);

		$pieces = explode('/', $uri);

		for ($i = 0; $i < count($pieces); $i++)
		{
			if (!isset($this->defaults[$pieces[$i]]))
			{
				if (!isset($this->segments[$i])) return true;

				$result = $this->segments[$i]->match($pieces[$i]);

				if ($result != false) {
					if ($this->segments[$i]->name != $this->segments[$i]->value) $this->params[$this->segments[$i]->name] = $result;
				} else {
					return false;
				}
			}
		}

		$path = '';
		foreach ($this->params as $arg)
		{
			if (file_exists($controllers_path.DIRECTORY_SEPARATOR."{$path}{$arg}_controller.php"))
			{
				$this->params['controller'] = "{$path}{$arg}";
				$rest = explode('/', str_replace("{$path}{$arg}/", '', $uri));
				$this->params['action'] = $rest[0];
				$this->params['id'] = $rest[1];
			}
			$path .= $arg.DS;
		}

		return true;
	}

	public function breakdown()
	{
		if (!isset($this->prototype) || $this->prototype == '') return false;

		foreach (explode('/', $this->prototype) as $piece)
		{
			$segment = new segment();
			$segment->name = str_replace(':', '', $piece);
			$segment->value = (!strstr($piece, ':')) ? $piece : null;
			$segment->constraint = $this->constraints[str_replace(':', '', $piece)];

			array_push($this->segments, $segment);
		}
	}
}

class segment
{
	public $name;
	public $value;
	public $constraint;

	public function __construct($params = array())
	{
		$this->name = $params['name'];
		$this->value = $params['value'];
		$this->constraint = $params['constraint'];
	}

	public function match($value)
	{
		$this->constraint = (!is_null($this->constraint)) ? $this->constraint : '/\w*/';

		if (!is_null($this->value)) return ($this->value == $value);
		if ((bool)preg_match($this->constraint, $value)) return $value;

		return false;
	}
}

?>
