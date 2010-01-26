<?php
/**
 * Short description for node.php
 *
 * Long description for node.php
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
 * Node class
 *
 * @uses          AppModel
 * @package       cookbook
 * @subpackage    cookbook.models
 */
class Node extends AppModel {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Node';
/**
 * displayField variable
 *
 * @var string
 * @access public
 */
	var $displayField = 'sequence';
/**
 * order property
 *
 * @var string 'lft'
 * @access public
 */
	var $order = 'Node.lft';
/**
 * viewAllLevel variable
 *
 * @var int
 * @access public
 */
	var $viewAllLevel = 5;
/**
 * language variable
 *
 * @var string
 * @access public
 */
	var $language = 'en';
/**
 * bypassSilent variable
 *
 * @var bool
 * @access public
 */
	var $bypassSilent = false;
/**
 * hasOne variable
 *
 * @var array
 * @access public
 */
	var $hasOne = array('Revision' => array('conditions' => array('Revision.lang' => 'en', 'Revision.status' => 'current')));
/**
 * hasMany variable
 *
 * @var array
 * @access public
 */
	var $hasMany = array('Comment' => array('conditions' => array('Comment.lang' => 'en', 'Comment.published' => true)));
/**
 * actsAs variable
 *
 * @var array
 * @access public
 */
	var $actsAs = array ('Tree');
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'merge_id' => 'numeric',
		'confirmation' => array('equalTo', '0'),
	);
/**
 * addToTree function
 *
 * @param mixed $revisionId
 * @param bool $approve
 * @access public
 * @return void
 */
	function addToTree($revisionId, $approve = false) {
		$id = $this->Revision->field('node_id');
		if ($id && $this->hasAny(array('id' => $id))) {
			// already in
			if ($approve) {
				$this->id = $id;
				$this->savefield('status', 1);
			}
			return $id;
		}

		$revision = $this->Revision->find('first', array('id' => $revisionId, 'recursive' => -1));
		$this->create();
		$toSave = array('parent_id' => $revision['Revision']['under_node_id']);
		if ($approve) {
			$toSave['status'] = 1;
		}
		$this->save($toSave);
		$this->Revision->save(array('node_id' => $this->id));

		if ($revision['Revision']['after_node_id']) {
			if ($revision['Revision']['under_node_id'] == $revision['Revision']['after_node_id']) {
				$this->moveUp(null, true);
			} else {
				$conditions = array('parent_id' => $revision['Revision']['under_node_id']);
				$order = 'lft';
				$children = $this->find('list', compact('conditions', 'order'));
				$keys = array_flip(array_reverse(array_keys($children)));
				if (isset($keys[$revision['Revision']['after_node_id']])) {
					$toMove = $keys[$revision['Revision']['after_node_id']] - 1;
					if ($toMove) {
						$this->moveUp(null, $toMove);
					}
				}
			}
		}
		$this->reset();
		return $this->id;
	}
