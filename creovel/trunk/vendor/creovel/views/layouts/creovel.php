<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>creovel <?=get_version()?> - A PHP Framework</title>
	<style type="text/css">
	body { margin: 20px; color: #333;	background-color: #fff;	font: 75% 'Lucida Grande', Geneva, Verdana, Arial, Helvetica, sans-serif; }
	a { color: #333; }
	a:hover { text-decoration: none; background-color: #e7e29a; }
	h1, p, ul { margin: 0 0 20px 0; padding: 0; color: #000; }
	h1 { margin-bottom: 0; font: 175% bold 'Myriad Apple', 'Lucida Grande', Geneva, Verdana, Arial, Helvetica, sans-serif; }
	ul.debug { padding: 0; list-style: none; }
	ul.debug li { margin-bottom: 2px; }
	
	.block { margin: 0 0 20px 0; padding: 0; background: #f9f6f3; border: 1px solid #3b3b3b; text-align: left; }
	.block h1 { padding: 8px; }
	.block td { padding: 4px 8px; border: 1px solid #3b3b3b; border-top: none; border-left: none; }
	.block td.sub { width: 200px; background-color: #ccc; font-weight: bold; vertical-align: top; }
	.block dl { margin: 0 0 8px 0; }
	.block dd { font-style: italic; }
	table.block { width: 100%; border-right: none; border-bottom: none; }
	
	.code { display: block; overflow: auto; margin: 0 0 10px 0; padding: 8px; background: #ccc; border: 1px dashed #333; text-align: left; font-family: monaco, 'Courier New', courier, monospace; font-size: 100%; }
	.code table.source td { white-space: nowrap; padding: 0; }
	.title { background-color: #aaa099; }
	
	h1.top { font-size: 250%; color: #b2a7a3; }
	p.top { font-size: 125%; color: #333; }
	
	.red { color: red; }	
	</style>
	<script type="text/javascript">
	function _Toggle(id){
		var obj = document.getElementById(id);
		if ( obj.style.display == 'none' ) {
			obj.style.display = '';
		} else {
			obj.style.display = 'none';
		}	
	}	
	</script>
	
</head>
<body>


<div id="wrapper">

	<div id="header"><a name="top"></a></div>
	
	
	<div id="content">
@@page_contents@@
	</div>
	
	<div id="footer">creovel <?=get_version()?> Copyright &copy; 2005-<?=date(Y)?> <a href="http://creovel.org">creovel.org</a> - A PHP Framework</div>

</div>

</body>
</html>