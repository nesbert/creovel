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
 * Google Maps Service Version 2
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @subpackage	services
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Nesbert Hidalgo
 * @version		2.3 (10/24/2006)
 *				2 (4/8/2006)
 */
class google_maps
{
	// public properties
	public $default_lng 		= -122.1419;	// default longitude point for map
	public $default_lat			= 37.4419;		// default latitude point for map
	public $width				= '500px';		// default map width
	public $height				= '300px';		// default map height
	public $zoom				= 13;			// default zoom level
	public $auto_zoom			= false;		// auto zoom and center map depending on markers
	public $controls			= true;			// show zoom and pan controls
	public $controls_size		= 'large';		// map controls size => 'large', 'small', 'tiny'
	public $scale_control		= true;			// mile/km indicator
	public $type_control		= 'map';		// map type -> false, 'map', 'satellite', 'hybrid'
	public $overview_control	= false;		// a collapsible overview map in the corner of the screen;
	public $markers;							// array of markers
	public $icons;								// array of icons
	public $listeners;							// array of listeners
	public $open_at;							// name of marker that will be opened on map load
	
	// private properties
	private $key;								// Google Maps API Key
	private $id;								// GMap object ID
	
	/**
	 * Class construct. You have the option to pass the Google Maps API key and DOM ID when
	 * initializing the class.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $key optional
	 * @param string $id optional
	 */
	public function __construct($key = null, $id = null)
	{
		// set API key
		$this->key = $key;
		// set map ID
		if ( $id ) {
			$this->set_id($id);
		} else {
			$this->set_id('map_'.uniqid());
		}
	}
	
	/**
	 * Set Google Maps API key. Required to use google maps.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $key required
	 * @link http://www.google.com/apis/maps/signup.html
	 */
	public function set_key($key)
	{
		$this->key = $key;
	}
	
	/**
	 * Set DOM ID for map.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $id required
	 */
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Google Maps RUN-TIME. Creates javascript code and map ojects used by this class.
	 * Outputs to screen where ever called.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $html_options optional
	 * @return string
	 */
	public function display_map($html_options = null)
	{
		if ( !strstr($this->width, 'px') && !strstr($this->width, '%') ) $this->width .= 'px';
		if ( !strstr($this->height, 'px') && !strstr($this->height, '%') ) $this->height .= 'px';
		$html_options['style'] = "width:{$this->width}; height:{$this->height}; " . $html_options['style'];
		?>
<?=$this->include_api()?>
<script type="text/javascript">
//<![CDATA[
if ( GBrowserIsCompatible() ) {

	/* Extending GMap2 Functionality */
	<?=$this->extend_gmap()?>
	
	// global vairables
	var <?=$this->id?>;
	
	// google map object
	function <?=$this->id?>Obj()
	{
		
		// properties
		<?=$this->gmap()?>
		<? if ( $this->auto_zoom ) echo "this.Bounds = new GLatLngBounds();\n"; ?>
		
		// controls
		this.Controls = new Object;
		<? if ( $this->controls ) echo $this->add_control('( this.Controls.ControlSize = '.$this->get_controls().' )'); ?>
		<? if ( $this->controls && $this->scale_control ) echo $this->add_control('( this.Controls.GScaleControl = new GScaleControl() )'); ?>
		<? if ( $this->controls && $this->type_control ) echo $this->add_control('( this.Controls.GMapTypeControl = new GMapTypeControl() )'); ?>
		<? if ( $this->overview_control ) echo $this->add_control('( this.Controls.GOverviewMapControl = new GOverviewMapControl() )'); ?>
		
		// center map
		<?=$this->set_center($this->default_lat, $this->default_lng, $this->zoom, $this->get_map_type())?>
		
		<?
			if ( count($this->icons) ) {
				echo "// icons\n";
				echo "\t\tthis.Icons = new Object;\n";
				foreach ( $this->icons as $icon => $vals ) echo $this->create_icon($vals['name'], $vals);
			}

			if ( count($this->markers) ) {
				echo "// markers\n";
				echo "\t\tthis.Markers = new Object;\n";
				foreach ( $this->markers as $marker => $result ) echo $this->create_marker($result['name'], $result)."\n";
			}
			if ( ( $this->auto_zoom || $this->open_at ) && count($this->markers) ) echo "\n\t\t// extra work\n";
			if ( $this->auto_zoom && count($this->markers) ) echo "\t\tthis.GMap.autoZoom(this.Bounds);\n";
			if ( $this->open_at && count($this->markers) ) echo "\t\tthis.GMap.openWindow(this.Markers.{$this->open_at});\n";
		?>		
	}
	
	<?=$this->onload("{$this->id} = new {$this->id}Obj();")?>
	<?=$this->unload()?>
	
}
<?=$this->display_warning_if_not_compatible()?>
//]]>
</script>
<div id="<?=$this->id?>"<?=html_options_str($html_options)?>></div>
		<?
	}
	
