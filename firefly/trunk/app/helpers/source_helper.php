<?

function parse_path($revision, $path)
{
	$path = str_replace('//', '/', $path);

	foreach(explode('/', $path) as $dir)
	{
		$curr_path .= "{$dir}/";
		echo "<a href=\"?revision={$revision}&path={$curr_path}\">{$dir}</a>/";
	}
}

?>
