<?php
/**
 * Layout used for debugging views.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title>creovel <?php echo CREOVEL_VERSION; ?> - A PHP Framework</title>
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
function $(id)
{
    return document.getElementById(id);
}

function toggle(id)
{
    var obj = $(id);
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
    
    <div id="footer">creovel <?php echo CREOVEL_VERSION; ?> Copyright &copy; 2005-<?=date('Y')?> <a href="http://creovel.org">creovel.org</a> - A PHP Framework</div>

</div>

</body>
</html>