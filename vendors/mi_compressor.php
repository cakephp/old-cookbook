<?php
/* SVN FILE: $Id: mi_compressor.php 939 2009-04-16 21:25:05Z ad7six $ */
/**
 * MiCompressor, a class used for shrinking CSS and JS files
 *
 * MiCompressor is a utility which serves 2 purposes:
 * 	Ask the class to return the request(s) necessary for a set of css/js files
 * 	Process a request for css/js file(s)
 *
 * PHP version 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.vendors
 * @since         v 1.0
 * @version       $Revision: 939 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-04-16 23:25:05 +0200 (Thu, 16 Apr 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * MiCompressor class
 *
 * Compress and minify multiple css and js files into a single file on demand.
 *
 * By default, in debug mode it only concatonates, in production mode contents are also runs the output through a
 * minifying routine. Some term definitions:
 * 	compress is used within the class to refer to data-compression (such as gzip etc.);
 * 	minify means stripping whitespace, rewriting to be less chars etc.
 *
 * This class is designed to be used with CakePHP but can be used alone (dependent on Minify only, if minified assets
 * don't already exist). If Minify can't be found minification will be skipped
 *
 * Note:
 * 	To avoid stale js/css files being served, The default salt is a constant SITE_VERSION (if defined). The salt
 * 	is used for the hash generation/comparison logic and allows you to avoid stale css/js e.g.:
 * 		define('SITE_VERSION', filemtime('somefilethatgetsupdatedwhenyoudeploy'));
 * 	In CakePHP a suggestion would be:
 * 		define('SITE_VERSION', filemtime('/app/config/bootstrap.php'));
 *	Or call this before using the class:
 * 		MiCompressor::config(array('salt' => 'some value'));
 *
 *
 * @abstract
 * @package       base
 * @subpackage    base.vendors
 */
