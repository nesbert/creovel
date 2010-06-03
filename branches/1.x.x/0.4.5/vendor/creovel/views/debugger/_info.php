<?php
/**
 * Partial view used by Creovel to display application information.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  Views
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
?><div class="block title">
    <h1>creovel <?php echo CREOVEL_VERSION; ?></h1>
</div>

<table cellspacing="0" class="block">
<tr><td class="sub">Version</td><td><?php echo CREOVEL_VERSION; ?></td></tr>
<tr><td class="sub">Release Date</td><td><?php echo CREOVEL_RELEASE_DATE; ?></td></tr>
<tr>
    <td class="sub">Registered Adapters</td>
    <td>
        <em>
        <?php if ($adapters = get_creovel_adapters()) foreach ($adapters as $name => $file) { ?>
            <?php
                // skip these
                switch ($name) {
                    case 'adapter_base':
                    case 'adapter_interface':
                        continue 2;
                }
            ?>
            <?php if (CREO('show_source')) { ?>
            <a href="<?php echo view_source_url($file); ?>"><?php echo classify($name); ?></a>
            <?php } else { ?>
            <?php echo classify($name); ?>
            <?php } ?>
        <?php } else { ?>
            Not Available
        <?php } ?>
        </em>
    </td>
</tr>
<tr>
    <td class="sub">Registered Modules</td>
    <td>
        <em>
        <?php if ($services = get_creovel_modules()) foreach ($services as $name => $file) { ?>
            <?php
                // skip these
                switch ($name) {
                    case 'module_base':
                        continue 2;
                }
            ?>
            <?php if (CREO('show_source')) { ?>
            <a href="<?php echo view_source_url($file); ?>"><?php echo classify($name); ?></a>
            <?php } else { ?>
            <?php echo classify($name); ?>
            <?php } ?>
        <?php } else { ?>
            Not Available
        <?php } ?>
        </em>
    </td>
</tr>
</table>

<h1>Environment</h1>
<table cellspacing="0" class="block environment">
<tr><td class="sub">Mode</td><td><?php echo CREO('mode'); ?></td></tr>
<?php if (count(CREO('routing'))) { ?>
    <?php $routing = CREO('routing'); ?>
<tr>
    <td class="sub">Routes</td>
    <td>
        <?php foreach ($routing['routes'] as $name => $route) { ?>
        <dl>
            <dt><?php echo $route['name']; ?> (<?php echo $route['url']; ?>)</dt>
        </dl>
        <?php } ?>
    </td>
</tr>
<?php } ?>
<?php if (count($GLOBALS['CREOVEL']['DATABASES'])) { ?>
<tr>
    <td class="sub">Database Settings</td>
    <td>
        <?php foreach ($GLOBALS['CREOVEL']['DATABASES'] as $mode => $data) { ?>
        <dl>
            <dt><?php echo strtolower($mode); ?></dt>
            <?php if (count($data)) foreach($data as $key => $val) { ?>
            <dd><?php echo strtolower($key); ?> =&gt; <?php echo (strtoupper($key) == 'PASSWORD' ? mask('hidelength') : $val); ?></dd>
            <?php } ?>
        </dl>
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>

<?php if (isset($_GET) && count($_GET)) { ?>
<h1>$_GET</h1>
<?php
$data = $_GET;
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>
<?php } ?>

<?php if (isset($_POST) && count($_POST)) { ?>
<h1>$_POST</h1>
<?php
$data = $_POST;
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>
<?php } ?>

<?php if (isset($_COOKIE) && count($_COOKIE)) { ?>
<h1>$_COOKIE</h1>
<?php
$data = $_COOKIE;
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>
<?php } ?>

<?php if (isset($_SESSION)) { ?>
<h1>$_SESSION</h1>
<?php
$data = $_SESSION;
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>
<?php } ?>

<h1>$_SERVER</h1>
<?php
$data = $_SERVER;
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>

<h1>Constants</h1>
<?php
$data = get_user_defined_constants();
include CREOVEL_PATH.'views'.DS.'debugger'.DS.'_info_table.php';
?>


<h1>Files</h1>
<table cellspacing="0" class="block constants">
<?php $total_filesize = 0; ?>
<?php foreach(get_included_files() as $file => $value) { ?>
<?php $total_filesize += filesize($value); ?>
<tr>
    <td class="sub"><?php print($file + 1); ?>.</td>
    <td>
        <?php if (CREO('show_source')) { ?>
        <a href="<?php echo view_source_url($value); ?>"><?php echo $value; ?> (<?php echo get_filesize($value); ?>)</a>
        <?php } else { ?>
        <?php echo $value; ?> (<?php echo get_filesize($value); ?>)
        <?php } ?>
    </td>
</tr>
<?php } ?>
<tr><td class="sub">Total</td><td><?php print($file + 1); ?> Files (<?php echo get_filesize($total_filesize); ?>)</td></tr>
</table>
