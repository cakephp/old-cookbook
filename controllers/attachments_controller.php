<?php
/**
 * Short description for attachments_controller.php
 *
 * Long description for attachments_controller.php
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
 * @package       base
 * @subpackage    base.controllers
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * AttachmentsController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class AttachmentsController extends AppController {
/**
 * name property
 *
 * @var string 'Attachments'
 * @access public
 */
	var $name = 'Attachments';
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array('limit' => 10, 'order' => 'Attachment.id DESC');
/**
 * publicAccess property
 *
 * If set to true, you don't need to login to see uploaded, mediaView served, content.
 * Otherwise, you do.
 *
 * @var bool true
 * @access public
 */
	var $publicAccess = true;
/**
 * beforeFilter method
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		if (isset($this->params['admin'])) {
			$this->helpers[] = 'Number';
		}
		if ($this->publicAccess && isset($this->Auth)) {
			$this->Auth->allow('view');
		}
		parent::beforeFilter();
	}
/**
 * admin_add method
 *
 * GET request for /admin/add/ModelName/ModelId will create an attachment for ModelName.ModelId
 *
 * @param string $class
 * @param mixed $foreignKey
 * @access public
 * @return void
 */
	function admin_add ($class = null, $foreignKey = null) {
		if (!$class|| !$foreignKey) {
			$this->Session->setFlash(__('Attachment controller - no class or foreignKey error.', true));
			$this->_back();
		}
		$this->Attachment->bindModel(array(
			'belongsTo' => array(
				$class => array (
					'class' => $class,
					'conditions' => array('Attachment.class' => $class)
				)
			)
		));

		$pClass = Inflector::pluralize($class);
		if ($this->data) {
			$this->data[$this->modelClass]['foreign_id'] = $foreignKey;
			$this->data[$this->modelClass]['class'] = $class;
			$this->data[$this->modelClass]['user_id'] = $this->Auth->user('id');
			$editTest['class'] = $class;
			$editTest['foreign_id'] = $foreignKey;
			$editTest['filename'] = $this->data[$this->modelClass]['filename']['name'];
			if ($id = $this->{$this->modelClass}->field('id', $editTest)) {
				$this->data[$this->modelClass]['id'] = $id;
			}
			if ($this->Attachment->save($this->data)) {
				if ($id) {
					$this->Session->setFlash(sprintf(__('Existing Attachment for %1$s, id %2$s updated', true), $class, $this->data[$this->modelClass]['foreign_id']));
				} else {
					$this->Session->setFlash(sprintf(__('New Attachment for %1$s, id %2$s added', true), $class, $this->data[$this->modelClass]['foreign_id']));
				}
				$this->redirect(array(
					'controller' => Inflector::underscore(Inflector::Pluralize($class)),
					'action' => 'edit',
					$this->data[$this->modelClass]['foreign_id']),
			       null, true);
			}
		}
		$this->_setSelects($class);
		$this->render('admin_add');
	}
/**
 * admin_edit method
 *
 * Edit descriptions etc for an attacment. Not used to upload a new version of a file
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function admin_edit($id) {
		if (!empty($this->data)) {
			$this->data[$this->modelClass]['user_id'] = $this->Auth->user('id');
			if ($this->Attachment->save($this->data)) {
				$this->Session->setFlash(sprintf(__('%1$s with id %2$s updated', true), $this->modelClass, $id));
				$this->_back();
			} else {
				$this->Session->setFlash(__('errors in form', true));
			}
		} else {
			$this->data = $this->Attachment->read(null, $id);
		}
		$this->_setSelects($this->data[$this->modelClass]['class']);
		$this->render('admin_edit');
	}
/**
 * admin_export method
 *
 * @return void
 * @access public
 */
	function admin_export() {
		$recursive = -1;
		$this->data = $this->Attachment->find('all', compact('recursive'));
		$filename = 'attachments_backup_' . date('Ymd-Hi') . '.xml';
		$this->RequestHandler->renderAs($this, 'xml');
		if (!isset($this->params['requested'])) {
			Configure::write('debug', 0);
			$this->RequestHandler->respondAs('xml', array('attachment' => $filename));
		}
		//$file = new File(TMP . $filename);
		//$file->write($out);
	}