/**
 * afterFind function
 *
 * When looking for English content, if there isn't a current revision, populate with default text
 * When looking for none English content, if there isn't a current revision populate with the English text
 * 	Auto correct any links in content to point at same-language pages
 *
 * @param mixed $results
 * @access public
 * @return void
 */
	function afterFind($results) {
		$before = $results;
		$defaultLang = Configure::read('Languages.default');
		if ($this->language == $defaultLang) {
			if (isset($results[0]['Revision'])) {
				foreach ($results as $i => $result) {
					if (array_key_exists('title', $result['Revision']) && !$result['Revision']['title']) {
						$results[$i]['Revision']['title'] = __('Default Title', true);
					}
					if (array_key_exists('slug', $result['Revision']) && !$result['Revision']['slug']) {
						$results[$i]['Revision']['slug'] = __('default_slug', true);
					}
					if (array_key_exists('content', $result['Revision']) && !$result['Revision']['content']) {
						$results[$i]['Revision']['content'] = __('Default Content', true);
					}
					if (array_key_exists('lang', $result['Revision']) && !$result['Revision']['lang']) {
						$results[$i]['Revision']['lang'] = $defaultLang;
					}
				}
			}
			return $results;
		}
		if (isset($results[0]['Revision'])) {
			$missing = false;
			foreach ($results as $result) {
				if (array_key_exists('title', $result['Revision']) && !$result['Revision']['title']) {
					$missing = true;
					break;
				}
				if (array_key_exists('slug', $result['Revision']) && !$result['Revision']['slug']) {
					$missing = true;
					break;
				}
				if (array_key_exists('content', $result['Revision']) && !$result['Revision']['content']) {
					$missing = true;
					break;
				}
			}
			$language = $this->language;
			if ($missing) {
				$this->setLanguage($defaultLang);
				if ($this->__queryData['order'] == array(null)) {
					unset($this->__queryData['order']);
				}
				$ids = Set::Extract($results, '/Node/id');
				$engResults = $this->find('all', am($this->__queryData, array('conditions' => array('Node.id' => $ids))));
				$this->setLanguage($language);
			}
			$root = trim(Router::url('/'), '/');
			if ($root) {
				$root .= '/';
			}
			$find = '@href="/' . $root . '(?!' . $language . '/)@';
			$replace = 'href="/' . $root . $language . '/';
			foreach ($results as $i => &$result) {
				if (isset($engResults[$i])) {
					if (array_key_exists('title', $result['Revision']) && !$result['Revision']['title']) {
						$result['Revision']['title'] = $engResults[$i]['Revision']['title'];
					}
					if (array_key_exists('slug', $result['Revision']) && !$result['Revision']['slug']) {
						$result['Revision']['slug'] = $engResults[$i]['Revision']['slug'];
					}
					if (array_key_exists('content', $result['Revision']) && !$result['Revision']['content']) {
						$result['Revision']['content'] =
							$engResults[$i]['Revision']['content'];
					}
					if (array_key_exists('lang', $result['Revision']) && !$result['Revision']['lang']) {
						$result['Revision']['lang'] = $defaultLang;
					}
				}
				//if (isset($result['Revision']['content']) && strpos('href="/', $result['Revision']['content'])) {
				if (isset($result['Revision']['content'])) {
					$result['Revision']['content'] = preg_replace($find, $replace, $result['Revision']['content']);
				}
			}
		}
		return $results;
	}
/**
 * beforeFind function
 *
 * @param mixed $queryData
 * @access public
 * @return void
 */
	function beforeFind($queryData) {
		if ($this->language != Configure::read('Languages.default')) {
			$this->__queryData = $queryData;
		}
		return true;
	}
/**
 * beforeSave function
 *
 * @access public
 * @return void
 */
	function beforeSave() {
		if (!$this->id&&isset($this->data['Node']['parent_id'])) {
			$parent = $this->getPath($this->data['Node']['parent_id'], array('id'));
			$this->data['Node']['depth'] = count($parent);
		}
		return true;
	}
/**
 * findNeighbors function
 *
 * @param mixed $nodeId
 * @param bool $individualNodeView
 * @access public
 * @return void
 */
	function findNeighbors($nodeId = null, $individualNodeView = false) {
		$prev = $prevConstraint = $next = $nextConstraint = null;
		if (!$nodeId) {
			$nodeId = $this->id;
		}
		$currentNode = $this->find('first', array(
			'conditions' => array('id' => $nodeId),
			'fields' => array ('lft', 'rght','depth'),
			'recursive' => -1
		));
		$currentNode = $currentNode['Node'];
		$viewAllLevel = max($this->viewAllLevel, $currentNode['depth'] + 1);
		$this->viewAllLevel = $viewAllLevel;
		$fields = array ('lft', 'rght', 'Node.id', 'Revision.id', 'depth', 'Revision.slug', 'sequence', 'Revision.title');
		@list ($index, $collection, $book) = $path = $this->getPath($nodeId, $fields, 0);
		$limits = $book?$book['Node']:($collection?$collection['Node']:$index['Node']);
		$prevConstraint['Node.lft BETWEEN ? AND ?'] = array($limits['lft'], ($currentNode['lft']-1));
	       	$prevConstraint['Node.depth <='] = $viewAllLevel;
		$prevConstraint['Node.show_in_toc'] = true;
		$nextConstraint['Node.depth <='] = $viewAllLevel;
		$nextConstraint['Node.show_in_toc'] = true;
		if ($individualNodeView) {
			$nextConstraint['Node.lft BETWEEN ? AND ?'] = array(($currentNode['lft']+1), $limits['rght']);
		} else {
			$nextConstraint['Node.lft BETWEEN ? AND ?'] = array(($currentNode['rght']+1), $limits['rght']);
		}
		$prev = $this->find($prevConstraint, $fields, 'Node.lft desc', 0);
		if (!$prev&&isset($path[$currentNode['depth']-1])) {
			$prev = $path[$currentNode['depth']-1];
		}
		$next = $this->find($nextConstraint, $fields, 'Node.lft asc', 0);
		if (!$next&&isset($path[$currentNode['depth']-2])) {
			$next = $path[$currentNode['depth']-2];
		}
		$return = array ($prev, $next);
		return $return;
	}
