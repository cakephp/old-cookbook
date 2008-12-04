<?php
/* SVN FILE: $Id: nodes_controller.php 704 2008-11-19 12:15:11Z AD7six $ */
/**
 * Short description for nodes_controller.php
 *
 * Long description for nodes_controller.php
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
 * @version       $Revision: 704 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-19 13:15:11 +0100 (Wed, 19 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * NodesController class
 *
 * @uses          AppController
 * @package       cookbook
 * @subpackage    cookbook.controllers
 */
class NodesController extends AppController {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Nodes';
/**
 * cacheAction variable
 *
 * Cache times are set in the actions, as if set here, given the numerous routes, a large number of permutations
 * need to be defined
 *
 * @var string
 * @access public
 */
	var $cacheAction = false;
/**
 * currentNode variable
 *
 * @var mixed
 * @access public
 */
	var $currentNode;
/**
 * currentPath variable
 *
 * @var mixed
 * @access public
 */
	var $currentPath;
/**
 * paginate variable
 *
 * @var array
 * @access public
 */
	var $paginate = array ('limit' => 2);
/**
 * beforeFilter function
 *
 * First, check if the url matches routes and if not redirect
 * Second, check the slug matches and if not redirect
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		parent::beforeFilter();
		if (Configure::read() && !$this->Node->find('count')) {
			$this->Node->initialize();
		}
		$this->Node->Revision->currentUserId = $this->Node->currentUserId;
		if (!isset($this->params['requested']) && $this->action != 'todo') {
			$urlIsCorrect = true;
			if ((!isset($this->params['admin']) &&
				(($this->params['lang'] == 'en' && $this->params['url']['url'] != '/')
				|| ($this->params['lang'] != 'en' && $this->params['url']['url'] != '/' . $this->params['lang'])))
			) {
				if ($this->action == 'view' && isset($this->params['pass'][0]) && $this->params['pass'][0] == Configure::read('Site.homeNode')) {
					if ($this->params['lang'] == 'en') {
						$urlIsCorrect = false;
						$url = '/';
					} elseif ($this->params['url']['url'] != $this->params['lang']) {
						$urlIsCorrect = false;
						$url = '/' . $this->params['lang'];
					}
				}
				if ($this->params['lang'] != 'en') {
					$this->params['pass']['lang'] = $this->params['lang'];
				}
				if ($this->params['url']['ext'] == 'html') {
					$normalized = Router::normalize($this->params['pass']);
				} else {
					$normalized = Router::normalize(am($this->params['pass'], array('ext' => $this->params['url']['ext'])));
				}
				if ($normalized != '/' . $this->params['url']['url']) {
					$urlIsCorrect = false;
					$url = '/' . $normalized;
				}
			}
			if (!isset($this->params['admin']) && isset($this->params['pass'][0])) {
				$urlSlug = isset($this->params['pass'][1])?$this->params['pass'][1]:'';
				$conditions['Node.id'] = $this->params['pass'][0];
				$fields = array ('Node.id', 'Node.id', 'Revision.slug');
				$recursive = 0;
				$result = $this->Node->find('first', compact('conditions', 'fields', 'recursive'));
				if ($result) {
					$this->Node->id = $this->currentNode = $result['Node']['id'];
				} else {
					$this->redirect($this->Session->read('referer'), null, true);
				}
				$base = '/';
				$here = '/' . $this->params['url']['url'];
				if ($this->params['lang'] != 'en') {
					$base .= $this->params['lang'] . '/';
					if (strlen($here) < 4 ) {
						$here .= '/';
					}
				}
				if (!($this->data)&&($base != $here)) {
					if ($urlSlug<>$result['Revision']['slug']) {
						$urlIsCorrect = false;
						$url = array($result['Node']['id'], $result['Revision']['slug']);
					}
				}
			} elseif (isset($this->params['pass'][0])) {
				$this->Node->id = $this->currentNode = $this->params['pass'][0];
			}
			if (!$urlIsCorrect) {
				if ($this->params['url']['ext'] != 'html') {
					$url['ext'] = $this->params['url']['ext'];
				}
				$this->redirect($url, 301);
			}
			$fields = array ('Node.id', 'Node.depth', 'Node.id', 'Node.lft', 'Node.rght', 'Node.comment_level', 'Node.edit_level', 'Revision.id', 'Revision.slug', 'Revision.title', 'Revision.content');
			if (!isset($this->currentNode)) {
				$topNode = $this->Node->find(array('Node.depth' => '0'), array('Node.id'), null, 0);
				$this->currentNode = $topNode['Node']['id'];
			}
			$this->currentPath = $this->Node->getPath($this->currentNode, $fields, 0);
			$this->set('currentPath', $this->currentPath);
		}
		if (!isset($this->params['url']['ext']) || $this->params['url']['ext'] != 'xml') {
			$this->Auth->allowedActions = array('index', 'view', 'single_page', 'toc', 'collections',
				'app_name', 'compare', 'history', 'stats', 'todo');
		}
	}
/**
 * beforeRender function
 *
 * @access public
 * @return void
 */
	function beforeRender() {
		$crumbPath = isset($this->currentPath) ? $this->currentPath : array();
		if (!isset($this->params['admin'])) {
			array_shift ($crumbPath);
			array_shift ($crumbPath);
		}
		$this->set('viewAllLevel', $this->Node->viewAllLevel);
		$this->set('crumbPath', $crumbPath);
		if (!isset($this->params['admin'])) {
			$titles = Set::extract($this->currentPath, '{n}.Revision.title');
			if ($titles) {
				array_shift($titles);
				$this->pageTitle = implode(' :: ', array_reverse($titles));
			}
		}
		parent::beforeRender();
	}
/**
 * admin_delete function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_delete($id = null) {
		$parent = $this->Node->field('parent_id');
		if ($this->Node->delete()) {
			$this->Node->reset($parent);
		}
		return $this->redirect(array('action' => 'toc', $parent), null, true);
	}
/**
 * admin_edit method
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function admin_edit($id) {
		if (!$this->Node->hasAny(array('id' => $id))) {
			$this->redirect(array('action' => 'index'), null, true);
		}
		if (!empty ($this->data)) {
			if ($this->Node->save($this->data)) {
				if ($this->data['Node']['show_subsections_inline']) {
					$lft = $this->Node->field('lft');
					$rght = $this->Node->field('rght');
					$this->Node->updateAll(array('sequence' => null, 'show_in_toc' => 0), array('lft >' => $lft, 'rght <' => $rght));
				}
				$this->Node->reset($this->Node->field('parent_id'));
				$this->Session->setFlash('Node updated');
				$this->redirect($this->Session->read('referer'), null, true);
			} else {
				$this->Session->setFlash('Please correct the errors below.');
			}
		} else {
			$this->data = $this->Node->read(null, $id);
		}
	}
/**
 * admin_export method
 *
 * @return void
 * @access public
 */
	function admin_export($id = null) {
		$this->data = $this->Node->exportData($id);
		$filename = 'contents_backup_' . date('Ymd-Hi') . '.xml';
		$this->RequestHandler->renderAs($this, 'xml');
		if (!isset($this->params['requested'])) {
			$this->RequestHandler->respondAs('xml', array('attachment' => $filename));
		}
		$this->render('view_all');
		$file = new File(TMP . $filename);
		$file->write(ob_get_clean());
	}
/**
 * admin_import method
 *
 * @return void
 * @access public
 */
	function admin_import() {
		if ($this->data) {
			if ($this->data['Node']['take_backup']) {
				$this->requestAction('/admin/nodes/export', array('return'));
			}
			if (!$this->data['Node']['file']['error']) {
				$xml = file_get_contents($this->data['Node']['file']['tmp_name']);
				if (!file_exists(TMP . $this->data['Node']['file']['name'])) {
					$file = new File(TMP . $this->data['Node']['file']['name']);
					$file->write($xml);
				}
			} elseif ($this->data['Node']['backup']) {
				$xml = file_get_contents(TMP . $this->data['Node']['backup']);
			} else {
				$this->Session->setFlash('No Xml file to import');
				$this->redirect(array());
			}
			list($return, $messages, $actions) = $this->Node->import($xml, $this->data['Node'], $this->Auth->user('id'));
			extract($actions);
			if ($messages) {
				$message = implode ($messages, '. ') . '.';
			} else {
				$message = 'No messages - possible problem in node import function';
			}
			$this->Session->setFlash($message);
			if ($mods && !$this->data['Node']['auto_approve']) {
				$this->redirect(array('controller' => 'revisions', 'action' => 'pending'));
			} else {
				$this->redirect(array('action' => 'index'));
			}
			$this->autoRender = false;
		} else {
			$schema = $this->Node->schema('id');
			if ($schema['length'] != 36 && $this->Node->find('count')) {
				$this->Session->setFlash('The import is designed only to work with UUID installs. Adding new content is disabled - only edits and moves possible.');
			}
		}
		$tmp = new Folder(TMP);
		$backups = $tmp->find('.*\.xml');
		if ($backups) {
			$backups = array_combine($backups, $backups);
			$backups = array_reverse($backups);
		} else {
			$backups = array();
		}
		$this->set('backups', $backups);
	}
/**
 * admin_index function
 *
 * @access public
 * @return void
 */
	function admin_index() {
		$this->paginate['limit'] = 10;
		$this->paginate['order'] = 'Node.lft';
		$this->paginate['recursive'] = 0;
		$this->Node->recursive = 0;
		if (isset($this->params['named']['restrict_to'])) {
			$restrictTo = $this->params['named']['restrict_to'];
			unset($this->params['named']['restrict_to']);
			$limits = $this->Node->find('first', array('conditions' => array('Node.id' => $restrictTo), 'fields' => array('lft', 'rght', 'Revision.title')));
			$this->params['named']['lft >='] = $limits['Node']['lft'];
			$this->params['named']['rght <='] = $limits['Node']['rght'];
			$this->Session->setFlash('Only "' . $limits['Revision']['title'] . '" and below');
		}
		$conditions = array('Revision.status' => 'pending', 'Revision.lang' => $this->params['lang']);
		$recursive = -1;
		$fields = array('DISTINCT node_id');
		$pendingUpdates = $this->Node->Revision->find('all', compact('conditions', 'recursive', 'fields'));
		$pendingUpdates = Set::extract($pendingUpdates, '{n}.Revision.node_id');
		parent::admin_index();
		$userIds = Set::extract($this->data, '{n}.Revision.user_id');
		$users = $this->Node->Revision->User->find('list', array('conditions' => array('User.id' => $userIds)));
		$this->set(compact('pendingUpdates', 'users'));
	}
/**
 * admin_merge method
 *
 * @TODO Finish this logic
 * @param mixed $id
 * @return void
 * @access public
 */
	function admin_merge($id) {
		if ($this->data) {
			if (!array_key_exists('confirmation', $this->data['Node'])) {
				$this->data['Node']['confirmation'] = false;
			} elseif ($this->data['Node']['confirmation']) {
				if ($this->Node->merge($this->data['Node']['id'], $this->data['Node']['merge_id'])) {
					$this->Session->setFlash('content merged successfully');
					$this->redirect(array('admin' => false, 'action' => 'view', $this->data['Node']['merge_id']));
				}
			}
			$preview = array('title' => '', 'content' => '');
			if ($this->data['Node']['merge_id']) {
				$preview['title'] = $this->Node->Revision->field('title', array('Revision.node_id' => $this->data['Node']['merge_id'], 'Revision.status' => 'current', 'lang' => $this->params['lang']));
				$preview['content'] = $this->Node->Revision->field('content', array('Revision.node_id' => $this->data['Node']['id'], 'Revision.status' => 'current', 'lang' => $this->params['lang']));
				$this->set(compact('preview'));
			}
			$this->Node->id = $this->data['Node']['id'];
		} else {
			$this->data = $this->Node->read();
		}
		$conditions = array();
		$depth = 0; //$this->Node->field('depth', array('id' => $this->data['Node']['id']));
		if ($depth > 2) {
			$book = $this->Node->book($this->data['Node']['id'], array('lft', 'rght'));
			$conditions['Node.lft <'] = $book['lft'];
			$conditions['Node.rght >'] = $book['rght'];
		} elseif ($depth == 2) {
			$collection = $this->Node->collection($this->data['Node']['id'], array('lft', 'rght'));
			$conditions['Node.lft <'] = $collection['lft'];
			$conditions['Node.rght >'] = $collection['rght'];
		}
		$this->set('nodes', $this->Node->generateTreeList($conditions));
	}
/**
 * admin_move method
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function admin_move($id) {
		if ($this->data) {
			$this->Node->id = $this->data['Node']['id'];
			if ($this->Node->saveField('parent_id', $this->data['Node']['parent_id'])) {
				$this->Node->reset();
				$this->Session->setFlash('Parent changed');
			}
			$this->redirect($this->Session->read('referer'));
		} else {
			$this->data = $this->Node->read();
		}
		$conditions = array();
		$depth = 0; //$this->Node->field('depth', array('id' => $this->data['Node']['id']));
		if ($depth > 2) {
			$book = $this->Node->book($this->data['Node']['id'], array('lft', 'rght'));
			$conditions['Node.lft <'] = $book['lft'];
			$conditions['Node.rght >'] = $book['rght'];
		} elseif ($depth == 2) {
			$collection = $this->Node->collection($this->data['Node']['id'], array('lft', 'rght'));
			$conditions['Node.lft <'] = $collection['lft'];
			$conditions['Node.rght >'] = $collection['rght'];
		}
		$this->set('nodes', $this->Node->generateTreeList($conditions));
	}
/**
 * admin_move_up function
 *
 * @param mixed $nodeId
 * @param int $step
 * @access public
 * @return void
 */
	function admin_move_up($nodeId, $step = 1) {
		if (!$this->Node->moveUp($nodeId, $step)) {
			$this->Session->setFlash('Could not move previous.');
		}
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_move_down function
 *
 * @param mixed $nodeId
 * @param int $step
 * @access public
 * @return void
 */
	function admin_move_down($nodeId, $step = 1) {
		if (!$this->Node->moveDown($nodeId, $step)) {
			$this->Session->setFlash('Could not move after.');
		}
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_promote function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function admin_promote($nodeId) {
		$this->Node->id = $nodeId;
		$node = $this->Node->read(null, $nodeId);
		$parent = $this->Node->getParentNode();
		if ($parent) {
			$this->Node->saveField('parent_id', $parent['Node']['parent_id']);
			$this->Node->reset($this->Node->field('parent_id'));
		} else {
			$this->Session->setFlash($node['Revision']['title'] . ' has no parent, cannot promote.');
		}
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_recover_tree function
 *
 * @param string $mode
 * @access public
 * @return void
 */
	function admin_recover_tree($mode = 'parent') {
		set_time_limit(1000);
		if ($mode == 'parent') {
			if ($this->Node->recover()) {
				$this->Session->setFlash('Based upon the parent id fields, the lft and rght fields have been repopulated.');
			} else {
				$this->Session->setFlash('Recovery was not successful!');
			}
		} else {
			if ($this->Node->recoverTree('MPTT')) {
				$this->Session->setFlash('Based upon the left and right fields, the parent Id has been repopulated.');
			} else {
				$this->Session->setFlash('Recovery was not successful!');
			}
		}
		$this->Node->reset();
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_remove function
 *
 * @param mixed $nodeId
 * @param bool $delete
 * @access public
 * @return void
 */
	function admin_remove($nodeId, $delete = false) {
		$parent = $this->Node->field('parent_id');
		$this->Node->removeFromTree($nodeId, $delete);
		$this->Node->reset($parent);
		return $this->redirect(array('action' => 'toc', $parent), null, true);
	}
/**
 * admin_reset function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_reset($id = null) {
		$this->Node->unbindModel(array('hasOne' => array('Revision')), false);
		$this->Node->reset($id);
		$this->Session->setFlash('Depths and Sequences regenerated');
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_reset_depths function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_reset_depths($id = null) {
		$this->Node->unbindModel(array('hasOne' => array('Revision')), false);
		$this->Node->resetDepths($id);
		$this->Session->setFlash('Depth values updated based upon position in tree');
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_reset_sequences function
 *
 * @param mixed $parentId
 * @access public
 * @return void
 */
	function admin_reset_sequences($parentId = null) {
		$this->Node->unbindModel(array('hasOne' => array('Revision')), false);
		$prefix = null;
		if ($parentId) {
			$prefix = $this->Node->field('Node.id');
		}
		$this->Node->resetSequences($parentId, $prefix);
		$this->Session->setFlash('Sequence values updated based upon position in tree');
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_set_parent function
 *
 * @param mixed $nodeId
 * @param mixed $parentId
 * @access public
 * @return void
 */
	function admin_set_parent($nodeId, $parentId = null) {
		if ($parentId) {
			$parent = $this->Node->field('parent_id', array('Node.id' => $parentId));
		} else {
			$parent = null;
		}
		$this->Node->saveField('parent_id', $parentId);
		$this->Node->reset($parent);
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_toc function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_toc($id = null) {
		$this->helpers[] = 'Tree';
		$fields = array('Node.*', 'Revision.title', 'Revision.slug');
		if ($id) {
			$conditions['OR']['Node.parent_id'] = array($id, $this->Node->field('parent_id'));
			$conditions['OR'][] = array(
				'Node.lft <=' => $this->Node->field('lft', array('Node.id' => $id)),
				'Node.rght >=' => $this->Node->field('rght', array('Node.id' => $id))
			);
			$this->data = $this->Node->find('all', compact('conditions', 'fields', 'recursive'));
		} else {
			$this->data = array($this->Node->find('first', compact('conditions', 'fields', 'recursive')));
		}
	}
/**
 * admin_verify_tree function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_verify_tree($detailed = true) {
		$return = $this->Node->verify($detailed);
		if ($return === true) {
			$this->Session->setFlash('Tree is valid');
			return $this->redirect($this->Session->read('referer'), null, true);
		} else {
			$flash = array('Errors Found:');
			foreach ($return as $error) {
				$flash[] = '&nbsp;&nbsp;' . implode($error, ' ');
			}
			$flash = implode($flash, '<br />');
			$this->Session->setFlash($flash);
			return $this->redirect($this->Session->read('referer'), null, true);
		}
	}
/**
 * admin_view function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function admin_view($nodeId) {
		$this->Node->viewAllLevel = 99;
		$this->view($nodeId);
	}
/**
 * collections method
 *
 * Only called by requestAction
 *
 * @access public
 * @return void
 */
	function collections() {
		$this->autoRender = false;
		$this->Node->setLanguage($this->params['lang']);
		$recursive = 0;
		$first = $this->Node->field('id', array('Node.parent_id' => null));
		$conditions = array('Node.parent_id' => $first);
		$fields = array('Node.id', 'Revision.title', 'Revision.slug');
		$data = $this->Node->find('all', compact('recursive', 'conditions', 'fields'));
		cache('views/collections_' . $this->params['lang'], serialize($data), CACHE_DURATION);
		return $data;
	}
/**
 * app_name method
 * 
 * Only called by requestAction
 *
 * @param string $lang
 * @return void
 * @access public
 */
	function app_name() {
		$this->autoRender = false;
		$this->Node->setLanguage($this->params['lang']);
		$recursive = 0;
		$conditions = array('Node.parent_id' => null);
		$fields = array('Revision.title', 'Revision.content');
		$data = $this->Node->find('first', compact('recursive', 'conditions', 'fields'));
		$return['name'] = $data['Revision']['title'];
		$return['tag_line'] = strip_tags($data['Revision']['content']);
		cache('views/app_name_' . $this->params['lang'], serialize($return), CACHE_DURATION);
		return $return;
	}
/**
 * index method
 *
 * @return void
 * @access public
 */
	function index() {
		$id = Configure::read('Site.homeNode');
		$this->Node->id = $this->currentNode = $id;
		if (!$this->Node->exists()) {
			$this->Node->id = $this->currentNode = $id = $this->Node->field('id', array('Node.depth' => 2));
		}
		if ($this->params['url']['ext'] == 'xml') {
			$url = array('action' => 'single_page', $id, $this->Node->Revision->field('slug', array('node_id' => $id)), 'ext' => 'xml');
			$this->redirect($url);
		}
		$fields = array ('Node.id', 'Node.depth', 'Node.id', 'Node.lft', 'Node.rght', 'Node.comment_level', 'Node.edit_level', 'Revision.id', 'Revision.slug', 'Revision.title', 'Revision.content');
		$this->currentPath = $this->Node->getPath($this->currentNode, $fields, 0);
		$this->set('currentPath', $this->currentPath);
		$this->_view();
	}
/**
 * toc function
 *
 * Generate the data required for either the toc page, or the menu navigation links
 * For the menu data (generated via a requestAction call) The logic is find all nodes that
 * 	Are direct children of what's being displayed
 * 	Are siblings of what's being displayed
 * 	Are in the path of what's being displayed
 * 	Are top level items
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function toc($nodeId = null) {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		if (isset($this->params['requested'])) {
			$this->currentPath = $this->params['currentPath'];
			$this->currentNode = $this->params['currentNode'];
			$this->set(array('currentPath' => $this->currentPath, 'currentNode' => $this->currentNode));
		}
		$this->Node->recursive = 0;
		$fields = array('Node.*', 'Revision.id', 'Revision.status', 'Revision.lang', 'Revision.title', 'Revision.slug');
		$path = $this->currentPath;
		if (count($path) > 2) {
			$direct = false;
			array_shift($path);
			array_shift($path);
			$ids = Set::extract($path, '/Node/id');
		} else {
			$direct = true;
		}
		if (isset($this->params['requested'])) {
			if ($direct) {
				$conditions['Node.parent_id'] = $this->currentNode['id'];
				return $this->Node->find('all', compact('conditions', 'fields', 'recursive', 'order'));
			}
			$conditions['Node.show_in_toc'] = 1;
			$conditions['Node.parent_id'] = $ids;
			$recursive = 0;
			$order = 'Node.lft ASC';
			$this->data = $this->Node->find('all', compact('conditions', 'fields', 'recursive', 'order'));
			return $this->data;
		} else {
			$book = array_shift($path);
			$conditions['Node.show_in_toc'] = 1;
			$conditions['Node.lft >='] = $book['Node']['lft'];
			$conditions['Node.rght <='] = $book['Node']['rght'];
			//$conditions['Node.depth <'] = 7;
			$this->data = $this->Node->find('all', compact('conditions', 'fields', 'recursive', 'order'));
		}
		$this->set('data', $this->data);
		$this->data = false;
	}
/**
 * todo method
 *
 * List all sections that are not translated
 *
 * @return void
 * @access public
 */
	function todo() {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$this->paginate['limit'] = 20;
		$conditions = array('Revision.status' => 'pending', 'Revision.lang' => $this->params['lang']);
		$recursive = -1;
		$fields = array('DISTINCT node_id');
		$pendingUpdates = $this->Node->Revision->find('all', compact('conditions', 'recursive', 'fields'));
		$pendingUpdates = Set::extract($pendingUpdates, '{n}.Revision.node_id');
		$this->set(compact('pendingUpdates'));
		if ($this->params['lang'] == 'en') {
			$this->data = $this->paginate(array('Revision.content LIKE' => '%<h%'));
			return $this->render('english_todo');
		} else {
			$this->data = $this->paginate(array('OR' => array('Revision.id' => null, 'Revision.flags LIKE' => 'englishChanged')));
		}
	}
/**
 * view function
 *
 * @param mixed $nodeId
 * @param mixed $slug
 * @access public
 * @return void
 */
	function view($nodeId = false, $slug = '') {
		if ($this->params['url']['ext'] == 'xml') {
			$this->redirect(am(array('action' => 'single_page'), $this->passedArgs));
		}
		if (!$nodeId || $nodeId == Configure::read('Site.homeNode')) {
			$this->redirect(array('index'));
		}
		$this->Node->id = $this->currentNode;
		$depth = $this->Node->field('Node.depth');
		if ($depth < $this->Node->viewAllLevel) {
			$this->_view();
		} else {
			$this->_view(true);
		}
	}
/**
 * single_page function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function single_page($nodeId = null) {
		if ($this->params['url']['ext'] == 'xml') {
			$this->data = $this->Node->exportData($this->currentNode);
			$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
			$this->render('view_all');
			return;
		}
		if ($this->currentNode) {
			$this->data = $this->Node->id = $this->currentNode;
			$depth = $this->Node->field('depth');
			if ($depth < 2 || $depth >= $this->Node->viewAllLevel) {
				$this->redirect(array('action' => 'view', $this->currentNode), null, true);
			}
			$this->Node->viewAllLevel = $depth;
		} else {
			$this->redirect('/', null, true);
		}
		$this->_view(true);
	}
/**
 * stats method
 *
 * @return void
 * @access public
 */
	function stats() {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$languages = Configure::read('Languages.all');
		$db =& ConnectionManager::getDataSource($this->Node->useDbConfig);
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$nodes = $this->Node->find('count', array('recursive' => -1));
		$counts = $this->Node->Revision->find('all', array(
			'conditions' => array('status' => 'current', 'lang' => $languages),
			'fields' => array('lang', 'COUNT(Revision.id) AS count'),
			'group' => 'lang',
			'order' => 'count DESC',
			'recursive' => -1));
		$counts = Set::combine($counts, '/Revision/lang', '/0/count');
		foreach ($languages as $lang) {
			if (!isset($counts[$lang])) {
				$counts[$lang] = 0;
			}
			if ($lang == 'en') {
				$userLimit = 15;
			} else {
				$userLimit = 6;
			}
			$data[$lang]['top_contributors'] = $this->Node->Revision->find('all', array(
				'conditions' => array('status' => 'current', 'lang' => $lang, 'user_id >' => 0),
				'fields' => array('user_id', 'COUNT(Revision.id) AS count'),
				'group' => 'user_id',
				'order' => 'count DESC',
				'limit' => $userLimit,
				'recursive' => -1));
			$data[$lang]['last_update'] = $this->Node->Revision->field(
				$db->calculate($this->Node, 'max', array('created')),
				array('status' => 'current', 'lang' => $lang));
		}
		$users = Set::extract($data, '{[a-z]+}.top_contributors.{n}.Revision.user_id');
		$userIds = array();
		foreach ($users as $lang => $ids) {
			if ($ids) {
				$userIds = am($userIds, $ids);
			}
		}
		$userIds = array_unique($userIds);
		$users = $this->Node->Revision->User->find('all', array(
			'conditions' => array('User.id' => $userIds),
			'fields' => array('User.id', 'User.username', 'User.realname', 'Profile.url'),
			'recursive' => 0));
		$users = Set::combine($users, '/User/id', '/');
		$this->set(compact('counts', 'data', 'nodes', 'users'));
	}
/**
 * add function
 *
 * @param mixed $parentId
 * @access public
 * @return void
 */
	function add($parentId = null) {
		if (!isset($this->params['admin']) && !$parentId && $this->Node->hasAny(array('Node.depth' => '> 0'))) {
			$this->Session->setFlash(__('Invalid Collection', true));
			return $this->redirect($this->Session->read('referer'), null, true);
		}
		$this->Node->create();
		if (isset($this->data['Revision']['under_node_id'])) {
			if (isset ($this->data['Revision']['content2'])) {
				$this->data['Revision']['content'] = $this->data['Revision']['content2'];
				unset ($this->data['Revision']['content2']);
			}

			$this->data['Revision']['status'] = 'pending';
			$this->data['Revision']['user_id'] = $this->Auth->user('id');
			$this->data['Revision']['node_id'] = 0;
			$parentId = $this->data['Revision']['under_node_id'];
			$this->Node->Revision->itsAnAdd = true;
			if ($this->Node->Revision->save($this->data, true)) {
				if ($this->Session->read('Auth.User.Level') == ADMIN) {
					$this->Node->Revision->publish($this->Node->Revision->id, $this->data['Revision']['reason'], true);
					if (!$this->data['Node']['show_in_toc']) {
						$this->Node->save($this->data);
						$this->Node->reset($parentId);
						$this->redirect(array('action' => 'view', $parentId));
					}
					$this->redirect(array('action' => 'view', $this->Node->id));
				}
				$this->Session->setFlash(__('Thanks for your contribution! Your suggestion has been submitted for review.', true));
				return $this->redirect($this->Session->read('referer'));
			} else {
				$this->data['Revision'] = $this->Node->Revision->data['Revision'];
			}
		} else {
			$this->data['Node']['show_in_toc'] = true;
			$this->data['Revision']['preview'] = true;
		}
		$parent = $this->currentPath[count($this->currentPath) -1]['Node'];
		if (!isset($this->data['Revision']['under_node_id']) && $parentId) {
			$this->data['Revision']['under_node_id'] = $parentId;
		}
		if ($parent['rght'] == $parent['lft'] + 1) {
			$afters = false;
		} elseif (!isset($this->data['Revision']['after_node_id']) && !$parent) {
			$afters[-1] = '-choose parent first-';
		} elseif (!isset($this->data['Revision']['after_node_id']) && $parent) {
			$afters[-1] = '-choose parent first-';
			$conditions = array('Node.parent_id' => $parent['id']);
			$order = 'Node.lft asc';
			$afters = array($parent['id'] => 'first');
			$more = $this->Node->generateTreeList($conditions, null, array('{0} {1}', '{n}.Node.id', '{n}.Revision.title'), null, 0);
			foreach ($more as $id => $display) {
				$afters[$id] = $display;
				$this->data['Revision']['after_node_id'] = $id;
			}
		} elseif (isset($this->data['Revision']['after_node_id'])) {
			if ($this->data['Revision']['after_node_id']<0) {
				$this->data['Revision']['after_node_id'] = 0;
			}
			$conditions = array('Node.parent_id' => $this->data['Revision']['under_node_id']);
			$order = 'Node.lft asc';
			$afters = array($parent['id'] => 'first');
			$more = $this->Node->generateTreeList($conditions, null, array('{0} {1}', '{n}.Node.id', '{n}.Revision.title'), null, 0);
			foreach ($more as $id => $display) {
				$afters[$id] = $display;
			}
		}
		$conditions = array();
		if (!isset($this->params['admin'])) {
			$conditions['Node.lft >='] = $parent['lft'];
			$conditions['Node.rght <='] = $parent['rght'];
		}
		$parents = $this->Node->generateTreeList($conditions, null, array('{0} {1}', '{n}.Node.id', '{n}.Revision.title'), null, 0);

		$this->helpers[] = 'Highlight';
		$this->set(compact('parents', 'afters'));
	}
/**
 * compare method
 *
 * @param mixed $id
 * @param mixed $slug
 * @param string $lang
 * @return void
 * @access public
 */
	function compare($id, $slug, $lang = 'en') {
		if ($this->Node->language == $lang) {
			$this->Session->setFlash(__('This function is for comparing different (public) language content', true));
			$this->redirect(array('action' => 'view', $id, $slug));
		}
		if (!isset($this->params['admin'])) {
			$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		}
		$recursive = 1;
		$this->Node->id = $this->currentNode;
		$fields = array('Node.id', 'Node.sequence', 'Revision.id', 'Revision.title', 'Revision.content', 'Revision.lang');
		$this->data['original'] = $this->Node->findById($this->currentNode, $fields, null, $recursive);
		$this->Node->setLanguage($lang);
		$this->data['compare'] = $this->Node->findById($this->currentNode, $fields, null, $recursive);
		$this->helpers[] = 'Highlight';
		$this->helpers[] = 'Diff';
		$this->render('compare');
	}
/**
 * edit function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function edit($id) {
		if (!empty ($this->data)) {
			if (isset ($this->data['Revision']['content2'])) {
				$this->data['Revision']['content'] = $this->data['Revision']['content2'];
				unset ($this->data['Revision']['content2']);
			}
			$this->data['Revision']['node_id'] = $id;
			$this->data['Revision']['status'] = 'pending';
			$this->data['Revision']['user_id'] = $this->Auth->user('id');
			if ($this->Node->Revision->save($this->data)) {
				if ($this->Session->read('Auth.User.Level') == ADMIN) {
					$this->Node->Revision->publish($this->Node->Revision->id, $this->data['Revision']['reason'], true);
					if ($this->Node->field('show_in_toc') != $this->data['Node']['show_in_toc']) {
						$this->Node->save($this->data);
						$parent = $this->Node->field('parent_id');
						$this->Node->reset($parent);
						if (!$this->data['Node']['show_in_toc']) {
							$this->redirect(array('action' => 'view', $parent));
						}
					}
					$this->redirect(array('action' => 'view', $id));
				}
				$this->Session->setFlash(__('Thanks for your contribution! Your suggestion has been submitted for review.', true));
				return $this->redirect($this->Session->read('referer'));
			} else {
				$this->data['Revision'] = $this->Node->Revision->data['Revision'];
			}
		} else {
			$this->data = $this->Node->read(null);
			$this->data['Revision']['reason'] = '';
			$this->data['Revision']['preview'] = true;
			$this->data['Node']['show_in_toc'] = true;
		}
		$Attachment = ClassRegistry::init('Attachment');
		$recursive = -1;
		$conditions = array('Attachment.class' => 'Node', 'Attachment.foreign_id' => $id);
		$this->set('attachments', $Attachment->find('all', compact('conditions', 'recursive')));
		$this->helpers[] = 'Highlight';
		$this->render('edit');
	}
/**
 * history method
 *
 * @param mixed $id
 * @param mixed $slug
 * @param bool $englishToo
 * @return void
 * @access public
 */
	function history($id, $slug, $englishToo = false) {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$language = array($this->params['lang']);
		if ($englishToo && $this->params['lang'] != 'en') {
			$language[] = 'en';
		}
		$conditions['Revision.node_id'] = $id;
		$conditions['Revision.lang'] = $language;
		$conditions['Revision.status'] = array('current', 'previous', 'pending');
		$this->paginate['recursive'] = 0;
		$this->paginate['limit'] = 100;
		$this->paginate['order'] = 'Revision.created desc';
		$this->paginate['fields'] = array(
			'Revision.id',
			'Revision.user_id',
			'Revision.lang',
			'Revision.status',
			'Revision.reason',
			'Revision.created',
		);
		$this->data = $this->paginate('Revision', $conditions);
		$userIds = array_unique(Set::extract($this->data, '{n}.Revision.user_id'));
		if ($userIds) {
			$users = $this->Node->Revision->User->find('all', array(
				'fields' => array('id', 'IF(display_name=1,realname,username) AS name'),
				'conditions' => array('id' => $userIds), 'recursive' => -1));
			if ($users) {
				$users = Set::combine($users, '{n}.User.id', '{n}.0.name');
			}
		} else {
			$users = array();
		}
		$this->set(compact('users'));
	}
/**
 * view function
 *
 * If view all, find the requested node and all nodes under.
 * If not, find only children which are set to not show in the TOC, and for any results
 * 	find all of their children
 * A time limit is set such that 10 nodes per second can be processed without timing out
 * this means 300 nodes typically before the custom time limit extension kicks in.
 *
 * @param bool $viewAll
 * @access protected
 * @return void
 */
	function _view($viewAll = false) {
		if (!isset($this->params['admin'])) {
			$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		}
		$recursive = 1;
		$this->Node->id = $this->currentNode;
		$fields = array('Node.id', 'Node.parent_id', 'Node.depth', 'Node.sequence', 'Node.lft', 'Node.rght', 'Node.edit_level', 'Node.comment_level', 'Revision.id', 'Revision.slug', 'Revision.title', 'Revision.content', 'Revision.flags', 'Revision.modified');
		$this->data = $this->Node->findById($this->currentNode, $fields, null, $recursive);
		$conditions = array(
			'Node.lft >' => $this->data['Node']['lft'],
			'Node.rght <' => $this->data['Node']['rght']
		);
		$count = ($this->data['Node']['rght'] - $this->data['Node']['lft']) / 2;
		set_time_limit(max(30, $count / 10));
		$order = 'Node.lft';
		$children = array();
		if ($viewAll) {
			$children = $this->Node->find('all',compact('conditions', 'fields', 'order', 'recursive'));
		} else {
			$conditions = array(
				'Node.show_in_toc' => false,
				'Node.parent_id' => $this->data['Node']['id']
			);
			$children = $this->Node->find('all',compact('conditions', 'fields', 'order', 'recursive'));
			$conditions = array();
			if ($children) {
				foreach ($children as $child) {
					$conditions['OR'][] = array(
						'Node.lft >' => $child['Node']['lft'],
						'Node.rght <' => $child['Node']['rght']
					);
				}
				$grandChildren = $this->Node->find('all',compact('conditions', 'fields', 'order', 'recursive'));
				$children = am($children, $grandChildren);
				$children = Set::sort($children, '/Node/lft', 'asc');
			}
		}
		if ($children) {
			$data = am(array ('Node' => $this->data), $children);
		} else {
			$data = array ('Node' => $this->data);
		}
		$neighbours = $this->Node->findNeighbors(null, $viewAll ? false : true);
		$conditions = array('Revision.status' => 'pending', 'Revision.lang' => $this->params['lang']);
		$recursive = -1;
		$fields = array('DISTINCT node_id');
		$pendingUpdates = $this->Node->Revision->find('all', compact('conditions', 'recursive', 'fields'));
		$pendingUpdates = Set::extract($pendingUpdates, '{n}.Revision.node_id');
		$this->set(compact('data', 'neighbours', 'children', 'pendingUpdates'));
		$this->set('loginFields', $this->Auth->fields);
		$this->helpers[] = 'Highlight';
		$this->helpers[] = 'Text';
		$this->render('view_all');
	}
}
?>