/**
 * admin_import method
 *
 * @return void
 * @access public
 */
	function admin_import() {
		if ($this->data) {
			if ($this->data['Attachment']['take_backup']) {
				$this->requestAction('/admin/attachments/export', array('return'));
			}
			if (!$this->data['Attachment']['file']['error']) {
				$xml = file_get_contents($this->data['Attachment']['file']['tmp_name']);
				$file = new File(TMP . 'attachments_imported_' . date('Ymd-Hi') . '.xml');
				$file->write($xml);
			} elseif ($this->data['Attachment']['backup']) {
				$xml = file_get_contents(TMP . $this->data['Attachment']['backup']);
			} else {
				$this->Session->setFlash('No Xml file to import');
				$this->redirect(array());
			}
			$uploads = 0;
			uses('Xml');
			$xml = new Xml($xml);
			$xml = Set::reverse($xml);
			$meta = Set::extract($xml, '/Contents/Meta');
			$attachments = Set::extract($xml, '/Contents/Attachment');
			if ($attachments) {
				foreach ($attachments as $row) {
					extract ($row['Attachment']);
					$conditions = compact('class', 'foreign_id', 'filename', 'dir');
					$existing = $this->Attachment->field('id', $conditions);
					$file = new File(APP . 'uploads' . DS . $dir . DS . $filename, true);
					$file->write(base64_decode($source));
					$this->Attachment->create();
					unset ($row['Attachment']['id']);
					if ($existing) {
						$this->Attachment->id = $existing;
					}
					if (!isset($row['Attachment']['user_id']) || !$row['Attachment']['user_id']) {
						$row['Attachment']['user_id'] = $this->Auth->user('id');
					}
					if ($this->Attachment->save($row)) {
						$this->Attachment->reprocess();
						$uploads++;
					}
				}
			}
			$message = array();
			if ($uploads) {
				$message[] = $uploads . ' images imported';
			}
			if ($message) {
				$message = implode ($message, '. ') . '.';
			} else {
				$message = 'File imported but no changes detected';
			}
			$this->Session->setFlash($message);
			$this->_back();
		}
		$tmp = new Folder(TMP);
		$backups = $tmp->find('attachments_.*\.xml');
		if ($backups) {
			$backups = array_combine($backups, $backups);
			$backups = array_reverse($backups);
		} else {
			$backups = array();
		}
		$this->set('backups', $backups);
	}
/**
 * admin_view method
 *
 * @param mixed $id
 * @param mixed $slug
 * @param string $size
 * @access public
 * @return void
 */
	function admin_view($id, $slug = null, $size = 'large') {
		$this->view($id, $slug, $size);
	}
/**
 * view method
 *
 * Serve up files directly from the uploads folder.
 *
 * @param mixed $id
 * @param mixed $name
 * @param mixed $size
 * @return void
 * @access public
 */
	function view($id, $name = null, $size = 'large') {
		Configure::write('debug', 2);
		$this->Attachment->recursive = -1;
		$correctSlug = false;
		if (is_numeric($id)) {
			$correctSlug = true;
			$row = $this->Attachment->read(null, $id);
		} else {
			$params = func_get_args();
			$file = array_pop($params);
			$folder = implode($params, '/');
			$extension = array_pop(explode('.', $file));
			if (strpos($extension, '_') !== false) {
				list($extension_, $size) = explode('_', $extension);
				$file = str_replace($extension, $extension_, $file);
				$extension = $extension_;
			}
			$conditions['dir'] = $folder;
			$conditions['filename'] = $file;
			$conditions['ext'] = $extension;
			$row = $this->Attachment->find('first', compact('conditions'));
		}
		if (!$row) {
			debug('No file found for ' . implode(func_get_args(), '/'));
			die;
		}
		extract ($row['Attachment']);
		if ($correctSlug) {
			if ($description) {
				$description = $this->Attachment->slug($description);
				if (!$description) {
					$description = $filename;
					if (substr($description, -3) != $ext) {
						$description .= '.' . $ext;
					}
				} else {
					$description = str_replace('-' . $ext, '.' . $ext, $description);
					if (strpos('.' . $ext, $description) === false) {
						$description .= '.' . $ext;
					}
				}
			}
			if ($name != $description) {
				$this->redirect(array($id, $description, $size), 301);
			}
		}
		$data = compact('modified');
		if (!file_exists(APP . 'uploads' . DS . $dir . DS . $filename . '_' . $size)) {
			$filename =  'missing.png';
			$ext = 'png';
			$data['path'] = 'uploads' . DS;
		} else {
			$data['path'] = 'uploads' . DS . $dir . DS;
		}
		$data['id'] = $filename;
		$data['extension'] = $ext;
		$data['download'] = isset($this->params['named']['download'])?$this->params['named']['download']:false;
		$data['name'] = $description;
		$data['size'] = $size;
		if ($this->publicAccess) {
			$data['cache'] = '+ 99days';
		}
		$this->set($data);
		$this->view = 'Media';
		$this->render();
		Configure::write('debug', 0);
	}
/**
 * setSelects method
 *
 * @param mixed $class
 * @return void
 * @access protected
 */
	function _setSelects($class = null) {
		if ($class) {
			$this->set('foreignClass',$class);
			$this->set('foreigns',$this->Attachment->$class->find('list'));
		}
		if (in_array('_setSelects', get_class_methods('AppController'))) {
			parent::_setSelects();
		}
	}
}
?>