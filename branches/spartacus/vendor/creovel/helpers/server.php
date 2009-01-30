<?php
/**
 * General server/networking functions.
 *
 * @package     Creovel
 * @subpackage  Creovel.Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
 **/

/**
 * Returns browser's IP address.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function ip()
{
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Return the http: or https: depending on environment.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http()
{
    return 'http'.(getenv('HTTPS') == 'on' ? 's' : '').'://';
}

/**
 * Returns the current server host.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function host()
{
    return $_SERVER['HTTP_HOST'];
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http_host()
{
    return http() . host();
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function url()
{
    return http_host() . $_SERVER['REQUEST_URI'];
}

/**
 * Returns the current server domain.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function domain()
{
    $url = explode('.', host());
    $tld = explode(':', $url[count($url) - 1]);
    return $url[count($url) - 2] . '.' . $tld[0];
}

/**
 * A top-level domain (TLD), sometimes referred to as a top-level domain name
 * (TLDN), is the last part of an Internet domain name; that is, the letters
 * that follow the final dot of any domain name. For example, in the domain
 * name www.example.com, the top-level domain is "com".
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function tld()
{
    $url = explode('.', domain());
    return isset($url[1]) ? $url[1] : '';
}