<?php

/**
 * AJAX methods go here. The following files should be included: prototype.js,
 * dragdrop.js, controls.js, effects.js. 
 */
 
/**
 * Creates an AJAX link tag. An AJAX call will be executed on every click.
 * Example:
 *
 * <code>
 * 	<?=link_to_remote('Go AJAK Go!!!', 'results', url_for('company', 'ajax_search') )?>
 *
 *	<div id="results"></div>
 * </code>
 *
 * @param string $link_title optional defaults to "Goto"
 * @param string $id_to_update required
 * @param string $url required
 * @param array $html_options optional
 */
 
function link_to_remote($link_title = 'Goto', $id_to_update, $url, $html_options = null) {

	if ( is_array($html_options) ){
	
		foreach ($html_options as $attribute => $value ) {
			if ( strstr($attribute, 'onclick') ) { continue; }
			$extra	.= ' '.$attribute.'="'.$value.'"';
		}
	
	}

	return "<a href=\"#\" onclick=\"new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true}); return false;\">".$link_title."</a>\n";
	
}

/**
 * Creates an AJAX form starter tag. This form will submited using AJAX
 * via post. Example:
 *
 * <code>
 * 	<?=form_remote_tag('results', '/company/ajax_search/')?>
 *		First Name: <input type="text" name="agent[name_first]" value=""  /><br />
 *		Last Name: <input type="text" name="agent[name_last]" value=""  /><br />
 *		Email: <input type="text" name="agent[email]" value=""  /><br />
 *		<input type="submit" value="Go AJAX go!!!" />
 *	<?=end_form_tag()?>
 *
 * 	<h1>Search Results</h1>
 *	<div id="results"></div>
 * </code>
 *
 * @param string $id_to_update required
 * @param string $url required
 */
 
function form_remote_tag($id_to_update, $url) {

	return "<form action=\"".$url."\" method=\"post\" onsubmit=\"new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true, parameters:Form.serialize(this)}); return false;\">\n";

}

/**
 * Creates an observer for a form field. Any time the field being observed is 
 * modified it does an ajax call. Example:
 *
 * <code>
 * 	<input type="text" id="search" name"search" value="" />
 *	<?= observe_field('search', 0.5, 'results', '/search/ajax_search/') ?>
 *
 * 	<h1>Search Results</h1>
 *	<div id="results"></div>
 * </code>
 *
 * @param string $id_of_field required
 * @param int $frequency optional default set to 0
 * @param string $id_to_update required
 * @param string $url required
 */
 
function observe_field($id_of_field, $frequency = 0, $id_to_update, $url) {
 
 	if ( !is_numeric($frequency) ) {
		$frequency = 0;
	}
	
 	return "<script type=\"text/javascript\">new Form.Element.Observer('".$id_of_field."', ".$frequency.", function(element, value) {new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true, parameters:value})})</script>\n";
 
}

/**
 * Creates an AJAX object that does period calls depending on the value of the frequency.
 * The bottom example will call the controller "company" and action "ajax_search" every
 * 5 seconds and update "results" div. 
 *
 * <code>
 * 	<?=periodically_call_remote('results', url_for('company', 'ajax_search'), 5)?>
 *
 *	<div id="results"></div>
 * </code>
 *
 * @param string $id_to_update required
 * @param string $url required
 * @param int $frequency optional default set to 3
 */

function periodically_call_remote($id_to_update, $url, $frequency = 3) {

	return "<script type=\"text/javascript\">new PeriodicalExecuter(function() {new Ajax.Updater('".$id_to_update."', '".$url."', {asynchronous:true, evalScripts:true})}, ".$frequency.")</script>";
	
}

/**
 * Creates an sortable list using dragdrop.js.
 *
 * <code>
 *	<ul id="shopping-list">
 *		<li id="li_1">Triscuit</li>
 *		<li id="li_2">Milk</li>
 *		<li id="li_3">Cake</li>
 *		<li id="li_4">Biscuit</li>
 *	</ul>
 *
 *	<?=create_sortable_list('shopping-list', 'li')?>
 * </code>
 *
 * Note:
 * To get the sortable list value you can use the function
 * Sortable.serialize. Example:
 *
 *	var list_order = Sortable.serialize('shopping-list');
 *
 * @param string/array $id_of_container required
 * @param int $list_tag optional but is required if not using an array
 */

function create_sortable_list($id_of_container, $list_tag = null) {

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

?>