<?php
/* SVN FILE: $Id: upload.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for upload.php
 *
 * Long description for upload.php
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
 * @subpackage    base.models.behaviors
 * @since         v 1.0
 * @version       $Revision: 689 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 11:30:07 +0100 (Wed, 05 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * UploadBehavior class
 *
 * A behavior adding validation and automatic processing for file uploads
 * The design of the behavior is to store meta data for uploaded files in a database table (can just be a file
 * field in your products table, or a dedicated 'attachments' table) although the behavior can be used with a
 * table-less model. Unmodified uploaded files are by default stored OUTSIDE the webroot. This allows the
 * application to use the original file as input to generate any different 'versions' that might be required.
 * Assuming that generating versions will fail if the file type is not what is expected; this helps defend against
 * malicious intentions such as phising
 * Suggested example setup would be:
 * app
 * 	controllers
 * 	models
 * 	views
 * 	uploads <- destination for pristine uploaded files
 * 		Post
 * 			PostId
 * 				uploadedFile.jpg
 * 				uploadedFile.pdf
 * 				uploadedFile.zip
 * 	webroot <- document root
 * 		files <- root destination for (none-image) uploaded files
 * 			Post
 * 				PostId
 * 					uploadedFile.pdf
 * 					(sanitized)extractedFromZip.doc
 * 					(sanitized)extractedFromZip.rtf
 * 					(sanitized)uploadedFile.zip
 * 		img <- root destination for (image) upload versions
 * 			Post
 * 				PostId
 * 					uploadedFile_small.jpg (see image upload behavior)
 * 					uploadedFile_big.jpg (see image upload behavior)
 *
 * @uses          AppBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class UploadBehavior extends ModelBehavior {
/**
 * name property
 *
 * @var string 'Upload'
 * @access public
 */
	var $name = 'Upload';
/**
 * errors property
 *
 * Array of (system-type) errors encountered when processing an upload
 *
 * @var array
 * @access public
 */
	var $errors = array();
/**
 * behaviorMap property
 *
 * Map of which more specific upload behaviors exist
 * Used in factoryMode to automatically load the more specific behavior if a match is found based on the data
 *
 * @var array
 * @access private
 */
	var $__behaviorMap = array(
		'pdf' => array('extension' => 'pdf'),
		'archive' => array(
			'extension' => array('bz2', 'gz', 'tar', 'zip'),
			//'mime' => array('application/zip', 'application/x-tar', 'application/gzip')
		),
		'image' => array('mime' => 'image/*'),
	);
/**
 * contentMap property
 *
 * @var array
 * @access private
 */
	var $__extContentMap = array(
		'bmp' => 'image/bmp',
		'bz2' => 'application/x-bzip',
		'csv' => 'application/vnd.ms-excel',
		'doc' => 'application/msword',
		'gif' => 'image/gif',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'pdf' => 'application/pdf',
		'png' => 'image/png',
		'psd' => 'image/x-psd',
		'sql' => 'text/x-sql',
		'swf' => 'application/x-shockwave-flash',
		'tar' => 'application/x-tar',
		'txt' => 'text/plain',
		'xls' => 'application/vnd.ms-excel',
		'xml' => 'application/xml',
		'zip' => 'application/x-zip',
		'*' => 'application/octet-stream'
	);
/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	var $_defaultSettings = array(
		'dirField' => 'dir',
		'fileField' => 'filename',
		'extField' => 'ext',
		'checksumField' => 'checksum',
		'mustUploadFile' => true,
		'allowedMime' => '*',
		'allowedExt' => '*',
		'allowedSize' => '8',// '*' for no limit (in any event limited by php settings)
		'allowedSizeUnits' => 'MB',
		'overwriteExisting' => true,
		'autoCreateVersions' => true,
		'baseDir' => '{APP}uploads',
		'dirFormat' => '{$class}/{$foreign_id}',// include {$baseDir} to have absolute paths (not recomended)
		'fileFormat' => '{$filename}',// include {$dir} to store the dir & filename in one field
		'pathReplacements' => array(),
		'versions' => array(
			'thumb' => array(
				'vBaseDir' => '{IMAGES}',
				'vDirFormat' => 'types/',
				'vFileFormat' => '{$ext}.png',
			),
			/*
			'copy' => array(
				'vBaseDir' => '{WWW_ROOT}files/',
				'vDirFormat' => '{$dir}/',
				'vFileFormat' => '{$filename}',
				'callback' => array('copy', '{$vAbsolute}')
			)
		 */
		),
		'factoryMode' => true,
	);
/**
 * autoConfig property
 *
 * Look for a config file for loading settings
 *
 * @var bool true
 * @access public
 */
	var $autoConfig = true;
