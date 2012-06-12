<?php
/**
 * Server Side Analytics
 *
 * Server Side Analytics is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * @copyright  Copyright (c) 2009 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
	
class ssga {
	
	// Default values, can be overwritten
	private static $default_account = '';
	private static $default_version = '4.3';
	private static $default_charset = 'UTF-8';
	private static $default_lang = 'en-US';
	private static $utm_gif = 'https://www.google-analytics.com/__utm.gif';
	
	// Manually set data ends up here. (account, version, hostname, charset, language)
	private static $set_data = array();

	public static function event($category, $action, $label = '', $value = '')
	{
		$category = (string) $category;
		$action = (string) $action;
		
		if ($label)
		{
			$label = '*'. ((string) $label);
		}
		
		if ($value)
		{
			$value = '('.((int) intval($value)).')';
		}

		// Google Analytics account
		$parameters['utmac'] = (isset(self::$set_data['utmac'])) ? self::$set_data['utmac'] : self::$default_account ;
		
		// Analytics version
		$parameters['utmwv'] = (isset(self::$set_data['utmwv'])) ? self::$set_data['utmwv'] : self::$default_version;
	
		// Host name
		$parameters['utmhn'] = (isset(self::$set_data['utmhn'])) ? self::$set_data['utmhn'] : $_SERVER['HTTP_HOST'];
		
		// Charset
		$parameters['utmcs'] = (isset(self::$set_data['utmcs'])) ? self::$set_data['utmcs'] : self::$default_charset;
		
		// Language
		$parameters['utmul'] = (isset(self::$set_data['utmul'])) ? self::$set_data['utmul'] : self::$default_lang;
		
		// Analytics type (event)
		$parameters['utmt'] = 'event';
		
		// Event String
		$parameters['utme']	= "5({$category}*{$action}{$label}{$value}";
		
		// Random number
		$parameters['utmn']	= mt_rand(100000000,999999999);

		// Random number (unique for all session requests)
		$parameters['utmhid'] = mt_rand(100000000,999999999);
		
		// Cookie Data
		$parameters['utmcc'] = self::_cookie_data();

		self::_curl($parameters);
	}
	
	public static function page_view($page_view,$page_title)
	{
		// Google Analytics account
		$parameters['utmac'] = (isset(self::$set_data['utmac'])) ? self::$set_data['utmac'] : self::$default_account ;
		
		// Analytics version
		$parameters['utmwv'] = (isset(self::$set_data['utmwv'])) ? self::$set_data['utmwv'] : self::$default_version;
	
		// Host name
		$parameters['utmhn'] = (isset(self::$set_data['utmhn'])) ? self::$set_data['utmhn'] : $_SERVER['HTTP_HOST'];
		
		// Charset
		$parameters['utmcs'] = (isset(self::$set_data['utmcs'])) ? self::$set_data['utmcs'] : self::$default_charset;
		
		// Language
		$parameters['utmul'] = (isset(self::$set_data['utmul'])) ? self::$set_data['utmul'] : self::$default_lang;
		
		// Page title
		$parameters['utmdt'] = (string) $page_title;
		
		// Page view
		$parameters['utmp']	= (string) $page_view; 
		
		// Random number
		$parameters['utmn']	= mt_rand(100000000,999999999);

		// Random number (unique for all session requests)
		$parameters['utmhid'] = mt_rand(100000000,999999999);
		
		// Cookie Data
		$parameters['utmcc'] = self::_cookie_data();
		
		self::_curl($parameters);
	}
	
	// Google Analytics account
	public static function set_account($account)
	{ 
		self::$set_data['utmac'] = $account;
	}
	
	// Analytics version
	public static function set_version($version)
	{ 
		self::$set_data['utmwv'] = $version;
	}

	// Host name
	public static function set_hostname($hostname)
	{
		self::$set_data['utmhn'] = $hostname;
	}
	
	// Charset
	public static function set_charset($charset)
	{
		self::$set_data['utmcs'] = $charset;
	}
	
	// Language
	public static function set_language($language)
	{
		self::$set_data['utmul'] = $language;
	}

	private static function _cookie_data()
	{
		$today = time();
		
		$num_1 = rand(10000000,99999999);
		$num_2 = rand(1000000000,2147483647);
		
		return "__utma=1.{$num_1}00145214523.{$num_2}.{$today}.{$today}.15;+"
			  ."__utmz=1.{$today}.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none);";
	}
	
	private static function _curl($parameters)
	{
		$c = curl_init();
		
		curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($c, CURLOPT_RETURNTRANSFER,	TRUE);
		curl_setopt($c, CURLOPT_URL, self::$utm_gif.'?'.http_build_query($parameters));
		
		if (isset($_SERVER['HTTP_REFERER']))
		{
			curl_setopt($c, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
		}
		
		curl_exec($c);
		curl_close($c);
	}
}      

/* End of file ssga.php */