/**
 * book function
 *
 * @param mixed $id
 * @param array $fields
 * @access public
 * @return void
 */
	function book($id, $fields = array('id')) {
		return $this->_getNode(2, $id, $fields);
	}

/**
 * copy method
 *
 * @param mixed $id
 * @param mixed $parentId null
 * @return void
 * @access public
 */
	function copy($id, $parentId = null) {
		set_time_limit(0);
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			return false;
		}
		$this->id = $id;
		$lft = $this->field('lft');
		$rght = $this->field('rght');
		$conditions = array(
			array(
				'Node.lft >=' => $lft,
				'Node.rght <=' => $rght
			),
		);
		$order = 'lft';
		$recursive = -1;
		$original = $this->find('all', compact('recursive', 'conditions', 'order'));
		$idMap = Set::extract($original, '/Node/id');
		$idMap = array_combine($idMap, $idMap);
		$this->query('START TRANSACTION');
		foreach($original as $row) {
			$oldId = $row['Node']['id'];
			unset($row['Node']['id']);
			unset($row['Node']['lft']);
			unset($row['Node']['rght']);
			unset($row['Node']['depth']);
			unset($row['Node']['sequence']);
			if (isset($idMap[$row['Node']['parent_id']])) {
				$row['Node']['parent_id'] = $idMap[$row['Node']['parent_id']];
			} elseif ($parentId) {
				$row['Node']['parent_id'] = $parentId;
			}
			$this->create();
			$this->save($row);
			$idMap[$oldId] = $this->id;
			$this->_copyRevisions($oldId, $this->id);
		}
		if ($this->field('depth', array('id' => $idMap[$id])) < 3) {
			$this->resetSequences($idMap[$id]);
		} else {
			$this->resetSequences($this->field('parent_id', array('id' => $idMap[$id])));
		}
		$this->query('COMMIT');
	}
/**
 * collection function
 *
 * @param mixed $id
 * @param array $fields
 * @access public
 * @return void
 */
	function collection($id, $fields = array('id')) {
		return $this->_getNode(1, $id, $fields);
	}
/**
 * exportData method
 *
 * Get data for export. if no id is passed (admin_export) list all nodes. If an id is passed, retrieve the node and its
 * children as well as the path to get there - to allow partial yet 'complete' exports/imports
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function exportData($id = null) {
		if ($id) {
			$this->id = $id;
			$lft = $this->field('lft');
			$rght = $this->field('rght');
			$conditions['OR'] = array(
				array(
					'Node.lft >=' => $lft,
					'Node.rght <=' => $rght
				),
				array(
					'Node.lft <' => $lft,
					'Node.rght >' => $rght
				)
			);
		} else {
			$conditions = array();
		}
		$fields = array(
			'Node.id',
			'Node.parent_id',
			'Node.depth',
			'Node.show_in_toc',
			'Revision.id',
			'Revision.lang',
			'Revision.title',
			'Revision.content',
			'Revision.reason',
			'Revision.user_id',
			'Revision.modified',
		);
		$order = 'lft';
		$recursive = 0;
		return $this->find('all', compact('fields', 'order', 'recursive', 'conditions'));
	}
/**
 * find function
 *
 * @param mixed $conditions
 * @param array $fields
 * @param mixed $order
 * @param mixed $recursive
 * @access public
 * @return void
 */
	function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		if ($conditions != 'list' || !isset($this->hasOne['Revision'])) {
			return parent::find($conditions, $fields, $order, $recursive);
		}
		$query = $fields;
		$query['fields'] = array('id', 'sequence', 'Revision.title');
		$query['recursive'] = 0;
		$results = parent::find('all', $query);
		if (!$results) {
			return $results;
		}
		$keyPath = "{n}.{$this->alias}.id";
		$valuePath = array("{0} {1}", "{n}.{$this->alias}.sequence", "{n}.Revision.title");
		return Set::combine($results, $keyPath, $valuePath);
	}