/**
 * setup method
 *
 * Initialize the component, setup validation rules/messages and check that the base directory is writable
 * If the base directory is not writable an error is triggered and the behavior is disabled
 *
 * @param mixed $model
 * @param array $config
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {
		if ($this->autoConfig && function_exists('autoConfig')) {
			autoConfig($this, $model->alias);
		}
		$this->settings[$model->alias] = am ($this->_defaultSettings, $config);
		extract ($this->settings[$model->alias]);
		uses('Folder');
		$baseDir = $this->_replacePseudoConstants($model, $baseDir);
		if (!file_exists($baseDir)) {
			new Folder($baseDir, true);
			if (!file_exists($baseDir)) {
				trigger_error('UploadBehavior::setup Base directory ' . $baseDir . ' doesn\'t exist and cannot be created.');
				$model->Behaviors->disable($this->name);
				return;
			}
		} elseif(!is_writable($baseDir)) {
			trigger_error('UploadBehavior::setup Base directory ' . $baseDir . ' is not writable.');
			$model->Behaviors->disable($this->name);
			return;
		}
		$this->settings[$model->alias]['baseDir'] = $baseDir;
		foreach ($this->settings[$model->alias]['versions'] as $key => $settings) {
			$this->version($model, $key, $settings);
		}
	}
/**
 * uploadErrors method
 *
 * @return void
 * @access public
 */
	function uploadErrors() {
		return $this->errors;
	}
/**
 * version method
 *
 * Get, Add, modify or delete version settings
 *
 * @param mixed $model
 * @param mixed $key
 * @param array $options
 * @return void
 * @access public
 */
	function version(&$model, $key = null, $options = array()) {
		if (!$key) {
			return $this->settings[$model->alias]['versions'];
		}
		if ($options === false) {
			unset ($this->settings[$model->alias]['versions'][$key]);
			return true;
		} elseif ($options) {
			extract($this->settings[$model->alias]);
			$options = am(array('vBaseDir' => $baseDir, 'vDirFormat' => $dirFormat, 'vFileFormat' => $fileFormat . '_' . $key), $options);
			if (isset($this->settings[$model->alias]['versions'][$key])) {
				$this->settings[$model->alias]['versions'][$key] = am($options, $this->settings[$model->alias]['versions'][$key]);
			} else {
				$this->settings[$model->alias]['versions'][$key] = $options;
			}
		}
		return $this->settings[$model->alias]['versions'][$key];
	}
/**
 * absolutePath method
 *
 * Convenience method
 *
 * @see path
 * @param mixed $model
 * @param mixed $id
 * @param string $to
 * @return void
 * @access public
 */
	function absolutePath(&$model, $id = null, $to = 'file') {
		if ($to == 'file' && isset($data[$model->alias]['original'])) {
			return $data[$model->alias]['original'];
		}
		return $this->_path($model, $id, $to, true);
	}
/**
 * afterDelete method
 *
 * Reset if running in factoryMode
 *
 * @return void
 * @access public
 */
	function afterDelete(&$model) {
		if ($this->name != 'Upload' && $factoryMode && $model->Behaviors->attached('Upload')) {
			$model->Behaviors->detatch($this->name);
			$model->Behaviors->enable('Upload');
		}
	}
/**
 * afterFind method
 *
 * If running in factory mode, and a single result is returned (a read/find) delegate to more specific
 * behavior if it exists so any methods specific to the type of file to be available
 * For each result, add the version info for convenience
 *
 * @param mixed $model
 * @param mixed $results
 * @param boolean $primary
 * @return void
 * @access public
 */
	function afterFind(&$model, $results, $primary = false) {
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload' && count($results) == 1) {
			$behavior = $this->__detectBehavior($model, $results[0]);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->disable('Upload');
				$model->Behaviors->$behavior->setup($model);
				return $model->Behaviors->$behavior->afterFind($model, $results, $primary);
			}
		}
		if ($model->findQueryType != 'list') {
			$data = $model->data;
			foreach ($results as $i => $result) {
				if (!isset($result[$model->alias][$fileField])
					|| isset($result[$model->alias]['versions'])) {
						return $results;
					}
				$model->id = $result[$model->alias][$model->primaryKey];
				$model->data = $result;
				$results[$i][$model->alias]['versions'] = $this->_path($model, null, 'versions');
			}
			$model->data = $data;
		}
		return $results;
	}
/**
 * afterSave method
 *
 * Reset if running in factoryMode
 *
 * @param mixed $model
 * @param mixed $created
 * @return void
 * @access public
 */
	function afterSave(&$model, $created) {
		extract ($this->settings[$model->alias]);
		if ($this->name != 'Upload' && $factoryMode && $model->Behaviors->attached('Upload')) {
			$model->Behaviors->detach($this->name);
			$model->Behaviors->enable('Upload');
		}
	}
/**
 * beforeDelete method
 *
 * Before deleting the record, delete the associated file(s)
 * If running in factory mode, delegate to more specific behavior if it exists
 *
 * @param mixed $model
 * @access public
 * @return void
 */
	function beforeDelete(&$model) {
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->disable('Upload');
				$model->Behaviors->$behavior->setup($model);
				$model->Behaviors->$behavior->beforeDelete($model);
			}
		}
		return $this->deleteFiles($model);
	}
/**
 * beforeSave method
 *
 * If the file field is an array process the file uploaded. No action if there is no file uploaded
 * Will prevent saving of 0byte files
 * If running in factory mode, delegate to more specific behavior if it exists
 *
 * @param mixed $model
 * @access public
 * @return void
 */
	function beforeSave(&$model) {
		return $this->process($model, $model->data, false);
	}
