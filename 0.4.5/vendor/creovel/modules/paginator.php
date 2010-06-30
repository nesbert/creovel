<?php
/**
 * Paging class for model result set and arrays.
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
class Paginator extends ModuleBase
{
    /**
     * Total number of records to paged.
     *
     * @var integer
     **/
    public $total_records;
    
    /**
     * Total number of pages.
     *
     * @var integer
     **/
    public $total_pages;
    
    /**
     * Current page number.
     *
     * @var integer
     **/
    public $current;
    
    /**
     * Next page number.
     *
     * @var integer
     **/
    public $next;
    
    /**
     * Previous page number.
     *
     * @var integer
     **/
    public $prev;
    
    /**
     * First page number.
     *
     * @var integer
     **/
    public $first;
    
    /**
     * Last page number.
     *
     * @var integer
     **/
    public $last;
    
    /**
     * Current page.
     *
     * @var integer
     **/
    public $page;
    
    /**
     * Pointer offset.
     *
     * @var integer
     **/
    public $offset;
    
    /**
     * Limit of records per page.
     *
     * @var integer
     **/
    public $limit;
    
    /**
     * URL string.
     *
     * @var string
     **/
    public $url;
    
    /**
     * Use pretty URLs for passing paging params.
     *
     * @var boolean
     **/
    public $pretty_urls = false;
    
    /**
     * Class construct set class properties if $data passed.
     *
     * @param array $data - Associative array of data.
     * @return void
     **/
    public function __construct($data = null)
    {
        parent::__construct();
        $this->set_properties($data);
    }
    
    /**
     * Set class properties.
     *
     * @param mixed $data Required Model Object or Array.
     * @param integer $page Optional page number.
     * @param integer $limit Optional records per page.
     * @return void
     **/
    public function set_properties($data, $page = null, $limit = null)
    {
        // set vars by type
        $page = $page ? $page : @$_GET['page'] ? $_GET['page'] : 1;
        $limit = $limit ? $limit : @$_GET['limit'] ? $_GET['limit'] : 10;
        
        switch (true) {
            case is_object($data):
                $total_records = $data->total_records;
                $page = isset($data->page) ? $data->page : $page;
                $limit = isset($data->limit) ? $data->limit : $limit;
                break;
            
            case is_array($data):
                $total_records = count($data);
                break;
            
            case is_numeric($data):
                $total_records = $data;
                break;
        }
        
        // set total_records, limit, total_pages
        $this->total_records = (int) $total_records;
        $this->limit = max((int) $limit, 1);
        $this->total_pages = (int) ceil($this->total_records / $this->limit);
        
        // set current page
        $this->current = (int) $page;
        $this->current = max($this->current, 1);
        $this->current = min($this->current, $this->total_pages);
        
        // set offest
        $this->offset = max(($this->current - 1) * $this->limit, 0);
        
        if ($this->current == 1) {
            $this->current_min = 1;
        } else {
            $this->current_min = $this->current * $this->limit + 1;
        }
        
        $this->current_max = $this->current_min + $this->limit - 1;
        
        if ($this->current_max > $this->total_records) {
            $this->current_max = $this->total_records;
        }
        
        // set next & previous pages
        $this->next = min($this->current + 1, $this->total_pages);
        $this->prev = max($this->current - 1, 1);
        $this->first = 1;
        $this->last = $this->total_pages;
    }
    
    /**
     * Page an array.
     *
     * @param array $data
     * @param boolean $preserve_keys
     * @param integer $limit Optional records per page.
     * @return mixed
     **/
    public function page_array($data, $preserve_keys = true, $limit = null)
    {
        if (!$this->total_records) $this->set_properties($data, null, $limit);
        return array_slice($data, $this->offset, $this->limit, $preserve_keys);
    }
    
    /**
     * Clean/create extra parameters links.
     *
     * @param array $data Required associative array of data.
     * @return string
     **/
    public function params_to_str($data)
    {
        $data = is_array($data) ? array_merge($_GET, $data) : $_GET;
        unset($data['page']);
        unset($data['limit']);
        return $data ? '&' . http_build_query($data) : '';
    }
    
    /**
     * Create link to page.
     *
     * @param string $label Required link label.
     * @param integer $page Required page number.
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param array $html_options Optional associative array of HTML options.
     * @return string
     **/
    private function link_to($label, $page, $extra_params = null, $html_options = null)
    {
        $extra_params = (isset($_GET['limit']) ? "&limit={$this->limit}" : '' ) . $this->params_to_str($extra_params);
        $html_options = (is_array($html_options) ? array_merge(array('href' => $this->url.'?page='.$page.$extra_params), $html_options) : array('href' => $this->url.'?page='.$page.$extra_params));
        return CTag::link_to($label, null, null, null, $html_options);
    }
    
    /**
     * Create link to the next page.
     *
     * @param string $label Required link label.
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param array $html_options Optional associative array of HTML options.
     * @return string
     **/
    public function link_to_next($label = 'Next', $extra_params = null, $html_options = null)
    {
        return $this->current < $this->last ? $this->link_to($label, $this->next, $extra_params, $html_options) : '';
    }
    
    /**
     * Create link to the previous page.
     *
     * @param string $label Required link label.
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param array $html_options Optional associative array of HTML options.
     * @return string
     **/
    public function link_to_prev($label = 'Prev', $extra_params = null, $html_options = null)
    {
        return $this->current > $this->first ? $this->link_to($label, $this->prev, $extra_params, $html_options) : '';
    }
    
    /**
     * Create link to the first page.
     *
     * @param string $label Required link label.
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param array $html_options Optional associative array of HTML options.
     * @return string
     **/
    public function link_to_first($label = 'First', $extra_params = null, $html_options = null)
    {
        return $this->current > $this->first ? $this->link_to($label, $this->first, $extra_params, $html_options) : '';
    }
    
    /**
     * Create link to the last page.
     *
     * @param string $label Required link label.
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param array $html_options Optional associative array of HTML options.
     * @return string
     **/
    public function link_to_last($label = 'Last', $extra_params = null, $html_options = null)
    {
        return ( $this->current < $this->last ? $this->link_to($label, $this->last, $extra_params, $html_options) : '' );
    }
    
    /**
     * Create paging links eg. << Prev 1 ... 13 14 15 16 17 ... 25 Next >>
     *
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param boolean $show_label Optional associative array of HTML options.
     * @return string
     **/
    public function paging_links($extra_params = null, $show_label = false)
    {
        $start_page = max($this->current - 2, 1);
        
        if ($this->total_pages > 1) {
            
            $str = '<div class="page-links">';
            
            if ($show_label) $str .= $this->paging_label();
            
            if ($this->current > 1) {
                $str .= '<a class="prev" href="' . $this->paging_link($this->prev, $extra_params) .
                        '">&laquo; Prev</a>';
            }
            
            if ( ($this->current - 3) >= 1 ) {
                $str .= '<a class="page-1" href="' . $this->paging_link(1, $extra_params) . '">1</a>';
                if ( ($this->current - 3) > 1 ) $str .= '<span class="dots">...</span>';
            }
            
            for ($i = $start_page; $i <= $this->current + 2; $i++) {
                
                if ($i > $this->total_pages) break;
                
                if ($this->current <> $i) {
                    $str .= '<a class="page-'.$i.'" href="' . $this->paging_link($i, $extra_params) .
                            '">' . $i . '</a>';
                } else {
                    $str .= '<a class="page-'. $i . ' current">' . $i . '</a>';
                }
                
            }
            
            if ( ($this->current + 3) <= $this->total_pages ) {
                if (($this->current + 3) < $this->total_pages) $str .= '<span class="dots">...</span>';
                $str .= '<a class="page-' . $this->total_pages . '" href="' . 
                            $this->paging_link($this->total_pages, $extra_params) . '">' .
                            $this->total_pages . '</a>';
            }
            
            if ($this->current < $this->total_pages) {
                $str .= '<a class="next" href="' . $this->paging_link($this->next, $extra_params) .
                        '">Next &raquo;</a>';
            }
            
            $str .= '</div>';
            
        } else {
            
            $str = '';
            
        }
        
        return $str;
    }
    
    /**
     * Create page limiting select box.
     *
     * @param array $extra_params Optional associative array of parameters
     * to be past via URL.
     * @param integer $default_limit Optional default limit set to 10.
     * @return string
     **/
    public function paging_limit($extra_params = null, $default_limit = 10)
    {    
        $extra_params = $this->params_to_str($extra_params);
        $default_limit = (int) ( $default_limit ? $default_limit : $this->limit );
        
        $str = '<select OnChange="location.href=this.options[this.selectedIndex].value">'."\n";
        
        // if default_limit not a default value(20,50,100)
        // create option for limit
        switch ($default_limit) {
        
            case $default_limit * 2:
            case $default_limit * 5:
            case $default_limit * 10:
                break;
                
            default:
                $str .= '<option value="'.$this->url.'?page='.$this->current.'&limit='.$default_limit.$extra_params.'"'.( $this->limit == $default_limit ? " selected" : "" ).'>'.$default_limit.'</option>'."\n";
            break;
            
        }
        
        $str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=20'.$extra_params.'"'.($this->limit == ($default_limit * 2) ? ' selected="selected"' : '' ).'>'.($default_limit * 2).'</option>'."\n";
        $str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=50'.$extra_params.'"'.($this->limit == ($default_limit * 5) ? ' selected="selected"' : '' ).'>'.($default_limit * 5).'</option>'."\n";
        $str .= '<option value="'.$this->url.'?page='.$this->current.'&limit=100'.$extra_params.'"'.($this->limit == ($default_limit * 10) ? ' selected="selected"' : '' ).'>'.($default_limit * 10).'</option>'."\n";
        $str .= "</select>\n";
        
        return $str;
    }
    
    /**
     * Create paging label: Page 1 of 10.
     * 
     * <code>
     * <span class="page-label">Page 1 of 10</span>
     * </code>
     *
     * @return string
     **/
    public function paging_label()
    {
        return '<span class="page-label">Page '.$this->current.' of '.$this->total_pages.'</span>';
    }
    
    /**
     * Total records paged.
     * 
     * @return integer
     **/
    public function total_records()
    {
        return (int) $this->total_records;
    }
    
    /**
     * Total number of pages.
     * 
     * @return integer
     **/
    public function total_pages()
    {
        return (int) $this->total_pages;
    }
    
    /**
     * Checks the result set have multiple pages.
     * 
     * @return integer
     **/
    public function needs_links()
    {
        return $this->total_pages() > 1;
    }
    
    /**
     * Create a page link.
     *
     * @return string
     **/
    public function paging_link($page, $extra_params = array())
    {
        $params = array('page' => $page) + (is_array($extra_params) ? $extra_params : array());
        if ($this->pretty_urls) {
            $return = array();
            foreach ($params as $k => $v) {
                $return[] = $k . '/' . urlencode($v);
            }
            return "{$this->url}/".implode('/', $return);
        } else {
            return "{$this->url}?" . http_build_query($params);
        }
    }
} // END class Paginator extends ModuleBase