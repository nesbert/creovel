<?php
/*
	Class: rss
	
	RSS class to handle news feeds (http://www.rssboard.org/rss-specification).
	
	Implements:
	
		Iterator
*/

class rss implements Iterator
{

	// Section: Public
	
	/*
		Property: encoding
		
		Content-Type encoding default set to 'utf-8'.
	*/
	
	public $encoding = "utf-8";
	
	/*
		Property: version
		
		RSS version default set to '2.0'.
	*/
	
	public $version = '2.0';
	
	/*
		Property: title
		
		The name of the channel. It's how people refer to your service. If you 
		have an HTML website that contains the same information as your RSS file, 
		the title of your channel should be the same as the title of your website.
	*/
	
	public $title = "RSS Syndication Title";
	
	/*
		Property: link
		
		The URL to the HTML website corresponding to the channel.
	*/
	
	public $link = BASE_URL;
	
	/*
		Property: description
		
		Description of your syndication.
	*/
	
	public $description = "Description of your syndication.";
	
	/*
		Property: language
		
		The language the channel is written in. This allows aggregators to group all
		Italian language sites, for example, on a single page. A list of allowable
		values for this element, as provided by Netscape, is here. You may also use
		values defined by the W3C(http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes).
	*/
	
	public $language = "en-us";
	
	public $copyright		= ""; // Copyright notice for content in the channel.
	public $managingEditor	= ""; // Email address for person responsible for editorial content.
	public $webMaster		= ""; // Email address for person responsible for technical issues relating to channel.
	public $pubDate			= ""; // The publication date for the content in the channel.
	public $lastBuildDate	= ""; // The last time the content of the channel changed.
	public $category		= ""; // Specify one or more categories that the channel belongs to.
	public $generator		= "Simple RSS a part of the Creovel - A PHP Framework (http://www.creovel.org)."; // A string indicating the program used to generate the channel.
	public $docs			= ""; // A URL that points to the documentation for the format used in the RSS file.
	public $cloud			= ""; // Allows processes to register with a cloud to be notified of updates to the channel, implementing a lightweight publish-subscribe protocol for RSS feeds.
	public $ttl				= ""; // Copyright notice for content in the channel.
	public $image			= ""; // Specifies a GIF, JPEG or PNG image that can be displayed with the channel.
	public $rating			= ""; // The PICS rating for the channel.
	public $textInput		= ""; // Specifies a text input box that can be displayed with the channel.
	public $skipHours		= ""; // A hint for aggregators telling them which hours they can skip.
	public $skipDays		= ""; // A hint for aggregators telling them which days they can skip.
	
	/*
		Property: items_as_array
		
		Return each item as an Array or Object.
	*/
	
	public $items_as_array = false;
	
	/*
		Property: items_as_array
		
		An array of items for this syndication.
	*/
	
	public $items = array();
	
	/*
		Function: initialize
		
		Initialize class with XML class.
	*/

	public function initialize()
	{
		if ( !is_object($this->xml) ) $this->xml = new xml;
		$this->xml->encoding = $this->encoding;
	}
	
	/*
		Function: load
		
		Loads RSS feed into class.
		
		Parameters:
		
			url - RSS URL.
	*/
	
	public function load($url)
	{
		$this->initialize();
		
		// check for cache and user cache
		if ( $this->cache && !$this->cache_expired($url) ) {
			$url = $this->cache_dir.$this->cache_file;
		}
		
		// load feed to xml parser
		$this->xml->load($url);
		
		//print_obj($this->xml, 1);
		
		// set rss version and map xml data to rss
		switch ( $this->version = $this->xml->data->children->attributes->version )
		{
			case 2:
			case 0.91:
				$this->_map_rss20();
			break;
			
			default:
				application_error("RSS {$this->version} version currently not supported!");
			break;
		}
		
		// check to cache feed
		$this->check_cache();
	}
	
	/*
		Function: add_item
		
		Add an item to $items property.
		
		Parameters:
		
			data - Array of data.
	*/
	
	public function add_item($data)
	{
		$this->items[] = $data;
	}
	
	/*
		Function: create_file
		
		Creates XML file string from the items loaded.
		
		Returns:
		
			String
	*/
	
	public function create_file()
	{
		$required_tags = array( 'title', 'language', 'link', 'description' );
		$optiona_tags = array( 'copyright'. 'managingEditor', 'webMaster', 'pubDate', 'lastBuildDate', 'category', 'generator', 'docs', 'cloud', 'ttl', 'image', 'rating', 'textInput', 'skipHours', 'skipDays' );
		$xml = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'."\n";
		$xml .= '<rss version="' . $this->version . '"' . ( isset($this->xmlns) ? $this->xmlns_str() : '' ) . '>'."\n";
			$xml .= '<channel>'."\n";
				foreach ( $required_tags as $tag ) {
					$xml .= $this->tag($tag, $this->$tag);
				}
				foreach ( $optiona_tags as $tag ) {
					if ( !$this->$tag ) continue;
					$xml .= $this->tag($tag, $this->$tag);
				}
				if ( count($this->items) ) foreach ( $this->items as $item ) {
					$xml .= self::item_str($item);
				}
			$xml .= '</channel>'."\n";
		$xml .= '</rss>'."\n";
		
		return $xml;
	}
	
