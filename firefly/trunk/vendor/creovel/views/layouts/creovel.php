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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>creovel <?=get_version()?> - A PHP Framework</title>
	<style type="text/css">
	#creovel { margin: 20px; color: #333; background-color: #fff; font: 75% 'Lucida Grande', Geneva, Verdana, Arial, Helvetica, sans-serif; }
	#creovel a { color: #333; }
	#creovel a:hover { text-decoration: none; background-color: #e7e29a; }
	#creovel h1, #creovel p, #creovel ul { margin: 0 0 20px 0; padding: 0; color: #000; }
	#creovel h1 { margin-bottom: 0; font: 175% bold 'Myriad Apple', 'Lucida Grande', Geneva, Verdana, Arial, Helvetica, sans-serif; }
	#creovel h1.top { font-size: 250%; color: #b2a7a3; }
	#creovel p.top { font-size: 125%; color: #333; }
	#creovel ul.debug { padding: 0; list-style: none; }
	#creovel ul.debug li { margin-bottom: 2px; }
	
	#creovel .block { margin: 0 0 20px 0; padding: 0; background: #f9f6f3; border: 1px solid #3b3b3b; text-align: left; }
	#creovel .block h1 { padding: 8px; }
	#creovel .block td { padding: 4px 8px; border: 1px solid #3b3b3b; border-top: none; border-left: none; }
	#creovel .block td.sub { width: 200px; background-color: #ccc; font-weight: bold; vertical-align: top; }
	#creovel .block dl { margin: 0 0 8px 0; }
	#creovel .block dd { font-style: italic; }
	#creovel table.block { width: 100%; border-right: none; border-bottom: none; }
	
	#creovel .code { display: block; overflow: auto; margin: 0 0 10px 0; padding: 8px; background: #ccc; border: 1px dashed #333; text-align: left; font-family: monaco, 'Courier New', courier, monospace; font-size: 100%; }
	#creovel .code table.source td { white-space: nowrap; padding: 0; }
	#creovel .title { background-color: #aaa099; }
	
	#creovel .red { color: red; }
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
<body id="creovel">


<div id="wrapper">

	<div id="header"><a name="top"></a></div>	
	
	<div id="content">
@@page_contents@@
	</div>
	
	<div id="footer">creovel <?=get_version()?> Copyright &copy; 2005-<?=date(Y)?> <a href="http://creovel.org">creovel.org</a> - A PHP Framework</div>

</div>

</body>
</html>