/**
 * beforeValidate method
 *
 * If the associated model is tableless setup the model schema to allow validation errors to be used
 * If running in factory mode, delegate to more specific behavior if it exists
 *
 * @param mixed $model
 * @return void
 * @access public
 */
	function beforeValidate(&$model) {
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->disable('Upload');
				$model->Behaviors->attach($behavior);
				$model->Behaviors->$behavior->setup($model);
				return $model->Behaviors->$behavior->beforeValidate($model);
			}
		}
		$this->_setupSchema($model);
		$this->_setupValidation($model);
		return true;
	}
/**
 * deleteFiles method
 *
 * Delete the files for this row - $which can either be 'all', 'original' or 'versions'
 * Will automatically delete empty folders after processing if permissions allow ( will not
 * raise an error if not possible to delete empty folders)
 * If running in factory mode, delegate to more specific behavior if it exists
 *
 * @param mixed $model
 * @param string $which
 * @return boolean True on success, false on failure
 * @access public
 */
	function deleteFiles(&$model, $id = null, $which = 'all') {
		if ($id && !is_int($id)) {
			$idSchema = $model->schema($model->primaryKey);
			if (!is_numeric($id) &&  $idSchema['length'] != 36) {
				$to = $id;
				$id = null;
			} elseif (is_array($id)) {
				extract (array_merge(array('id' => null), $id));
			}
		}
		if (!$id) {
			$id = $model->id;
		}
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->attach($behavior);
				$model->Behaviors->$behavior->setup($model);
				$return = $model->Behaviors->$behavior->deleteFiles($model, $id, $which);
				$model->Behaviors->detach($behavior);
				return $return;
			}
		}
		$paths = $this->_path($model, null, 'all', true);
		$folders = array();
		if (in_array($which, array('all', 'versions'))) {
			foreach ($paths['version'] as $file) {
				if (file_exists($file) && !unlink($file)) {
					$this->errors[] = 'Couldn\'t delete file ' . $file;
				}
				$folder = dirname($file);
				if (!in_array($folder, $folders)) {
					$folders[] = $folder;
				}
			}
		}
		if (in_array($which, array('all', 'original'))) {
			if (file_exists($paths['original']) && !unlink($paths['original'])) {
				$this->errors[] = 'Couldn\'t delete file ' . $paths['original'];
			}
			$folder = dirname($file);
			if (!in_array($folder, $folders)) {
				$folders[] = $folder;
			}
		}
		foreach ($folders as $folder) {
			$dir = new Folder($folder);
			if ($dir->read() == array(array(), array())) {
				$dir->delete();
			}
		}
		return !$this->errors;
	}
/**
 * checkUploadedAFile method
 *
 * Prevent saving a record if no file was uploaded
 *
 * @param mixed $model
 * @param mixed $fieldData
 * @return void
 * @access public
 */
	function checkUploadedAFile(&$model, $fieldData) {
		extract ($this->settings[$model->alias]);
		if (is_array($fieldData[$fileField]) && $fieldData[$fileField]['error'] == 4) {
			return false;
		}
		return true;
	}
/**
 * copy method
 *
 * @param mixed $model
 * @param mixed $id
 * @param mixed $from
 * @param mixed $to
 * @return void
 * @access public
 */
	function copy(&$model, $id = null, $from = null, $to = null) {
		if (!$to && !$from) {
			$from = $this->absolutePath($model);
			$to = $id;
		} elseif(!$to && strpos($id, DS) !== false) {
			$to = $from;
			$from = $id;
		}
		$path = dirname($to);
		new Folder($path, true);
		return copy($from, $to);
	}
/**
 * checkUploadError method
 *
 * @param mixed $model
 * @param mixed $fieldData
 * @return boolean true if a file was uploaded successfully, false if an error was encountered
 * @access public
 */
	function checkUploadError (&$model, $fieldData) {
		extract ($this->settings[$model->alias]);
		if (isset($fieldData[$fileField]) && is_array($fieldData[$fileField])) {
			$fieldData = $fieldData[$fileField];
		} else {
			return true;
		}
		if ($fieldData['size'] && $fieldData['error']) {
			return false;
		}
		return true;
	}
/**
 * checkUploadMime method
 *
 * Based on the config settings, check the uploaded mime type and reject if not an allowed mime type
 * Warning: the mimetype is set by the browser and may be inaccurate/manipulated
 *
 * @param mixed $model
 * @param mixed $fieldData
 * @return boolean true if a file is an acceptable mime type, false otherwise
 * @access public
 */
	function checkUploadMime (&$model, $fieldData) {
		extract ($this->settings[$model->alias]);
		if (isset($fieldData[$fileField]) && is_array($fieldData[$fileField])) {
			$fieldData = $fieldData[$fileField];
		} else {
			return true;
		}
		if (!$fieldData['size'] || $allowedMime == '*') {
			return true;
		}
		if (is_array($allowedMime)) {
			if (in_array($fieldData['type'], $allowedMime)) {
				return true;
			}
		} elseif ($fieldData['type'] == $allowedMime) {
			return true;
		}
		return false;
	}
