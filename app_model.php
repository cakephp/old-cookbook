<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.app
 * @since         CakePHP v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
class AppModel extends Model {
/**
 * Convenience method to enable debugging from any point in a models use - and enable logging
 * from that point forward (will remove any queries logged before the call)
 *
 * @param int $val
 * @param bool $force
 * @access public
 * @return void
 */
	function debug($val = 2, $force = false) {
		if ($force || Configure::read()) {
			Configure::write('debug',$val);
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			if ($val > 1) {
				$db->fullDebug = true;
				$db->_queriesCnt = 0;
				$db->_queriesTime = null;
				$db->_queriesLog = array();
				$db->_queriesLogMax = 200;
			} else {
				$db->fullDebug = false;
			}
		}
	}
/**
 * searchConditions method
 *
 * Get generic search conditions - searching in all fields of the model, and checking associated models if
 * appropriate. Override to make more specific
 *
 * @param string $term
 * @param bool $extended
 * @return void
 * @access public
 */
	function searchConditions($term = '', $extended = false) {
		if (strpos($term, '%') === false) {
			$term = '%' . $term . '%';
		}
		$models = array($this);
		if ($this->recursive != -1) {
			foreach (array('belongsTo', 'hasOne') as $association) {
				foreach ($this->$association as $alias => $modelArray) {
					if (is_string($modelArray)) {
						$alias = $modelArray;
					}
					if ($this->$alias->useDbConfig != $this->useDbConfig) {
						continue;
					}
					$models[] = $this->$alias;
				}
			}
		}
		$conditions = array();
		foreach ($models as $model) {
			foreach ($model->schema() as $key => $details) {
				if (in_array($details['type'], array('string', 'text'))) {
					$conditions['OR'][$model->alias . '.' . $key . ' LIKE'] = $term;
				}
			}
		}
		return $conditions;
	}
/**
 * noHtml method
 *
 * @param mixed $vals
 * @return void
 * @access public
 */
	function noHtml($vals) {
		foreach ($vals as $val) {
			$noHtml = strip_tags($val);
			if ($noHtml != $val) {
				return false;
			}
		}
		return true;
	}
/**
 * schema method
 *
 * Prevent fullDebug for describe queries, so they aren't in the log
 *
 * @param bool $field
 * @return void
 * @access public
 */
	function schema($field = false) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$fullDebug = $db->fullDebug;
		$db->fullDebug = false;
		$return = parent::schema($field);
		$db->fullDebug = $fullDebug;
		return $return;
	}

}
?>