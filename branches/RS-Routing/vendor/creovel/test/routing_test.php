<?

class routing_test extends unittest
{
	public function setup()
	{
		
		mkdir(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp');
		mkdir(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin');
		touch(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin_controller.php');
		touch(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'index_controller.php');
	}

	public function teardown()
	{
		unlink(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'index_controller.php');
		unlink(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin_controller.php');
		rmdir(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'admin');
		rmdir(CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp');
	}

	public function test_add_route()
	{
		$this->routing = new routing();
		$this->routing->add_route(new route(array( 'prototype' => ':controller/:action/:id' )));

		$this->assert_equal(1, count($this->routing->routes));
	}

	public function test_routing_match()
	{
		$this->routing = new routing();
		$this->routing->add_route(new route(array( 'prototype' => 'projects/:project_id/:controller/:action/:id' )));
		$this->routing->add_route(new route(array( 'prototype' => 'date/:year/:month/:day', 'defaults' => array( 'controller' => 'blog', 'action' => 'by_date', 'month' => null, 'day' => null ), 'constraints' => array( 'year' => '/\d{4}/', 'month' => '/\d{1,2}/', 'day' => '/\d{1,2}/' ) )));
		$this->routing->add_route(new route(array( 'prototype' => ':controller/:action/:id' )));

		$this->assert_equal(3, count($this->routing->routes));

		$route = $this->routing->which_route('projects/24/tickets/view/2096');
		$this->assert_equal('projects/:project_id/:controller/:action/:id', $route->prototype);
		$this->assert_equal('24', $route->params['project_id']);
		$this->assert_equal('tickets', $route->params['controller']);
		$this->assert_equal('view', $route->params['action']);
		$this->assert_equal('2096', $route->params['id']);

		$route = $this->routing->which_route('date/2007/03/01');
		$this->assert_equal('date/:year/:month/:day', $route->prototype);
		$this->assert_equal('blog', $route->params['controller']);
		$this->assert_equal('by_date', $route->params['action']);
		$this->assert_equal('2007', $route->params['year']);
		$this->assert_equal('03', $route->params['month']);
		$this->assert_equal('01', $route->params['day']);

		$route = $this->routing->which_route('date/2007/3/1');
		$this->assert_equal('date/:year/:month/:day', $route->prototype);
		$this->assert_equal('blog', $route->params['controller']);
		$this->assert_equal('by_date', $route->params['action']);
		$this->assert_equal('2007', $route->params['year']);
		$this->assert_equal('3', $route->params['month']);
		$this->assert_equal('1', $route->params['day']);

		$route = $this->routing->which_route('date/2007/');
		$this->assert_equal('date/:year/:month/:day', $route->prototype);
		$this->assert_equal('blog', $route->params['controller']);
		$this->assert_equal('by_date', $route->params['action']);
		$this->assert_equal('2007', $route->params['year']);
		$this->assert_equal(null, $route->params['month']);
		$this->assert_equal(null, $route->params['day']);

		$route = $this->routing->which_route('users/view/2096');
		$this->assert_equal(':controller/:action/:id', $route->prototype);
		$this->assert_equal('users', $route->params['controller']);
		$this->assert_equal('view', $route->params['action']);
		$this->assert_equal('2096', $route->params['id']);

		$route = $this->routing->which_route('index/index');
		$this->assert_equal(':controller/:action/:id', $route->prototype);
		$this->assert_equal('index', $route->params['controller']);
		$this->assert_equal('index', $route->params['action']);
		$this->assert_equal(null, $route->params['id']);

		$route = $this->routing->which_route('/');
		$this->assert_equal(':controller/:action/:id', $route->prototype);
		$this->assert_equal('index', $route->params['controller']);
		$this->assert_equal('index', $route->params['action']);
		$this->assert_equal(null, $route->params['id']);

		$route = $this->routing->which_route('admin/index/manage', CREOVEL_PATH.'test'.DIRECTORY_SEPARATOR.'temp');
		$this->assert_equal(':controller/:action/:id', $route->prototype);
		$this->assert_equal('admin/index', $route->params['controller']);
		$this->assert_equal('manage', $route->params['action']);
		$this->assert_equal(null, $route->params['id']);
	}

	public function test_breakdown_prototype()
	{
		$route = new route(array( 'prototype' => ':controller/:action/:id' ));

		$this->assert_equal(3, count($route->segments));
		$this->assert_equal('controller', $route->segments[0]->name);
		$this->assert_equal('action', $route->segments[1]->name);
		$this->assert_equal('id', $route->segments[2]->name);
	}

	public function test_route_match()
	{
		$route = new route(array( 'prototype' => ':controller/:action/:id' ));
		$this->assert_true($route->match('users/view/2096'));

		$route = new route(array( 'prototype' => 'projects/:project_id/:controller/:action/:id' ));
		$this->assert_true($route->match('projects/24/tickets/view/2096'));

		$route = new route(array( 'prototype' => 'date/:year/:month/:day', 'constraints' => array( 'year' => '/\d{4}/', 'month' => '/\d{1,2}/', 'day' => '/\d{1,2}/' ) ));
		$this->assert_true($route->match('date/2007/03/01'));
	}

	public function test_segment_with_value_match()
	{
		$segment = new segment(array( 'name' => 'controller', 'value' => 'users', 'constraint' => '/\w*/' ));

		$this->assert_true($segment->match('users'));
		$this->assert_false($segment->match('nomas'));
	}

	public function test_segment_with_no_value_match()
	{
		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\w*/' ));
		$this->assert_true($segment->match('users'));

		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\w*/' ));
		$this->assert_true($segment->match('nomas'));

		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\d/' ));
		$this->assert_true($segment->match('2096'));

		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\d{4}/' ));
		$this->assert_true($segment->match('2007'));

		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\d{1,2}/' ));
		$this->assert_true($segment->match('1'));

		$segment = new segment(array( 'name' => 'controller', 'constraint' => '/\d{1,2}/' ));
		$this->assert_true($segment->match('26'));
	}
}

?>
