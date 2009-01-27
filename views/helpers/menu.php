<?php
/* SVN FILE: $Id: menu.php 699 2008-11-19 12:11:38Z AD7six $ */
/**
 * Short description for menu.php
 *
 * Long description for menu.php
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
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * MenuHelper class
 *
 * @uses          AppHelper
 * @package       base
 * @subpackage    base.views.helpers
 */
class MenuHelper extends AppHelper {
/**
 * name property
 *
 * @var string 'Menu'
 * @access public
 */
	var $name = 'Menu';
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Tree');
/**
 * defaultSettings property
 *
 * @var array
 * @access private
 */
	var $__defaultSettings = array(
		'order' => null,
		'indent' => null,
		'genericElement' => 'menu/generic',
		'hereMode' => 'active', // active // text // false[do nothing]
		'hereKey' => null, // the key for the item to mark as active
		'activeMode' => 'url', // url // controller[name] // action[and controller name] // false [do nothing]
		'uniqueKey' => 'title',
		'onlyActiveChildren' => false,
		'overwrite' => false,
		'showWarnings' => true
	);
/**
 * settings property
 *
 * @var array
 * @access public
 */
	var $settings = array();
/**
 * data property
 *
 * Holds the menu data as they get built. References flatData.
 *
 * @var array
 * @access private
 */
	var $__data = array();
/**
 * flatData property
 *
 * A flat list of menu data
 *
 * @var array
 * @access private
 */
	var $__flatData = array();
/**
 * here property
 *
 * Place holder for router normalized "here"
 *
 * @var string ''
 * @access private
 */
	var $__here = '';
/**
 * beforeRender method
 *
 * If genericElement is set, 'render' the named element. This can be used to prevent repeating menu logic if
 * for example there are some menu items which don't change based on the specific view file
 *
 * @access public
 * @return void
 */
	function beforeRender() {
		if (!isset($this->params['requested']) && $this->__defaultSettings['genericElement']) {
			$view =& ClassRegistry:: getObject('view');
			if ($view) {
				echo $view->element($this->__defaultSettings['genericElement']);
			}
		}
		return true;
	}
/**
 * settings method
 *
 * Define "here" and initialize or change settings
 *
 * @param string $section
 * @param array $settings
 * @access public
 * @return void
 */
	function settings($section = 'main', $settings = array()) {
		if (!$this->__here) {
			if (isset($this->params['url']['url'])) {
				$this->__here = Router::normalize('/' . $this->params['url']['url']);
			} else {
				$this->__here = '/';
			}
		}
		if (!isset($this->settings[$section])) {
			foreach ($settings as $key => $_) {
				if (!isset($this->__defaultSettings[$key])) {
					unset ($settings[$key]);
				}
			}
			$settings = array_merge($this->__defaultSettings, $settings);
			$this->settings[$section] = $settings;
		} elseif ($settings) {
			$this->settings[$section] = array_merge($this->settings[$section], $settings);
		}
		if (is_null($this->settings[$section]['order'])) {
			$this->settings[$section]['order'] = count($this->settings);
		}
	       return $this->settings[$section];
	}
/**
 * addm method
 *
 * Add Multiple menu items at once - use array syntax
 *
 * @param string $section
 * @param array $data
 * @access public
 * @return void
 */
	function addm($section = 'main', $data = array()) {
		if (is_array($section)) {
			$section = 'main';
			$data = $section;
		}
		foreach ($data as $row) {
			$this->add(array_merge(array('section' => $section), $row));
		}
	}
/**
 * Add a menu item.
 *
 * Add a menu item syntax examples:
 * 	$menu->add($title, $url); adds an entry with $title and $url to the menu named "main"
 * 	$menu->add('main', $title, $url); as above but explicit
 * 	$menu->add('context', $title, $url); add an entry with $title and $url to the menu named "context"
 * 	$menu->add('context', $title, $url, 'subSection'); add an entry with $title and $url to subsection "subSection for the menu named "context"
 * 	$menu->add(array('url' => $url, 'title' => $title, 'options' => array('escapeTitle' => false))); array syntax, not escaping title
 * 	$menu->add(array('url' => $url, 'title' => $title, 'options' => array('htmlAttributes' => array('id' => 'foo'))); array syntax, setting id for link
 *
 * @param string $section
 * @param mixed $title
 * @param mixed $url
 * @param mixed $under
 * @param array $options
 * @param array $settings
 * @access public
 * @return void
 */
	function add($section = 'main', $title = null, $url = null, $under = null, $options = array(), $settings = array()) {
		$here = $inPath = $activeChild = $sibling = false;
		if (is_array($section)) {
			$settings = $section;
			extract(array_merge(array('section' => 'main'), $section));
		} elseif (($section && $url !== false) || (is_string ($url) && $url[0] != 'h' && $url[0] != '/'&& $url[0] != '#') || is_array($under)) {
			if ($under) {
				$options = $under;
			}
			$settings = array();
			$options = $under;
			$under = $url;
			$url = $title;
			$title = $section;
			$section = 'main';
		}
		if (!isset($this->settings[$section])) {
			$this->settings($section, $settings);
		}
		extract(array_merge($this->settings[$section], $settings));
		if (isset($$uniqueKey)) {
			if (is_array($$uniqueKey)) {
				if ($uniqueKey == 'url') {
					$key = Router::normalize($$uniqueKey);
				} else {
					$key = serialize($$uniqueKey);
				}
			} else {
				$key = $$uniqueKey;
			}
		} else {
			$key = $title;
		}
		if (is_array($under)) {
			if ($uniqueKey == 'url') {
				$under = Router::normalize($under);
			} else {
				$under = serialize($under);
			}
		}
		list($here, $markActive, $url) = $this->__setHere($section, $url, $key, $activeMode, $hereMode, $options);
		$children = array();
		if ($under) {
			if (!isset($this->__flatData[$section][$under])) {
				$parent = array('title' => null, 'url' => false, 'options' => array(), 'here' => false,
					'under' => false, 'inPath' => false, 'activeChild' => false, 'sibling' => false, 'markActive' => false,
					'children' => array());
				$parent[$uniqueKey] = strpos('{', $under)?unserialize($under):$under;
				$this->__flatData[$section][$under] = $parent;
				$this->__data[$section][$under] =& $this->__flatData[$section][$under];
			}
			$this->__flatData[$section][$key] = compact('title', 'url', 'options', 'under', 'here', 'inPath', 'activeChild', 'sibling',
				'markActive', 'children');
			$this->__flatData[$section][$under]['children'][$key] =& $this->__flatData[$section][$key];
		} elseif (!isset($this->__flatData[$section][$key]) || $overwrite) {
			$this->__flatData[$section][$key] = compact('title', 'url', 'options', 'under', 'here', 'inPath', 'activeChild', 'sibling',
				'markActive', 'children');
			$this->__data[$section][$key] =& $this->__flatData[$section][$key];
		} elseif ($showWarnings)  {
			$altKey = $uniqueKey == 'title'?'url':'title';
			trigger_error ('MenuHelper::add<br /> Duplicate menu item detected for item "' . $title . '" in menu ' . $section . '.' .
				'<br />You can change the field used to detect duplicates which is currently set to ' . $uniqueKey . ',' .
			      	' can be changed to ' . $altKey . '.');
		}
		if ($hereMode == 'text' && $here == true) {
			$this->__flatData[$section][$key]['url'] = false;
		}
	}
/**
 * del method
 *
 * Delete a menu item. Specify the section name alone to delete the entire section.
 * Specify the section and key to delete a single menu item.
 * Specify just the key to delete an entry from the main (or only) menu section.
 *
 * @param mixed $section
 * @param mixed $key
 * @return void
 * @access public
 */
	function del($section, $key = null) {
		if (is_null($key)) {
			if (isset($this->__flatData[$section])) {
				unset ($this->__flatData[$section]);
				unset ($this->__data[$section]);
				return;
			}
			$key = $section;
			$section = 'main';
		}
		unset ($this->__flatData[$section][$key]);
		unset ($this->__data[$section][$key]);
	}
/**
 * sections method
 *
 * Return the names of all sections currently stored by the helper
 *
 * @access public
 * @return mixed array of menu sections if no order passed. name of the section name matching the order if passed.
 */
	function sections ($order = null) {
		$sequence = array();
		foreach ($this->settings as $key => $settings) {
			if ($order !== null && $settings['order'] == $order) {
				return $key;
			} elseif (!isset($sequence[$settings['order']])) {
				$sequence[$settings['order']] = $key;
			} else {
				$sequence[$settings['order'] . rand()] = $key;
			}
		}
		if ($order !== null) {
			return false;
		}
		ksort($sequence);
		return $sequence;
	}
/**
 * generate menu method
 *
 * generate menu syntax examples:
 * 	echo $menu->generate(); echo the main menu
 * 	echo $menu->generate('menu'); as above but explicit
 * 	echo $menu->generate('menu', array('element' => 'menus/item'); use an element for each item's content
 * 	echo $menu->generate('menu', array('callback' => 'menuItem'); use loose method menuItem for each item's content
 * 	echo $menu->generate('menu', array('callback' => array(&$object, 'method'); call $object->method($data) for each item's content
 *
 * @param mixed $section the section name or the numerical order
 * @param array $settings to be passed to the tree helper
 * @param bool $createEmpty
 * @access public
 * @return void
 */
	function generate ($section = 'main', $settings = array(), $createEmpty = true, $debug = false) {
		if (is_array($section)) {
			extract(array_merge(array('section' => 'main'), $section));
		}
		if (!isset($this->settings[$section])) {
			if (is_numeric($section)) {
				$order = $section;
				$match = false;
				foreach ($this->settings as $section => $_) {
					if ($_['order'] == $order) {
						$match = true;
						break;
					}
				}
				if (!$match) {
					return;
				}
			}
			return false;
		}
		$settings = array_merge($this->settings[$section], $settings);
		$settings = array_merge(array('callback' => array(&$this, 'menuItem'), 'model' => false, 'class' => 'menu'), $settings);
		extract ($settings);
		if (isset($this->__data[$section])) {
			if ($onlyActiveChildren) {
				$pkey = false;
				if (isset($this->settings[$section]['hereKey'])) {
					$key = $this->settings[$section]['hereKey'];
					$pkey = $this->__flatData[$section][$key]['under'];
					unset($this->settings[$section]['hereKey']);
					if (isset($this->__flatData[$section][$key]['children'])) {
						foreach ($this->__flatData[$section][$key]['children'] as $i => $_i) {
							$this->__flatData[$section][$key]['children'][$i]['activeChild'] = true;
						}
					}
					$under = $this->__flatData[$section][$key]['under'];
					while ($under) {
						$this->__flatData[$section][$under]['inPath'] = true;
						$under = $this->__flatData[$section][$under]['under'];
					}
				}
				foreach ($this->__flatData[$section] as $i => $row) {
					if (!$row['under'] && !$row['here']) {
						$this->__flatData[$section][$i]['sibling'] = true;
					} elseif ($row['under'] == $pkey && !$row['activeChild'] && !$row['here']) {
						$this->__flatData[$section][$i]['sibling'] = true;
					} elseif (!($row['here'] || $row['inPath']|| $row['activeChild'] || $row['sibling'])) {
						unset($this->__flatData[$section][$i]);
					}
				}
				$this->__cleanData($this->__data[$section], $section);
			}
			$data = $this->__data[$section];
			if ($debug) {
				$settings = array(
					'callback' => array(&$this, 'debugItem'),
					'debug' => true,
					'indent' => true,
					'model' => false,
					'class' => 'menu',
					'itemType' => false,
					'type' => false
				);
			} else {
				unset ($this->settings[$section]);
				unset ($this->__data[$section]);
				unset ($this->__flatData[$section]);
			}
		} elseif ($createEmpty) {
			return '<ul><!-- Empty menu --></ul>';
		} else {
			return false;
		}
		$return = $this->Tree->generate($data, $settings);
		if ($debug) {
			return '<pre><h2> Menu Section:' . $section . '</h2>' . $return . '</pre>';
		}
		return $return;
	}
/**
 * debug method
 *
 * @param mixed $section
 * @return void
 * @access public
 */
	function debug($section = null) {
		if (!$section) {
			foreach ($this->settings as $section) {
				$this->debug($section);
			}
			return;
		}
		return $this->generate(array('section' => $section, 'debug' => true));
	}
/**
 * internal callback
 *
 * Used to return the output from the html helper using the parameters for this menu option
 *
 * @param array $data
 * @access public
 * @return void
 */
	function debugItem($data = array()) {
		foreach (array_keys($data) as $key) {
			if ($key != 'data') {
				$debug[$key] = (string)$data[$key];
			}
		}
		$htmlAttributes = array();
		$markActive = false;
		$confirmMessage = false;
		$escapeTitle = true;
		extract ($data);
		extract ($data);
		if ($options) {
			extract ($options);
		}
		if ($markActive) {
			$this->Tree->addItemAttribute('class', 'active');
			if (isset ($htmlAttributes['class'])) {
				$htmlAttributes['class'] .= ' active';
			} else {
				$htmlAttributes['class'] = 'active';
			}
		}
		$return = str_pad('title', 25, ' ') . ':' . $title . "\r\n";
		$return .= str_pad('url', 25, ' ') . ':' . str_replace(Router::url('/'), '/', Router::url($url)) . "\r\n";
		$return .= str_pad('here?', 25, ' ') . ':' . ($here?'yes':'no');
		$return .= "\r\n";
		//$return .= str_pad('in path?', 25, ' ') . ':' . ($inPath?'yes':'no') . "\r\n";
		//$return .= str_pad('active child?', 25, ' ') . ':' . ($activeChild?'yes':'no') . "\r\n";
		//$return .= str_pad('active sibling?', 25, ' ') . ':' . ($sibling?'yes':'no') . "\r\n";
		$return .= str_pad('first child?', 25, ' ') . ':' . ($firstChild?'yes':'no') . "\r\n";
		$return .= str_pad('last child?', 25, ' ') . ':' . ($lastChild?'yes':'no') . "\r\n";
		$return .= str_pad('has children?', 25, ' ') . ':' . ($hasChildren?'yes':'no');
		if ($hasChildren) {
			$bits = array();
			if ($numberOfDirectChildren) {
				$bits[] = 'direct: ' . $numberOfDirectChildren;
			}
			/* Unreachable, but present just incase ported somewhere else */
			if ($numberOfTotalChildren) {
				$bits[] = 'total: ' . $numberOfTotalChildren;
				$bits[] = 'visible?: ' . ($hasVisibleChildren?'yes':'no');
			}
			/* Unreachable end */
			$return .= ' (' . implode(', ', $bits) . ")";
		}
		$return .= "\r\n";
		$return .= str_pad('depth', 25, ' ') . ':' . $depth . "\r\n";
		return $return;
	}
/**
 * internal callback
 *
 * Used to return the output from the html helper using the parameters for this menu option
 *
 * @param array $data
 * @access public
 * @return void
 */
	function menuItem($data = array()) {
		$htmlAttributes = array();
		$markActive = false;
		$confirmMessage = false;
		$escapeTitle = true;
		extract ($data);
		extract ($data);
		if ($options) {
			extract ($options);
		}
		if ($markActive) {
			$this->Tree->addItemAttribute('class', 'active');
			if (isset ($htmlAttributes['class'])) {
				$htmlAttributes['class'] .= ' active';
			} else {
				$htmlAttributes['class'] = 'active';
			}
		}
		if ($url === false) {
			return $title;
		} else {
			return $this->Html->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
		}
	}
/**
 * setHere method
 *
 * Used internally to detect whether the current menu item links to the page currently
 * being rendered and modify the url if appropriate
 *
 * @param mixed $section
 * @param mixed $url
 * @param mixed $activeMode
 * @param mixed $hereMode
 * @access private
 * @return array($here, $markActive, $url)
 */
	function __setHere($section, $url, $key, $activeMode, $hereMode, $options) {
		$view =& ClassRegistry:: getObject('view');
		if (isset($this->settings[$section]['hereKey']) || !$view) {
			return array(false, false, $url);
		}
		$here = $markActive = false;
		if (!empty($options['markActive'])) {
			$here = true;
		} elseif ($activeMode == 'url' && Router::normalize($url) == $this->__here) {
			$here = true;
		} elseif (is_array($url) &&
			(!isset($url['controller']) ||
				Inflector::underscore($url['controller']) == Inflector::underscore($view->name)))  {
			if ($activeMode == 'controller') {
				$here = true;
			} elseif ($activeMode == 'action' &&
				(!isset($url['action']) || $url['action'] == Inflector::underscore($view->action))) {
				$here = true;
			}
		}
		if ($here) {
			$this->settings[$section]['hereKey'] = $key;
			if ($hereMode == 'text') {
				$url = false;
			} elseif ($hereMode == 'active') {
				$markActive = true;
			}
		}
		if ($here && $hereMode == 'active') {
			$this->Tree->addItemAttribute('class', 'active');
			if (isset ($htmlAttributes['class'])) {
				$htmlAttributes['class'] .= ' active';
			} else {
				$htmlAttributes['class'] = 'active';
			}
		}

		return array($here, $markActive, $url);
	}
/**
 * cleanData method
 *
 * Shouldn't really be necessary. Ensures that any item(s) which have been suppressed by the "only show active"
 * logic are removed
 *
 * @param mixed $array
 * @param mixed $section
 * @access private
 * @return void
 */
	function __cleanData(&$array, $section) {
		foreach ($array as $key => $row) {
			if (!isset($this->__flatData[$section][$key])) {
				unset ($array[$key]);
			} elseif (isset($row['children']) && $row['children']) {
				$this->__cleanData($array[$key]['children'], $section);
			}
		}
	}
}
?>