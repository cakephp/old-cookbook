<?php
/* SVN FILE: $Id: comment.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for comment.php
 *
 * Long description for comment.php
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
 * Comment class
 *
 * @uses          AppModel
 * @package       cookbook
 * @subpackage    cookbook.models
 */
class Comment extends AppModel {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name= 'Comment';
/**
 * belongsTo variable
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Node',
		'Revision' => array(
			'foreignKey' => false,
			'conditions' => array(
				'Revision.status' => 'current',
				'Revision.node_id = Comment.node_id',
				'Revision.lang = Comment.lang'
			),
			'fields' => array('slug', 'title')
		),
		'User' => array('className' => 'Users.User')
	);
/**
 * validate variable
 *
 * @var array
 * @access public
 */
	var $validate = array(
			      'title'=> array(
					      'required'=> VALID_NOT_EMPTY
					     ),
			      'body'=>array(
					    'required'=> VALID_NOT_EMPTY
					   )
			     );
/**
 * beforeSave function
 *
 * @access public
 * @return void
 */
	function beforeSave() {
		if (
			(array_key_exists('lang', $this->data['Comment']) && !$this->data['Comment']['lang']) ||
			(!$this->id && !array_key_exists('lang', $this->data['Comment']))
		) {
			$this->data['Comment']['lang'] = $this->Node->language;
		}
		return true;
	}
}
?>