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
 * @version		2 (4/8/2006)
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
	public $listeners;							// array of listeners
	public $icons;								// array of icons
	public $open_at;							// name of marker that will be opened on map load
	
	// private properties
	private $key;								// Google Maps API Key
	private $id;								// GMap object ID
	
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

	public function set_key($key)
	{
		$this->key = $key;
	}
	
	public function set_id($id)
	{
		$this->id = $id;
	}
	
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
	function <?=$this->id?>_obj()
	{
		
		// properties
		<?=$this->gmap()?>
		<? if ( $this->auto_zoom ) echo "this.bounds = new GLatLngBounds();"; ?>
		
		<? if ( count($this->icons) ) { ?>// icons
		this.icons = new Object;
		<? foreach ( $this->icons as $icon => $vals ) { echo $this->create_icon($vals['name'], $vals); } } ?>
		<? if ( $this->controls ) { ?>
		
		// controls
		<?=$this->add_control($this->get_controls())?>
		<? if ( $this->scale_control ) echo $this->add_control('new GScaleControl()');?>
		<? if ( $this->type_control ) echo $this->add_control('new GMapTypeControl()');?>
		<? if ( $this->overview_control ) echo $this->add_control('new GOverviewMapControl()');?>
		<? } ?>
		
		// center map
		<?=$this->set_center($this->default_lat, $this->default_lng, $this->zoom, $this->get_map_type())?>
		<?
			if ( count($this->markers) ) {
				echo "\n// markers\n";
				echo "this.markers = new Object;\n";
				foreach ( $this->markers as $marker => $result ) echo $this->create_marker($result['name'], $result)."\n";
			}
			if ( ( $this->auto_zoom || $this->open_at ) && count($this->markers) ) echo "// extra work\n";
			if ( $this->auto_zoom && count($this->markers) ) echo "this.map.autoZoom(this.bounds);\n";
			if ( $this->open_at && count($this->markers) ) echo "this.map.openWindow(this.markers.{$this->open_at});\n";
		?>
		
	}
	
	<?=$this->onload("{$this->id} = new {$this->id}_obj();")?>
	<?=$this->unload()?>
	
}
<?=$this->display_warning_if_not_compatible()?>
//]]>
</script>
<div id="<?=$this->id?>"<?=html_options_str($html_options)?>></div>
		<?
	}
	
	public function include_api()
	{
		static $return;
		if ( !$return ) {
			return $return = '<script src="http://maps.google.com/maps?file=api&v=2&key=' . $this->key . '" type="text/javascript"></script>'."\n";
		}
	}
	
	public function gmap($container = null, $options = null)
	{
		$id = $container ? $container : $this->id;
		return 'this.map = new GMap2(document.getElementById("' . $id . '"));'."\n";
	}
	
	public function glatlng($latitude, $longitude)
	{
		return 'new GLatLng(' . (float) $latitude . ', ' . (float) $longitude . ')';
	}
	
	public function set_center($latitude, $longitude, $zoom = 4, $map_type = null)
	{
		return 'this.map.setCenter(' . $this->glatlng($latitude, $longitude) . ', ' . (int) $zoom . ( $map_type ? ', ' . $map_type : '' ) . ");\n";
	}
	
	public function pan_to($latitude, $longitude)
	{	
		return 'this.map.panTo(' . $this->glatlng($latitude, $longitude) . ");\n";
	}
	
	public function add_control($control, $position = null)
	{
		return 'this.map.addControl('. $control .");\n";
	}
	

	
	public function create_marker($name, $latitude, $longitude = null, $icon = '', $html_or_tabs = '', $onclick = false)
	{
		if ( is_array($latitude) ) {
			$icon = $latitude['icon'];
			$html_or_tabs = ( $latitude['tabs'] ? $latitude['tabs'] : $latitude['html'] );
			$onclick = $latitude['onclick'];
			$longitude = $latitude['longitude'];
			$latitude = $latitude['latitude'];
		}
		$return = "this.latlng = {$this->glatlng($latitude, $longitude)};\n";
		if ( $this->auto_zoom ) $return .= "this.bounds.extend(this.latlng);\n";
		$return .= "this.markers.{$name} = this.map.createMarker(this.latlng" . ( $icon ? ', this.icons.' . $icon : '' ). ");\n";
		switch ( true ) {
		
			case ( is_array($html_or_tabs) ):
				$return .= 'this.markers.' . $name . '.tabs = [';
				foreach ( $html_or_tabs as $tab => $content ) {
					$return .= 'new GInfoWindowTab("' . addslashes($tab) . '", "' . addslashes($content) .  '"), ';
				}
				$return = rtrim($return, ', ');
				$return .= "];\n";
				$return .= "this.map.createOnClickInfoWindowTabs(this.markers.{$name});\n";
			break;
		
			case ( $html_or_tabs ):
				$return .= 'this.markers.' . $name . '.html = "'. addslashes($html_or_tabs) .'";'."\n";
				$return .= "this.map.createOnClickInfoWindow(this.markers.{$name});\n";
			break;
			
		}
		$return .= "this.map.addOverlay(this.markers.{$name});";
		return $return;
	}
	
	public function create_listener($source, $event, $handler = null)
	{
		if ( is_array($source) ) {
			$handler = $source['handler'];
			$event = $source['event'];
			$source = $source['source'];
		}
		return "GEvent.addListener({$source}, '{$event}', {$handler});";
	}
	
	public function create_icon($name, $data)
	{
		$name = 'this.icons.' . $name;
		$return = $name . ' = new GIcon(' . ( $data['baseIcon'] ? 'this.icons.' . $data['baseIcon'] : '' ) . ");\n";
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
	
	public function gpoint($x, $y = null)
	{
		if ( strstr($x, ',') ) {
			$temp = explode(',', $x);
			$x = $temp[0];
			$y = $temp[1];
		}
		return "new GPoint(" . (int) $x . ", " . (int) $y .")";
	}
	
	public function gsize($width, $height = null)
	{
		if ( strstr($width, ',') ) {
			$temp = explode(',', $width);
			$width = $temp[0];
			$height = $temp[1];
		}
		return "new GSize(" . (int) $width . ", " . (int) $height .")";
	}
	
	public function open_window($name)
	{
		return "{$this->id}.map.openWindow(map.markers.{$name});";
	}
	
	public function add($marker)
	{
		$this->add_marker($marker);
	}
	
	public function add_marker($marker)
	{
		$this->markers[] = $marker;
	}
	
	public function add_listener($args)
	{
		$this->listeners[] = $args;
	}
	
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