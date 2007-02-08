<?php
/*

Class: google_maps
	Google Maps Service Version 2.
	
Notes:
	Let me know if you change anything with this class [Nes 02/08/2007].

Todo:
	* Added support/interface for compressing polylines data http://www.google.com/apis/maps/documentation/#Encoded_Polylines
	* Implement Markers Manager http://www.google.com/apis/maps/documentation/#Marker_Manager

*/

class google_maps
{
	/*
	
	Property: default_lng
		public, float - Default longitude point for map set to _-122.1419_.

	Property: default_lat
		public, float - Default latitude point for map set to _37.4419_.

	Property: width
		public, string - Default map width set to _500px_.

	Property: height
		public, string - Default map height set to _300px_.

	Property: zoom
		public, int - Default zoom level set to _13_.

	Property: auto_zoom
		public, bool - Auto zoom and center map depending on markers. Default set to _false_.

	Property: controls
		public, bool - Show zoom and pan controls. Default set to _true_.

	Property: controls_size
		public, string - Map controls sizes _large_, _small_, _tiny_.

	Property: scale_control
		public, bool - Mile/km indicator. Default set to _true_.

	Property: type_control
		public, string - Map types _false_, _map_, _satellite_, _hybrid_.

	Property: overview_control
		public, string - A collapsible overview map in the corner of the screen. Default set to _false_.

	Property: markers
		public, array - Array of markers.

	Property: icons
		public, array - Array of icons.

	Property: listeners
		public, array - Array of listeners.

	Property: open_at
		public, string - Name of marker that will be opened on map load.

	Property: geocoder
		public, bool - Aet GClientGeocoder object. Ddefault set to _false_.

	Property: markers_object
		public, bool - Aet this.Markers for javascript class. Default set to _false_.

	Property: key
		private, string - Google Maps API Key

	Property: id
		private, string - GMap object ID

	*/
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
	public $geocoder			= false; 		// set GClientGeocoder object
	public $markers_object		= false; 		// set this.Markers for javascript class
	
	// private properties
	private $key;								// Google Maps API Key
	private $id;								// GMap object ID
	
	// Section: Public
	
	/*
	
	Function: __construct
		Class construct. You have the option to pass the Google Maps API key and DOM ID
		when initializing the class.
	
	Parameters:
		key - string optional <set_key>
		id - string optional <set_id>
	
	Return:
		null
	
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
	
	/*
	
	Function: set_key
		Set Google Maps API key. Required to use google maps.
	
	Parameters:
		key - string required
	
	Return:
		null
	
	See Also:
		http://www.google.com/apis/maps/signup.html
	
	*/
	
	public function set_key($key)
	{
		$this->key = $key;
	}
	
	/*
	
	Function: set_id
		Set DOM ID for map.
	
	Parameters:
		id - string required
	
	Return:
		null
	
	*/
	
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	/*
	
	Function: display_map
		Google Maps RUN-TIME. Creates javascript code and map ojects used by this class.
		Outputs to screen where ever called.
	
	Parameters:
		html_options - array optional
	
	Return:
		string
	
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
	<?=$this->_extend_gmap()?>
	
	// global vairables
	var <?=$this->id?>;
	<? if ( $this->geocoder ) echo "var geocoder;\n"; ?>
	
	// google map object
	function <?=$this->id?>Obj()
	{
		
		// properties
		<?=$this->gmap()?>
		<? if ( $this->geocoder ) echo "geocoder = new GClientGeocoder();\n"; ?>
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
		?>
		<?
			if ( count($this->markers) || $this->markers_object ) {
				echo "// markers\n";
				echo "\t\tthis.Markers = new Object();\n";
				if ( count($this->markers) ) foreach ( $this->markers as $marker => $result ) echo $this->create_marker($result['name'], $result)."\n";
			}
			if ( ( $this->auto_zoom || $this->open_at ) && count($this->markers) ) echo "\n\t\t// extra work\n";
			if ( $this->auto_zoom && count($this->markers) ) echo "\t\tthis.GMap.autoZoom(this.Bounds);\n";
			if ( $this->open_at && count($this->markers) ) echo "\t\tthis.GMap.openWindow(this.Markers.{$this->open_at});\n";
		?>		
	}
	
	<?=$this->onload("{$this->id} = new {$this->id}Obj();")?>
	<?=$this->unload()?>
	
}
<?=$this->_display_warning_if_not_compatible()?>
//]]>
</script>
<div id="<?=$this->id?>"<?=html_options_str($html_options)?>></div>
		<?
	}
	
	/*
	
	Function: include_api
		Create JavaScript include string for Google Maps API.
	
	Return:
		string
	
	*/
	
	public function include_api()
	{
		static $return;
		if ( !$return ) {
			return $return = '<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=' . $this->key . '"></script>'."\n";
		}
	}
	
	/*
	
	Function: gmap
		Create GMap2 object.
	
	Parameters:
		container - string required did id of map
		options - array optional not being used
	
	Return:
		string
	
	*/
	
	public function gmap($container = null, $options = null)
	{
		$id = $container ? $container : $this->id;
		return 'this.GMap = new GMap2(document.getElementById("' . $id . '"));'."\n";
	}
	
	/*
	
	Function: glatlng
		Create GLatLng (geographical coordinates longitude and latitude) object.
	
	Parameters:
		latitude - string required
		longitude - string required
	
	Return:
		string
	
	*/
	
	public function glatlng($latitude, $longitude)
	{
		return 'new GLatLng(' . (float) $latitude . ', ' . (float) $longitude . ')';
	}
	
