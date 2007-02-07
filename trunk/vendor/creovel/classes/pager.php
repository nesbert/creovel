<?

/*

Class: pager
	Can be used to page a model or an array

*/

class pager {

	public $total_records;		// total number of records to page
	public $total_pages;		// total number of pages
	
	public $current;			// current page number
	public $next;				// next page number
	public $prev;				// previous page number
	public $first;				// first page number
	public $last;				// last page number

	public $offset;				// pointer offest
	public $limit;				// limit of records per page
	
	public $url;				// host url

	// Section: Public

	/*
	
	Function: __construct

	Paramerters:
		data - associative array of data

	*/

	public function __construct($data = null)
	{
		$this->set_properties($data);
	}
	
	/*

	Function:			
		Set class properties
	
	Parameters:
		data - required
		page  - optional
		limit - optional
	
	*/

	public function set_properties($data, $page = null, $limit = null) {

		// set vars by type
		$page = ( $page ? $page : ( $_GET['page'] ? $_GET['page'] : 1 ) );
		$limit = ( $limit ? $limit : ( $_GET['limit'] ? $_GET['limit'] : 10 ) );

		switch ( true ) {
		
			case ( is_object($data) ):
				$total_records = $data->total_records;
				$page = ( isset($data->page) ? $data->page : $page );
				$limit = ( isset($data->limit) ? $data->limit : $limit );
			break;
			
			case ( is_array($data) ):
				$total_records = count($data);
			break;
			
			case ( is_numeric($data) ):
				$total_records = $data;
			break;
			
		}			
	
		// set total_records, limit, total_pages
		$this->total_records	= (int) $total_records;
		$this->limit 			= max((int) $limit, 1);
		$this->total_pages		= ceil($this->total_records / $this->limit);
		
		// set current page
		$this->current 		= (int) $page;
		$this->current 		= max($this->current, 1);  
		$this->current 		= min($this->current, $this->total_pages);
		
		// set offest
		$this->offset 		= max(($this->current - 1) * $this->limit, 0);
		
		// set next & previous pages
		$this->next 		= min($this->current + 1, $this->total_pages);
		$this->prev			= max($this->current - 1, 1);
		$this->first		= 1;
		$this->last			= $this->total_pages;
		
		// set url path
		$url = @explode('?', $_SERVER['REQUEST_URI']);
		$this->url = $url[0];

	}
	
	/*
	
	Function: page_array
		Page an array

	Parameters:	
		data - required
		preserve_keys - optional
		limit - optional
	
	Returns:
		mixed

	*/

	public function page_array($data, $preserve_keys = true, $limit = false)
	{
		if ( !$this->total_records ) $this->set_properties($data, null, $limit);
		return array_slice($data, $this->offset, $this->limit, $preserve_keys);
	}	
	
	/*
	
	Function: params_to_str
		Clean/create extra params links

	Parameters:	
		data - required

	Returns:
		return

	*/

	public function params_to_str($data)
	{
		$data = is_array($data) ? array_merge($_GET, $data) : $_GET;
		$str = '';
		foreach ( $data as $key => $val ) {
			if ( $key == 'page' || $key == 'limit') continue;
			$str .="&".$key."=".urlencode($val);
		}
		return $str;
	}
	
	/*
			
	Function: link_to
		Create link to page

	Parameters:	
		label - required
		page - required
		extra_params - optional
		html_options - optional

	Returns:
		string

	*/

	private function link_to($label, $page, $extra_params = null, $html_options = null)
	{
		$extra_params = ( isset($_GET['limit']) ? "&limit={$this->limit}" : '' ).$this->params_to_str($extra_params);
		$html_options = ( is_array($html_options) ? array_merge(array('href' => $this->url.'?page='.$page.$extra_params), $html_options) : array('href' => $this->url.'?page='.$page.$extra_params) );
		return link_to($label, null, null, null, $html_options);
	}
	
	/*

	Function: link_to_next
		Create link to the mext page

	Parameters:	
		label - optional
		extra_params - optional
		html_options - optional
	
	Returns:
		string

	*/

	public function link_to_next($label = 'Next', $extra_params = null, $html_options = null)
	{
		return ( $this->current < $this->last ? $this->link_to($label, $this->next, $extra_params, $html_options) : '' );
	}

	/*
			
	Function: link_to_prev
		Create link to the previous page

	Parameters:	
		label - optional
		extra_params - optional
		html_options - optional

	Returns:
		string

	*/

	public function link_to_prev($label = 'Prev', $extra_params = null, $html_options = null)
	{
		return ( $this->current > $this->first ? $this->link_to($label, $this->prev, $extra_params, $html_options) : '' );
	}