	/**
	 * Create JavaScript include string for API.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */
	public function include_api()
	{
		static $return;
		if ( !$return ) {
			return $return = '<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=' . $this->key . '"></script>'."\n";
		}
	}
	
	/**
	 * Create GMap2 object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */
	public function gmap($container = null, $options = null)
	{
		$id = $container ? $container : $this->id;
		return 'this.GMap = new GMap2(document.getElementById("' . $id . '"));'."\n";
	}
	
	/**
	 * Create GLatLng (geographical coordinates longitude and latitude) object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $latitude required
	 * @param string $longitude required
	 * @return string
	 */
	public function glatlng($latitude, $longitude)
	{
		return 'new GLatLng(' . (float) $latitude . ', ' . (float) $longitude . ')';
	}
	
	/**
	 * Set center point of map.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $latitude required
	 * @param string $longitude required
	 * @param string $zoom optional default set to 4
	 * @param string $map_type optional 'map', 'satellite', 'hybrid'
	 * @return string
	 */
	public function set_center($latitude, $longitude, $zoom = 4, $map_type = null)
	{
		return 'this.GMap.setCenter(' . $this->glatlng($latitude, $longitude) . ', ' . (int) $zoom . ( $map_type ? ', ' . $map_type : '' ) . ");\n";
	}
	
	/**
	 * Pan map to a geographical coordinates longitude and latitude.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $latitude required
	 * @param string $longitude required
	 * @return string
	 */
	public function pan_to($latitude, $longitude)
	{	
		return 'this.GMap.panTo(' . $this->glatlng($latitude, $longitude) . ");\n";
	}
	
	/**
	 * Add a control to map.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $control required
	 * @param string $position optional
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GControl
	 * @return string
	 */
	public function add_control($control, $position = null)
	{
		return 'this.GMap.addControl('. $control .");\n";
	}
	
	/**
	 * Create a GMarker object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $name required
	 * @param string/array $latitude required
	 * @param string $longitude optional
	 * @param string $icon optional
	 * @param string $html_or_tabs optional
	 * @param bool $onclick optional
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GMarker
	 * @return string
	 */	
	public function create_marker($name, $latitude, $longitude = null, $icon = '', $html_or_tabs = '', $onclick = false)
	{
		// if $latitude is an array use its values instead.
		if ( is_array($latitude) ) {
			$icon = $latitude['icon'];
			$html_or_tabs = ( $latitude['tabs'] ? $latitude['tabs'] : $latitude['html'] );
			$onclick = $latitude['onclick'];
			$longitude = $latitude['longitude'];
			$latitude = $latitude['latitude'];
		}
		
		// set GLatLng points
		$return = "\t\tthis.LatLng = {$this->glatlng($latitude, $longitude)};\n";
		
		// if auto_zoom set add marker location to bounds
		if ( $this->auto_zoom ) $return .= "\t\tthis.Bounds.extend(this.LatLng);\n";
		
		// create marker object
		$return .= "\t\tthis.Markers.{$name} = this.GMap.createMarker(this.LatLng" . ( $icon ? ', this.Icons.' . $icon : '' ). ");\n";
		
		switch ( true )
		{
			// set InfoWindow tabs		
			case ( is_array($html_or_tabs) ):
				$return .= "\t\tthis.Markers." . $name . '.tabs = [';
				foreach ( $html_or_tabs as $tab => $content ) {
					$return .= 'new GInfoWindowTab("' . addslashes($tab) . '", "' . addslashes($content) .  '"), ';
				}
				$return = rtrim($return, ', ');
				$return .= "];\n";
				$return .= "\t\tthis.GMap.createOnClickInfoWindowTabs(this.Markers.{$name});\n";
			break;
			
			// set regular InfoWidow		
			case ( $html_or_tabs ):
				$return .= "\t\tthis.Markers." . $name . '.html = "'. addslashes($html_or_tabs) .'";'."\n";
				$return .= "\t\tthis.GMap.createOnClickInfoWindow(this.Markers.{$name});\n";
			break;
			
		}
		
		// add marker overlay to map
		$return .= "\t\tthis.GMap.addOverlay(this.Markers.{$name});";
		
		return $return;
	}
	
