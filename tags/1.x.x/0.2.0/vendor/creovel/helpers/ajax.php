<?php
/*

Script: ajax
	AJAX methods go here. The following files should be included: prototype.js, dragdrop.js, controls.js, effects.js. 
 
*/

/*

Function: link_to_remote
	Creates an AJAX link tag. An AJAX call will be executed on every click.

	(start code)
		<?=link_to_remote('Go AJAK Go!!!', 'results', url_for('company', 'ajax_search') )?>
 		<div id="results"></div>
	(end)

Parameters:
	link_title - optional defaults to "Goto"
	id_to_update - required
	url - required
	html_options - optional

*/
 
function link_to_remote($link_title = 'Goto', $id_to_update, $url, $html_options = null)
{
	if ( is_array($html_options) ) foreach ($html_options as $attribute => $value ) {
		if ( strstr($attribute, 'onclick') ) { continue; }
		$extra	.= ' '.$attribute.'="'.$value.'"';
	}
	return "<a href=\"#\" onclick=\"new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true}); return false;\">".$link_title."</a>\n";
}

/*

Function: form_remote_tag
	Creates an AJAX form starter tag. This form will submited using AJAX via post.

	(start code)
		<?=form_remote_tag('results', '/company/ajax_search/')?>
			First Name: <input type="text" name="agent[name_first]" value=""  /><br />
			Last Name: <input type="text" name="agent[name_last]" value=""  /><br />
			Email: <input type="text" name="agent[email]" value=""  /><br />
			<input type="submit" value="Go AJAX go!!!" />
		<?=end_form_tag()?>

		<h1>Search Results</h1>
		<div id="results"></div>
	(end) 

Parameters:
	id_to_update - required
	url - required

*/
 
function form_remote_tag($id_to_update, $url)
{
	return "<form action=\"".$url."\" method=\"post\" onsubmit=\"new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true, parameters:Form.serialize(this)}); return false;\">\n";
}

/*

Function: observe_field
	Creates an observer for a form field. Any time the field being observed is modified it does an ajax call. Example:
	
	(start code)
		<input type="text" id="search" name"search" value="" />
		<?= observe_field('search', 0.5, 'results', '/search/ajax_search/') ?>

		<h1>Search Results</h1>
		<div id="results"></div>
	(end) 

Parameters:
	id_of_field - required
	frequency - optional default set to 0
	id_to_update - required
	url - required

*/
 
function observe_field($id_of_field, $frequency = 0, $id_to_update, $url, $form = false)
{
 	if ( !is_numeric($frequency) ) {
		$frequency = 0;
	}
	if ($form) {
		$params = 'Form.serialize(element.form)';
	} else {
		$params = "element.name+'='+value";
	}
 	return "<script type=\"text/javascript\">new Form.Element.Observer('".$id_of_field."', ".$frequency.", function(element, value) {new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true, parameters:".$params."})})</script>\n";
}

/*

Function: periodically_call_remote
	Creates an AJAX object that does period calls depending on the value of the frequency. The bottom example will call the controller "company" and action "ajax_search" every 5 seconds and update "results" div. 

	(start code)
		<?=periodically_call_remote('results', url_for('company', 'ajax_search'), 5)?>
		<div id="results"></div>
	(end)

Parameters:
	id_to_update - required
	url - required
	frequency - optional default set to 3

*/

function periodically_call_remote($id_to_update, $url, $frequency = 3)
{
	return "<script type=\"text/javascript\">new PeriodicalExecuter(function() {new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true})}, ".$frequency.")</script>";
}

/*

Function: create_sortable_list
	Creates an sortable list using dragdrop.js.

	(start code)	
		<ul id="shopping-list">
			<li id="li_1">Triscuit</li>
			<li id="li_2">Milk</li>
			<li id="li_3">Cake</li>
			<li id="li_4">Biscuit</li>
		</ul>
		<?= create_sortable_list('shopping-list', 'li') ?>
	(end)

	Note: To get the sortable list value you can use the function Sortable.serialize.

	(start code) 
		var list_order = Sortable.serialize('shopping-list');
	(end)

Parameters:	
	id_of_container - required
	list_tag - optional but is required if not using an array

*/

function create_sortable_list($id_of_container, $list_tag = null)
{
	$str .= "<script type=\"text/javascript\">\n";
	$str .= "// <![CDATA[\n";
	if ( is_array($id_of_container) ) {
		foreach ( $id_of_container as $id => $tag ) {
			$str .= "	Sortable.create('".$id."',{tag:'".$tag."'});\n";
		}
	} else {
		$str .= "	Sortable.create('".$id_of_container."',{tag:'".$list_tag."'});\n";
	}
	$str .= "// ]]>\n";
	$str .= "</script>\n";
	return $str;
}

/*

Function: create_popup
	Creates draggable popup.

	(start code)
		<?= create_popup('popup_id', 'Popup') ?>
	(end)

Parameters: 
	element_id - required
	title - optional default value is Popup

*/

function create_popup($element_id, $title = 'Popup')
{
	?>
<div class="popup" id="<?=$element_id?>_container" style="position:absolute; display:none;">

	<div class="title_bar" id="<?=$element_id?>_title_bar">
		<div class="title"><?=$title?></div>
		<div class="buttons"><a class="close" href="javascript: hidePopUp();"><span>Close Window</span></a></div>
	</div>
	
	<div class="body" id="<?=$element_id?>"></div>
	<div class="bottom"></div>
	
</div>
	<?php
}

?>