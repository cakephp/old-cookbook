<?php
/* SVN FILE: $Id: mi_compressor.php 800 2009-02-26 18:49:55Z ad7six $ */
/**
 * Short description for mi_compressor.php
 *
 * Long description for mi_compressor.php
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
 * @version       $Revision: 800 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-02-26 19:49:55 +0100 (Thu, 26 Feb 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * MiCompressor class
 *
 * Compress multiple css and js files into a single file on demand.
 *
 * By default, in debug mode it only concatonates, in production mode contents are also runs the output through a
 * minifying routine
 *
 * @abstract
 * @package       base
 * @subpackage    base.vendors
 */
abstract class MiCompressor {
/**
 * start property
 *
 * @static
 * @var mixed null
 * @access public
 */
	public static $start = null;
/**
 * cacheDuration property
 *
 * @static
 * @var string '+1 year'
 * @access public
 */
	public static $cacheDuration = '+1 year';
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
		)
	);
/**
 * compressCss method
 *
 * @param mixed $css
 * @param string $file
 * @param string $logPrefix
 * @static
 * @return void
 * @access public
 */
	public static function compressCss($css, $file = '', $logPrefix = '') {
		MiCompressor::log("$logPrefix	compress $file.css");
		return Minify_CSS::minify($css);
	}
/**
 * compressJs method
 *
 * This can be rather intensive - use with care
 *
 * @TODO don't strip license blocks
 * @param mixed $js
 * @param string $file
 * @param string $logPrefix
 * @static
 * @return void
 * @access public
 */
	public static function compressJs($js, $file = '', $logPrefix = '') {
		MiCompressor::log("$logPrefix	compressing $file.js");
		return JSMin::minify($js);
	}