	/*
		Function: item_str
		
		Create item XML string for one item.
		
		Parameters:	
		
			item - Array
			
		Returns:
		
			String
	*/
	
	public function item_str($item)
	{
		// make sure items is an array
		if ( is_object($item) ) $item = (array) $item;
		
		$required = array(
							'title' 		=> $item['title'],			// The title of the item.
							'link'			=> $item['link'],			// The URL of the item.
							'description'	=> $item['description'], 	// The item synopsis.
							);
		
		$optional = array(
							'author' 		=> $item['author'],			// Email address of the author of the item.
							'category'		=> $item['category'],		// Includes the item in one or more categories.
							'comments'		=> $item['comments'],		// URL of a page for comments relating to the item.
							'enclosure'		=> $item['enclosure'],		// Describes a media object that is attached to the item.
							'guid'			=> $item['guid'],			// A string that uniquely identifies the item.
							'pubDate'		=> $item['pubDate'],		// Indicates when the item was published.
							'source'		=> $item['source'],			// The RSS channel that the item came from.
							);
		
		if ( !$optional['guid'] ) $optional['guid'] = $required['link'];
		
		// remove duplicate values from $data array
		foreach ( array_merge($required, $optional) as $tag => $value ) {
			unset($item[ $tag ]);
		}
		
		$str = '';
		$str .= '<item>'."\n";
			foreach ( $required as $tag => $value ) {
			$str .= $this->tag($tag, $value);
			}
			foreach ( $optional as $tag => $value ) {
				if ( $value ) $str .= $this->tag($tag, $value);
			}
			if ( count($item) ) foreach ( $item as $tag => $value ) {
				if ( !$value ) continue;
				if ( is_array($value) && array_key_exists($tag, $this->xmlns) ) foreach ( $value as $key => $val ) {
					$name = strtolower($key{0}).substr(camelize($key), 1);
					if ( is_array($val) ) {
						$str .= "<{$tag}:{$name}>\n";
						foreach ( $val as $a => $b) {
							$name2 = strtolower($a{0}).substr(camelize($a), 1);
							$str .= $this->tag($tag . ':' . $name2, $b);
						}
						$str .= "</{$tag}:{$name}>\n";
					} else {
						$str .= $this->tag($tag . ':' . $name, $val);
					}
				}
			}
		$str .= '</item>'."\n";
		return $str;
	}
	
	/*
		Function: xmlns_str
		
		Creates the XML file string.
		
		Returns:
		
			String
	*/
	
	public function xmlns_str()
	{
		if ( !is_array($this->xmlns) ) die('Error: XML namespace(xmlms) not an array...');
		$str = ' ';
		foreach (  $this->xmlns as $namespace => $namespaceURI ) {
			$str .= 'xmlns:' . $namespace . '="' . $namespaceURI . '" ';
		}
		return $str;
	}
	
	/*
		Function:
		
		Creates the XML tag string.
		
		Parameters:
		
			tag - required
			value - required
			has_ending_tag - optinal
			
		Returns:
		
			String
	*/
	
	public function tag($tag, $value, $has_ending_tag = true)
	{
		if ( $tag == 'image' ) {
			$return = "<{$tag}>\n";
			foreach ( $value as $k => $v ) {
				$return .= $this->tag($v->name, $v->cdata);
			}
			$return .="</{$tag}>\n";
			return $return;
		}
		if ( is_array($value) ) {
			$str = '';
			foreach ( $value as $attribute => $val ) {
				if ( $attribute == 'value' ) continue;
				$str .= " {$attribute}=\"{$val}\"";
			}
			if ( $has_ending_tag ) {
				return "<{$tag}{$str}>" . $this->xmlentities($value['value']) . "</{$tag}>\n";
			} else {
				return "<{$tag}{$str} />";
			}
		} else {
			return "<{$tag}>" . $this->xmlentities($value) . "</{$tag}>\n";
		}
	
	}
	
	/*
		Function: xmlentities
		
		http://us3.php.net/manual/en/function.htmlentities.php#46785
		
		Parameters:	
		
			string - required
			quote_style - required
		
		Returns:
		
			String
	*/
	
	public function xmlentities($string, $quote_style=ENT_QUOTES)
	{
		static $trans;
		if (!isset($trans)) {
			$trans = get_html_translation_table(HTML_ENTITIES, $quote_style);
			foreach ($trans as $key => $value)
				$trans[$key] = '&#'.ord($key).';';
			// dont translate the '&' in case it is part of &xxx;
			$trans[chr(38)] = '&';
		}
		// after the initial translation, _do_ map standalone '&' into '&#38;'
		return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&#38;" , strtr($string, $trans));
	}
	
	/*
		Function: feed
		
		Creates RSS feed and changes the header content-type.
	*/
	
