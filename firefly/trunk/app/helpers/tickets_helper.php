<?

function filter_link($filter, $value, $params)
{
	$items['milestone'] = $params['milestone'];
	$items['status'] = $params['status'];
	$items['part'] = $params['part'];
	$items['severity'] = $params['severity'];

	$items[$filter] = $value;

	$link = "?";
	foreach ($items as $k => $v) if ($v != '') $link .= "{$k}={$v}&";

	return substr($link, 0, -1);
}

?>