/**
 * checkUploadSize method
 *
 * If the uploaded file exceeds the config settings - reject.
 * Note that file uploads are limited primarily by php's settings
 *
 * @param mixed $model
 * @param mixed $fieldData
 * @return boolean true if a file is smaller than the max file size, false otherwise
 * @access public
 */
	function checkUploadSize (&$model, $fieldData) {
		extract ($this->settings[$model->alias]);
		if (isset($fieldData[$fileField]) && is_array($fieldData[$fileField])) {
			$fieldData = $fieldData[$fileField];
		} else {
			return true;
		}
		if (!$fieldData['size']) {
			return false;
		} elseif( $allowedSize == '*') {
			return true;
		}
		$factor = 1;
		switch ($allowedSizeUnits) {
		case 'KB':
			$factor = 1024;
		case 'MB':
			$factor = 1024 * 1024;
		}
		if ($fieldData['size'] < ($allowedSize * $factor)) {
			return true;
		}
		return false;
	}
/**
 * hasChanged method
 *
 * Check if the file changed since it was uploaded - by checking if it exists and whether the checksum
 * still matches
 *
 * @param mixed $model
 * @param mixed $id
 * @return void
 * @access public
 */
	function hasChanged(&$model, $id = null) {
		extract ($this->settings[$model->alias]);
		if (!$id) {
			$id = $model->id;
		}
		if (!$model->hasField($checksumField)) {
			return false;
		}
		$file = $this->_path($model, $id, 'file', true);
		if (!file_exists($file)) {
			return true;
		}
		return (md5_file($file) != $model->field($checksumField));
	}
/**
 * metadata method
 *
 * Get the metadata directly from the file
 *
 * @param mixed $model
 * @param mixed $id
 * @param mixed $file
 * @param mixed $data
 * @return void
 * @access public
 */
	function metadata(&$model, $id = null, $filename = null, &$data = array()) {
		extract ($this->settings[$model->alias]);
		if (!$id) {
			$id = $model->id;
		}
		if (!$filename) {
			$filename = $this->_path($model, $id, 'file', true);
		}
		$bits = explode('.', $filename);
		if (count($bits) > 1) {
			$ext = low(array_pop($bits));
			$data[$model->alias][$extField] = $ext;
		} else {
			$ext = false;
		}
		if ($ext && isset($this->__extContentMap[$ext])) {
			$data[$model->alias]['mimetype'] = $this->__extContentMap[$ext];
		} else {
			$data[$model->alias]['mimetype'] = $this->__extContentMap['*'];
		}
		$data[$model->alias]['extension'] = $ext;
		if (file_exists($filename)) {
			$data[$model->alias]['filesize'] = filesize($filename);
			$data[$model->alias]['checksum'] = md5_file($filename);
			$data[$model->alias][$fileField] = basename($filename);
		}
		return $data[$model->alias];
	}
/**
 * process method
 *
 * @param mixed $model
 * @param array $data
 * @param bool $direct
 * @return void
 * @access public
 */
	function process(&$model, &$data = array(), $direct = true) {
		extract ($this->settings[$model->alias]);
		if ($data) {
			$model->data = $data;
		}
		if ($direct && !$model->validates()) {
			return false;
		}
		if (!isset($model->data[$model->alias]['tempFile'])) {
			if (!isset($model->data[$model->alias][$fileField])) {
				return true;
			} elseif (!is_array($model->data[$model->alias][$fileField])) {
				return true;
			} elseif (!$model->data[$model->alias][$fileField]['size']) {
				return false;
			}
		}
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->disable('Upload');
				$model->Behaviors->attach($behavior);
				$model->Behaviors->$behavior->setup($model);
				return $model->Behaviors->$behavior->beforeSave($model);
			}
		}
		$import = true;
		if (!isset($model->data[$model->alias]['tempFile'])) {
			$import = false;
			$model->data[$model->alias]['tempFile'] = $model->data[$model->alias][$fileField]['tmp_name'];
		}
		if (!$this->_beforeProcessUpload($model, $model->data)) {
			return false;
		}
		if ($import) {
			if ($model->data[$model->alias]['tempFile'] != $model->data[$model->alias]['original']) {
				copy($model->data[$model->alias]['tempFile'], $model->data[$model->alias]['original']);
				unlink($model->data[$model->alias]['tempFile']);
			}
		} else {
			if(!move_uploaded_file($model->data[$model->alias]['tempFile'], $model->data[$model->alias]['original'])) {
				$this->errors[] = 'Couldn\'t move the uploaded file.';
			}
		}
		$this->_afterProcessUpload($model, $model->data);
		return true;
	}
/**
 * relativePath method
 *
 * Convenience method
 *
 * @see path
 * @param mixed $model
 * @param mixed $id
 * @param string $to
 * @return void
 * @access public
 */
	function relativePath(&$model, $id = null, $to = 'file') {
		return $this->_path($model, $id, $to, false);
	}