abstract class MiCompressor {
/**
 * map property
 *
 * To allow disassociation from parameters used in the request, and file path locations
 *
 * @static
 * @var array
 * @access public
 */
	public static $map = array(
		'js' => array(
			'jquery' => array(
				'aplugin' => 'subfolder/path/here.js'
			)
		),
		'css' => array(
			'jquery' => array(
				'aplugin' => 'subfolder/path/here.css',
				'aplugin' => array(
					'subfolder/path/moreThan.css',
					'subfolder/path/oneFileRequired.css'
				)
			)
		)

	);
/**
 * settings property
 *
 * Active settings - edit/set/see via MiCompressor::config()
 *
 * @var array
 * @access protected
 */
	protected static $settings = array();
/**
 * defaultSettings property
 *
 * Don't edit this.
 * The settings which can be auto-set are set here, these settings are used as a fallback if a requested setting
 * hasn't been set, or the Configure class (if it exists) return null
 *
 * @var array
 * @access protected
 */
	protected static $defaultSettings = array(
		'MiCompressor.debug' => null,
		'MiCompressor.log' => null,
		'MiCompressor.cacheClear' => null,
		'MiCompressor.cacheDuration' => null,
		'MiCompressor.cacheDir' => null,
		'MiCompressor.salt' => null,
		'MiCompressor.bypassLoadMinifyLib' => null,
		'MiCompressor.minify' => null,
		'MiCompressor.minify.css' => null,
		'MiCompressor.minify.js' => null,
		'Asset.compress' => null,
	);
/**
 * initialized property
 *
 * Flag to know if the class variables have been initialized yet.
 *
 * @var bool false
 * @access protected
 */
	protected static $initialized = false;
/**
 * start property
 *
 * Start time
 *
 * @static
 * @var mixed null
 * @access protected
 */
	protected static $start = null;
/**
 * loadedFiles property
 *
 * To prevent a valid request which includes the same file twice (either expicitly or through @import logic)
 * this variable holds the names of the files already loaded for the current request
 *
 * @var array
 * @access protected
 */
	protected static $loadedFiles = array();
/**
 * config method
 *
 * Set/See settings
 *
 * @param array $settings array()
 * @param bool $reset false - reset to defaults before porcessing $settings?
 * @static
 * @return current settings
 * @access public
 */
	public static function config($settings = array(), $reset = false) {
		if ($reset) {
			MiCompressor::$settings = array();
		}
		return MiCompressor::$settings = array_merge(MiCompressor::$settings, $settings);
	}
/**
 * log method
 *
 * Record to the log (the head doc block in debug mode) or output the log (call with no params)
 *
 * @param mixed $string
 * @static
 * @return logs contents if requested, otherwise null
 * @access public
 */
	public static function log($string = null) {
		if (MiCompressor::$start === null) {
			MiCompressor::$start = getMicrotime();
		}
		static $log = array();
		if ($string === null) {
			$settings = MiCompressor::$settings;
			ksort($settings);
			foreach ($settings as $k => &$v) {
				$v = ' ' . str_pad(str_replace('MiCompressor.', '', $k), 15, ' ', STR_PAD_RIGHT) . "\t: " . $v;
			}
			$settings[] = '';
			$head = array_merge(array(
				'MiCompressor log - (only generated in debug mode, or if MiCompressor.log is set to true) ' . date("D, M jS Y, H:i:s"),
				null), $settings);
			$log = array_merge($head, $log);
			$return = "/**\r\n * " . implode("\r\n * ", $log) . "\r\n */\r\n";
			$log = array();
			return $return;
		}
		$time = getMicrotime() - MiCompressor::$start;
		$log[] = str_pad(number_format($time, 3, '.', ''), 6, ' ', STR_PAD_LEFT) . 's ' . $string;
	}
/**
 * minifyCss method
 *
 * Pass in a string of css, get out a minified version
 *
 * @param mixed $css
 * @param string $logPrefix the indent
 * @static
 * @return string minified css
 * @access public
 */
	public static function minifyCss($css, $logPrefix = '') {
		MiCompressor::log("{$logPrefix}minifying combined css file");
		return Minify_CSS::minify($css);
	}
/**
 * minifyJs method
 *
 * Pass in a string of javascript, and get out a minified version
 * This can be rather intensive - use with care
 *
 * @TODO don't strip license blocks
 * @param mixed $js
 * @param string $file
 * @param string $logPrefix the indent
 * @static
 * @return minified js
 * @access public
 */
	public static function minifyJs($js, $file = '', $logPrefix = '') {
		MiCompressor::log("$logPrefix	minifying $file.js");
		return JSMin::minify($js);
	}
/**
 * process method
 *
 * For each of the requested files, find them, concatonate them - if requested minify them - and return
 * For js files, each individual file is minifyed. For css, their combined contents are minifyed
 * Called internally by serve, public to allow other (external) parse logic if necessary
 *
 * @param mixed $files
 * @param mixed $type
 * @param mixed $minify
 * @static
 * @return string the files contents, optionally minifyed, as a string
 * @access public
 */
	public static function process($files, $type = null, $minify = null) {
		if ($type === null) {
			if (strpos($_GET['url'], 'js/') === 0) {
				$type = 'js';
			} else {
				$type = 'css';
			}
		}
		if ($minify === null) {
			$minify = MiCompressor::cRead('MiCompressor.minify.' . $type, 'minify');
		}

		if ($minify && !MiCompressor::loadMinifyLib($type)) {
			MiCompressor::log("PROBLEM: Unable to load $type . No minifying");
			$minify = false;
		}

		$files = (array)$files;
		$return = '';
		foreach ($files as $filename => $params) {
			if (is_string($params) && is_numeric($filename)) {
				$filename = $params;
				$params = array();
			}
			if (substr($filename, - strlen($type)) === $type) {
				$filename = substr($filename, 0, - strlen($type) - 1);
			}
			$iType = $type;
			$iType[0] = strtoupper($iType[0]);
			$iFilename = $filename;
			$iFilename[0] = strtoupper($iFilename[0]);
			$method = 'load' . $iType . $iFilename;
			if (method_exists('MiCompressor', $method)) {
				MiCompressor::log("Loading $filename.$type with method $method");
				$return .= MiCompressor::$method($params, $minify) . "\r\n";
			} elseif (strpos($filename, 'jquery.') === 0) {
				$method = "load{$iType}JqueryPlugin";
				MiCompressor::log("Loading $filename.$type with method $method");
				$return .= MiCompressor::$method(str_replace('jquery.', '', $filename), $minify) . "\r\n";
			} else {
				MiCompressor::log("Loading $filename.$type with method loadFile");
				$return .= MiCompressor::loadFile($filename, $params, $minify, $type) . "\r\n";

			}
		}
		if ($minify && $type === 'css') {
			$return = MiCompressor::minifyCss($return, "\t");
		}
		return $return;
	}
/**
 * serve. The main entry point for this class
 *
 * Parse the request, check the cache and send the content to the browser directly
 * If the request is invalid issue a 404 header and return no content
 * otherwise generate the content and send to the browser
 *
 * Example Cake use (contents of mini.css file):
 *  	App::import('Vendor', 'MiCompressor');
 *  	list($_, $request) = explode('?', $_SERVER['REQUEST_URI']);
 *  	echo MiCompressor::serve($request, 'css');
 *
 * Example Standalone use (contents of mini.css file):
 * 	$base = '/path/to/mi_compressor/containing/folder/';
 * 	include($base . 'mi_compressor.php');
 *      // Optional Start
 * 	ini_set('include_path', $base . 'minify/lib:' . ini_get('include_path'));
 * 	include($base . 'minify/lib/JSMin.php');
 * 	include($base . 'minify/lib/Minify/CSS.php');
 * 	include($base . 'minify/lib/HTTP/ConditionalGet.php');
 *      // Optional End
 * 	MiCompressor::config(array(
 * 		'MiCompressor.debug' => 0,
 * 		'MiCompressor.minify' => 1,
 *	));
 * 	echo MiCompressor::serve($_GET, 'css');
 *
 * @param string $request
 * @param mixed $type
 * @static
 * @return contents to be served up
 * @access public
 */
	public static function serve($request = '', $type = null) {
		MiCompressor::$loadedFiles = array();

		if (is_array($request)) {
			if (count($request) == 1 && !isset($request['request'])) {
				$hash = current($request);
				$request = key($request);
			} else {
				extract($request);
			}
		}
		MiCompressor::log('Request String: ' . $request);
		$start = getMicrotime();
		ob_start();
		if (MiCompressor::cRead('Asset.compress') && @ini_get("zlib.output_compression") != true && extension_loaded("zlib") &&
			(strpos(env('HTTP_ACCEPT_ENCODING'), 'gzip') !== false)) {
			MiCompressor::log('Enabling compression, turn on zlib.output_compression if possible');
			ob_start('ob_gzhandler');
		}
		$requests = MiCompressor::parse($request, $hash);

		if (!$requests) {
			header('HTTP/1.0 404 Not Found');
			return false;
		}
		if ($type === null) {
			if (strpos(ltrim($_GET['url'], '/'), 'js') === 0) {
				$type = 'js';
			} else {
				$type = 'css';
			}
		}
		if ($cachePath = MiCompressor::cachePath($type, $hash, true)) {
			MiCompressor::log('Cache Path: ' . $cachePath);
			if (MiCompressor::cRead('cacheClear')) {
				$path = MiCompressor::cRead('cacheDir') . $type . '_*';
				$files = glob($path);
				foreach ($files as $file) {
					MiCompressor::log("Removing Cache File " . str_replace(MiCompressor::cRead('cacheDir'), '', $file));
					@unlink($file);
				}
			}
			$cached = cache($cachePath, null, MiCompressor::cRead('cacheDuration'));
			if ($cached) {
				MiCompressor::log("Cache File: $cachePath found and returned");
				return MiCompressor::out($cachePath, $cached);
			}
		}
		$return = MiCompressor::process($requests, $type);
		$eTag = md5($return);
		if ($cachePath) {
			MiCompressor::log("Generating cache file $cachePath to expire in " . MiCompressor::cRead('cacheDuration'));
		}
		MiCompressor::log('eTag: ' . $eTag);
		MiCompressor::log('Finished');
		if (MiCompressor::cRead('log')) {
			$return = MiCompressor::log() . $return;
		}
		$return = '/* eTag:' . $eTag . ' */' . $return;
		// Slightly out of sequence so the cache file contains the log
		if ($cachePath) {
			cache($cachePath, $return, MiCompressor::cRead('cacheDuration'));
		}
		return MiCompressor::out(MiCompressor::cRead('cacheDir') . $cachePath, $return);
	}
/**
 * url method
 *
 * Generate the url(s) corresponding to the requested files
 *
 * @param array $request
 * @param array $params
 * @static
 * @return mixed string or array of strings correpsonding to the request
 * @access public
 */
	public static function url($request = array(), $params = array()) {
		extract(am(array(
			'sendAlone' => MiCompressor::cRead(),
			'type' => 'js',
			'sizeLimit' => false,
		), $params));
		$stack = array();
		$i = 0;
		foreach ($request as $key => &$value) {
			$_sendAlone = $sendAlone;
			if (is_string($key)) {
				if (is_array($value) && isset($value['sendAlone'])) {
					$_sendAlone = $value['sendAlone'];
					unset($value['sendAlone']);
				}
				if (!$value) {
					$value = $key;
				} elseif($type === 'js' && $key === 'jquery' && ($sizeLimit || $_sendAlone)) {
					$i++;
					$stack[$i][] = 'jquery';
					foreach ($value as $plugin) {
						$i++;
						$stack[$i][] = 'jquery.' . $plugin;
						$i++;
					}
					continue;
				} else {
					foreach ($value as $_k => &$_v) {
						if (!is_numeric($_k)) {
							$_v = $_k . '=' . $_v;
						}
					}
					$value = $key . ',' . implode(',', $value);
				}
			}
			if ($_sendAlone) {
				$i++;
			}
			$stack[$i][] = $value;
			if ($_sendAlone) {
				$i++;
			}
		}
		$return = array();
		foreach ($stack as &$files) {
			$url = MiCompressor::_url($files, $type, $sizeLimit);
			foreach((array)$url as $u) {
				$return[] = $u;
			}
		}
		if (count($return) === 1) {
			return $return[0];
		}
		return $return;
	}
/**
 * cachePath method
 *
 * @param mixed $type
 * @param mixed $hash
 * @param bool $relative return a relative or absolute path
 * @static
 * @return string the absolute/relative path to the corresponding cache file
 * @access protected
 */
	protected static function cachePath($type, $hash, $relative = false) {
		$debug = MiCompressor::cRead();
		$clear = MiCompressor::cRead('cacheClear');
		$return = $type . '_' . $hash . '_' . ($debug?'d':'p') . ($clear?'t':'p');
		$return = MiCompressor::cRead('cacheDir') . $return;
		if ($relative) {
			return str_replace(CACHE, '', $return);
		}
		return $return;
	}
/**
 * c(onfigure)Read method
 *
 * Write default settings as appropriate on first read
 * Use the Configure class if it exists, otherwise use default setting
 * First none-null result encountered wins.
 *
 * Call with multiple paramters naming fallback settings e.g.
 * $debug = MiCompressor::cRead('cacheClear', 'debug', 'Asset.someothersetting');
 * Again,  first none-null result wins.
 *
 * @param string $setting 'debug'
 * @param bool $prefix true
 * @static
 * @return mixed, config value
 * @access protected
 */
	protected static function cRead($setting = 'debug', $prefix = true) {
		if (!strpos($setting, '.') && $prefix) {
			$setting = 'MiCompressor.' . $setting;
		}
		if (!MiCompressor::$initialized) {
			MiCompressor::$initialized = true;

			$debug = MiCompressor::cRead('debug');
			if ($debug === null) {
				$debug = MiCompressor::cRead('debug', false);
			}
			if ($debug) {
				MiCompressor::$defaultSettings['MiCompressor.cacheDuration'] = '+10 minutes';
			} else {
				MiCompressor::$defaultSettings['MiCompressor.cacheDuration'] = '+1 year';
			}
			MiCompressor::$settings['MiCompressor.debug'] = $debug;
			unset (MiCompressor::$settings['debug']);

			MiCompressor::cRead('log', 'debug');
			MiCompressor::cRead('cacheClear', 'debug');

			$minify = MiCompressor::cRead('minify');
			if ($minify === null) {
				MiCompressor::$settings['MiCompressor.minify'] = !$debug;
			}

			$dir = MiCompressor::cRead('cacheDir');
			if (!$dir) {
				MiCompressor::$defaultSettings['MiCompressor.cacheDir'] = CACHE;
			}
			MiCompressor::$defaultSettings['MiCompressor.webrootDir'] = WWW_ROOT;
			MiCompressor::$defaultSettings['MiCompressor.cssDir'] = CSS;
			MiCompressor::$defaultSettings['MiCompressor.jsDir'] = JS;
			$salt = MiCompressor::cRead('salt');
			if (!$salt) {
				MiCompressor::$defaultSettings['MiCompressor.salt'] = SITE_VERSION;
			}

			if (!class_exists('App')) {
				MiCompressor::$defaultSettings['MiCompressor.bypassLoadMinifyLib'] = true;
			}
		}
		if (array_key_exists($setting, MiCompressor::$settings)) {
			return MiCompressor::$settings[$setting];
		}

		if (isset(MiCompressor::$defaultSettings[$setting])) {
			return MiCompressor::$settings[$setting] = MiCompressor::$defaultSettings[$setting];
		}

		if (class_exists('Configure')) {
			$return = Configure::read($setting);
			$fallbacks = func_get_args();
			array_shift($fallbacks);
			if ($return === null && $fallbacks) {
				$return = call_user_func_array(array('MiCompressor', 'cRead'), $fallbacks);
			}
			return MiCompressor::$settings[$setting] = $return;
		}

		$return = MiCompressor::$defaultSettings[$setting];
		$fallbacks = func_get_args();
		array_shift($fallbacks);
		if ($return === null && $fallbacks) {
			$return = call_user_func_array(array('MiCompressor', 'cRead'), $fallbacks);
		}
		return MiCompressor::$settings[$setting] = $return;
	}
/**
 * hash method
 *
 * Return the hash to be used for the passed arg
 *
 * @param mixed $arg array or string of requests
 * @static
 * @return string the hash of the passed arg
 * @access protected
 */
	protected static function hash($arg) {
		$salt = MiCompressor::cRead('salt');
		if (is_array($arg)) {
			$arg = implode('|', $arg);
		}
		if (function_exists('uses')) {
			uses('Security');
			return Security::hash($salt . $arg, null, true);
		}
		return sha1($salt . $arg);
	}
/**
 * loadMinifyLib method
 *
 * @param mixed $type
 * @static
 * @return bool true on success
 * @access protected
 */
	protected static function loadMinifyLib($type) {
		if (MiCompressor::cRead('bypassLoadMinifyLib')) {
			if (!class_exists('HTTP_ConditionalGet')) {
				MiCompressor::log('PROBLEM: HTTP_ConditionalGet (part of the Minify lib) not found, couldn\'t load the minify classes.');
				return false;
			}
			return true;
		}
		if (!class_exists('App')) {
			MiCompressor::log('PROBLEM: App class (part of CakePHP lib) not found, couldn\'t load the minify classes.');
		       	MiCompressor::log('	To Resolve set MiCompressor.bypassLoadMinifyLib to true and include the minify classes manually.');
			return false;
		}
		if($type === 'js') {
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/JSMin.php'));
		} elseif ($type === 'css') {
			ini_set('include_path', dirname(__FILE__) . '/minify/lib'. PATH_SEPARATOR . ini_get('include_path'));
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/Minify/CSS.php'));
		} else {
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/HTTP/ConditionalGet.php'));
		}
	}
/**
 * loadCssJqueryPlugin method
 *
 * @param string $plugin ''
 * @param bool $minify false
 * @return void
 * @access protected
 */
	protected static function loadCssJqueryPlugin($plugin = '', $minify = false) {
		if (isset(MiCompressor::$map['css']['jquery'][$plugin])) {
			$filename = MiCompressor::$map['css']['jquery'][$plugin];
		} else {
			$filename = "jquery.$plugin.css";
		}
		if (is_array($filename)) {
			$return = '';
			foreach($filename as $f) {
				$return .= MiCompressor::loadCssJqueryPlugin($f, $minify);
			}
			return $return;
		}
		if ($minify) {
			$found = true;
			$filename = str_replace('.css', '.min.css', $filename);
			if (file_exists(CSS . $filename)) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log('	File ' . CSS . "$filename found");
				echo file_get_contents(CSS . $filename);
			} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' =>	"css/jquery/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version css/jquery/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' =>	"css/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version css/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
			} else {
				$found = false;
			}
			if ($found) {
				return ob_get_clean();
			}
			$filename = str_replace('.min.css', '.css', $filename);
		}
		MiCompressor::log('	' . $filename);
		if (file_exists(CSS . $filename)) {
			MiCompressor::log('	File ' . CSS . "$filename found");
			echo file_get_contents(CSS . $filename);
		} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' =>	"css/jquery/$filename"))) {
			MiCompressor::log("	Found vendor version css/jquery/$filename for Jquery $plugin");
		} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' =>	"css/$filename"))) {
			MiCompressor::log("	Found vendor version css/$filename for Jquery $plugin");
		} elseif (App::import('Vendor', 'css/jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
			MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
		} else {
			MiCompressor::log("	PROBLEM: The jquery plugin jquery/plugins/$plugin/$filename could not be loaded.");
		}
		return ob_get_clean();

	}