	/*
	
	Function: set_center
		Set center point of map.
	
	Parameters:
		latitude - string required
		longitude - string required
		zoom - int optional default set to 4
		map_type - string optional 'map', 'satellite', 'hybrid'
	
	Return:
		string
	
	*/
	
	public function set_center($latitude, $longitude, $zoom = 4, $map_type = null)
	{
		return 'this.GMap.setCenter(' . $this->glatlng($latitude, $longitude) . ', ' . (int) $zoom . ( $map_type ? ', ' . $map_type : '' ) . ");\n";
	}
	
	/*
	
	Function: pan_to
		Pan map to a geographical coordinates longitude and latitude.
	
	Parameters:
		latitude - string required
		longitude - string required
	
	Return:
		string
	
	*/
	
	public function pan_to($latitude, $longitude)
	{	
		return 'this.GMap.panTo(' . $this->glatlng($latitude, $longitude) . ");\n";
	}
	
	/*
	
	Function: add_control
		Add a control to map.
	
	Parameters:
		control - string required
		position - string optional
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GControl
		
	*/
	
	public function add_control($control, $position = null)
	{
		return 'this.GMap.addControl('. $control .");\n";
	}
	
	/*
	
	Function: create_marker
		Create a GMarker object.
	
	Parameters:
		name - string required
		latitude - string/array required
		longitude - string optional
		icon - string optional
		html_or_tabs - string optional
		onclick - bool optional
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GMarker
		
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
	
	/*
	
	Function: create_icon
		Create a GIcon object.
	
	Parameters:
		name - string required
		data - array required
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GIcon
		
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
	
	/*
	
	Function: create_listener
		Create a GEvent listner object.
	
	Parameters:
		source - string required
		event - string required
		handler - string optional
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GEvent
		
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
	
	/*
	
	Function: gpoint
		Create a GPoint object.
	
	Parameters:
		x - int required
		y - int optional
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GPoint
		
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
	
	/*
	
	Function: gsize
		Create a GSize object.
	
	Parameters:
		width - int required
		height - int optional
	
	Return:
		string
	
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GSize
		
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
	
	/*
	
	Function: add
		Alias to add_marker().
	
	Parameters:
		marker - array required
	
	See Also:
		<add_marker>
		
	*/
	
	public function add($marker)
	{
		$this->add_marker($marker);
	}
	
	/*
	
	Function: add_marker
		Add a marker to class markers array.
	
	Parameters:
		marker - array required
		
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
	
	/*
	
	Function: add_listener
		Add a listener to class listeners array.
	
	Parameters:
		args - array required
		
	*/
	
	public function add_listener($args)
	{
		$this->listeners[] = $args;
	}
	
	/*
	
	Function: get_controls
		Add an icon to class icons array.
	
	Parameters:
		args - array required
		
	*/
	
	public function add_icon($args)
	{
		$this->icons[] = $args;
	}
	
	/*
	
	Function: get_controls
		Get this map's control size ( 'tiny' = GSmallZoomControl, 'small' = GSmallMapControl, 'large' = GLargeMapControl ).
	
	Returns:
		string
		
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
	
	/*
	
	Function: get_map_type
		Get this map's type constant ( 'map' = G_NORMAL_MAP, 'satellite' = G_SATELLITE_MAP, 'hybrid' = G_HYBRID_MAP ).
	
	Returns:
		string
		
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

	/*
	
	Function: onload
		Add javascript to window.onload function.
	
	Parameters:
		js_str - string required
		
	Returns:
		string
		
	*/
	
	public function onload($js_str)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.onload = new Function("' . $code . '");'."\n";
	}
	
	/*
	
	Function: unload
		Add javascript to window.unload function.
	
	Parameters:
		js_str - string optional
		
	Returns:
		string
		
	*/
	
	public function unload($js_str = null)
	{
		static $code;
		$code = ( $js_str ? $code.$js_str : $js_str );
		return 'window.unload = new Function("GUnload();' . $code . '");'."\n";
	}
	
	/*
	
	Function: geocode_address
		Get coordinates from address provided.
	
	Parameters:
		address - string required
		
	Returns:
		array
		
	*/
	
	public function geocode_address($address)
	{
		$temp = $this->geocode_http($address);
		$coordinates['latitude'] = $temp['Latitude'];
		$coordinates['longitude'] = $temp['Longitude'];
		return $coordinates;
	}
	
	/*
	
	Function: geocode_http
		Geocode through google using HTTP Request and return a formatted array.
	
	Parameters:
		address - string required
		
	Returns:
		array
		
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
	
	/*
	
	Function: get_status_description
		Returns Address Accuracy Description from is numeric equivalent.
	
	Parameters:
		code - integer required
		
	Returns:
		string
		
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode
	
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
	
	/*
	
	Function: get_accuracy_description
		Returns Address Accuracy Description from is numeric equivalent.
	
	Parameters:
		code - integer required
		
	Returns:
		string
		
	See Also:
		http://www.google.com/apis/maps/documentation/reference.html#GGeoAddressAccuracy
	
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
	
	// Section: Private
	
	/*
	
	Function: _extend_gmap
		Add additional functionallty to GMap2 object.
	
	Returns:
		string
	
	*/
	
	private function _extend_gmap()
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
	
	/*
	
	Function: _display_warning_if_not_compatible
		Alert user if browser not compatible with Google Maps.
	
	Returns:
		string
	
	*/
	
	private function _display_warning_if_not_compatible()
	{
		static $return;
		return $return = ( !$return ? 'if ( !GBrowserIsCompatible() ) alert("Attention: Unable to display map!\nYour current browser is not compatible with Google Maps.\nPlease upgrade to Firefox 1.+ or Internet Explorer 6.+");'."\n" : '' );
	}
	
}
?>