/**
 * reprocess method
 *
 * Does not affect the original upload file
 * If $clearFolders is true, will delete the containing folder for versions before processing
 * 	useful only if files are organized such that all versions for one file are in the same
 * 	folder, and that folder only contains files for the same upload
 * Using the original upload file as the input, regenerate versions and reset size and checksum values
 * Useful if behavior settings change (e.g. image thumb size is changed system wide) or the original file
 * is overwritten with an updated version
 * If running in factory mode, delegate to more specific behavior if it exists
 *
 * @param mixed $model
 * @param mixed $id
 * @param boolean $clearFolder
 * @return void
 * @access public
 */
	function reprocess(&$model, $id = null, $clearFolders = false) {
		if ($id && !is_int($id)) {
			$idSchema = $model->schema($model->primaryKey);
			if (!is_numeric($id) &&  $idSchema['length'] != 36) {
				$clearFolders = $id;
				$id = null;
			} elseif (is_array($id)) {
				extract (array_merge(array('id' => null), $id));
			}
		}
		if (!$id) {
			$id = $model->id;
		}
		if (!$id) {
			return false;
		}
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->attach($behavior);
				$model->Behaviors->$behavior->setup($model);
				$return = $model->Behaviors->$behavior->reprocess($model, $id, $clearFolders);
				$model->Behaviors->detach($behavior);
				return $return;
			}
		}
		if ($clearFolders) {
			$path = $this->_path($model, null, 'all', true);
			$folders = array_unique($path['versionDir']);
			foreach ($folders as $path) {
				if (!file_exists($path)) {
					continue;
				}
				$folder = new Folder($path, false);
				$folder->delete();
			}
		}
		$this->_afterProcessUpload($model, $model->read(null, $id));
		$data = $this->metaData($model, $id);
		$data[$model->primaryKey] = $id;
		$model->save($data);
		return !$this->errors;
	}
/**
 * afterProcessUpload method
 *
 * Process any configured versions
 *
 * @param mixed $model
 * @param mixed $data
 * @return boolean
 * @access protected
 */
	function _afterProcessUpload(&$model, &$data) {
		extract($this->settings[$model->alias]);
		if (isset($data[$model->alias]['original'])) {
			$original = $data[$model->alias]['original'];
		} else {
			$original = $model->absolutePath();
		}
		if (file_exists($original)) {
			foreach($versions as $id => $vData) {
				$this->_clearReplace($model);
				$callback = false;
				extract($vData);
				if (!$callback) {
					continue;
				}
				$vAbsolute = $vBaseDir;
				if ($vDirFormat) {
					$vAbsolute .= DS . $vDirFormat . DS;
				}
				$vAbsolute .= $vFileFormat;
				$vData['vAbsolute'] = $vAbsolute;
				$this->__addReplace($model, '{$vBaseDir}', $vBaseDir);
				$this->__addReplace($model, '{$vDirFormat}', $vDirFormat);
				$this->__addReplace($model, '{$vFileFormat}', $vFileFormat);
				$this->__addReplace($model, '{$vAbsolute}', $vAbsolute);
				$vData = $this->_replacePseudoConstants($model, $vData);
				extract($vData);
				if (!is_array($callback[0])) {
					$callback = array($callback);
				}
				foreach ($callback as $params) {
					$method = array_shift($params);
					array_unshift($params, $id);
					if (!call_user_func_array(array(&$model, $method), $params)) {
						array_shift($params);
						$this->errors[] = 'failed to perform ' . $method . ' (' . implode ($params, ', ') . ')';
					}
				}
			}
		} else {
			$this->errors[] = 'Couldn\'t open the original file ' . $original;
		}
		return !$this->errors;
	}
/**
 * beforeProcessUpload method
 *
 * Anything to process before uploading a file
 * Set up all the meta data for saving, determine the filename to be saved
 *
 * @param mixed $model
 * @param mixed $data
 * @access protected
 * @return void
 */
	function _beforeProcessUpload(&$model, &$data) {
		$this->errors = array();
		$this->_clearReplace($model);
		extract ($this->settings[$model->alias]);
		if (is_array($data[$model->alias][$fileField])) {
			$file = $data[$model->alias]['tempFile'] = $data[$model->alias][$fileField]['tmp_name'];
			$filename = $data[$model->alias][$fileField]['name'];
			$data[$model->alias]['mimetype'] = $data[$model->alias][$fileField]['type'];
			$data[$model->alias]['filesize'] = $data[$model->alias][$fileField]['size'];
		} else {
			$file = $data[$model->alias]['tempFile'];
			$this->metaData($model, null, $file, $data);
			$filename = $data[$model->alias][$fileField];
		}
		list($filenameOnly, $extension, $filename) = $this->__filename($model, $fileFormat);
		$dir = $this->__path($model, $dirFormat);
		uses('Sanitize');
		$relativePath = $dir . DS . $filename;
		$path = $baseDir . DS . $relativePath;
		if(file_exists($path)) {
			if($overwriteExisting) {
				if(!unlink($path)) {
					$this->errors[] = 'The file ' . $relativePath . ' already exists and cannot be deleted.';
				}
			} else {
				$count = 2;
				while(file_exists($baseDir . $dir . DS . $filenameOnly . '_' . $count . $extension)) {
					$count++;
				}
				$filename = $filenameOnly .= '_' . $count;
				if ($extension) {
					$fielname .= '.' . $extension;
				}
				list($filenameOnly, $extension, $filename) = $this->__filename($model, $filename);
				$relativePath = $dir . DS . $filename;
				$path = $baseDir . DS . $relativePath;
			}
		}
		$conditions = array();
		if ($dirField) {
			$conditions[$dirField] = $dir;
		}
		if ($fileField) {
			$conditions[$fileField] = $filename;
		}
		if ($conditions && $id = $model->field($model->primaryKey, $conditions)) {
			if($overwriteExisting) {
				$model->id = $id;
			} else {
				$this->errors[] = 'The file is already in the system';
				return false;
			}
		}
		$folder = dirname($path);
		if (!new Folder($folder, true)) {
			$this->errors[] = 'Could not create the folder ' . $folder;
		}
		if ($dirField) {
			$data[$model->alias][$dirField] = $dir;
		}
		if ($fileField) {
			$data[$model->alias][$fileField] = $filename;
		}
		if ($extField) {
			$data[$model->alias][$extField] = $extension;
		}
		if ($checksumField) {
			$data[$model->alias][$checksumField] = md5_file($file);
		}
		$data[$model->alias]['relativePath'] = $relativePath;
		$data[$model->alias]['original'] = $path;
		return !$this->errors;
	}