	/**
	 * Create a GIcon object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $name required
	 * @param array $data required
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GIcon
	 * @return string
	 */
	public function create_icon($name, $data)
	{
		$name = "\t\t".'this.Icons.' . $name;
		$return = $name . ' = new GIcon(' . ( $data['baseIcon'] ? 'this.Icons.' . $data['baseIcon'] : '' ) . ");\n";
		if ( $data['image'] ) $return .= $name . '.image = "' . $data['image'] . '";'."\n";
		if ( $data['shadow'] ) $return .= $name . '.shadow = "' . $data['shadow'] . '";'."\n";
		if ( $data['iconSize'] ) $return .= $name . '.iconSize = ' . $this->gsize($data['iconSize']) . ';'."\n";
		if ( $data['shadowSize'] ) $return .= $name . '.shadowSize = ' . $this->gsize($data['shadowSize']) . ';'."\n";
		if ( $data['iconAnchor'] ) $return .= $name . '.iconAnchor = ' . $this->gpoint($data['iconAnchor']) . ';'."\n";
		if ( $data['infoWindowAnchor'] ) $return .= $name . '.infoWindowAnchor = ' . $this->gpoint($data['infoWindowAnchor']) . ';'."\n";
		if ( $data['infoShadowAnchor'] ) $return .= $name . '.infoShadowAnchor = ' . $this->gpoint($data['infoShadowAnchor']) . ';'."\n";
		if ( $data['printImage'] ) $return .= $name . '.printImage = "' . $data['printImage'] . '";'."\n";
		if ( $data['mozPrintImage'] ) $return .= $name . '.mozPrintImage = "' . $data['mozPrintImage'] . '";'."\n";
		if ( $data['printShadow'] ) $return .= $name . '.printShadow = "' . $data['printShadow'] . '";'."\n";
		if ( $data['transparent'] ) $return .= $name . '.transparent = "' . $data['transparent'] . '";'."\n";
		return $return."\n";
	}
	
	/**
	 * Create a GPoint object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $source required
	 * @param string $event required
	 * @param string $handler optional
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GEvent
	 * @return string
	 */
	public function create_listener($source, $event, $handler = null)
	{
		if ( is_array($source) ) {
			$handler = $source['handler'];
			$event = $source['event'];
			$source = $source['source'];
		}
		return "GEvent.addListener({$source}, '{$event}', {$handler});";
	}
	
	/**
	 * Create a GPoint object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param int $x required
	 * @param int $y optional
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GPoint
	 * @return string
	 */
	public function gpoint($x, $y = null)
	{
		if ( strstr($x, ',') ) {
			$temp = explode(',', $x);
			$x = $temp[0];
			$y = $temp[1];
		}
		return "new GPoint(" . (int) $x . ", " . (int) $y .")";
	}
	
	/**
	 * Create a GSize object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param int $width required
	 * @param int $height optional
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GSize
	 * @return string
	 */
	public function gsize($width, $height = null)
	{
		if ( strstr($width, ',') ) {
			$temp = explode(',', $width);
			$width = $temp[0];
			$height = $temp[1];
		}
		return "new GSize(" . (int) $width . ", " . (int) $height .")";
	}
	
	/**
	 * Alias to add_marker().
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $marker required
	 */
	public function add($marker)
	{
		$this->add_marker($marker);
	}
	
