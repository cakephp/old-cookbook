<?php
/* SVN FILE: $Id: changes_controller.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for changes_controller.php
 *
 * Long description for changes_controller.php
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
 * ChangesController class
 *
 * @uses          AppController
 * @package       cookbook
 * @subpackage    cookbook.controllers
 */
class ChangesController extends AppController {
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array('order' => 'Change.created DESC', 'limit' => 50);
/**
 * beforeFilter method
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}
/**
 * admin_init method
 *
 * @return void
 * @access public
 */
	function admin_init($clean = false) {
		if ($clean) {
			$this->Change->deleteAll('1=1');
		}
		$recursive = -1;
		$fields = array('id', 'status', 'user_id', 'created', 'modified', 'reason');
		foreach ($this->Change->Revision->find('all', compact('recursive', 'fields')) as $revision) {
			$this->Change->create();
			$change = array(
				'revision_id' => $revision['Revision']['id'],
				'user_id' => $revision['Revision']['user_id'],
				'author_id' => $revision['Revision']['user_id'],
				'status_from' => 'new',
				'status_to' => 'pending',
				'created' => $revision['Revision']['created'],
				'comment' => $revision['Revision']['reason']?$revision['Revision']['reason']:'Edit submitted'
			);
			$this->Change->save($change);
			if ($revision['Revision']['status'] != 'pending') {
				switch ($revision['Revision']['status']) {
				case 'previous':
				case 'current':
					$status = 'published';
					$comment = 'publishing this change';
					break;
				case 'reject':
					$status = 'rejected';
					$comment = 'Change not accepted';
					break;
				default:
					$status = 'unknown';
					$comment = 'no comment';
				}
				$change = array(
					'revision_id' => $revision['Revision']['id'],
					'user_id' => 4, // attach all imports to John
					'author_id' => $revision['Revision']['user_id'],
					'status_from' => 'pending',
					'status_to' => $status,
					'created' => $revision['Revision']['modified'],
					'comment' => $comment
				);
				$this->Change->create();
				$this->Change->save($change);
			}
		}
		$this->redirect('/admin');
	}
/**
 * index method
 *
 * @return void
 * @access public
 */
	function index($nodeId = null) {
		$conditions = array(
			'Revision.title !=' => ''
		);
		$language = $this->params['lang'];
		if (isset($this->params['named']['language'])) {
			$language = $this->params['named']['language'];
		}
		if ($language != '*') {
			$conditions['Revision.lang'] = $language;
		}
		if ($nodeId) {
			$this->Change->Node->recursive = 0;
			$node = $this->Change->Node->read(array('Node.sequence', 'Revision.title'), $nodeId);
			$title = '';
			if ($node['Node']['sequence']) {
				$title .= $node['Node']['sequence'] . ' - ';
			}
			$title .= $node['Revision']['title'];
			$this->pageTitle = sprintf(__('Recent Changes for %s', true), $title);
			$conditions['Revision.node_id'] = $nodeId;
		} else {
			if ($language == '*') {
				$this->pageTitle = __('Recent Changes for all languages', true);
			} else {
				$this->pageTitle = __('Recent Changes', true);
			}

		}
		if (isset($this->params['named']['user'])) {
			$userId = $this->Change->User->field('id', array('username' => $this->params['named']['user']));
			if ($userId) {
				$conditions['Change.author_id'] = $userId;
				$this->pageTitle .= ' ' . sprintf(__('by %s', true), $this->params['named']['user']);
			}
		}
		if (isset($this->params['named']['status'])) {
			$conditions['Change.status_to'] = $this->params['named']['status'];
			$this->pageTitle .= ' ' . sprintf(__('restricted to status: %s', true), $this->params['named']['status']);
		}
		$this->data = $this->paginate($conditions);
	}
}
?>