/**
 * clearReplace method
 *
 * Remove existing path replacements in preparation for the next (if any) upload
 *
 * @param mixed $model
 * @return void
 * @access protected
 */
	function _clearReplace(&$model) {
		$this->settings[$model->alias]['pathReplacements'] = array();
		extract ($this->settings[$model->alias]);
		$this->settings[$model->alias]['baseDir'] = $this->_replacePseudoConstants($model, $baseDir);
		$original = $this->absolutePath($model);
		if ($original) {
			$this->__addReplace($model, '{$original}', $original);
			$file = basename($original);
			$bits = explode('.', $file);
			if (count($bits) > 1) {
				$extension = low(array_pop($bits));
				$this->__addReplace($model, '{$extension}', $extension);
				$file = implode('.', $bits);
				$this->__addReplace($model, '{$filenameOnly}', $file);
			} else {
				$this->__addReplace($model, '{$extension}', '');
				$this->__addReplace($model, '{$filenameOnly}', '');
			}
		}
	}

/**
 * path method
 *
 * Return the path to the file, the containing folder (for the original file)
 * the versions, a specific version or all
 *
 * @param mixed $model
 * @param mixed $id
 * @param string $to 'file', 'folder', 'versions', or 'all'
 * @param boolean $absolute
 * @return mixed string the file or folder path, or array for versions or all
 * @access protected
 */
	function _path(&$model, $id = null, $to = null, $absolute = false) {
		if ($id && !is_int($id)) {
			$idSchema = $model->schema($model->primaryKey);
			if (!is_numeric($id) &&  $idSchema['length'] != 36) {
				$absolute = $to;
				$to = $id;
				$id = null;
			} elseif (is_array($id)) {
				extract (array_merge(array('id' => null), $id));
			}
		}
		if (!$id) {
			$id = $model->id;
		}
		if ($to === null) {
			$to = 'file';
		}
		if (isset($model->data[$model->alias][$model->primaryKey]) &&
			$model->data[$model->alias][$model->primaryKey] != $id) {
				$model->read(null, $id);
		}
		extract ($this->settings[$model->alias]);
		if ($factoryMode && $this->name == 'Upload') {
			$behavior = $this->__detectBehavior($model, $model->data);
			if ($behavior && $model->Behaviors->attach($behavior, array('factoryMode' => true))) {
				$model->Behaviors->attach($behavior);
				$model->Behaviors->$behavior->setup($model);
				$return = $model->Behaviors->$behavior->_path($model, $id, $to, $absolute);
				$model->Behaviors->detach($behavior);
				return $return;
			}
		}
		if (in_array($to, array('file', 'folder', 'all'))) {
			if ($absolute) {
				$folder = $baseDir . DS;
			} else {
				$folder = '';
			}
			if ($dirField && $model->hasField($dirField)) {
				if (isset($model->data[$model->alias][$dirField])) {
					$folder .= $model->data[$model->alias][$dirField];
				} else {
					$folder .= $model->field($dirField);
				}
			} else {
				$folder .= $this->_replacePseudoConstants($model, $dirFormat);
			}
			if ($to == 'folder') {
				return $folder;
			}
		}
		if (in_array($to, array('file', 'all'))) {
			if ($absolute) {
				$original = $folder . DS;
			} else {
				$original = '';
			}
			if ($fileField && isset($model->data[$model->alias][$fileField])) {
				if (is_string($model->data[$model->alias][$fileField])) {
					$original .= $model->data[$model->alias][$fileField];
				} else {
					$original .= $model->data[$model->alias][$fileField]['name'];
				}
			} else {
				$original .= $model->field($fileField);
			}
			if ($to == 'file') {
				return $original;
			}
		}
		$this->__filename($model, $model->data[$model->alias][$fileField]);
		if (in_array($to, array('versions', 'all')) || isset($versions[$to])) {
			if (isset($versions[$to])) {
				$versions = array($to => $versions[$to]);
			}
			foreach ($versions as $key => $details) {
				$vBaseDir = $baseDir;
				$vDirFormat = $dirFormat;
				$vFileFormat = $fileFormat;
				extract($details);
				if ($absolute) {
					$versionDir[$key] = $this->_replacePseudoConstants($model, $vBaseDir) . DS;
				} else {
					$versionDir[$key] = '';
				}
				$vDir = $this->_replacePseudoConstants($model, $vDirFormat);
				if ($vDir) {
					$version[$key] = $versionDir[$key] .= $vDir . DS;
				}
				$version[$key] .= $this->_replacePseudoConstants($model, $vFileFormat);
			}
			if ($to == 'versions') {
				return $version;
			}
		}
		if (isset($version[$to])) {
			return $version[$to];
		}
		return compact('file', 'folder', 'version', 'versionDir');
	}