/**
 * generatetreelist function
 *
 * @param mixed $conditions
 * @access public
 * @return void
 */
	function generatetreelist($conditions = null) {
		return $this->find('list', array('order' => 'lft', 'conditions' => $conditions));
	}
/**
 * import method
 *
 * accepts an xml dump (generated from the nodes controller admin_export function) and syncronizes the current structure
 * and contents with the file contents.
 *
 * @param mixed $xmlFile
 * @return void
 * @access public
 */
	function import($xml, $options = array(), $thisUser = false) {
		Configure::write('debug', 2);
		$schema = $this->schema('id');
		$delete_missing = false;
		$allow_moves = false;
		$auto_approve = false;
		extract($options);
		$deletes = $adds = $moves = $mods = 0;
		$errors = array();
		uses('Xml');
		$xml = new Xml($xml);
		$xml = Set::reverse($xml);
		$meta = Set::extract($xml, '/Contents/Meta');
		$nodes = Set::extract($xml, '/Contents/Node');
		$ids = Set::extract($nodes, '/Node/id');
		set_time_limit(count($ids) * 2);
		if ($delete_missing) {
			$toDelete = $this->find('list', array('conditions' => array('NOT' => array('Node.id' => $ids))));
			$deletes = count($toDelete);
			foreach ($toDelete as $id => $_) {
				$this->delete($id);
			}
		}
		$first = $this->field('id', array('Node.parent_id' => null));
		if (!$first) {
			$allow_moves = true;
			$auto_approve = true;
		}
		$importLang = isset($meta[0]['Meta']['lang'])?$meta[0]['Meta']['lang']:Configure::read('Languages.default');;
		$this->setLanguage($importLang);
		$message = array();
		$counters = array();
		$webroot = Router::url('/');
		$i = 0;
		foreach ($nodes as $i => $row) {
			$parent = isset($row['Node']['parent_id'])?$row['Node']['parent_id']:null;
			if ($i == 0) {
				if ($first && $first != $row['Node']['id']) {
					return array(false, array('This import file is incompatible with the current install'), array());
				}
				if (isset($row['Node']['position'])) {
					$counters[$parent] = $row['Node']['position'];
				}
			}
			if (!$parent && $i > 0) {
				$errors[] = 'Duplicate root node detected Id: ' . $row['Node']['id'] . ', halting processing';
				break;
			}
			$current = $this->find('first', array(
				'conditions' => array('Node.id'	 => $row['Node']['id']),
				'recursive' => 0,
				'fields' => array('Node.parent_id', 'Node.show_in_toc', 'Node.lft',
				'Revision.id', 'Revision.title', 'Revision.content')
			));
			$showInToc = null;
			if (isset($row['Node']['show_in_toc'])) {
				$showInToc = $row['Node']['show_in_toc'];
			} elseif (isset($row['Node']['ShowInToc'])) {
				$showInToc = false;
			}
			if (!$current) {
				/* This code shouldn't be necessary, and is ONLY necessary at all for uuid installs
				 * it adds the node to the tree as the last node to allow moving it around.
				 * without this code the uuid changes on save
				 */
				if ($schema['length'] == 36) {
					$max = $this->query('SELECT MAX(`rght`) as `rght` FROM `nodes`', false);
					$max = $max[0][0]['rght'];
					$lft = $max + 1;
					$rght = $lft + 1;
					$this->query('INSERT INTO `nodes` (`id`, `lft`, `rght`) VALUES (\'' . $row['Node']['id'] . "', $lft, $rght)");
				}
				/* This code shouldn't be necessary end */
				$adds++;
			}
			if (!$current ||
				($current['Node']['parent_id'] != $parent && $allow_moves)) {
				$this->id = $row['Node']['id'];
				$toSave = array('id' => $row['Node']['id'], 'parent_id' => $parent);
				if ($showInToc !== null) {
					$toSave['show_in_toc'] = $showInToc;
				}
				$this->save($toSave);
				if ($showInToc !== null && !$showInToc) {
					die;
				}
				if ($current) {
					$moves++;
				}
			} elseif ($showInToc !== null && $current['Node']['show_in_toc'] != $showInToc) {
				$this->id = $row['Node']['id'];
				$this->saveField('show_in_toc', $showInToc);
			}
			if (!isset($counters[$parent])) {
				$counters[$parent] = 0;
			} else {
				$counters[$parent]++;
			}
			if ($current) {
				$previousSiblings = $this->find('count', array('recursive' => -1,
					'conditions' => array('parent_id' => $parent, 'lft <' => $current['Node']['lft'])));
			} else {
				$previousSiblings = $this->find('count', array('recursive' => -1,
					'conditions' => array('parent_id' => $parent, 'id !=' => $row['Node']['id'])));
			}
			$difference = $previousSiblings - $counters[$parent];
			if ($difference) {
				if ($difference > 0) {
					$this->moveUp($row['Node']['id'], $difference);
				} else {
					$this->moveDown($row['Node']['id'], $difference);
				}
			}
			if (!isset($row['Node']['Revision']['id']) || !$row['Node']['Revision']['id']) {
				if (isset($row['Node']['Revision']['lang']) && $row['Node']['Revision']['lang'] != $importLang) {
					$message['language'] = 'Some sections in the import have not been translated, import English version to inherit original contents';
				}
				continue;
			}
			$title = $row['Node']['Revision']['title'];
			$content = isset($row['Node']['Revision']['content'])?$row['Node']['Revision']['content']:'';
			if ($webroot != '/') {
				$content = preg_replace('@(href|src)=(\'|")/@', '\\1=\\2' . $webroot, $content);
			}
			$exists = $this->Revision->find('first', array('conditions' => compact('title', 'content')));
			$different = false;
			$compare = preg_replace("/[\r\n\t ]/", '', $current['Revision']['title']);
			$import = preg_replace("/[\r\n\t ]/", '', $title);
			if ($compare != $import) {
				$different = true;
			} else {
				$compare = preg_replace("/[\r\n\t ]/", '', $current['Revision']['content']);
				$import = preg_replace("/[\r\n\t ]/", '', $content);
				if ($compare != $import) {
					$different = true;
				}
			}
			if (!$exists && $different) {
				$reason = isset($row['Node']['Revision']['reason'])?$row['Node']['Revision']['reason']:'Revision Imported';
				$user_id = isset($row['Node']['Revision']['user_id'])?$row['Node']['Revision']['user_id']:$thisUser;
				$this->Revision->create();
				$toSave = array(
					'node_id' => $row['Node']['id'],
					'under_node_id' => $parent,
					'status' => 'pending',
					'user_id' => $user_id,
					'title' => $title,
					'content' => $content,
					'reason' => $reason
				);
				if (!$this->Revision->save($toSave)) {
					$errors[] = 'Could not save revision ' . $title;
				}
				if ($auto_approve || !$current) {
					$this->Revision->publish($this->Revision->id, $reason . ' (auto approved)');
				}
				if ($current) {
					$mods++;
				}
			} elseif ($exists && $auto_approve) {
				if ($exists['Revision']['status'] != 'current') {
					$this->Revision->create();
					$reason = isset($exists['Revision']['reason'])?$exists['Revision']['reason']:'Revision Imported';
					$this->Revision->publish($exists['Revision']['id'], $reason . ' (auto approved)');
					$mods++;
				}
			}
		}
		$didAnything = $deletes + $adds + $moves + $mods;
		$message[] = 'File imported, ' . $i + 1 . ' nodes/revisions processed (of ' . count($ids) . ' present in the input file)';
		if ($errors) {
			$messageM = count($errors) . ' errors encountered! ';
			$messageM .= implode(', ', $errors);
			$message[] = $messageM;
		}
		if (!$didAnything) {
			$message[] = 'No changes detected';
		} else {
			if ($deletes) {
				$message[] = $deletes . ' nodes deleted: (' . implode(', ', $toDelete) . ')';
			}
			if ($adds) {
				$messageM = $adds . ' new nodes & revisions created';
				if ($auto_approve) {
					$messageM .= ' (all automatically approved)';
				}
				$message[] = $messageM;
			}
			if ($moves) {
				$message[] = $moves . ' nodes moved';
			}
			if ($mods) {
				$messageM = $mods . ' edits imported';
				if ($auto_approve) {
					$messageM .= ' (automatically approved)';
				}
				$message[] = $messageM;
			}
			$this->reset();
		}
		return array(true, $message, compact('adds', 'deletes', 'moves', 'mods', 'errors'));
	}