/**
 * loadFile method
 *
 * For the requested file, find it and return it. if minify is set to true send through minify(Css|Js) as
 * appropriate first.
 *
 * For CSS files, check for @import declarations and auto-correct any url() references in the file
 *
 * @param string $filename
 * @param mixed $params
 * @param bool $minify
 * @static
 * @return string the file's contents if appropriate
 * @access protected
 */
	protected static function loadFile($filename = '', $params = array(), $minify = false, $type = 'js') {
		if ($filename[0] === '/') {
			$base = MiCompressor::cRead('webrootDir');
		} elseif ($type === 'js') {
			$base = MiCompressor::cRead('jsDir');
		} elseif($type === 'css') {
			$base = MiCompressor::cRead('cssDir');
		} else {
			var_dump($base); die;
		}
		$file = $base . $filename . '.' . $type;
		$minFile = $base . $filename . '.min.' . $type;
		if (in_array($filename . '.' . $type, MiCompressor::$loadedFiles)) {
			MiCompressor::log("File $filename.type already loaded");
			return;
		}
		MiCompressor::$loadedFiles[] = $filename . '.' . $type;

		if ($minify && file_exists($minFile)) {
			$file = $minFile;
			MiCompressor::log("File $file found");
			return file_get_contents($file);
		} elseif (file_exists($file)) {
			MiCompressor::log("File $file found");
			$return = file_get_contents($file);
		} else {
			if ($filename[0] === '/') {
				$vendorFile = substr($filename . '.' . $type, 1);
			} else {
				$vendorFile = $type . '/' . $filename . '.' . $type;
			}
			ob_start();
			if (class_exists('App') &&
				!App::import('Vendor', str_replace('.', '_', $filename . $type), array('file' => $vendorFile))) {
				$minify = false;
				MiCompressor::log("PROBLEM: No file for $vendorFile could be found");
			}
			$return = ob_get_clean();
		}
		if ($type === 'css') {
			if (strpos($filename, '/', 1)) {
				$baseFolder = dirname($filename) . '/';
			} else {
				$baseFolder = '';
			}
			preg_match_all('/@import\s*(?:url\()?(?:["\'])([^"\']*)\.css(?:["\'])\)?;/', $return, $matches);
			foreach ($matches[1] as $i => $cssFile) {
				if ($minify) {
					$return = str_replace($matches[0][$i], '', $return);
					$return .= MiCompressor::loadFile($baseFolder . $cssFile, $params, $minify, 'css');
				} elseif ($baseFolder) {
					$replace = str_replace($cssFile, $baseFolder . $cssFile, $matches[0][$i]);
					$return = str_replace($matches[0][$i], $replace, $return);
				}
			}
			if ($baseFolder && strpos($return, 'url')) {
				preg_match_all('@url\s*\((?:[\s"\']*)([^\s"\']*)(?:[\s"\']*)\)@', $return, $matches);
				$corrected = false;
				$urls = array_unique($matches[1]);
				foreach ($urls as $url) {
					if (strpos($url, $baseFolder) !== 0 && $url[0] !== '/') {
						$corrected = true;
						$return = str_replace($url, $baseFolder . $url, $return);
					}
				}
				if ($corrected) {
					MiCompressor::log("\t Auto corrected url paths in $filename");
				}
			}
		} elseif ($minify) {
			$minifyMethod = 'minify' . Inflector::camelize($type);
			return MiCompressor::$minifyMethod($return, $filename);
		}
		return $return;
	}