/**
 * replacePseudoConstants method
 *
 * for the passed string look for and replace any pseudo constants.
 * {CONSTANT} will be replaced with the defined CONSTANT (if it's defined)
 * {$dataVariable} will be replaced with $this->data['ModelAlias']['dataVariable'] if it is set
 * {$databaseField} will be replaced with $model->field('databaseField');
 * {$random} will be replaced with a random 5 digit number, regenerated each time this method is called
 *
 * @param mixed $model
 * @param mixed $string
 * @return boolean true on success, false on error
 * @access protected
 */
	function _replacePseudoConstants(&$model, $string) {
		extract($this->settings[$model->alias]);
		if (is_array($string)) {
			foreach ($string as $i => $str) {
				$string[$i] = $this->_replacePseudoConstants($model, $str);
			}
			return $string;
		}
		$_replacements = $this->settings[$model->alias]['pathReplacements'];
		$random = uniqid('');
		$random = substr($random, strlen($random) -5, strlen($random));
		preg_match_all('@{\$?([^{}]*)}@', $string, $r);
		foreach ($r[1] as $i => $match) {
			$_found = false;
			if (!isset($this->settings[$model->alias]['pathReplacements'][$r[0][$i]])) {
				if (up($match) == $match) {
					$constants = get_defined_constants();
					if (isset($constants[$match])) {
						$this->__addReplace($model, $r[0][$i], $constants[$match]);
						$_found = true;
					}
					if (!$_found) {
						$this->errors[] = 'Cannot replace ' . $match . ' as the constant ' . $match . ' is not defined.';
					}
				} else {
					if (isset($$match)) {
						$this->__addReplace($model, $r[0][$i], $$match);
						$_found = true;
					} elseif (isset($model->data[$model->alias][$match])) {
						$this->__addReplace($model, $r[0][$i], $model->data[$model->alias][$match]);
						$_found = true;
					} elseif ($model->id && $model->hasField($match)) {
						$this->__addReplace($model, $r[0][$i], $model->field($match));
						$_found = true;
					}
					if (!$_found) {
						$this->errors[] = 'Cannot replace ' . $match . ' as the variable $' . $match . ' cannot be determined.';
						$this->errors[] = $model->data;
					}
				}
			}
		}
		$markers = array_keys($this->settings[$model->alias]['pathReplacements']);
		$replacements = array_values($this->settings[$model->alias]['pathReplacements']);
		$this->settings[$model->alias]['pathReplacements'] = $_replacements;
		return str_replace ($markers, $replacements, $string);
	}
/**
 * setupSchema method
 *
 * @TODO How to do this without directly accessing the _schema field
 * @param mixed $model
 * @return void
 * @access protected
 */
	function _setupSchema($model) {
		$schema = $model->schema();
		extract ($this->settings[$model->alias]);
		if (!$schema) {
			$model->_schema = $schema[$fileField] = array(
				'type' => 'string',
				'null' => null,
				'default' => null,
				'length' => 100
			);
		}
	}
/**
 * setupValidation method
 *
 * Add validation rules specific to this behavior. Prepend the behaviors validation rules
 * To allow the behavior to modify the model's data for any other validation rules
 *
 * @param mixed $model
 * @return void
 * @access protected
 */
	function _setupValidation(&$model) {
		extract ($this->settings[$model->alias]);
		if (isset($model->validate[$fileField])) {
			$existingValidations = $model->validate[$fileField];
			if (!is_array($existingValidations)) {
				$existingValidations = array($existingValidations);
			}
		} else {
			$existingValidations = array();
		}
		if ($mustUploadFile) {
			$validations['uploadAFile'] = array(
				'on' => 'create',
				'rule' => 'checkUploadedAFile',
				'message' => 'Please select a file to upload.',
				'last' => true
			);
		}
		$validations['uploadError'] = array(
			'rule' => 'checkUploadError',
			'message' => 'An error was generated during the upload.',
			'last' => true
		);
		if (is_array($allowedMime)) {
			$allowedMimes = implode(',', $allowedMime);
		} else {
			$allowedMimes = $allowedMime;
		}
		$validations['uploadMime'] = array(
			'rule' => 'checkUploadMime',
			'message' => 'The submitted mime type is not permitted, only ' . $allowedMimes . ' permitted.',
			'last' => true
		);
		if ($allowedExt != '*') {
			if (is_array($allowedExt)) {
				$allowedExts = implode(',', $allowedExt);
			} else {
				$allowedExts = $allowedExt;
				$allowedExt = array($allowedExt);
			}
			$validations['uploadExt'] = array(
				'rule' => array('extension', $allowedExt),
				'message' => 'The submitted file extension is not permitted, only ' . $allowedExts . ' permitted.',
				'last' => true
			);
		}
		$validations['uploadSize'] = array(
			'rule' => 'checkUploadSize',
			'message' => 'The file uploaded is too big, only files less than ' . $allowedSize . ' ' . $allowedSizeUnits .' permitted.',
			'last' => true
		);
		$model->validate[$fileField] = am($validations, $existingValidations);
	}
