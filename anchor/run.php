<?php

/*
 * Set your applications current timezone
 */
date_default_timezone_set(Config::app('timezone', 'UTC'));

/*
 * Define the application error reporting level based on your environment
 */
switch(constant('ENV')) {
	case 'dev':
		ini_set('display_errors', true);
		error_reporting(-1);
		break;

	default:
		error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
}

/*
 * Set autoload directories to include your app models and libraries
 */
Autoloader::directory(array(
	APP . 'models',
	APP . 'libraries'
));

/**
 * Helpers
 */
function __($line, $fallback = '') {
	$args = array_slice(func_get_args(), 1);

	return Language::line($line, $fallback, $args);
}

function is_admin() {
	return strpos(Uri::current(), 'admin') === 0;
}

function is_installed() {
	return Config::get('db') !== null;
}

function slug($str, $separator = '-') {
	$str = normalize($str);

	// replace non letter or digits by separator
	$str = preg_replace('#[^\\pL\d]+#u', $separator, $str);

	return trim(strtolower($str), $separator);
}

function parse($str, $markdown = true) {
	// process tags
	$pattern = '/[\{\{]{1}([a-z]+)[\}\}]{1}/i';

	if(preg_match_all($pattern, $str, $matches)) {
		list($search, $replace) = $matches;

		foreach($replace as $index => $key) {
			$replace[$index] = Config::meta($key);
		}

		$str = str_replace($search, $replace, $str);
	}

	$str = html_entity_decode($str, ENT_NOQUOTES, System\Config::app('encoding'));

    //  Parse Markdown as well?
	if($markdown === true) {
	    $md = new Markdown;
	    $str = $md->transform($str);
	}

	return $str;
}

function readable_size($size) {
	$unit = array('b','kb','mb','gb','tb','pb');

	return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

/**
 * Anchor setup
 */
Anchor::setup();