/**
 * initialize method
 *
 * If the database is empty, populate it with some sample content
 * Debug messages are supressed during execution unless $debug is true
 *
 * @param int $books
 * @param int $sections
 * @param bool $debug
 * @return void
 * @access public
 */
	function initialize($collections = 2, $books = 2, $sections = 2, $debug = false) {
		if (!$debug) {
			$debug = Configure::read();
			Configure::write('debug', 0);
		}
		if ($this->find('count') || $this->Revision->find('count')) {
			return false;
		}
		$this->create();
		$this->save(array('parent_id' => null));
		$id = $this->id;
		$toSave = array(
			'node_id' => $id,
			'title' => 'Your Collections',
			'content' => 'Edit the collection index to change this text',
			'status' => 'current',
			'lang' => Configure::read('Languages.default')
		);
		$this->Revision->create();
		$this->Revision->save($toSave);
		for ($i=1; $i<=$collections; $i++) {
			$this->__initCollection($i, $books, $sections, $id);
		}
		$this->reset();
		if ($debug) {
			Configure::write('debug', $debug);
		}
	}
/**
 * initCollection method
 *
 * @param mixed $i
 * @param mixed $books
 * @param mixed $sections
 * @param mixed $id
 * @return void
 * @access private
 */
	function __initCollection($i, $books, $sections, $id) {
		$toSave = array('status' => 'current', 'lang' => Configure::read('Languages.default'), 'content' => 'a collection of books');
		$this->create();
		$this->save(array('parent_id' => $id));
		$id = $this->id;
		$this->Revision->create();
		$this->Revision->save(am($toSave, array('node_id' => $id, 'title' => 'Collection ' . $i)));
		for ($i=1; $i<=$books; $i++) {
			$this->__initBook($i, $sections, $id);
		}
	}