/**
 * Bespoke load method for Jquery.
 *
 * Allow for loading the distributed, already minifyed, version of jquery; and load plugins from
 * parameters for ease in views. requesting 'mini.js?...|jquery,abc,xyz|...' will load jquery, with the abc and xyz
 * plugins
 *
 * @param array $plugins
 * @param bool $minify
 * @static
 * @return void
 * @access protected
 */
	protected static function loadJsJquery($plugins = array(), $minify = false) {
		ob_start();
		if ($minify && file_exists(JS . 'jquery.min.js')) {
			MiCompressor::log('	File ' . JS . 'jquery.min.js found');
			echo file_get_contents(JS . 'jquery.min.js');
		} elseif (file_exists(JS . 'jquery.js')) {
			MiCompressor::log('	File ' . JS . 'jquery.js found');
			$return = file_get_contents(JS . 'jquery.js');
			if ($minify) {
				MiCompressor::log('	WARNING: ' . JS . 'jquery.min.js not found. minifying on the fly');
				echo MiCompressor::minifyJs($return, 'jquery');
			} else {
				echo $return;
			}
		} elseif ($minify && App::import('Vendor', 'jquery', array('file' => 'jquery/jquery/dist/jquery.min.js'))) {
			MiCompressor::log('	Found vendor version for jquery/jquery/dist/jquery.min.js');
		} elseif (App::import('Vendor', 'jquery', array('file' => 'jquery/jquery/dist/jquery.js'))) {
			if ($minify) {
				MiCompressor::log('	WARNING: No vendor version for jquery/jquery/dist/jquery.min.js.'
					. ' minifying on the fly');
				$contents = ob_get_clean();
				ob_start();
				echo MiCompressor::minifyJs($contents, 'jquery', "\t");
			} else {
				MiCompressor::log('	Found vendor version for jquery/jquery/dist/jquery.js');
			}
		} else {
			if ($minify) {
				MiCompressor::log('	PROBLEM: No vendor version for jquery/jquery/dist/jquery.min.js');
			}
			MiCompressor::log('	PROBLEM: No vendor version for jquery/jquery/dist/jquery.js found');
			MiCompressor::log('	To correct this problem obtain jquery.js from jquery.com and place in your webroot OR');
			MiCompressor::log('	Run the following to generate from the jquery vendor folder:');
			MiCompressor::log('		cd /base/vendor/jquery/');
			MiCompressor::log('		make');
		}
		foreach ($plugins as $plugin) {
			echo MiCompressor::loadJsJqueryPlugin($plugin, $minify);
		}
		return ob_get_clean();
	}
