<?php
/**
 * General server/networking functions.
 *
 * @package Creovel
 * @subpackage Creovel.Helpers
 **/

/**
 * Returns browser's IP address.
 *
 * @return string
 */
function ip()
{
	return $_SERVER['REMOTE_ADDR'];
} 

/**
 * Return the http: or https: depending on environment.
 *
 * @return string
 */
function http()
{
	return 'http'.( getenv('HTTPS') == 'on' ? 's' : '' ).'://';
}

/**
 * Returns the current server host.
 *
 * @return string
 */
function host()
{
	return $_SERVER['HTTP_HOST'];
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 */
function http_host()
{
	return http() . host();
}

/**
 * Returns the current server domain.
 *
 * @return string
 */
function domain()
{
	$url = explode('.', host());
	return $url[count($url) - 2] . '.' . $url[count($url) - 1];
}