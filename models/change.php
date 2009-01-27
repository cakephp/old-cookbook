<?php
/**
 * Short description for change.php
 *
 * Long description for change.php
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework <http://www.cakephp.org/>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     CakePHP(tm) : Rapid Development Framework <http://www.cakephp.org/>
 * @link          http://www.cakephp.org
 * @package       cookbook
 * @subpackage    cookbook.models
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Change class
 *
 * @uses          AppModel
 * @package       cookbook
 * @subpackage    cookbook.models
 */
class Change extends AppModel {
/**
 * name property
 *
 * @var string 'Change'
 * @access public
 */
	var $name = 'Change';
/**
 * order property
 *
 * @var string 'created'
 * @access public
 */
	var $order = 'Change.created';
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'User' => array('className' => 'Users.User', 'fields' => 'username'),
		'Author' => array('className' => 'Users.User', 'fields' => 'username'),
		'Revision' => array('fields' => array('id', 'node_id', 'user_id', 'lang', 'slug', 'title', 'status')),
		'Node' => array('foreignKey' => false, 'conditions' => array('Revision.node_id = Node.id'), 'fields' => array('id', 'sequence'))
	);
/**
 * beforeSave method
 *
 * Prevent duplicate log messages
 *
 * @return void
 * @access public
 */
	function beforeSave() {
		extract ($this->data[$this->alias]);
		if ($this->find('count', array('conditions' => compact('revision_id', 'status_from', 'status_to'), 'recursive' => -1))) {
			return false;
		};
		return true;
	}
}
?>