/**
 * Bespoke load method for a Jquery plugin file
 *
 * @param stirng $plugin
 * @param bool $minify
 * @static
 * @return void
 * @access protected
 */
	protected static function loadJsJqueryPlugin($plugin = '', $minify = false) {
		if (isset(MiCompressor::$map['js']['jquery'][$plugin])) {
			$filename = MiCompressor::$map['js']['jquery'][$plugin];
		} else {
			$filename = "jquery.$plugin.js";
		}
		if (is_array($filename)) {
			$return = '';
			foreach($filename as $f) {
				$return .= MiCompressor::loadJsJqueryPlugin($f, $minify);
			}
			return $return;
		}

		$_minify = $minify;
		echo "\r\n";
		if ($minify) {
			$found = true;
			$filename = str_replace('.js', '.min.js', $filename);
			if (file_exists(JS . $filename)) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log('	File ' . JS . "$filename found");
				echo file_get_contents(JS . $filename);
			} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/jquery/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version js/jquery/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version js/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
			} else {
				$found = false;
			}
			if ($found) {
				echo ob_get_clean();
				return;
			}
			$filename = str_replace('.min.js', '.js', $filename);
		}
		ob_start();
		MiCompressor::log('	' . $filename);
		if (file_exists(JS . $filename)) {
			MiCompressor::log('	File ' . JS . "$filename found");
			echo file_get_contents(JS . $filename);
		} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/jquery/$filename"))) {
			MiCompressor::log("	Found vendor version js/jquery/$filename for Jquery $plugin");
		} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/$filename"))) {
			MiCompressor::log("	Found vendor version js/$filename for Jquery $plugin");
		} elseif (App::import('Vendor', 'jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
			MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
		} else {
			MiCompressor::log("	PROBLEM: The jquery plugin jquery/plugins/$plugin/$filename could not be loaded.");
			$_minify = false;
		}
		if ($_minify) {
			$pluginContents = ob_get_clean();
			ob_start();
			echo MiCompressor::minifyJs($pluginContents, 'jquery.' . $plugin, "\t");
		}
		return ob_get_clean();
	}
