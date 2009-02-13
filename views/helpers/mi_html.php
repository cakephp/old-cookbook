<?php
/* SVN FILE: $Id: mi_html.php 736 2009-01-16 16:54:44Z ad7six $ */
/**
 * Short description for mi_html.php
 *
 * Long description for mi_html.php
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
App::import('Helper', 'Html');
App::import('Vendor', 'MiCompressor');
/**
 * MiHtmlHelper class
 *
 * @uses          HtmlHelper
 * @package       base
 * @subpackage    base.views.helpers
 */
class MiHtmlHelper extends HtmlHelper {
/**
 * name property
 *
 * @var string 'MiHtml'
 * @access public
 */
	var $name = 'MiHtml';
/**
 * css property
 *
 * @var array
 * @access private
 */
	var $__css = array();
/**
 * css method
 *
 * Example usage, from anywhere at all:
 * 	$html->css('this', null, null, null, false);
 * 	....
 * 	$html->css('that', null, null, null, false);
 * 	...
 * 	$html->css('other', null, null, null, false);
 *
 * In the layout, call with no parameters to output:
 * 	echo $html->css();
 *
 * With the given example it would generate a link to /app/css/mini.css?this|that|other to be picked up by the
 * mi_compressor vendor class
 *
 * If $sendAlone is true (defaults to true in development mode) each file is output individually
 * If $sizeLimit is set (defaults to null for browsers and 25K for mobile devices in production mode)
 * 	file concatonation is bypassed if the request cache file exists and is greater than the size limit
 *
 * @param mixed $path
 * @param mixed $rel
 * @param array $htmlAttributes
 * @param bool $inline
 * @param mixed $sendAlone
 * @param mixed $sizeLimit Maximum filesize in bytes
 * @return void
 * @access public
 */
	function css($path = null, $rel = null, $htmlAttributes = array(), $inline = true, $sendAlone = null, $sizeLimit = null) {
		if ($inline && $path) {
			return parent::css($path, $rel, $htmlAttributes, $inline);
		}
		if ($path === null) {
			if (!$this->__css) {
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
			$return = '';
			foreach ($this->__css as $result) {
				extract($result);
				$url = MiCompressor::url($files, array(
					'type' => 'css', 'sendAlone' => $sendAlone, 'sizeLimit' => $sizeLimit));
				foreach((array)$url as $u) {
					$return .= parent::css($u, $rel, $htmlAttributes, true);
				}
				continue;
			}
			$this->__css = array();
			return $return;
		}
		if (is_array($path)) {
			foreach ($path as $url) {
				$this->css($url, $rel, $htmlAttributes, false);
			}
			return;
		}
		if (!$rel) {
			$rel = 'stylesheet';
		}
		if (!$htmlAttributes) {
			$htmlAttributes = array ('title' => 'Standard', 'media' => 'screen');
		}
		$key = Inflector::slug($rel . serialize($htmlAttributes));
		$this->__css[$key]['rel'] = $rel;
		$this->__css[$key]['htmlAttributes'] = $htmlAttributes;
		if (empty($this->__css[$key]['files']) || !in_array($path, $this->__css[$key]['files'])) {
			$this->__css[$key]['files'][] = $path;
		}
	}
/**
 * link method
 *
 * For any link to an action that only works by post - add the class confirm.
 *
 * @param mixed $title
 * @param mixed $url
 * @param array $htmlAttributes
 * @param bool $confirmMessage
 * @param bool $escapeTitle
 * @return void
 * @access public
 */
	function link($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = true) {
		if (!isset($this->__view)) {
			$this->__view =& ClassRegistry::getObject('view');
		}
		if (isset($this->__view->viewVars['postActions']) && is_array($url)) {
			$controller = $this->__view->name;
			if (isset($url['controller'])) {
				$controller = $url['controller'];
			}
			$controller = Inflector::underscore($controller);
			if (isset($this->__view->viewVars['postActions'][$controller])) {
				$postActions = $this->__view->viewVars['postActions'][$controller];
				if (isset($url['admin']) || isset($this->__view->params['admin'])) {
					$prefix = 'admin_';
				} else {
					$prefix = '';
				}
				if (isset($url['action'])) {
					$action = $url['action'];
				} else {
					$action = $this->__view->action;
				}
				$action = $prefix . $action;
				if (in_array($action, $postActions)) {
					if (isset($htmlAttributes['class'])) {
						$htmlAttributes['class'] .= ' confirm';
					} else {
						$htmlAttributes['class'] = 'confirm';
					}
				}
			}
		}
		return parent::link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
	}
}
?>