	/**
	 * Add a marker to class markers array.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $marker required
	 */
	public function add_marker($marker)
	{
		// auto set default coors if first marker
		if ( !count($this->markers) ) {
			$this->default_lat = ( $marker['latitude'] ? $marker['latitude'] : $marker['Latitude'] );
			$this->default_lng = ( $marker['longitude'] ? $marker['longitude'] : $marker['Longitude'] );
		}
		$this->markers[] = $marker;
	}
	
	/**
	 * Add a listener to class listeners array.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $args required
	 */
	public function add_listener($args)
	{
		$this->listeners[] = $args;
	}
	
	/**
	 * Add an icon to class icons array.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $args required
	 */
	public function add_icon($args)
	{
		$this->icons[] = $args;
	}
	
	/**
	 * Get this map's control size ( 'tiny' = GSmallZoomControl, 'small' = GSmallMapControl, 'large' = GLargeMapControl ).
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */
	private function get_controls()
	{
		switch ( $this->controls_size )
		{
			case 'tiny':
				$control = 'GSmallZoomControl';
			break;
		
			case 'small':
			default:
				$control = 'GSmallMapControl';
			break;
		
			case 'large':
				$control = 'GLargeMapControl';
			break;
		}
		return "new {$control}()";
	}
	
	/**
	 * Get this map's type constant ( 'map' = G_NORMAL_MAP, 'satellite' = G_SATELLITE_MAP, 'hybrid' = G_HYBRID_MAP ).
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */
	public function get_map_type()
	{
		switch ( $this->type_control )
		{
			case 'hybrid':
				$type = 'G_HYBRID_MAP';
			break;
		
			case 'satellite':
				$type = 'G_SATELLITE_MAP';
			break;
		
			case 'map':
			default:
				$type = 'G_NORMAL_MAP';
			break;
		}
		return $type;
	}
	