/**
 * out method
 *
 * Send appropriate headers based on the request, and if necessary send the contents
 *
 * @param mixed $file
 * @param string $contents
 * @static
 * @return string the contents to output, or null if no output is to be sent (to trigger a 304 header upstream)
 * @access protected
 */
	protected static function out($file, $contents = null, $eTag = null) {
		if (preg_match('@^/\* eTag:(\S+) \*/@', $contents, $match)) {
			$eTag = $match['1'];
		}
		if ($eTag) {
			$contents = str_replace('/* eTag:' . $eTag .' */', '', $contents);
		}
		if (!MiCompressor::loadMinifyLib('headers')) {
			return $contents;
		}
		if (file_exists($file)) {
			$fileCreated = filectime($file);
		} else {
			$fileCreated = time();
		}
		$fileExpires = strtotime(MiCompressor::cRead('cacheDuration'), $fileCreated);

		$params = array(
			'lastModifiedTime' => $fileCreated,
			'setExpires' => $fileExpires
		);
		if ($eTag) {
			$params['eTag'] = $eTag;
		}
		$cg = new HTTP_ConditionalGet($params);
		$cg->sendHeaders();
		if ($cg->cacheIsValid) {
			return;
		}
		return $contents;
	}
/**
 * parse method
 *
 * Used to determine which component files, and sub files have been requested
 * For the request string of the form: file|file2|file3|file4,x,y=300,z|file5.. generate an array of the form:
 * array(
 * 	file => array(),
 * 	file2 => array(),
 * 	file3 => array(),
 *	file4 => array('x', 'y' => 300, 'z'),
 *	file5 => array()
 * )
 * This format allows passing parameters to scripts when included (accessible as $params);
 *
 * A hash is automatically added to the url by the MiHtml and MiJavascript helpers. The hash is checked in this
 * method and if it doesn't match (this should never happen for a valid request), will either add a log message if not
 * in production mode or return false. Compressing (in particular Js files) is expensive, therefore using a hash
 * prevents a malicious user from requesting arbritary varying urls and tying up the server
 *
 * The hash is returned primarily to be used as the default cache key
 *
 * @param string $requestString
 * @param mixed $hash
 * @static
 * @return mixed array of files to process, or false for an invalid/doctored request
 * @access protected
 */
	protected static function parse($requestString = '', &$hash = null) {
		if ($hash === null) {
			$requests = explode('|', $requestString);
			$hash = array_pop($requests);
		} else {
			$requests = explode('|', $requestString);
			if ($key = array_search('hash', $requests)) {
				unset ($requests[$key]);
				$hash = 'hash=' . $hash;
			}
		}
		$problem = false;
		if (strpos($hash, 'hash=') !== 0) {
			$problem = true;
			MiCompressor::log('PROBLEM: No hash in the request string');
		} else {
			list(,$hash) = explode('=', $hash);
			$_hash = MiCompressor::hash($requests);
			if ($hash !== $_hash) {
				$problem = true;
				MiCompressor::log('PROBLEM: Hash doesn\'t match. Expected: ' . $_hash);
			}
		}
		if ($problem && !MiCompressor::cRead()) {
			return false;
		}

		$return = array();
		foreach ($requests as $filename) {
			$params = array();
			if (strpos($filename, ',')) {
				$params = explode(',', $filename);
				$filename = array_shift($params);
				foreach ($params as $k => $v) {
					if (strpos($v, '=')) {
						unset ($params[$k]);
						list($k, $v) = explode('=', $v);
						$params[$k] = $v;
					}
				}
			}
			$return[$filename] = $params;
		}
		return $return;
	}
