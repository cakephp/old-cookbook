<?php
/* SVN FILE: $Id: mi_javascript.php 736 2009-01-16 16:54:44Z ad7six $ */
/**
 * Short description for mi_javascript.php
 *
 * Long description for mi_javascript.php
 *
 * PHP versions 4 and 5
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
 * @subpackage    base.views.helpers
 * @since         v 1.0
 * @version       $Revision: 736 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-01-16 17:54:44 +0100 (Fri, 16 Jan 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Component', 'RequestHandler');
App::import('Helper', 'Javascript');
App::import('Vendor', 'MiCompressor');
/**
 * MiJavascriptHelper class
 *
 * @uses          JavascriptHelper
 * @package       base
 * @subpackage    base.views.helpers
 */
class MiJavascriptHelper extends JavascriptHelper {
/**
 * name property
 *
 * @var string 'MiHtml'
 * @access public
 */
	var $name = 'MiJavascript';
/**
 * js property
 *
 * @var array
 * @access private
 */
	var $__js = array();
/**
 * viewJs property
 *
 * @var array
 * @access private
 */
	var $__viewJs = array();
/**
 * beforeLayout method
 *
 * Shuffle the vars so js added in view files are after js added in thelayout
 *
 * @return void
 * @access public
 */
	function beforeLayout () {
		$this->__viewJs = $this->__js;
		$this->__js = array();
	}
/**
 * link method
 *
 * Example usage, from anywhere at all:
 * 	$javascript->link('this', false);
 * 	....
 * 	$javascript->link('that', false);
 * 	...
 * 	$javascript->link(array('jquery' => 'plugin1'), false);
 * 	...
 * 	$javascript->link(array('jquery' => 'plugin2'), false);
 *
 * In the layout (preferably right at the end), call with no parameters to output:
 * 	echo $javascript->link();
 *
 * With the given example it would generate a link to /app/js/mini.js?jquery,plugin1,plugin2|this|that to be picked up
 * by the mi_compressor vendor class. Note that jquery and plugins will always be first if included
 *
 * If $sendAlone is true (defaults to true in development mode) each file is output individually
 * If $sizeLimit is set (defaults to null for browsers and 25K for mobile devices in production mode)
 * 	file concatonation is bypassed if the request cache file exists and is greater than the size limit
 *
 * @param mixed $url
 * @param bool $inline
 * @param mixed $sendAlone
 * @param mixed $sizeLimit Maximum filesize in bytes
 * @return void
 * @access public
 */
	function link($url = null, $inline = true, $sendAlone = null, $sizeLimit = null) {
		if ($url && $inline) {
			return parent::link($url, $inline);
		}
		if ($url === null) {
			$this->__js = Set::merge($this->__js, $this->__viewJs);
			$this->__viewJs = array();
			if (!$this->__js) {
				return;
			}
			if ($sendAlone === null) {
				$sendAlone = Configure::read();
			}
			if (!$sendAlone && $sizeLimit === null) {
				if (!isset($this->__RequestHandler)) {
					$this->__RequestHandler = new RequestHandlerComponent();
				}
				if ($this->__RequestHandler->isMobile()) {
					$sizeLimit = 25 * 1024;
				}
			}
			if (isset($this->__js['jquery'])) {
				$this->__js = am(array('jquery' => $this->__js['jquery']), $this->__js);
			}
			$url = MiCompressor::url($this->__js, array(
				'type' => 'js', 'sendAlone' => $sendAlone, 'sizeLimit' => $sizeLimit));
			$this->__js = array();
			$return = '';
			foreach((array)$url as $u) {
				$return .= parent::link($u);
			}
			return $return;
		}
		if (is_array($url)) {
			foreach ($url as $key => $value) {
				if (is_numeric($key)) {
					if (!in_array($value, $this->__js)) {
						$this->__js[] = $value;
					}
					continue;
				}
				if (!isset($this->__js[$key])) {
					$this->__js[$key] = (array)$value;
				} else {
					$this->__js[$key] = array_unique(am($this->__js[$key], (array)$value));
				}
			}
		} elseif ($url) {
			if (!in_array($url, $this->__js)) {
				$this->__js[] = $url;
			}
			return;
		}
	}
}
?>