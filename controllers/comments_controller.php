<?php
/* SVN FILE: $Id: comments_controller.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for comments_controller.php
 *
 * Long description for comments_controller.php
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
 * @subpackage    cookbook.controllers
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CommentsController class
 *
 * @uses          AppController
 * @package       cookbook
 * @subpackage    cookbook.controllers
 */
class CommentsController extends AppController {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Comments';
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array('order' => 'Comment.created DESC', 'recursive' => 1);
/**
 * beforeFilter function
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allowedActions = array('index', 'view', 'recent');
	}
/**
 * admin_index function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function admin_index($nodeId=null) {
		$counts = $this->Comment->find('all', array(
			'recursive' => -1,
			'fields' => array('lang', 'COUNT(id) as count'),
			'order' => array('lang'),
			'group' => 'lang'
		));
		$counts = Set::combine($counts, '/Comment/lang', '/0/count');
		$language = isset($this->passedArgs['language'])?$this->passedArgs['language']:$this->params['lang'];
		$this->set(compact('counts', 'language'));

		$this->Comment->recursive = 2;
		$this->paginate['limit']= 10;
		$this->paginate['order'] = 'Comment.id desc';
		unset ($this->params['named']['language']);
		$this->params['named']['lang'] = $language;
		if ($nodeId) {
			$this->params['named']['node_id'] = $nodeId;
		}
		parent::admin_index();
	}
/**
 * add function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function add($id=null) {
		$Node = $this->Comment->Node->read(null , $id);
		if (!$Node) {
			$this->Session->setFlash(__('Invalid Node.', true));
			return $this->redirect($this->Session->read('referer'), null, true);
		}
		if (!empty ($this->data)) {
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['node_id'] = $Node['Node']['id'];
			$this->data['Comment']['revision_id'] = $Node['Revision']['id'];
			$this->Comment->create();
			if($this->Comment->save($this->data)) {
				$this->Session->setFlash(__('Your comment has been added', true));
				return $this->redirect($this->Session->read('referer'), null, true);
			} else {
				$this->Session->setFlash(__('Please correct errors below.', true));
			}
		}
		$this->set(compact('id','parentId', 'Node'));
	}
/**
 * index function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function index($id=null) {
		if (!$id && isset($this->params['id'])) {
			$id = $this->params['id'];
		}
		$this->params['id'] = $id;
		$this->Comment->Node->recursive = 0;
		$Node = $this->Comment->Node->read(null, $id);
		if (!$Node) {
			$this->Session->setFlash(__('Invalid Node.', true));
			return $this->redirect($this->Session->read('referer'), null, true);
		}
		if (($Node['Node']['comment_level'] > READ) && ($this->Auth->user('Level') < $Node['Node']['comment_level'])) {
			$this->Session->setFlash(__('No permissions to see comments for that section', true));
			$this->redirect($this->Session->read('referer'));
		}
		$title = '';
		if ($Node['Node']['sequence']) {
			$title .= $Node['Node']['sequence'] . ' - ';
		}
		$title .= $Node['Revision']['title'];
		$this->pageTitle = sprintf(__('Comments for %s', true), $title);
		$conditions['Comment.node_id'] = $Node['Node']['id'];
		$conditions['Comment.lang'] = $this->params['lang'];
		$conditions['Comment.published'] = 1;
		if ($this->params['url']['ext'] == 'html') {
			$order = 'Comment.created ASC';
		} else {
			$order = 'Comment.created DESC';
		}
		$recursive = 0;
		$this->data = $this->Comment->find('all', compact('conditions', 'order', 'recursive'));
		$userIds = array_unique(Set::extract($this->data, '{n}.Comment.user_id'));
		if ($userIds) {
			$commenters = $this->Comment->User->find('all', array('fields' => array('id', 'IF(display_name=1,realname,username) AS name'),
				'conditions' => array('id' => $userIds), 'recursive' => -1));
			if ($commenters) {
				$commenters = Set::combine($commenters, '{n}.User.id', '{n}.0.name');
			}
		} else {
			$commenters = array();
		}
		$this->set(compact('Node', 'commenters'));

	}
/**
 * view function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function view($id=null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Comment.', true));
			return $this->redirect($this->Session->read('referer'), null, true);
		}
		$this->data= $this->Comment->read(null,$id);
	}
/**
 * recent method
 *
 * @return void
 * @access public
 */
	function recent() {
		$conditions = array();
		$language = $this->params['lang'];
		if (isset($this->params['named']['language'])) {
			$language = $this->params['named']['language'];
		}
		if ($language != '*') {
			$conditions['Comment.lang'] = $language;
			$this->pageTitle = __('Recent Comments', true);
		} else {
			$this->pageTitle = __('Recent Comments for all languages', true);
		}
		if (isset($this->params['named']['node'])) {
			$conditions['Comment.node_id'] = $this->params['named']['node'];
		}
		if (isset($this->params['named']['user'])) {
			$userId = $this->Comment->User->field('id', array('username' => $this->params['named']['user']));
			if ($userId) {
				$conditions['Comment.user_id'] = $userId;
				$this->pageTitle .= ' ' . sprintf(__('by %s', true), $this->params['named']['user']);
			}
		}
		$this->data = $this->paginate($conditions);
		$userIds = array_unique(Set::extract($this->data, '{n}.Comment.user_id'));
		if ($userIds) {
			$commenters = $this->Comment->User->find('all', array('fields' => array('id', 'IF(display_name=1,realname,username) AS name'),
				'conditions' => array('id' => $userIds), 'recursive' => -1));
			if ($commenters) {
				$commenters = Set::combine($commenters, '{n}.User.id', '{n}.0.name');
			}
		} else {
			$commenters = array();
		}
		$this->set(compact('commenters'));

		$this->render('index');
	}
}
?>