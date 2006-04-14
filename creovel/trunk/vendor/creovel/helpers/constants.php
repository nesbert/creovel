<?php
/**
 * Common contants declared here.
 */

define(VERSION, 0.01);
define(RELEASE_DATE, '11/24/05');

define(DS, DIRECTORY_SEPARATOR);
define(ROOT, dirname(dirname(__FILE__)).DS);
define(SECOND,  1);
define(MINUTE, 60 * SECOND);
define(HOUR,   60 * MINUTE);
define(DAY,    24 * HOUR);
define(WEEK,    7 * DAY);
define(MONTH,  30 * DAY);
define(YEAR,  365 * DAY);
?>