/**
 * initBook method
 *
 * @param mixed $i
 * @param mixed $sections
 * @param mixed $id
 * @return void
 * @access private
 */
	function __initBook($i, $sections, $id) {
		$toSave = array('status' => 'current', 'lang' => Configure::read('Languages.default'), 'content' => 'a book about... ' . $i);
		$this->create();
		$this->save(array('parent_id' => $id));
		$id = $this->id;
		$this->Revision->create();
		$this->Revision->save(am($toSave, array('node_id' => $id, 'title' => 'Book ' . $i)));
		for ($i=1; $i<=$sections; $i++) {
			$sid = $this->__initSection($i, $id);
			for ($j=1; $j<=$sections; $j++) {
				$ssid = $this->__initSection($j, $sid);
				for ($k=1; $k<=$sections; $k++) {
					$this->__initSection($k, $ssid);
				}
			}
		}
	}
/**
 * initSection method
 *
 * @param mixed $i
 * @param mixed $id
 * @return void
 * @access private
 */
	function __initSection($i, $id) {
		$this->create();
		$this->save(array('parent_id' => $id));
		$id = $this->id;
		$this->Revision->create();
		$toSave = array('status' => 'current', 'lang' => Configure::read('Languages.default'), 'content' => 'Section ' . $id . ' content');
		$this->Revision->save(am($toSave, array('node_id' => $id, 'title' => 'Section id ' . $id)));
		return $this->Revision->id;
	}