	public function feed()
	{
		header('Content-Type: application/xml');
		die($this->create_file());
	}
	
	/*
		Function: pubDate
		
		Formats publish date from a date srting.
		
		Parameters:
		
			date - Date/Time string.
			
		Returns:
		
			Publish date.
	*/
	
	public function pubDate($date)
	{
		return gmdate('D, d M Y H:i:s', strtotime($date)).' GMT';
	}
	
	/*
		Function: cache_expired
		
		Checks if cache file has expired.
		
		Parameters:
		
			url - RSS feed URL.
			
		Returns:
		
			Boolean.
	*/
	
	public function cache_expired($url)
	{
		// check and set vars
		if ( !$this->cache_dir )  $this->set_cache_dir();
		if ( !$this->cache_file ) $this->set_cache_file($url);
		
		// if file exists and has not expired
		if ( file_exists( $this->cache_filename() ) ) {
			if ( ( time() - filemtime($this->cache_filename())) <= ( MINUTE * $this->cache_time )) {
				return false;
			}
		}
		
		// set cache_feed and return has expired
		$this->cache_feed = true;
		return true;
	}
	
	/*
		Function: cache_filename
		
		Returns full path and name of cached file.
		
		Returns:
		
			String.
	*/
	
	public function cache_filename()
	{
		return $this->cache_dir.$this->cache_file;
	}
	
	/*
		Function: check_cache
		
		Checks if feed should cached and creates cache.
		
		Returns:
		
			Boolean.
	*/
	
	public function check_cache()
	{
		if ( $this->cache_feed && $this->cache_dir && $this->cache_file ) {
			return file_put_contents($this->cache_filename(), $this->create_file());
		} else {
			return false;
		}
	}
	
	/*
		Function: set_cache_dir
		
		Sets cache directory.
	*/
	
	public function set_cache_dir($path = null)
	{
		$path = $path ? $path : '/tmp/creovel/';
		
		if (!file_exists($path)) {
			mkdir($path);
		}
		
		$this->cache_dir = $path;
	}
	
	/*
		Function: set_cache_file
		
		Sets cache file name.
	*/
	
	public function set_cache_file($path)
	{
		$file = pathinfo($path, PATHINFO_BASENAME);
		$this->cache_file = underscore(str_replace(array(BASE_URL, $file), '', $path)).$file;
	}
	
	/*
		Function: set_cache_time
		
		Sets cache life span in minutes.
		
		Pramameters:
		
			min - Time in minutes default set to 30 minutes.
	*/
	
	public function set_cache_time($min = 30)
	{
		$this->cache_time = (integer) $min;
	}
	
	/*
		Function: cache_feed
		
		Cache RSS feed.
		
		Pramameters:
		
			min - Time in minutes to cache feed.
	*/
	
	public function cache_feed($min = null)
	{
		$this->cache = true;
		$this->set_cache_time($min);
	}
	
	// Iterator Implementation
		
	public function rewind()
	{
		reset($this->items);
	}
	
	public function current()
	{
		if ( $var = current($this->items) ) {
			return $var;
		} else {
			return false;
		}
	}
	
	public function key()
	{
		return key($this->items);
	}
	
	public function next()
	{
		return next($this->items);
	}
	
	public function valid()
	{
		return $this->current() !== false;
	}
	
	// Section: Private
	
	/*
		Property: items_as_array
		
		An array of items for this syndication.
	*/
	
	private $cache = false;
	
	/*
		Property: cache_dir
		
		Folder in which cached data should be stored. Make sure directory has proper permission (chmod this folder to 777).
	*/
	
	private $cache_dir;
	
	/*
		Property: cache_dir
		
		Cache file name..
	*/
	
	private $cache_file;
	
	/*
		Property: cache_time
		
		The time in minute the feeds should be cashed. Default set to 30 minutes.
	*/
	
	private $cache_time = 30;
	
	/*
		Property: xml
		
		XML object.
	*/
	
	private $xml;
	
	/*
		Function: _map_rss20
		
		Map XML data to RSS 2.0 structure.
	*/
	
	private function _map_rss20()
	{
		foreach ( $this->xml->data->children->children[0]->children as $element ) {
			// load items else load properties
			if ( $element->name == 'item' ) {
				(object) $item = null;
				foreach ( $element->children as $elm ) {
					$property = $elm->name;
					// handle nested item elements
					if ( !$elm->cdata && is_array($elm->children) ) {
						foreach ( $elm->children as $child ) {
							$child_name = $child->name;
							$item->$property->$child_name = $child->cdata;
						}
						if ( $this->items_as_array ) {
							$item->$property = (array) $item->$property;
						}
					} else {
						$item->$property = $elm->cdata;
					}
				}
				if ( $this->items_as_array ) {
					$item = (array) $item;
				}
				// load item to class
				$this->add_item($item);
			} else {
				$property = $element->name;
				
				if ( $property == 'image' ){
					$this->$property = $element->children;
				} else {
					$this->$property = $element->cdata;
				}
			}
		}
	}

}
?>