/**
 * url method
 *
 * @param mixed $files
 * @param string $type
 * @param bool $sizeLimit
 * @static
 * @return mixed the url or urls corresponding to the requested files
 * @access protected
 */
	protected static function _url($files, $type = 'js', $sizeLimit = false) {
		$string = implode('|', $files);
		$hash = MiCompressor::hash($string);
		if ($sizeLimit && count($files) > 1) {
			$cachePath = MiCompressor::cachePath($type, $hash);
			if (file_exists($cachePath) && filesize($cachePath) > ($sizeLimit - 45)) {
				foreach($files as $file) {
					$return[] = MiCompressor::_url(array($file), $type);
				}
				return $return;
			}
		}
		$string .= '|hash=' . $hash;
		return "/$type/mini.$type?" . $string;
	}
}
/**
 * Included for compatibility to allow use and testing in a standalone manner
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('WWW_ROOT')) {
	define('WWW_ROOT', dirname(__FILE__));
}
if (!defined('CSS')) {
	define('CSS', WWW_ROOT . 'css' . DS);
}
if (!defined('JS')) {
	define('JS', WWW_ROOT . 'js' . DS);
}
if (!defined('CACHE')) {
	define('CACHE', dirname(__FILE__) . DS . 'cache' . DS);
}
if (!defined('SITE_VERSION')) {
	define('SITE_VERSION', 42);
}

if (!function_exists('getMicrotime')) {
/**
 * Returns microtime for execution time checking
 *
 * @return float Microtime
 */
	function getMicrotime() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
if (!function_exists('cache')) {
/**
 * cache method
 *
 * Duplicate of CakePHP cache method to allow using this script standalone
 *
 * @param mixed $path
 * @param mixed $data
 * @param mixed $expires null
 * @return string cache contents
 * @access public
 */
	function cache($path, $data, $expires = null) {
		$now = time();
		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}

		$filename = MiCompressor::cRead('cacheDir') . $path;
		$timediff = $expires - $now;
		$filetime = false;
		if (file_exists($filename)) {
			$filetime = @filemtime($filename);
		}
		if ($data === null) {
			if (file_exists($filename) && $filetime !== false) {
				if ($filetime + $timediff < $now) {
					@unlink($filename);
				} else {
					$data = @file_get_contents($filename);
				}
			}
		} elseif (is_writable(dirname($filename))) {
			@file_put_contents($filename, $data);
		}
		return $data;
	}
}
?>