/**
 * merge method
 *
 * @param mixed $id
 * @param mixed $mergeId
 * @return void
 * @access public
 */
	function merge($id, $mergeId) {
		$recursive = -1;
		$conditions['Revision.node_id'] = $mergeId;
		$conditions['Revision.status'] = 'current';
		$fields = array('lang', 'title');
		$titles = $this->Revision->find('list', compact('recursive', 'conditions', 'fields', 'order'));

		$conditions['Revision.node_id'] = $id;
		$fields = array('lang', 'content');
		$contents = $this->Revision->find('list', compact('recursive', 'conditions', 'fields', 'order'));
		$fields = array('lang', 'title');
		$oldTitles = $this->Revision->find('list', compact('recursive', 'conditions', 'fields', 'order'));
		$toSave = array();
		$defaultLang = Configure::read('Languages.default');
		foreach ($contents as $lang => $content) {
			$title = isset($titles[$lang])?$titles[$lang]:$titles[$defaultLang];
			$toSave[$lang] = array(
				'node_id' => $mergeId,
				'title' => $title,
				'content' => $content,
				'lang' => $lang,
				'reason' => 'Merging "' . $oldTitles[$defaultLang] . '" content into "' . $title . '"',
				'user_id' => $this->currentUserId
			);
			if ($lang != $defaultLang) {
				$toSave[$lang]['flags'] = 'englishChanged';
			}
		}
		foreach ($titles as $lang => $title) {
			if (isset($toSave[$lang])) {
				continue;
			}
			$toSave[$lang] = array(
				'node_id' => $mergeId,
				'title' => $title,
				'content' => $contents[$defaultLang],
				'lang' => $lang,
				'reason' => 'Merging "' . $oldTitles[$defaultLang] . '" content into "' . $title . '"',
				'user_id' => $this->currentUserId,
				'flags' => 'englishChanged'
			);
		}
		$this->Revision->recursive = -1;
		$this->Comment->updateAll(array('Comment.node_id' => "'$mergeId'"), array('Comment.node_id' => $id));
		$this->Revision->updateAll(array('Revision.node_id' => "'$mergeId'"), array('Revision.node_id' => $id));
		$this->Revision->updateAll(
			array('Revision.status' => '"previous"'),
			array('Revision.status' => 'current', 'Revision.node_id' => $id)
		);
		$parentId = $this->field('parent_id', array('id' => $id));
		$this->removeFromTree($id);
		$this->reset($parentId);
		foreach ($toSave as $revision) {
			$this->Revision->create();
			$this->Revision->save($revision);
			$this->Revision->publish($this->Revision->id, $revision['reason']);
		}
		return true;
	}
/**
 * moveUp method
 *
 * After calling the tree behavior method, reset the sequences
 *
 * @param mixed $id
 * @param mixed $steps
 * @param bool $auto
 * @return void
 * @access public
 */
	function moveUp($id = null, $steps = null, $auto = true) {
		if ($this->Behaviors->Tree->moveUp($this, $id, $steps) && $auto) {
			$this->resetSequences($this->field('parent_id'));
		}
		return;
	}
/**
 * moveDown method
 *
 * After calling the tree behavior method, reset the sequences
 *
 * @param mixed $id
 * @param mixed $steps
 * @param bool $auto
 * @return void
 * @access public
 */
	function moveDown($id = null, $steps = null, $auto = true) {
		if ($this->Behaviors->Tree->moveDown($this, $id, $steps) && $auto) {
			$this->resetSequences($this->field('parent_id'));
		}
		return;
	}

/**
 * reset function
 *
 * @param mixed $parentId
 * @access public
 * @return void
 */
	function reset($parentId = null) {
		$this->resetDepths($parentId);
		$this->resetSequences($parentId);
	}