	/*

	Function: link_to_first	
		Create link to the first page

	Parameters:	
		label - optional
		extra_params - optional
		html_options - optional

	Returns:
		string

	*/

	public function link_to_first($label = 'First', $extra_params = null, $html_options = null)
	{
		return ( $this->current > $this->first ? $this->link_to($label, $this->first, $extra_params, $html_options) : '' );
	}
	
	/*

	Function: link_to_last
		Create link to the last page

	Parameters:	
		label - optional
		extra_params - optional
		html_options - optional

	Returns:
		string

	*/

	public function link_to_last($label = 'Last', $extra_params = null, $html_options = null)
	{
		return ( $this->current < $this->last ? $this->link_to($label, $this->last, $extra_params, $html_options) : '' );
	}

	/*
	
	Function: paging_links
		Create paging links eg. << Prev 1 ... 13 14 15 16 17 ... 25 Next >>

	Parameters:	
		extra_params - optional
		show_label - optional

	Returns:
		string
	 
	*/

	public function paging_links($extra_params = null, $show_label = false)
	{
	
		$extra_params = ( isset($_GET['limit']) ? "&limit={$this->limit}" : '' ).$this->params_to_str($extra_params);
		$start_page = max($this->current - 2, 1);
		
		if ( $this->total_pages > 1 ) {
		
			$str = '<div class="page-links">';
			
			if ( $show_label ) $str .= $this->paging_label();
		
			if ( $this->current > 1 ) {
				$str .= '<a class="prev" href="'.$this->url.'?page='.$this->prev.$extra_params.'">&laquo; Prev</a>';
			}
	
			if ( ($this->current - 3) >= 1 ) {
				$str .= '<a class="page-1" href="'.$this->url.'?page=1'.$extra_params.'">1</a>';
				if ( ($this->current - 3) > 1 ) $str .= '<span="dots">...</span>';
			}
		
			for ( $i = $start_page; $i <= $this->current + 2; $i++ ) {
			
				if ( $i > $this->total_pages ) break;
			
				if ( $this->current <> $i ) {
					$str .= '<a class="page-'.$i.'" href="'.$this->url.'?page='.$i.$extra_params.'">'.$i.'</a>';
				} else {
					$str .= '<a class="page-'.$i.' current">'.$i.'</a>';
				}
				
			}
			
			if ( ($this->current + 3) <= $this->total_pages ) {			
				if ( ($this->current + 3) < $this->total_pages ) $str .= '<span="dots">...</span>';
				$str .= '<a class="page-'.$this->total_pages.'" href="'.$this->url.'?page='.$this->total_pages.$extra_params.'">'.$this->total_pages.'</a>';
			}
			
			if ( $this->current < $this->total_pages ) {
				$str .= '<a class="next" href="'.$this->url.'?page='.$this->next.$extra_params.'">Next &raquo;</a>';
			}
			
			$str .= '</div>';
			
		} else {
			
			$str = '';
			
		}
		
		return $str;
	
	}
	
	/*
	
	Function: paging_link
		Create page limiting selectbox

	Parameters:	
		extra_params - optional
		default_limit - optional

	Returns:
		string

	*/

	public function paging_limit($extra_params = null, $default_limit = 10)
	{	
		$extra_params = $this->params_to_str($extra_params);
		$default_limit = (int) ( $default_limit ? $default_limit : $this->limit );
		
		$str = '<select OnChange="location.href=this.options[this.selectedIndex].value">'."\n";
		
		// if default_limit not a default value(20,50,100) create option for limit
		switch ( $default_limit ) {
		
			case 20:
			case 50:
			case 100:
				break;
				
			default:
				$str .= '<option value="'.$this->url.'?page='.$this->current.'&limit='.$default_limit.$extra_params.'"'.( $this->limit == $default_limit ? " selected" : "" ).'>'.$default_limit.'</option>'."\n";
			break;
			
		}
		
		$str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=20'.$extra_params.'"'.( $this->limit == 20 ? ' selected="selected"' : '' ).'>20</option>'."\n";
		$str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=50'.$extra_params.'"'.( $this->limit == 50 ? ' selected="selected"' : '' ).'>50</option>'."\n";
		$str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=100'.$extra_params.'"'.( $this->limit == 100 ? ' selected="selected"' : '' ).'>100</option>'."\n";
		$str .= "</select>\n";
		
		return $str;		
	}
	
	/*
	
	Function: paging_label	
		Create paging label: Page 1 of 10

	Returns:	
		string

	*/

	public function paging_label()
	{
		return '<span class="page-label">Page '.$this->current.' f '.$this->total_pages.'</span>';
	}
}

?>
