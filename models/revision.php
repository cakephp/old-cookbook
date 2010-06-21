<?php
/**
 * Short description for revision.php
 *
 * Long description for revision.php
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
 * Revision class
 *
 * @uses          AppModel
 * @package       cookbook
 * @subpackage    cookbook.models
 */
class Revision extends AppModel {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Revision';
/**
 * displayField variable
 *
 * @var string
 * @access public
 */
	var $displayField = 'title';
/**
 * order property
 *
 * @var string 'created DESC'
 * @access public
 */
	var $order = 'Revision.created DESC';
/**
 * viewAllLevel variable
 *
 * @var int
 * @access public
 */
	var $viewAllLevel = 3;
/**
 * belongsTo variable
 *
 * @var array
 * @access public
 */
	var $belongsTo = array('User' => array('className' => 'Users.User'), 'Node');
/**
 * actsAs variable
 *
 * @var array
 * @access public
 */
	var $actsAs = array (
		'Slugged' => array('length' => 50, 'label' => 'title', 'overwrite' => true, 'unique' => false, 'mode' => 'id', 'multibyte' => true),
		'Searchable.Searchable'
	);
/**
 * validate variable
 *
 * @var array
 * @access public
 */
	var $validate = array(
			'preview' => array('rule' => array('equalTo', '0')),
			'title' => array(
				//array('rule' => 'noHtml', 'message' => 'No Html in section titles'),
				'missing' => '/[^\\s]/',
				array('rule' => array('duplicateSubmission', 'title'), 'on' => 'create',
					'message' => 'No change detected or there\'s already an identical submission pending'),
			),
			'content' => array(
				'missing' => array('rule' => '/[^\\s]/', 'last' => true),
				array('rule' => 'noHeaders', 'message' => 'Please create subsections instead of headers in content'),
			),
		);
/**
 * itsAnAdd property
 *
 * @var bool false
 * @access public
 */
	var $itsAnAdd = false;
/**
 * afterSave method
 *
 * @param mixed $created
 * @return void
 * @access public
 */
	function afterSave($created) {
		if (!$created) {
			return;
		}
		$comment = $this->field('reason');
		if (!$comment) {
			$comment = 'Edit Submitted';
		}
		$change['status_from'] = 'new';
		$change['status_to'] = 'pending';
		$change['revision_id'] = $this->id;
		$change['author_id'] = $this->field('user_id');
		$change['comment'] = $comment;
		$change['user_id'] = $this->field('user_id');
		$Change = ClassRegistry::init('Change');
		$Change->create();
		$Change->save($change);
		$this->Behaviors->enable('Searchable');
	}
/**
 * beforeSave function
 *
 * @access public
 * @return void
 */
	function beforeSave() {
		if (
			(array_key_exists('lang', $this->data['Revision']) && !$this->data['Revision']['lang']) ||
			(!$this->id && !array_key_exists('lang', $this->data['Revision']))
		) {
			$this->data['Revision']['lang'] = $this->Node->language;
		}
		$this->Behaviors->disable('Searchable');
		return true;
	}
/**
 * beforeValidate method
 *
 * @return void
 * @access public
 */
	function beforeValidate() {
		if (isset($this->data['Revision']['content'])) {
			$contents = $this->data['Revision']['content'];
			$firstTag = strpos($contents, '<');
			if ($firstTag > 10 || $firstTag === false) {
				preg_match_all('@<\?php([\\s\\S]*?)\?>@i',  $contents, $codeSegments, PREG_PATTERN_ORDER);
				if ($codeSegments[1]) {
					foreach ($codeSegments[1] as $id => $text) {
						$contents = str_replace($text, '{{{segment' . $id . '}}}', $contents);
					}
				}
				$contents = str_replace('<', '&lt;', $contents);
				$contents = str_replace('>', '&gt;', $contents);
				$contents = explode("\r\n", $contents);
				foreach ($contents as $i => $para) {
					$para = preg_replace("/^[\r\t\n ]*|[\r\t\n ]*$/", '', $para);
					$para = trim($para);
					if (!$para) {
						unset ($contents[$i]);
					}
				}
				if ($contents) {
					$this->data['Revision']['content'] = $contents = '<p>' . implode($contents, "</p>\n<p>") . '</p>';
				}
				if ($codeSegments[1]) {
					foreach ($codeSegments[1] as $id => $text) {
						$contents = str_replace('{{{segment' . $id . '}}}', $text, $contents);
					}
				}
			}
			if ($contents) {
				$contents = preg_replace('/<p>\W*Note:?\s*/', '<p class="note">', $contents);
				$contents = preg_replace('/<p>\W*Figure:?\s*/', '<p class="caption">Figure: ', $contents);
				$contents = preg_replace('/<p>\W*Table:?\s*/', '<p class="caption">Table: ', $contents);
				$contents = preg_replace('/<p>&lt;\?php/', '<pre>&lt;?php', $contents);
				$contents = preg_replace('/\?&gt;<\/p>/', '?&gt;</pre>', $contents);
				preg_match_all('@<pre[^>]*>([\\s\\S]*?)</pre>@i',  $contents, $result, PREG_PATTERN_ORDER);
				if (!empty($result['0'])) {
					$count = count($result['0']);
					for($i = 0; $i < $count; $i++) {
						$replaced = str_replace('<', '&lt;', $result['1'][$i]); // ensure escaping
						$replaced = str_replace('>', '&gt;', $replaced); // ensure escaping
						$replaced = str_replace($result[1][$i], $replaced, $result[0][$i]);
						$contents = str_replace($result[0][$i], $replaced, $contents);
					}
				}
				$this->data['Revision']['content'] = $contents;
			}
		}
		return true;
	}
/**
 * pending function
 *
 * @param mixed $nodeId
 * @access public
 * @return void
 */
	function pending($nodeId = null) {
		$currentRevision = $this->field('id');
		$conditions = array('Revision.status' => 'pending', 'Revision.node_id' => $nodeId, 'Revision.lang' => $this->Node->language);
		$fields = array('id');
		$recursive = -1;
		$return = $this->find('all', compact('conditions', 'fields', 'recursive'));
		$return = Set::extract($return, '{n}.Revision.id', '{n}.Revision.id');
		return $return;
	}
/**
 * publish function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function publish($id = null, $comment = 'Publishing this change', $flagTranslations = false) {
		if ($id) {
			$this->id = $id;
		} else {
			$id = $this->id;
		}
		$nodeId = $this->Node->addToTree($id, true);
		$this->Node->id = $nodeId;
		$this->Node->saveField('status', '1');

		$conditions = array('Revision.node_id' => $nodeId, 'Revision.lang' => $this->field('lang'), 'Revision.status' => 'current', 'NOT' => array('Revision.id' => $id));
		$revisions = $this->find('list', array('conditions' => $conditions));
		foreach($revisions as $revision => $_title){
			$update = array(
				'id' => $revision,
				'status' => 'previous'
			);
			$this->create($update);
			$this->save();
			//$this->delete_from_index($revision);
		}
		$this->id = $id;
		$change['status_from'] = $this->field('status');
		$change['status_to'] = 'published';
		$change['revision_id'] = $this->id;
		$change['author_id'] = $this->field('user_id');
		$change['comment'] = $comment;
		$change['user_id'] = $this->currentUserId;
		$return = $this->saveField('status', 'current');
		$data = $this->read();
		$defaultLang = Configure::read('Languages.default');
		if ($data['Revision']['lang'] == $defaultLang && $flagTranslations) {
			$conditions = array();
			$conditions['Revision.node_id'] = $data['Revision']['node_id'];
			$conditions['Revision.status'] = array('current', 'pending');
			$conditions['NOT']['Revision.lang'] = $defaultLang;
			$hasTranslations = $this->find('count', compact('conditions'));
			if ($hasTranslations) {
				$revisions = $this->find('list', compact('conditions'));
				foreach ($revisions as $revision => $title) {
					$this->flag($revision, 'englishChanged');
				}
			}
		} else {
			$isSignificant = false;
		}

		$Change = ClassRegistry::init('Change');
		$Change->create();
		$Change->save($change);
		return $return;
	}
/**
 * reject method
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function reject($id, $comment = 'Change not accepted') {
		$change['status_from'] = $this->field('status');
		$change['status_to'] = 'rejected';
		$change['revision_id'] = $this->id;
		$change['author_id'] = $this->field('user_id');
		$change['comment'] = $comment;
		$change['user_id'] = $this->currentUserId;
		$return = $this->saveField('status', 'reject');
		if ($return) {
			$Change = ClassRegistry::init('Change');
			$Change->save($change);
		}
		return $return;
	}
/**
 * hide function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function hide($id) {
		$this->id = $id;
		$this->saveField('status', 'pending');

		$conditions = array('Revision.node_id' => $id, 'Revision.lang' => $this->field('lang'), 'Revision.status' => 'pending', 'NOT' => array('Revision.id' => $id));
		$fields = array('id');
		$order = 'Revision.modified DESC';
		$previous = $this->find('first', compact('conditions', 'fields', 'order'));
		if ($previous) {
			$this->id = $previous['Revision']['id'];
			$this->saveField('status', 'current');
		} else {
			$nodeId = $this->field('node_id');
			$this->Node->id = $nodeId;
			$this->Node->saveField('status', 0);
		}
		$this->delete_from_index($id);
		return true;
	}
/**
 * flag method
 *
 * @param mixed $id
 * @param string $flag
 * @return void
 * @access public
 */
	function flag($id, $flag = '') {
		$flags = $this->field('flags', $id);
		if ($flags) {
			$flags = explode(',', $flags);
		} else {
			$flags = array();
		}
		if (!in_array($flag, $flags)) {
			$flags[] = $flag;
			$this->id = $id;
			$this->saveField('flags', implode(',', $flags));
			$this->create();
		}
	}
/**
 * reset function
 *
 * For each node and each language, if there are no current or previous revisions - skip, nothing to do
 * If there is a current revision - skip, nothing to do
 * Otherwise, make the most recent approved (previous) revision the current content
 *
 * @access public
 * @return void
 */
	function reset() {
		$this->recursive = -1;
		$nodes = array_keys($this->Node->find('list', array(
			'conditions' => array('Node.id >' => 0),
			'order' => 'id',
			'recursive' => -1
		)));
		set_time_limit (max(count($nodes) / 10, 30));
		$this->unbindModel(array('belongsTo' => array('Node')), false);
		$order = 'Revision.id DESC';
		$fields = array('id');
		foreach ($nodes as $id) {
			$langs = $this->find('list', array(
				'fields' => array('lang', 'lang'),
				'conditions' => array('node_id' => $id)
			));
			foreach ($langs as $lang) {
				$conditions = array(
					'node_id' => $id,
					'lang' => $lang,
					'NOT' => array('status' => 'rejected')
				);
				$count = $this->find('count', compact('conditions'));
				if (!$count) {
					continue;
				}
				$conditions['Revision.status'] = 'current';
				if ($this->find('count', compact('conditions')) === 1) {
					continue;
				}
				$conditions['Revision.status'] = 'previous';
				$last = $this->find('first', compact('conditions', 'order', 'fields'));
				if (!$last) {
					unset($conditions['Revision.status']);
					$last = $this->find('first', compact('conditions', 'order', 'fields'));
				}
				$this->updateAll(array('status' => '"current"'), array('Revision.id' => $last['Revision']['id']));
				$conditions['Revision.status'] = 'current';
				$conditions['NOT'] = array('Revision.id' => $last['Revision']['id']);
				$this->updateAll(array('Revision.status' => '"previous"'), $conditions);
			}
		}
	}
/**
 * resetSlugs function
 *
 * Warning - This method is intensive if run on the whole table - if called with no params before starting, the
 * searchable behavior is disabled if it is attached; Then for each live revision, the title is resaved to trigger
 * the slug behavior to update the slug. By comparing before and after, and only if $updateSearchIndex is set to true
 * (the default), then search index is then updated with a high time limit. In this way if updating the index cause the
 * function to time out the slugs should have at least already been updated.
 *
 * @param mixed $id
 * @param bool $updateSearchIndex
 * @access public
 * @return array the revisions which have been updated
 */
	function resetSlugs($id = null, $updateSearchIndex = true) {
		if ($this->Behaviors->attached('Searchable') && !$id) {
			$this->Behaviors->disable('Searchable');
		}
		$order = 'node_id';
		if ($id) {
			$conditions['Revision.id'] = $id;
		} else {
			$conditions['Revision.status'] = 'current';
		}
		$data = $this->find('list', compact('order', 'conditions'));
		if (count($data) > 3000) {
			set_time_limit (count($data) / 100);
		}
		$fields = array('slug');
		$slugsBefore = $this->find('list', compact('order', 'conditions', 'fields'));
		foreach ($data as $id => $title) {
			$this->id = $id;
			$this->save(array('title' => $title));
		}
		$slugsAfter = $this->find('list', compact('order', 'conditions', 'fields'));
		$diff = array_diff($slugsAfter, $slugsBefore);
		$updatedRevisions = array_keys($diff);
		if ($this->Behaviors->attached('Searchable')) {
			$this->Behaviors->enable('Searchable');
			if ($updatedRevisions && $updateSearchIndex) {
				if (count($updatedRevisions) > 30) {
					set_time_limit (count($updatedRevisions));
				}
				$counter = 1;
				define('NOW', getMicrotime());
				foreach ($updatedRevisions as $id) {
					debug ($counter++ . ' :' . round(getMicrotime() - NOW, 4));
					$this->add_to_index($id);
				}
			}
		}
		return $updatedRevisions;
	}
/**
 * checkWellFormed function
 *
 * @TODO implement
 * @param mixed $content
 * @access protected
 * @return void
 */
	function checkWellFormed($content) {
		// To be called as part of validation routine
	}
	function duplicateSubmission() {
		$this->data[$this->alias];
		$row = array(
			'lang' => $this->Node->language,
			'node_id' => $this->data[$this->alias]['node_id'],
			'title' => $this->data[$this->alias]['title'],
			'content' => $this->data[$this->alias]['content'],
			'status' => array('pending', 'current')
		);
		return !$this->hasAny($row);
	}
/**
 * getId function
 *
 * @param mixed $id
 * @param mixed $depth
 * @param array $fields
 * @access protected
 * @return void
 */
	function _getId($id, $depth = null, $fields = array('id')) {
		/*
		   $conditions['Node.sequence'] = $id;
		   $result = $this->find($conditions, array ('id', 'slug'), null, -1);
		 */
		$conditions = array();
		if ($depth) {
			$conditions['Node.depth'] = $depth;
		}
		if (is_numeric($id)) {
			$conditions['Revision.node_id'] = $id;
		} else {
			$conditions['Revision.slug'] = $id;
		}
		$result = $this->find($conditions, array ('id'), null, 0);
		if ($result['Node']['id']) {
			if ($fields == array('id')) {
				return $result['Node']['id'];
			}
			return $result['Node'];
		}
		return false;
	}
/**
 * clearCache function
 *
 * @param mixed $type
 * @access protected
 * @return void
 */
	function _clearCache($type = null) {
		clearCache(null, 'views');
		clearCache(null, 'views', ''); // clear elements
	}

/**
 * find_index function
 *
 * this is the find funtion used by the searchable behavior for the index generation
 * Optimized to /NOT/ look for the book and collection for each row when building the index
 * This optimization logic only works if results are returned ordered by Node.lft
 *
 * @param string $type
 * @param array $options
 * @access public
 * @return void
 */
	function find_index($type = 'all', $options = array()){
		$this->unbindModel(array('belongsTo' => array('User')));
		$params = Set::merge(array('conditions' => array('Revision.status' => 'current', 'Node.id >' => 0), 'order' => 'Node.lft ASC', 'recursive' => 0), $options);
		$params['order'] = 'Node.lft';
		$results = $this->find('all', $params);
		$collection = null;
		$book = null;
		foreach($results as &$result){
			if ($result['Node']['depth'] == 1) {
				$collection = $result['Node']['id'];
				$book = null;
			} elseif ($result['Node']['depth'] == 2) {
				$book = $result['Node']['id'];
			}
			$result['Revision']['collection'] = $collection;
			$result['Revision']['book'] = $book;
			if (!$collection) {
				if ($result['Node']['depth'] > 0) {
					$result['Revision']['collection'] =
						$this->Node->collection($result['Revision']['node_id']);
				}
			}
			if (!$book) {
				if ($result['Node']['depth'] > 1) {
					$result['Revision']['collection'] =
						$this->Node->collection($result['Revision']['node_id']);
				}
			}
		}
		if($type =='first' && count($results)){
			$results = $results[0];
		}
		return $results;
	}
/**
 * noHeaders method
 *
 * @param mixed $vals
 * @return void
 * @access public
 */
	function noHeaders($vals) {
		if ($this->itsAnAdd == true) {
			return true;
		}
		foreach ($vals as $val) {
			if (strpos($val, '<h') !== false) {
				return false;
			}
		}
		return true;
	}
}
?>