/**
 * addReplace method
 *
 * Add a find and replace pair
 *
 * @param mixed $model
 * @param mixed $find
 * @param string $replace
 * @return void
 * @access private
 */
	function __addReplace(&$model, $find, $replace = '') {
		if (is_array($find)) {
			foreach ($find as $f => $r) {
				$this->__addReplace($model, $f, $r);
			}
			return;
		}
		if (is_array($replace)) {
			$replace = array_shift($replace);
		}
		$replace = $this->_replacePseudoConstants($model, $replace);
		$this->settings[$model->alias]['pathReplacements'][$find] = $replace;
	}
/**
 * detectBehavior method
 *
 * Based on the passed data, check if a more specific behavior exists and if so return its name
 *
 * @param mixed $model
 * @param mixed $data
 * @return mixed false if no behavior matches, the name of the matched behavior otherwise
 * @access private
 */
	function __detectBehavior(&$model, &$data = null) {
		extract ($this->settings[$model->alias]);
		if (!$data && $model->id) {
			$_data = $model->data;
			$data = $model->read();
			$model->data = $_data;
		}
		$mime = false;
		if (isset($data[$model->alias][$fileField]) && is_array($data[$model->alias][$fileField])) {
			$mime = $data[$model->alias][$fileField]['type'];
		} elseif (isset($data[$model->alias]['mimetype'])) {
			$mime = $data[$model->alias]['mimetype'];
		}
		$extension = false;
		if (isset($data[$model->alias][$fileField]) && is_array($data[$model->alias][$fileField])) {
			$file = $data[$model->alias][$fileField]['name'];
		} elseif (isset($data[$model->alias][$fileField])) {
			$file = $data[$model->alias][$fileField];
		} else {
			return false;
		}
		$bits = explode('.', $file);
		if (count($bits) > 1) {
			$extension = low(array_pop($bits));
		}
		$behavior = false;
		foreach ($this->__behaviorMap as $type => $tests) {
			$match = 0;
			foreach ($tests as $field => $value) {
				switch ($field) {
				case 'mime':
					$value = str_replace('*', '', $value);
					if (strpos($mime, $value) !== false) {
						$match++;
					}
				case 'extension':
					if (is_string($value)) {
						if ($extension == $value) {
							$match++;
						}
					} elseif (in_array($extension, $value)) {
						$match++;
					}
				}
			}
			if ($match == count($tests)) {
				$behavior = $type;
				break;
			}
		}
		if ($behavior) {
			$behavior = Inflector::classify($behavior) . 'Upload';
		} else {
			return false;
		}
		$behaviors = Configure::listObjects('behavior');
		if (in_array($behavior, $behaviors)) {
			return $behavior;
		}
		return false;
	}
/**
 * filename method
 *
 * Clean the string and return something suitable to use as a filename
 * Allows double file extensions (.tar.gz)
 *
 * @param mixed $model
 * @param mixed $string
 * @return array  a 'filename safe' string, the extension, the full filename
 * @access private
 */
	function __filename(&$model, $string) {
		extract ($this->settings[$model->alias]);
		if (strpos($string,'{') !== false) {
			$string = low($this->_replacePseudoConstants($model, $string));
		}
		$string = str_replace('__dot__', '.', Inflector::slug(str_replace('.', '__dot__', $string)));
		$bits = explode('.', $string);
		if (count($bits) > 1) {
			$ext = low(array_pop($bits));
		} else {
			$ext = false;
		}
		$filename = $full = implode('.', $bits);
		if($ext) {
			$full .= '.' . $ext;
		}
		$this->__addReplace($model, '{$filenameOnly}', $filename);
		$this->__addReplace($model, '{$extension}', $ext);
		$this->__addReplace($model, '{$filename}', $full);
		return array($filename, $ext, $full);
	}
/**
 * path method
 *
 * Replace any pseudo constants and create the folder
 *
 * @param mixed $model
 * @param mixed $path
 * @return string the path to the folder
 * @access private
 */
	function __path (&$model, $path) {
		extract ($this->settings[$model->alias]);
		if (strpos($path,'{') !== false) {
			$path = $this->_replacePseudoConstants($model, $path);
		}
		if (!$path) {
			$this->errors[] = 'Couldn\'t determine the path ' . $dir;
			return false;
		} else {
			if (!(new Folder ($baseDir . DS . $path, true))) {
				$this->errors[] = 'Couldn\'t create the path ' . $dir;
				return false;
			};
		}
		$this->__addReplace($model, '{$dir}', $path);
		return $path;
	}
}
?>