	/**
	 * Add javascript to window.onload function.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $js_str optional
	 * @return string
	 */	
	public function onload($js_str)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.onload = new Function("' . $code . '");'."\n";
	}
	
	/**
	 * Add javascript to window.unload function.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $js_str optional
	 * @return string
	 */	
	public function unload($js_str = null)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.unload = new Function("GUnload();' . $code . '");'."\n";
	}
	
	/**
	 * Get coordinates from address provided.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $address required
	 * @return array
	 */
	public function geocode_address($address)
	{
		$temp = $this->geocode_http($address);
		$coordinates['latitude'] = $temp['Latitude'];
		$coordinates['longitude'] = $temp['Longitude'];
		return $coordinates;
	}
	
	/**
	 * Geocode through google using HTTP Request and return a formatted array.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $address required
	 * @return array
	 */
	public function geocode_http($address)
	{
		if ( !$address ) return false;
		
		// create xml object and load xml data
		$xml = new xml;		
		$xml->load( 'http://maps.google.com/maps/geo?q=' . urlencode($address) . '&output=xml&key=' . $this->key );
		
		// get address coordinates
		$coords = explode(',', $xml->data->children->children[0]->children[2]->children[2]->children[0]->cdata);
		
		// map array 
		$return = array(
					'Input' => $xml->data->children->children[0]->children[0]->cdata,
					$xml->data->children->children[0]->children[1]->name => array(
																				ucwords($xml->data->children->children[0]->children[1]->children[1]->name) => $xml->data->children->children[0]->children[1]->children[1]->cdata,
																				ucwords($xml->data->children->children[0]->children[1]->children[0]->name) => $xml->data->children->children[0]->children[1]->children[0]->cdata,
																				'Description' => $this->get_status_description( $xml->data->children->children[0]->children[1]->children[0]->cdata )
																				),
					'Address' => $xml->data->children->children[0]->children[2]->children[0]->cdata,
					$xml->data->children->children[0]->children[2]->children[1]->name => array(
																							'Accuracy' => $xml->data->children->children[0]->children[2]->children[1]->attributes->Accuracy,
																							'AccuracyDescription' => $this->get_accuracy_description( $xml->data->children->children[0]->children[2]->children[1]->attributes->Accuracy ),
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[0]->cdata,
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[0]->cdata,
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[0]->cdata,
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->children[0]->cdata,
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->children[1]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->children[1]->children[0]->cdata,
																							$xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->children[2]->name => $xml->data->children->children[0]->children[2]->children[1]->children[0]->children[1]->children[1]->children[1]->children[2]->children[0]->cdata
																							),
					'Longitude' => $coords[0],
					'Latitude' => $coords[1],
					'Altitude' => $coords[2]
					);
		
		return $return;
	}
	
	/**
	 * Returns Status Code Description from is numeric equivalent.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param int $code required
	 * @return string
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode
	 */	
	public function get_status_description($code)
	{
		switch ( $code )
		{
			case 200:
				return 'No errors occurred; the address was successfully parsed and its geocode has been returned.';
			break;
			
			case 500:
				return 'A geocoding request could not be successfully processed, yet the exact reason for the failure is not known.';
			break;
			
			case 601:
				return 'The HTTP q parameter was either missing or had no value.';
			break;
			
			case 602:
				return 'No corresponding geographic location could be found for the specified address. This may be due to the fact that the address is relatively new, or it may be incorrect.';
			break;
			
			case 603:
				return 'The geocode for the given address cannot be returned due to legal or contractual reasons.';
			break;
			
			case 610:
				return 'The given key is either invalid or does not match the domain for which it was given. (Since 2.55)';
			break;
		
		}	
	}
	
	/**
	 * Returns Address Accuracy Description from is numeric equivalent.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param int $code required
	 * @return string
	 * @link http://www.google.com/apis/maps/documentation/reference.html#GGeoAddressAccuracy
	 */	
	public function get_accuracy_description($code)
	{
		switch ( $code )
		{
			case 0:
				return 'Unknown location.';
			break;
			
			case 1:
				return 'Country level accuracy.';
			break;
			
			case 2:
				return 'Region (state, province, prefecture, etc.) level accuracy.';
			break;
			
			case 3 :
				return 'Sub-region (county, municipality, etc.) level accuracy.';
			break;
			
			case 4:
				return 'Town (city, village) level accuracy.';
			break;
			
			case 5:
				return 'Post code (zip code) level accuracy.';
			break;
			
			case 6:
				return 'Street level accuracy.';
			break;
			
			case 7:
				return 'Intersection level accuracy.';
			break;
			
			case 8:
				return 'Address level accuracy.';
			break;
		}	
	}
	
	/**
	 * Add additional functionallty to GMap2 object.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return string
	 */
	private function extend_gmap()
	{
		static $count;
		switch ( true ) {
			case ( !$count && $this->auto_zoom ):
			?>// auto-zooom
	GMap2.prototype.autoZoom = function(bounds) {
		var center_lat = (bounds.getNorthEast().lat() +	bounds.getSouthWest().lat()) / 2.0;
		var center_lng = (bounds.getNorthEast().lng() +	bounds.getSouthWest().lng()) / 2.0;
		var center = new GLatLng(center_lat, center_lng)
		var zoom = this.getBoundsZoomLevel(bounds, this.getSize());
		this.setCenter(center, zoom);
		this.savePosition();
	}
	<?
			case ( !$count ):
			?>// create markers
	GMap2.prototype.createMarker = function(latlng, icon) {
		var marker = new GMarker(latlng, icon);
		return marker;
	}
	// create onclick information window
	GMap2.prototype.createOnClickInfoWindow = function(marker) {
		GEvent.addListener(marker, "click", function() { marker.openInfoWindowHtml(marker.html); });
	}
	// create onclick information tabbed window
	GMap2.prototype.createOnClickInfoWindowTabs = function(marker) {
		GEvent.addListener(marker, "click", function() { marker.openInfoWindowTabsHtml(marker.tabs); });
	}
	// create onclick information tabbed window
	GMap2.prototype.openWindow = function(marker) {
		if ( marker.tabs ) {
			marker.openInfoWindowTabsHtml(marker.tabs);
		} else {
			marker.openInfoWindowHtml(marker.html);
		}
	}
			<?
			break;
		}
		$count++;
	}
	
	/**
	 * Alert user if browser not compatible with Google Maps.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return string
	 */	
	private function display_warning_if_not_compatible()
	{
		static $return;
		return $return = ( !$return ? 'if ( !GBrowserIsCompatible() ) alert("Attention: Unable to display map!\nYour current browser is not compatible with Google Maps.\nPlease upgrade to Firefox 1.+ or Internet Explorer 6.+");'."\n" : '' );
	}
	
}
?>