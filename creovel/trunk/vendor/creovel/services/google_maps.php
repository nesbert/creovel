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

/*
 * Google Maps Service Version 2 (4/8/2006)
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @subpackage	services
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Nesbert Hidalgo
 */
class google_maps
{

	private $key;								// Google Maps API Key
	private $id;								// GMap object ID

	public $default_lat = 37.4419;				// default latitude point for map
	public $default_lng = -122.1419;			// default longitude point for map
	public $width = '500px';					// default map width
	public $height = '300px';					// default map height
	public $zoom = 13;							// default zoom level
	public $auto_zoom = false;					// auto zoom and center map depending on markers
	public $controls = true;					// show zoom and pan controls
	public $controls_size = 'large';			// map controls size => 'large', 'small', 'tiny'
	public $scale_control = true;				// mile/km indicator
	public $type_control = 'map';				// map type -> false, 'map', 'satellite', 'hybrid'
	public $markers;							// array of markers
	public $listeners;							// array of listeners
	public $icons;								// array of icons
	public $open_at;							// name of marker that will be opened on map load
	
	public function __construct($key, $id = null)
	{
		// set API key
		$this->key = $key;
		// set map ID
		if ( $id ) $this->set_id($id);
	}

	public function set_id($id)
	{
		$this->id = $id;
	}
	
	public function display_map($html_options = null)
	{
		if ( substr($this->width, -2) != 'px' ) $this->width .= 'px';
		if ( substr($this->height, -2) != 'px' ) $this->height .= 'px';
		$html_options['style'] = "width:{$this->width}; height:{$this->height}; " . $html_options['style'];
		?>
		<?=$this->include_api()?>
		<script type="text/javascript">
		//<![CDATA[
		if ( GBrowserIsCompatible() ) {
		
			/* Extending GMap2 functionality */
			<?=$this->extend_gmap()?>
			
			// global vairables
			var <?=$this->id?>;
			
			// google map object
			function <?=$this->id?>_obj() {
			
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
				<? } ?>
				
				// center map
				<?=$this->set_center($this->default_lat, $this->default_lng, $this->zoom)?>
				
				<? if ( count($this->markers) ) { ?>// markers
				this.markers = new Object;
				<? foreach ( $this->markers as $marker => $result ) echo $this->create_marker($result['name'], $result)."\n"; } ?>
				
				// extra work
				<? if ( $this->auto_zoom && count($this->markers) ) echo "this.map.autoZoom(this.bounds);\n" ; ?>
				<? if ( $this->open_at && count($this->markers) ) echo "this.map.openWindow(this.markers.{$this->open_at});\n" ; ?>
				
			}
			
			<?=$this->onload("{$this->id} = new {$this->id}_obj();")?>
			<?=$this->unload()?>
			
		}
		
		<?=$this->display_warning_if_not_compatible()?>		
		//]]>
		</script>
		<!--[if IE]>
		<style type="text/css">v\:* { behavior:url(#default#VML); }</style>
		<![endif]-->
		<div id="<?=$this->id?>"<?=html_options_str($html_options)?>></div>
		<?
	}
	
	public function include_api()
	{
		static $return;
		return $return = ( !$return ? '<script src="http://maps.google.com/maps?file=api&v=2&key=' . $this->key . '" type="text/javascript"></script>'."\n" : '' );
	}
	
	public function gmap($container = null, $options = null)
	{
		$id = $container ? $container : $this->id;
		return 'this.' . $id . ' = new GMap2(document.getElementById("' . $id . '"));'."\n";
	}
	
	public function glatlng($latitude, $longitude)
	{
		return 'new GLatLng(' . (float) $latitude . ', ' . (float) $longitude . ')';
	}
	
	public function set_center($latitude, $longitude, $zoom = 4)
	{
		return 'this.' . $this->id . '.setCenter(' . $this->glatlng($latitude, $longitude) . ', ' . (int) $zoom . ");\n";
	}
	
	public function pan_to($latitude, $longitude)
	{	
		return 'this.' . $this->id . '.panTo(' . $this->glatlng($latitude, $longitude) . ");\n";
	}
	
	public function add_control($control, $position = null)
	{
		return 'this.' . $this->id . '.addControl('. $control .");\n";
	}
	
	private function get_controls()
	{
		switch ( $this->controls_size ) {
		
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
		$return .= "this.{$this->id}.addOverlay(this.markers.{$name});";
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
	
	private function get_map_type()
	{
		switch ( $this->type_control ) {
		
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
	
	public function onload($js_str)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.onload = new Function("' . $code . '");'."\n";
	}
	
	public function unload($js_str = null)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.unload = new Function("GUnload();' . $code . '");'."\n";
	}

	public function geocode_address($address, $appid = 'MyAppID')
	{
        
		// use yahoo for geocoding
        $url = "http://api.local.yahoo.com/MapsService/V1/geocode?appid=" . rawurlencode($appid) . "&location=" . rawurlencode($address);
		
        if ( $xml = file_get_contents($url) ) {
        
            preg_match('!<Latitude>(.*)</Latitude><Longitude>(.*)</Longitude>!U', $xml, $match);
            
            $coordinates['latitude'] = $match[1];
            $coordinates['longitude'] = $match[2];
        
        }
		
        return $coordinates;
		
    }
	
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
	
	private function display_warning_if_not_compatible()
	{
		static $return;
		return $return = ( !$return ? 'if ( !GBrowserIsCompatible() ) alert("Attention: Unable to display map!\nYour current browser is not compatible with Google Maps.\nPlease upgrade to Firefox 1.+ or Internet Explorer 6.+");'."\n" : '' );
	}
	
}
?>