/**
 * log method
 *
 * Record to the log (the head doc block in debug mode) or output the log (call with no params)
 *
 * @param mixed $string
 * @static
 * @return void
 * @access public
 */
	public static function log($string = null) {
		if (MiCompressor::$start === null) {
			MiCompressor::$start = getMicrotime();
		}
		static $log = array();
		if ($string === null) {
			$log = am(array(
				'MiCompressor log - only generated in debug mode. Generated ' . date("D, M jS Y, H:i:s"),
				null), $log);
			$return = "/**\r\n * " . implode("\r\n * ", $log) . "\r\n */\r\n";
			$log = array();
			return $return;
		}
		$time = getMicrotime() - MiCompressor::$start;
		$log[] = str_pad(number_format($time, 3, '.', ''), 6, ' ', STR_PAD_LEFT) . 's ' . $string;
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
 * @access public
 */
	public static function parse($requestString = '', &$hash = null) {
		$debug = (class_exists('Configure')?Configure::read():false);
		$requests = explode('|', $requestString);
		$hash = array_pop($requests);
		$problem = false;
		if (strpos($hash, 'hash=') !== 0) {
			$problem = true;
			MiCompressor::log('PROBLEM: No hash in the request string');
		} else {
			list(,$hash) = explode('=', $hash);
			uses('Security');
			$salt = defined('SITE_VERSION')?SITE_VERSION:'';
			if ($hash != Security::hash($salt . implode('|', $requests), null, true)) {
				$problem = true;
				MiCompressor::log('PROBLEM: Hash doesn\'t match');
			}
		}
		if ($problem && $debug) {
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
 * process method
 *
 * For each of the requested files, find them, concatonate them - if requested compress them - and return
 * For js files, each individual file is compressed. For css, their combined contents are compressed
 *
 * @param mixed $files
 * @param mixed $type
 * @param mixed $compress
 * @static
 * @return string the files' contents, optionally compressed, as a string
 * @access public
 */
	public static function process($files, $type = null, $compress = null) {
		if ($type === null) {
			if (strpos($_GET['url'], 'js/') === 0) {
				$type = 'js';
			} else {
				$type = 'css';
			}
		}
		$debug = (class_exists('Configure')?Configure::read():false);
		if ($compress === null) {
			$compress = !$debug;
		}
		if ($compress && !MiCompressor::loadCompressLib($type)) {
			MiCompressor::log("PROBLEM: Unable to load $type compressor. No minifying");
			$compress = false;
		}

		$files = (array)$files;
		$return = '';
		if ($type === 'css') {
			$_compress = $compress;
			$compress = false;
		}
		foreach ($files as $filename => $params) {
			if (is_string($params) && is_numeric($filename)) {
				$filename = $params;
				$params = array();
			}
			if (substr($filename, - strlen($type)) === $type) {
				$filename = substr($filename, 0, - strlen($type) - 1);
			}
			$method = 'load' . Inflector::camelize($type) . Inflector::camelize($filename);
			if (method_exists('MiCompressor', $method)) {
				MiCompressor::log("Loading $filename.$type with method $method");
				$return .= MiCompressor::$method($params, $compress) . "\r\n";
			} else {
				MiCompressor::log("Loading $filename.$type with method loadFile");
				$return .= MiCompressor::loadFile($filename, $params, $compress, $type) . "\r\n";

			}
		}
		if (!empty($_compress) && $type === 'css') {
			$return = MiCompressor::compressCss($return, 'combined');
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
 * If in debug mode, the cache duration is set to +10 minutes. otherwise it is +1 year
 * $compress defaults to true in production mode, false in debug mode
 * $log defaults to true in production mode, false in debug mode
 * $cachePath defaults to $type _ the hash _ d(ebug)|p(roduction) . t(emp)|p(ermanent)
 * $clear defaults to false in production mode, true in debug mode
 *
 * A cache file is always generated, but if $clear is true, the cache is cleared for all cache files of the same type
 * The log is always generated - but it's only included in the content if $log is true. Otherwise it is available
 * for debugging purposes
 *
 * @param string $request
 * @param mixed $type
 * @param mixed $compress
 * @param mixed $log
 * @param mixed $cachePath
 * @param mixed $clear
 * @static
 * @return void
 * @access public
 */
	public static function serve($request = '', $type = null, $compress = null, $log = null, $cachePath = null, $clear = null) {
		if (is_array($request)) {
			extract($request);
		}
		MiCompressor::log('Request String: ' . $request);
		$start = getMicrotime();
		ob_start();
		if (Configure::read('Asset.compress') && @ini_get("zlib.output_compression") != true && extension_loaded("zlib") &&
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
		$appDebug = (class_exists('Configure')?Configure::read():false);
		if ($compress === null) {
			if (!class_exists('Configure')) {
				$compress = true;
			} else {
				$compress = Configure::read('Asset.compress.' . $type);
				if ($compress === null) {
					$compress = Configure::read('Asset.compress');
				}
				if ($compress === null) {
					$compress = !$appDebug;
				}
			}
		}
		if ($log === null) {
			$log = $appDebug;
		}
		if ($clear === null) {
			$clear = $appDebug;
		}
		if ($cachePath === null) {
			$cachePath = MiCompressor::__cachePath($type, $hash, $appDebug, $clear, true);
		}
		MiCompressor::log('Cache Path: ' . $cachePath);
		if ($appDebug) {
			MiCompressor::$cacheDuration = '+10 minutes';
		}

		if ($clear && function_exists('uses')) {
			uses('Folder');
			if (class_exists('Folder')) {
				$folder = new Folder(CACHE . 'views/');
				$files = $folder->find($type . '_' . '.*');
				foreach ($files as $file) {
					MiCompressor::log("Removing Cache File " . $file);
					@unlink(CACHE . 'views/' . $file);
				}
			}
		}
		if ($cachePath) {
			$cached = cache($cachePath, null, MiCompressor::$cacheDuration);
			if ($cached) {
				MiCompressor::log("Cache File: $cachePath found and returned");
				return MiCompressor::out($cachePath, $cached);
			}
		}
		$return = MiCompressor::process($requests, $type, $compress);
		$eTag = md5($return);
		if ($cachePath) {
			MiCompressor::log("Generating cache file $cachePath to expire in " . MiCompressor::$cacheDuration);
		}
		MiCompressor::log('eTag: ' . $eTag);
		MiCompressor::log('Finished');
		if ($log) {
			$return = MiCompressor::log() . $return;
		}
		$return = '/* eTag:' . $eTag . ' */' . $return;
		// Slightly out of sequence so the cache file contains the log
		if ($cachePath) {
			cache($cachePath, $return, MiCompressor::$cacheDuration);
		}
		return MiCompressor::out(CACHE . $cachePath, $return);
	}
/**
 * loadCompressLib method
 *
 * @param mixed $type
 * @static
 * @return void
 * @access private
 */
	private static function loadCompressLib($type) {
		if($type === 'js') {
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/JSMin.php'));
		} elseif ($type === 'css') {
			ini_set('include_path', dirname(__FILE__) . '/minify/lib:' . ini_get('include_path'));
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/Minify/CSS.php'));
		} else {
			return App::import('Vendor', $type . 'Min', array('file' => 'minify/lib/HTTP/ConditionalGet.php'));
		}
	}
/**
 * loadFile method
 *
 * For the requested file, find it and return it. if compress is set to true send through compress(Css|Js) as
 * appropriate first
 *
 * @param string $filename
 * @param mixed $params
 * @param bool $compress
 * @static
 * @return void
 * @access private
 */
	private static function loadFile($filename = '', $params = array(), $compress = false, $type = 'js') {
		if ($type === 'js') {
			$base = JS;
		} elseif($type === 'css') {
			$base = CSS;
		} else {
			var_dump($base); die;
		}
		$file = $base . $filename . '.' . $type;
		if ($compress && file_exists($base . $filename . '.min.' . $type)) {
			$file = $base . $filename . '.min.' . $type;
			MiCompressor::log("File $file found");
			return file_get_contents($file);
		} elseif (file_exists($file)) {
			MiCompressor::log("File $file found");
			$return = file_get_contents($file);
		} else {
			ob_start();
			if (!App::import('Vendor', str_replace('.', '_', $filename . $type), array('file' => $type . '/' . $filename . '.' . $type))) {
				$compress = false;
				MiCompressor::log("PROBLEM: No file for $type/$filename.{$type} could be found");
			}
			$return = ob_get_clean();
		}
		if ($type === 'css') {
			preg_match_all('/@import\s*(?:url\()?(?:["\'])([^"\']*)\.css(?:["\'])\)?;/', $return, $matches);
			foreach ($matches[1] as $i => $cssFile) {
				$cssFile = dirname($filename) . '/' . $cssFile;
				$import = MiCompressor::loadFile($cssFile, $params, false, 'css');
				$return = str_replace($matches[0][$i], $import, $return);
			}
		}
		if ($compress) {
			$compressMethod = 'compress' . Inflector::camelize($type);
			return MiCompressor::$compressMethod($return, $filename);
		}
		return $return;
	}
/**
 * Bespoke load method for Jquery.
 *
 * Allow for loading the distributed, already compressed, version of jquery; and load plugins from
 * parameters for ease in views. requesting 'mini.js?...|jquery,abc,xyz|...' will load jquery, with the abc and xyz
 * plugins
 *
 * @param array $plugins
 * @param bool $compress
 * @static
 * @return void
 * @access private
 */
	private static function loadJsJquery($plugins = array(), $compress = false) {
		ob_start();
		if ($compress && file_exists(JS . 'jquery.min.js')) {
			MiCompressor::log('	File ' . JS . 'jquery.min.js found');
			echo file_get_contents(JS . 'jquery.min.js');
		} elseif (file_exists(JS . 'jquery.js')) {
			MiCompressor::log('	File ' . JS . 'jquery.js found');
			$return = file_get_contents(JS . 'jquery.js');
			if ($compress) {
				MiCompressor::log('	WARNING: ' . JS . 'jquery.min.js not found. compressing on the fly');
				echo MiCompressor::compressJs($return, 'jquery');
			} else {
				echo $return;
			}
		} elseif ($compress && App::import('Vendor', 'jquery', array('file' => 'jquery/jquery/dist/jquery.min.js'))) {
			MiCompressor::log('	Found vendor version for jquery/jquery/dist/jquery.min.js');
		} elseif (App::import('Vendor', 'jquery', array('file' => 'jquery/jquery/dist/jquery.js'))) {
			if ($compress) {
				MiCompressor::log('	WARNING: No vendor version for jquery/jquery/dist/jquery.min.js.'
					. ' compressing on the fly');
				$contents = ob_get_clean();
				ob_start();
				echo MiCompressor::compressJs($contents, 'jquery', "\t");
			} else {
				MiCompressor::log('	Found vendor version for jquery/jquery/dist/jquery.js');
			}
		} else {
			if ($compress) {
				MiCompressor::log('	PROBLEM: No vendor version for jquery/jquery/dist/jquery.min.js');
			}
			MiCompressor::log('	PROBLEM: No vendor version for jquery/jquery/dist/jquery.js found');
			MiCompressor::log('	To correct this problem obtain jquery.js from jquery.com and place in your webroot OR');
			MiCompressor::log('	Run the following to generate from the jquery vendor folder:');
			MiCompressor::log('		cd /base/vendor/jquery/');
			MiCompressor::log('		make');
		}
		foreach ($plugins as $plugin) {
			if (isset(MiCompressor::$map['js']['jquery'][$plugin])) {
				$filename = MiCompressor::$map['js']['jquery'][$plugin];
			} else {
				$filename = "jquery.$plugin.js";
			}
			$_compress = $compress;
			ob_start();
			echo "\r\n";
			if ($compress) {
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
				} elseif (App::import('Vendor', 'jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
					MiCompressor::log('	' . $filename);
					MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
				} else {
					$found = false;
				}
				if ($found) {
					echo ob_get_clean();
					continue;
				}
				$filename = str_replace('.min.js', '.js', $filename);
			}
			MiCompressor::log('	' . $filename);
			if (file_exists(JS . $filename)) {
				MiCompressor::log('	File ' . JS . "$filename found");
				echo file_get_contents(JS . $filename);
			} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/jquery/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version js/jquery/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'js/jquery/' . $plugin, array('file' =>	"js/$filename"))) {
				MiCompressor::log('	' . $filename);
				MiCompressor::log("	Found vendor version js/$filename for Jquery $plugin");
			} elseif (App::import('Vendor', 'jquery/' . $plugin, array('file' => "jquery/plugins/$plugin/$filename"))) {
				MiCompressor::log("	Found vendor version jquery/plugins/$plugin/$filename for Jquery $plugin");
			} else {
				MiCompressor::log("	PROBLEM: The jquery plugin jquery/plugins/$plugin/$filename could not be loaded.");
				$_compress = false;
			}
			if ($_compress) {
				$pluginContents = ob_get_clean();
				ob_start();
				echo MiCompressor::compressJs($pluginContents, 'jquery.' . $plugin, "\t");
			}
			echo ob_get_clean();
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
 * @access private
 */
	private static function out($file, $contents = null, $eTag = null) {
		if ($contents === null) {
			$contents = file_get_contents($file);
			debug ($contents);
			die;
		}
		if (preg_match('@^/\* eTag:(\S+) \*/@', $contents, $match)) {
			$eTag = $match['1'];
		}
		if ($eTag) {
			$contents = str_replace('/* eTag:' . $eTag .' */', '', $contents);
		}
		if (!MiCompressor::loadCompressLib('headers')) {
			return $contents;
		}
		if (file_exists($file)) {
			$fileCreated = filectime($file);
		} else {
			$fileCreated = time();
		}
		$fileExpires = strtotime(MiCompressor::$cacheDuration, $fileCreated);

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
 * url method
 *
 * @param array $request
 * @param array $params
 * @return void
 * @access public
 */
	function url($request = array(), $params = array()) {
		extract(am(array(
			'salt' => defined('SITE_VERSION')?SITE_VERSION:'',
			'sendAlone' => Configure::read(),
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
			$url = MiCompressor::__url($files, $type, $salt, $sizeLimit);
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
 * @param mixed $appDebug
 * @param bool $clear
 * @param bool $relative
 * @return void
 * @access private
 */
	function __cachePath($type, $hash, $appDebug = null, $clear = false, $relative = false) {
		if ($appDebug === null) {
			$appDebug = (class_exists('Configure')?Configure::read():false);
		}
		if ($clear === null) {
			$clear = $appDebug;
		}
		$return = 'views/' . $type . '_' . $hash . '_' . ($appDebug?'d':'p') . ($clear?'t':'p');
		if ($relative) {
			return $return;
		}
		return CACHE . $return;
	}
/**
 * url method
 *
 * @param mixed $files
 * @param string $type
 * @param string $salt
 * @param bool $sizeLimit
 * @return void
 * @access private
 */
	function __url($files, $type = 'js', $salt = '', $sizeLimit = false) {
		$string = implode('|', $files);
		$hash = Security::hash($salt . $string, null, true);
		if ($sizeLimit && count($files) > 1) {
			$cachePath = MiCompressor::__cachePath($type, $hash);
			if (file_exists($cachePath) && filesize($cachePath) > ($sizeLimit - 45)) {
				foreach($files as $file) {
					$return[] = MiCompressor::__url(array($file), $type, $salt);
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
if (!defined('CACHE')) {
	define('CACHE', 'cache');
	define('CSS', 'css');
	define('JS', 'js');
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
?>