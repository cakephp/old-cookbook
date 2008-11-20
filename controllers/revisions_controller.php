<?php
/* SVN FILE: $Id: revisions_controller.php 711 2008-11-19 18:20:57Z AD7six $ */
/**
 * Short description for revisions_controller.php
 *
 * Long description for revisions_controller.php
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
 * @version       $Revision: 711 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-19 19:20:57 +0100 (Wed, 19 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * RevisionsController class
 *
 * @uses          AppController
 * @package       cookbook
 * @subpackage    cookbook.controllers
 */
class RevisionsController extends AppController {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Revisions';
/**
 * paginate variable
 *
 * @var array
 * @access public
 */
	var $paginate = array('order' => 'Revision.created DESC');
/**
 * beforeFilter function
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		if ($this->action == 'view') {
			$urlSlug = isset($this->params['pass'][1])?$this->params['pass'][1]:'';
			$conditions['Revision.id'] = $this->params['pass'][0];
			$fields = array ('id', 'lang', 'slug', 'node_id');
			$recursive = 0;
			$result = $this->Revision->find('first', compact('conditions', 'fields', 'recursive'));
			$this->currentNode = $result['Revision']['node_id'];
			if (!($this->data)&&($urlSlug<>$result['Revision']['slug'])) {
				$this->redirect(array($result['Revision']['id'], $result['Revision']['slug']), null, true);
			}
		} elseif (isset($this->params['pass'][0])) {
			$this->Revision->id = $this->currentNode = $this->params['pass'][0];
		}

		if (!$this->currentNode) {
			$conditions = array('Node.depth' => '0', 'Node.sequence' => 0);
			$fields = array('id');
			$recursive = 0;
			$topNode = $this->Revision->find('first', compact('conditions', 'fields', 'recursive'));
			$this->currentNode = $topNode['Node']['id'];
		}
		if ($this->currentNode) {
			$fields = array('Node.id', 'Node.depth', 'Node.sequence', 'Node.lft', 'Node.rght', 'Node.edit_level', 'Node.comment_level', 'Revision.id', 'Revision.slug', 'Revision.title');
			$this->currentPath = $this->Revision->Node->getPath($this->currentNode, $fields, 0);
		}
		$this->set('currentPath', $this->currentPath);
		$this->Auth->allowedActions = array('search', 'results', 'view', 'compare');
		parent::beforeFilter();
	}
/**
 * beforeRender function
 *
 * @access public
 * @return void
 */
	function beforeRender() {
		$crumbPath = isset($this->currentPath)?$this->currentPath:array();
		$this->set('crumbPath', $crumbPath);
		parent::beforeRender();
	}
/**
 * search function
 *
 * @access public
 * @return void
 */
	function search(){
		$this->set('query' , '');
		if(!empty($this->data)){
			$params['query'] = $this->data['Search']['query'];
			$params['collection'] = $this->data['Search']['collection'];
			$params['lang'] = $this->data['Search']['lang'];
			$params['action'] = 'results';
			$this->redirect($params);
		}
	}

/**
 * results function
 *
 * updated to self correct if the url the index has does not match the current content's url and cache
 * results pages
 * Caching is enabled if the passed term contains a single sluggable character. This partially, but not completely,
 * prevents utf8 search results overwritting themselves
 *
 * @access public
 * @return void
 */
	function results(){
		if(empty($this->passedArgs['query'])){
			$this->redirect($this->referer());
		}
		$this->helpers[] ='Searchable.Search';
		$this->helpers[] ='Paginator';
		$limit = !empty($this->passedArgs['limit']) ? $this->passedArgs['limit'] : 20;
		$page = !empty($this->passedArgs['page']) ? $this->passedArgs['page'] : 1;
		$lang = !empty($this->passedArgs['lang']) ? $this->passedArgs['lang'] : $this->params['lang'];
		$collection = !empty($this->passedArgs['collection']) ? $this->passedArgs['collection'] : 2;

		// we should make a query object and use Zend_Search_Lucene api to construct it
		$query = $this->passedArgs['query'];
		$langQuery = ' AND lang:'. $lang;
		$collectionQuery = ' AND collection:'. $collection;
		$results = $this->Revision->search($query.$langQuery.$collectionQuery, $limit, $page);
		$searchSlugs = Set::combine($results, '/Result/cake_id', '/Result/slug');
		$conditions['id'] = array_keys($searchSlugs);
		$fields = array('slug');
		$recursive = -1;
		$slugs = $this->Revision->find('list', compact('conditions', 'fields', 'recursive'));
		$diff = array_diff($searchSlugs, $slugs);
		if ($diff) {
			foreach ($diff as $id => $_) {
				$this->Revision->add_to_index($id);
			}
			$results = $this->Revision->search($query.$langQuery.$collectionQuery, $limit, $page);
		}
		//we need to pop the lang val from the terms
		$terms = $this->Revision->terms();
		$terms = array_diff($terms, array($lang, $collection));
		if ($results) {
			//Paginator cheating ;) maybe put it in an element in the view?
			$count = $this->Revision->hits_count();
			$pageCount = ceil($count/$limit);
			$this->params['paging']['Revision']['page'] = $page;
			$this->params['paging']['Revision']['count'] = $count;
			$this->params['paging']['Revision']['current'] = $limit;
			$this->params['paging']['Revision']['nextPage'] = $page + 1 < $pageCount ? $page + 1 : '';
			$this->params['paging']['Revision']['prevPage'] =  $page - 1 < 1 ? '' : $page - 1;
			$this->params['paging']['Revision']['pageCount'] = $pageCount;
			$this->params['paging']['Revision']['options']['page'] = $page;
			$this->params['paging']['Revision']['options']['limit'] = $limit;
			$this->params['paging']['Revision']['defaults']['page'] = $page;
			$this->params['paging']['Revision']['defaults']['limit'] = $limit;
			$this->set(compact('results', 'count', 'page', 'limit', 'terms', 'query'));
		} else {
			// fallback
			$conditions = array('OR' => array(
				'Revision.title LIKE' => '%' . $this->passedArgs['query'] . '%',
				'Revision.content LIKE' => '%' . $this->passedArgs['query'] . '%'
			));
			$nodes = $this->paginate('Node', $conditions);
			if (Configure::read() && $nodes) {
				$this->Session->setFlash('Search Index needs rebuilding - the index returned no results, ' .
				       'but a simple LIKE %' . $this->passedArgs['query'] . '% search did.');
			}
			foreach ($nodes as $row) {
				$this->Revision->id = $row['Revision']['id'];
				$this->Revision->read();
				$this->Revision->add_to_index($row['Revision']['id']);
				$result = $row['Revision'];
				$result['content'] = strip_tags($result['content']);
				$result['cake_model'] = 'Revision';
				$result['cake_id'] = $row['Revision']['id'];
				$results[]['Result'] = $result;
			}
			$this->set(compact('results', 'terms'));
		}
		if (Inflector::slug($this->passedArgs['query'])) {
			$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		}
	}
/**
 * admin_build_index method
 *
 * Build or refresh the search index. This is an intensive method!
 * If $clear evaluates to true, the existing index folder will be deleted. This ensures that all file permissions
 * are as expected.
 * A time limit of 2s per current revision is set - which should be sufficient even on a loaded/low power
 * machine. Small sections take ~0.1 to index, large sections can take upto 30s
 *
 * @param bool $clear
 * @return void
 * @access public
 */
	function admin_build_index($clear = false){
		$count = $this->Revision->find('count', array('status' => 'current'));
		set_time_limit (max(30, $count) * 2);
		if ($clear) {
			if (file_exists(TMP . 'search_index')) {
				$folder = new Folder(TMP . 'search_index');
				if (!$folder->delete()) {
					$this->Session->setFlash('The search index folder "' . TMP . 'search_index/" could not be deleted!<br />Manually delete this folder, ensure that the webuser can write to "' . TMP . '", and try again');
					$this->redirect(array('action' => 'index'));
				}
				$this->Session->setFlash('The search index "' . TMP . 'search_index" was deleted');
			}
		}
		$start = getMicrotime();
		$this->Revision->build_index();
		$this->Session->setFlash('Search index rebuilt');
		if (Configure::read()) {
			$time = round(getMicrotime() - $start, 1);
			$this->log('Clearing cache files ' . $time . 's', 'searchable');
		}
		clearCache();
		if (Configure::read()) {
			$time = round(getMicrotime() - $start, 1);
			$this->log('Cache cleared ' . $time . 's', 'searchable');
		}
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_add_to_tree function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_add_to_tree ($id) {
		if ($this->Revision->Node->addToTree($id)) {
			$this->Session->setFlash('Revision '.$id.' added to the tree, move if necessary. NOT PUBLIC YET (need to approve).');
		} else {
			$this->Session->setFlash('Error adding revision '.$id.' to the tree.');
		}
		return $this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_approve function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_approve($id) {
		$data = $this->Revision->read(array('lang', 'node_id'), $id);
		$isSignificant = false;
		if (
			$this->params['lang'] == 'en' &&
			$data['Revision']['node_id'] &&
			$this->Revision->find('list',
				array('conditions' => array(
					'Revision.node_id' => $data['Revision']['node_id'],
					'Revision.status' => array('current', 'pending'),
					'Revision.lang !=' => 'en'
				))
			)
		) {
			$isSignificant = true;
		}
		$this->set('isSignificant', $isSignificant);
		if ($this->data) {
			if (isset($this->data['Revision']['is_significant'])) {
				$isSignificant = $this->data['Revision']['is_significant'];
			}
			if ($this->Revision->publish($id, $this->data['Revision']['reason'], $isSignificant)) {
				$this->Session->setFlash('Revision '.$id.' is now public.');
				// Reuse the ignore redirect logic
				$this->admin_ignore($id);
			} else {
				$this->Session->setFlash('Could not approve revision '.$id.'');
			}
		} else {
			$this->data['Revision']['reason'] = $this->Revision->field('reason');
		}
		$this->render('admin_change_status');
	}
/**
 * admin_reject function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_reject($id = null) {
		if ($this->data) {
			if ($this->Revision->reject($id, $this->data['Revision']['reason'])) {
				$this->Session->setFlash('Revision ' . $id . ' marked as rejected');
				// Reuse the ignore redirect logic
				$this->admin_ignore($id);
			} else {
				$this->Session->setFlash('Could not reject revision '.$id.'');
			}
		} else {
			$this->data = $this->Revision->read('reason', $id);
		}
		$this->render('admin_change_status');
	}
/**
 * admin_reset method
 *
 * @return void
 * @access public
 */
	function admin_reset() {
		$this->Revision->reset();
		$this->redirect($this->Session->read('referer'), null, false);
	}
/**
 * admin_edit function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_edit($id) {
		if (!$this->Revision->hasAny(array($this->Revision->primaryKey => $id))) {
			$this->redirect(array('action' => 'index'), null, true);
		}
		if (!empty ($this->data)) {
			if (isset ($this->data['Revision']['content2'])) {
				$this->data['Revision']['content'] = $this->data['Revision']['content2'];
				unset ($this->data['Revision']['content2']);
			}
			if ($this->Revision->save($this->data)) {
				$this->Session->setFlash($this->Revision->alias . ' updated');
				$this->redirect($this->Session->read('referer'), null, true);
			} else {
				$this->Session->setFlash('Please correct the errors below.');
			}
		} else {
			$this->data = $this->Revision->read(null, $id);
		}
		$users = $this->Revision->User->find('list');
		$nodes = $this->Revision->Node->generateTreeList();
		$under_nodes = $after_nodes = array();
		if (!$this->data['Node']['id']) {
			$under_nodes = $nodes;
			$after_nodes = $this->Revision->Node->generateTreeList(array('Node.parent_id' => $this->data['Revision']['under_node_id']));
			$nodes = array(null => 'Not added yet');
		}
		$this->set(compact('users', 'nodes', 'under_nodes', 'after_nodes'));
	}
/**
 * admin_hide function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_hide($id) {
		if (!$this->Revision->hasAny(array('Revision.id' => $id, 'Revision.status' => 'current'))) {
			$this->Session->setFlash('Could not hide revision '.$id.', not the currently visible version');
		} elseif ($this->Revision->hide($id)) {
			$this->Session->setFlash('Revision '.$id.' is now hidden.');
		} else {
			$this->Session->setFlash('Could not revert revision '.$id.', unknown problem');
		}
		$this->redirect($this->Session->read('referer'), null, true);
	}
/**
 * admin_history function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function admin_history($nodeId) {
		$this->paginate['limit'] = 20;
		$this->paginate['order'] = 'Revision.created desc';
		$this->params['named']['node_id'] = $nodeId;
		$this->admin_index();
		$this->render('admin_index');
	}
/**
 * admin_ignore method
 *
 * Called when reviewing content, just redirects to the next pending revision
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function admin_ignore($id = null) {
		if ($this->action == 'admin_ignore') {
			$this->Session->setFlash('Revision ' . $id . ' left in the pending queue');
		}
		$this->Revision->order = array('Revision.id ASC');
		$id = $this->Revision->field('id', array('id >' . $id, 'status' => 'pending', 'lang' =>
			$this->Revision->field('lang')));
		if ($id) {
			return $this->redirect(array('action' => 'view', $id), null, true);
		} else {
			return $this->redirect(array('action' => 'pending'), null, true);
		}
	}
/**
 * admin_index method
 *
 * @return void
 * @access public
 */
	function admin_index() {
		if (isset($this->params['named']['restrict_to'])) {
			$restrictTo = $this->params['named']['restrict_to'];
			unset($this->params['named']['restrict_to']);
			$limits = $this->Revision->Node->find('first', array('conditions' => array('Node.id' => $restrictTo), 'fields' => array('lft', 'rght', 'Revision.title')));
			$this->params['named']['Node.lft >='] = $limits['Node']['lft'];
			$this->params['named']['Node.rght <='] = $limits['Node']['rght'];
			$this->Session->setFlash('Only "' . $limits['Revision']['title'] . '" and below');
		}
		return parent::admin_index();
	}
/**
 * admin_invalid method
 *
 * @return void
 * @access public
 */
	function admin_invalid($fix = false) {
		$query = 'SELECT nodes.id, revisions.lang, count(revisions.id) FROM nodes left join revisions on revisions.node_id = nodes.id WHERE revisions.status = "current" GROUP BY nodes.id, revisions.lang HAVING count(revisions.id) > 1';
		$results = $this->Revision->query($query);
		if ($results) {
			if ($fix) {
				debug ($results);
				die;
			}
			$this->Session->setFlash('Duplicate "current" revisions found');
			$nodes = Set::extract('/nodes/id', $results);
			$this->params['named']['node_id'] = $nodes;
			$this->params['named']['status'] = 'current';
			$this->params['named']['lang'] = 'en';
			$this->admin_index();
			$this->render('admin_index');
			return;
		}
		$results = $this->Revision->Node->find('list', array('conditions' => array('Revision.id' => null)));
		if ($results) {
			if ($fix) {
				set_time_limit (max(30, count($results) * 2));
				$problem = array();
				foreach ($results as $node_id => $_) {
					$id = $this->Revision->find('first', array(
						'fields' => array('Revision.id'),
						'recursive' => -1,
						'conditions' => array(
							'Revision.node_id' => $node_id,
							'Revision.status' => 'previous'
						),
						'order' => 'Revision.id DESC'
					));
					if (!$id) {
						$id = $this->Revision->find('first', array(
							'fields' => array('Revision.id'),
							'recursive' => -1,
							'conditions' => array(
								'Revision.node_id' => $node_id,
								'Revision.status' => 'pending'
							),
							'order' => 'Revision.id DESC'
						));
					}
					if (!$id) {
						$id = $this->Revision->find('first', array(
							'fields' => array('Revision.id'),
							'recursive' => -1,
							'conditions' => array(
								'Revision.node_id' => $node_id,
							),
							'order' => 'Revision.id DESC'
						));
					}
					if ($id) {
						$this->Revision->id = $id['Revision']['id'];
						$this->Revision->publish();
					} else {
						if (!$this->Revision->find('count', array('conditions' => array('Revision.node_id' => $node_id)))) {
							$this->Revision->Node->del($node_id);
						} else {
							$problem[] = $node_id;
						}
					}
				}
				if (!$problem) {
					$this->redirect(array('fix'));
				}
				$this->Session->setFlash('Nodes with no current revision found');
				$this->params['named']['node_id'] = array_keys($results);
				$this->params['named']['lang'] = 'en';
				$this->admin_index();
				$this->render('admin_index');
				return;
			}
		}
		if ($fix) {
			$this->Session->setFlash('Any problems encountered have been automatically fixed.');
			$this->redirect(array('controller' => 'nodes', 'action' => 'index'));
		} else {
			$this->Session->setFlash('No invalid revisions found');
		}
		$this->redirect($this->Session->read('referer'));
	}
/**
 * admin_pending function
 *
 * @access public
 * @return void
 */
	function admin_pending () {
		$this->paginate['limit'] = 10;
		$this->paginate['order'] = 'Revision.id ASC';
		$this->paginate['conditions'] = array('Revision.status' => 'pending');
		$this->Revision->recursive = 1;
		$this->Revision->bindModel(array('belongsTo' => array(
			'AfterNode' => array(
				'className' => 'Node',
				'foreignKey' => 'after_node_id',
			),
			'UnderNode' => array(
				'className' => 'Node',
				'foreignKey' => 'under_node_id',
			)
		)),false );
		$this->admin_index();
	}
/**
 * admin_reset_slugs function
 *
 * @param mixed $id
 * @param bool $updateIndex
 * @access public
 * @return void
 */
	function admin_reset_slugs($id = null, $updateIndex = false) {
		$updates = $this->Revision->resetSlugs($id, $updateIndex);
		if (!$updates) {
			$this->Session->setFlash('Function ran successfully however no slugs updated!');
		} else {
			$conditions = array('Revision.id' => $updates);
			$fields = array('CONCAT(Revision.lang, ": ", Node.sequence, " - ", Revision.title) as c_title');
			$order = array('Node.lft', 'Revision.lang');
			$recursive = 0;
			$titles = $this->Revision->find('all', compact('conditions', 'fields', 'order', 'recursive'));
			$titles = Set::extract($titles, '/0/c_title');
			$message = 'Function ran successfully updating the slugs for the following: <br />' . implode($titles, '<br />') . '<br />The search index was ' . ($updateIndex?'':'NOT') . ' updated.';
			$this->Session->setFlash($message);
		}
		$this->redirect($this->Session->read('referer'), null, false);
	}
/**
 * admin_view function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_view($id) {
		$this->Revision->recursive = 0;
		$this->Revision->bindModel(array('belongsTo' => array(
			'AfterNode' => array(
				'className' => 'Node',
				'foreignKey' => 'after_node_id',
			),
			'UnderNode' => array(
				'className' => 'Node',
				'foreignKey' => 'under_node_id',
			)
		)),false );
		$this->Node->Revision->recursive = 0;
		$this->data = $this->Revision->read(null, $id);
		$user = $this->Revision->User->read(null, $this->data['Revision']['user_id']);
		$user = $user['User'];
		if ($this->data['Revision']['status'] != 'current') {
			$this->Revision->Node->setLanguage($this->data['Revision']['lang']);
			$this->set('current', $this->Revision->Node->find('first', array('conditions' => array('Node.id' => $this->data['Revision']['node_id']))));
		}
		$this->Revision->id = $this->currentNode;
		$conditions = array('Node.parent_id' => $this->currentNode, 'Revision.status' => 'current');
		$fields = array ('Node.depth', 'Node.sequence', 'Node.parent_id', 'Node.lft', 'Node.rght', 'id', 'slug', 'title', 'content');
		$recursive = 0;
		$children = $this->Revision->find('all', compact('conditions', 'fields', 'recursive'));
		$viewAllLevel = $this->Revision->viewAllLevel;
		$comments = $this->Revision->Node->Comment->findAllByNode_id($this->data['Node']['id']);

		$this->helpers[] = 'Highlight';
		$this->helpers[] = 'Diff';
		$this->set(compact('neighbours', 'children', 'viewAllLevel', 'comments', 'user'));
	}
/**
 * compare method
 *
 * @param mixed $id1
 * @param mixed $id2
 * @return void
 * @access public
 */
	function compare($id1 = null, $id2 = null) {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$this->Revision->recursive = 0;
		$this->data['this'] = $this->Revision->find('first', array('conditions' => array('Revision.id' => $id1)));
		if (!in_array($this->data['this']['Revision']['status'], array('current', 'previous'))) {
			$this->Session->setFlash(__('Only current and previous revisions can be viewed', true));
			$this->redirect($this->Session->read('referer'));
		}
		$this->data['other'] = $this->Revision->find('first', array('conditions' => array('Revision.id' => $id2)));
		if (!in_array($this->data['other']['Revision']['status'], array('current', 'previous'))) {
			$this->Session->setFlash(__('Only current and previous revisions can be viewed', true));
			$this->redirect($this->Session->read('referer'));
		}
		if ($this->data['this']['Revision']['node_id'] != $this->data['other']['Revision']['node_id']) {
			$this->Session->setFlash(__('Only possible to compare revisions of the same node', true));
			$this->redirect($this->Session->read('referer'));
		}
		$this->helpers[] = 'Highlight';
		$this->helpers[] = 'Diff';
		$this->render('view');
	}
/**
 * view method
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function view($id) {
		$this->cacheAction = array('duration' => CACHE_DURATION, 'callbacks' => false);
		$this->Revision->recursive = 0;
		$this->data['this'] = $this->Revision->find('first', array('conditions' => array('Revision.id' => $id)));
		if (!in_array($this->data['this']['Revision']['status'], array('current', 'previous'))) {
			$this->Session->setFlash(__('Only current and previous revisions can be viewed', true));
			$this->redirect($this->Session->read('referer'));
		}
		if ($this->data['this']['Revision']['status'] != 'current') {
			$this->Revision->Node->setLanguage($this->data['this']['Revision']['lang']);
			$this->data['current'] = $this->Revision->Node->find('first', array(
				'conditions' => array('Node.id' => $this->data['this']['Revision']['node_id'])));
		}
		$this->helpers[] = 'Highlight';
		$this->helpers[] = 'Diff';
	}
}
?>