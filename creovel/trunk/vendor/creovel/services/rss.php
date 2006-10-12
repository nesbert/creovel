<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/**
 * RSS class to handle news feeds.
 * http://www.rssboard.org/rss-specification
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @subpackage	services
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Nesbert Hidalgo
 * @version		0.1 (10/2/2006)
 * @todo		need a write a base XML class (builder, parser)
 *				add support for RSS 0.91, 0.92 and 2.0 versions
 *				right own RSS parser lastRSS does not handle complex feeds
 */
class rss implements Iterator
{

	public $encoding		= "utf-8";
	public $title			= "RSS Syndication Title"; // The name of the channel. It's how people refer to your service. If you have an HTML website that contains the same information as your RSS file, the title of your channel should be the same as the title of your website.
	public $link			= BASE_URL; // The URL to the HTML website corresponding to the channel.
	public $description		= "Description of your syndication.";

	public $language		= "en-us"; // The language the channel is written in. This allows aggregators to group all Italian language sites, for example, on a single page. A list of allowable values for this element, as provided by Netscape, is here. You may also use values defined by the W3C(http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes).
	/*public $copyright		= ""; // Copyright notice for content in the channel.
	public $managingEditor	= ""; // Email address for person responsible for editorial content.
	public $webMaster		= ""; // Email address for person responsible for technical issues relating to channel.
	public $pubDate			= ""; // The publication date for the content in the channel.
	public $lastBuildDate	= ""; // The last time the content of the channel changed.
	public $category		= ""; // Specify one or more categories that the channel belongs to.
	public $generator		= "RSS v0.1 a part of the creovel (http://www.creovel.org)."; // A string indicating the program used to generate the channel.
	public $docs			= ""; // A URL that points to the documentation for the format used in the RSS file.
	public $cloud			= ""; // Allows processes to register with a cloud to be notified of updates to the channel, implementing a lightweight publish-subscribe protocol for RSS feeds.
	public $ttl				= ""; // Copyright notice for content in the channel.
	public $image			= ""; // Specifies a GIF, JPEG or PNG image that can be displayed with the channel.
	public $rating			= ""; // The PICS rating for the channel.
	public $textInput		= ""; // Specifies a text input box that can be displayed with the channel.
	public $skipHours		= ""; // A hint for aggregators telling them which hours they can skip.
	public $skipDays		= ""; // A hint for aggregators telling them which days they can skip.
	*/

	public $items;
	
	private $version		= "2.0";
	
	/**
	 * Description.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param bool $var required
	 * @return object
	 */
	public function initialize()
	{
	}
	
	/**
	 * Description.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param bool $var required
	 * @return object
	 */
	public function add_item($data)
	{
		$this->items[] = $data;
	}
	
	/**
	 * Creates the XML file string.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */
	public function xml()
	{
		$required_tags = array('title', 'language', 'link', 'description');
		$xml = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'."\n";
		$xml .= '<rss version="' . $this->version . '"' . ( isset($this->xmlns) ? $this->xmlns_str() : '' ) . '>'."\n";
			$xml .= '<channel>'."\n";
				foreach ( $required_tags as $tag ) {
					$xml .= $this->tag($tag, $this->$tag);
				}
				if ( count($this->items) ) foreach ( $this->items as $item ) {
					$xml .= self::item_str($item);
				}
			$xml .= '</channel>'."\n";
		$xml .= '</rss>'."\n";
		return $xml;
	}

	/**
	 * Create item XML string for one item.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $item required
	 * @return string
	 */
	public function item_str($item)
	{
		$required_item = array(
							'title' 		=> $item['title'],			// The title of the item.
							'link'			=> $item['link'],			// The URL of the item.
							'description'	=> $item['description'], 	// The item synopsis.
							);
							
		$optional_item = array(
							'author' 		=> $item['author'],			// Email address of the author of the item.
							'category'		=> $item['category'],		// Includes the item in one or more categories.
							'comments'		=> $item['comments'],		// URL of a page for comments relating to the item.
							'enclosure'		=> $item['enclosure'],		// Describes a media object that is attached to the item..
							'guid'			=> $item['guid'],			// A string that uniquely identifies the item.
							'pubDate'		=> $item['pubDate'],		// Indicates when the item was published.
							'source'		=> $item['source'],			// The RSS channel that the item came from.
							);
							
		if ( !$optional_item['guid'] ) $optional_item['guid'] = $required_item['link'];
		
		// remove duplicate values from $data array
		foreach ( array_merge($required_item, $optional_item) as $tag => $value ) {
			unset($item[ $tag ]);
		}
		
		//print_obj($required_data, 1);
		
		$str = '';
		$str .= '<item>'."\n";
			foreach ( $required_item as $tag => $value ) {
			$str .= $this->tag($tag, $value);
			}
			foreach ( $optional_item as $tag => $value ) {
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
	
	/**
	 * Creates the XML file string.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
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
	
	/**
	 * Creates the XML tag string.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $tag required
	 * @param mixed $value required
	 * @param bool $has_ending_tag optinal
	 * @return string
	 */
	public function tag($tag, $value, $has_ending_tag = true)
	{
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
	
	/**
	 * http://us3.php.net/manual/en/function.htmlentities.php#46785
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $string required
	 * @return string
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
	
	/**
	 * Creates RSS feed.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	public function feed()
	{
		header('Content-Type: application/xml');
		die($this->xml());
	}
	
	/**
	 * Loads RSS feed into class.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
		 */
	public function load($url)
	{
		require_once(CREOVEL_PATH.'vendor/lastRSS.php');
		
		// create lastRSS object
		$rss = new lastRSS; 
		
		// setup transparent cache
		/*
		$rss->cache_dir = './cache'; 
		$rss->cache_time = 3600; // one hour
		*/
		
		// load some RSS file
		if ( $rs = $rss->get($url) ) {
			// here we can work with RSS fields
			foreach ( $rs as $tag => $value ) {
				$this->$tag = $value;
			}
		} else {
			die ('Error: RSS file not found...');
		}	
	}
	
	public function rewind()
	{
		reset($this->items);
	}
	
	public function current()
	{
		if ( $var = current($this->items) ) {
			return (object) $var;
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
	
}
?>