/**
 * resetDepths function
 *
 * @param mixed $parentId
 * @access public
 * @return void
 */
	function resetDepths($parentId = null) {
		if ($parentId) {
			$conditions['Node.lft >'] = $this->field('lft', array('id' => $parentId));
			$conditions['Node.rght <'] = $this->field('rght', array('id' => $parentId));
		} else {
			$conditions = array();
			$table = $this->table;
			$this->query("UPDATE $table SET depth = (
				SELECT wrapper.parents FROM (
					SELECT
						this.id as row,
						COUNT(parent.id) as parents
					FROM
						$table AS this
					LEFT JOIN $table AS parent ON (
						parent.lft < this.lft AND
						parent.rght > this.rght)
					GROUP BY
						this.id
				) AS wrapper WHERE wrapper.row = $table.id)");
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			if (!$db->error) {
				return true;
			}
		}
		$nodes = $this->find('list', compact('conditions'));
		foreach ($nodes as $nodeId => $node) {
			$this->id = $nodeId;
			$parent = $this->getPath($nodeId, array('id'));
			$this->saveField('depth', count($parent) - 1);
		}
		return true;
	}
/**
 * resetSequences function
 *
 * @param mixed $parentId
 * @param string $prefix
 * @param bool $start
 * @access public
 * @return void
 */
	function resetSequences($parentId = null, $prefix = '', $start = true) {
		if ($prefix == '' && $start == true) {
			$this->id = $parentId;
			$depth = $this->field('depth');
			$prefix = $this->field('sequence');
		}
		$this->recursive = -1;
		$nodes = $this->findAllByParent_id($parentId, null, 'lft ASC');
		$prefix = $prefix?$prefix.'.':$prefix;
		$index = $parentId?1:'';
		if ($nodes) {
			foreach ($nodes as $node) {
				$node = $node['Node'];
				if ($node['depth'] > 2) {
					if ($node['show_in_toc']) {
						$this->create();
						$this->id = $node['id'];
						$this->saveField('sequence', $prefix.$index);
						$this->resetSequences($node['id'], $prefix.$index, false);
						$index++;
					} else {
						$this->updateAll(array('sequence' => null, 'show_in_toc' => 0), array('lft >=' => $node['lft'], 'rght <=' => $node['rght']));
					}
				} else {
					$this->resetSequences($node['id'], '', false);
				}
			}
		}
		return true;
	}
/**
 * setLanguage function
 *
 * @param string $lang
 * @access public
 * @return void
 */
	function setLanguage($lang = null) {
		if (!$lang) {
			$lang = Configure::read('Languages.default');
		}
		$bind['hasOne']['Revision']['conditions']['Revision.lang'] = $lang;
		$bind['hasOne']['Revision']['conditions']['Revision.status'] = 'current';
		$bind['hasMany']['Comment']['conditions']['Comment.lang'] = $lang;
		$bind['hasMany']['Comment']['conditions']['Comment.published'] = true;
		$this->language = $lang;
		$this->bindModel($bind, false);
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
 * getNode function
 *
 * @param mixed $depth
 * @param mixed $id
 * @param array $fields
 * @access protected
 * @return void
 */
	function _getNode($depth, $id, $fields = array('id')) {
		$cId = $this->_getId($id, $depth);
		if ($cId && $fields == array('id')) {
			return $cId;
		} else {
			$id = $this->_getId($id);
		}
		$data = $this->find(array('Node.id' => $id), array('lft', 'rght'), null, 0);
		$conditions['Node.lft <'] = $data['Node']['lft'];
		$conditions['Node.rght >'] = $data['Node']['rght'];
		$conditions['Node.depth'] = $depth;
		$result = $this->find($conditions, $fields, null, 0);
		if (isset($result['Node'])) {
			if (count($fields) == 1) {
				return $result['Node'][$fields[0]];
			}
			return $result['Node'];
		}
		return false;
	}

/**
 * copyRevisions method
 *
 * @param mixed $from id
 * @param mixed $to to
 * @return void
 * @access protected
 */
	function _copyRevisions($from, $to) {
		$recursive = -1;
		$conditions = array(
			'Revision.node_id' => $from,
			'Revision.status' => 'current'
		);
		$rows = $this->Revision->find('all', compact('recursive', 'conditions', 'fields'));
		foreach($rows as $row) {
			unset($row['Revision']['id']);
			unset($row['Revision']['under_node_id']);
			unset($row['Revision']['after_node_id']);
			$row['Revision']['node_id'] = $to;
			$this->Revision->create();
			$this->Revision->save($row);
		}
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
}
?>