<?php
/* SVN FILE: $Id: filter.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for filter.php
 *
 * Long description for filter.php
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
 * @package       mi-base
 * @subpackage    mi-base.app.controllers
 * @since         v 1.0
 * @version       $Revision: 689 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 11:30:07 +0100 (Wed, 05 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * FilterComponent class
 *
 * @uses          Object
 * @package       mi-base
 * @subpackage    mi-base.app.controllers
 */
class FilterComponent extends Object {
/**
 * name property
 *
 * @var string 'Filter'
 * @access public
 */
	var $name = 'Filter';
/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array('Session');
/**
 * ignore property
 *
 * Named parameters to exclude when determining the filter to apply
 *
 * @var array
 * @access private
 */
	var $__ignore = array('limit', 'show', 'sort', 'page', 'direction', 'step');
/**
 * startup method
 *
 * @param controller
 * @access public
 * @return void
 */
	function startup (&$controller) {
		$this->controller =& $controller;
	}
/**
 * Determine the conditions to apply based on either the POSTed filter conditions or the session
 * stored filter conditions, and/or any additional named parameter filters
 *
 * @TODO Rewrite to Use, or update and ticket, postConditions
 * @param string $mode
 * @param array $ignore additional named params to ignore
 * @param array $filter initial filter conditions
 * @access public
 * @return $conditions array of conditions to apply
 */
	function parse($mode = 'both', $ignore = array(), $filter = array()) {
		if (is_array($mode)) {
			extract (array_merge(array('mode' => 'both'), $mode));
		}
		$mode = low($mode);
		if ($mode == 'post' || $mode == 'both') {
			$operators = array(
				'equal' => '= ',
				'greaterThan' => '> ',
				'greaterThanOrEqual' => '>= ',
				'lessThan' => '< ',
				'lessThanOrEqual' => '<= ',
				'notEqual' => '!= ',
				'like' => 'LIKE ',
				'notLike' => 'NOT LIKE ',
				'null' => 'NULL',
				'notNull' => 'NOT NULL',
				'between' => 'BETWEEN ',
				'in' => 'in');
			$this->controller->set('filterOptions', $operators);
			$filter = array();
			if ($this->controller->data) {
				$operator = false;
				foreach ($this->controller->data as $alias => $fields) {
					if (isset($this->controller->$alias)) {
						$inst = $this->controller->$alias;
					} elseif(isset($inst->{$this->controller->modelClass}->$alias)) {
						$inst = $inst->{$this->controller->modelClass}->$alias;
					} else {
						$inst = ClassRegistry::init($alias);
					}
					$i = 0;
					foreach ($fields as $field => $value) {
						$value = $fields[$field];
						$i++;
						if ($i % 2) {
							$field = str_replace('_type', '', $field);
							if (!$value) {
								if (!$this->controller->data[$alias][$field]) {
									continue;
								} else {
									$value = 'equal';
								}
							}
							$operator = $operators[$value];
							if ($value == 'null') {
								$filter[$alias . '.' . $field] = null;
								$fields[$field] =  null;
							} elseif ($value == 'notNull') {
								$filter[$alias . '.' . $field . ' !='] = null;
								$fields[$field] =  null;
							} elseif (in_array($value, array('like', 'notLike')) && strpos('%', $this->controller->data[$alias][$field]) === false) {
								$fields[$field] =  $fields[$field] . '%';
							}
						} elseif (!in_array($value, array(null, '', 'NOT NULL'))) {
							if (!$operator) {
								$this->controller->data[$alias][$field . '_type'] = 'equal';
								$operator = '= ';
							}
							if ($operator == 'in') {
								$filter[$alias . '.' . $field] = explode(',', $value);
								foreach ($filter[$alias . '.' . $field] as $key => $val) {
									$filter[$alias . '.' . $field][$key] = trim($val);
								}
							} elseif (is_array($value)) {
								$value = $inst->deconstruct($field, $value);
								if ($value) {
									$filter[$alias . '.' . $field . ' ' . $operator] = $value;
								}
							} else {
								$filter[$alias . '.' . $field . ' ' . $operator] = $value;
							}
						}
					}
				}
				$this->Session->write($this->controller->modelClass . '.filter', $filter);
				$this->Session->write($this->controller->modelClass . '.filterForm', $this->data);
			} elseif ($this->Session->check($this->controller->modelClass . '.filter')) {
				$filter = $this->Session->read($this->controller->modelClass . '.filter');
			}
		}
		if ($mode == 'named' || $mode == 'both') {
			$ignore = array_merge($this->__ignore, $ignore);
			$filter = am($filter, $this->controller->params['named']);
			foreach ($ignore as $ignore) {
				unset ($filter[$ignore]);
			}
		}
		foreach ($filter as $key => $condition) {
			if (!strpos($key, '.')) {
				unset($filter[$key]);
				$filter[$this->controller->modelClass . '.' . $key] = $condition;
			}
		}
		return $filter;
	}
}
?>