<?php
/* SVN FILE: $Id: image_upload.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for image_upload.php
 *
 * Long description for image_upload.php
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
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
require_once('upload.php');
/**
 * ImageUploadBehavior class
 *
 * Extending the Upload behavior, adding image specific logic uses directy system calls to
 * imagemagick - which naturally must be installed for it to work
 *
 * @uses          UploadBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class ImageUploadBehavior extends UploadBehavior {
/**
 * name property
 *
 * @var string 'ImageUpload'
 * @access public
 */
	var $name = 'ImageUpload';
/**
 * mapMethods property
 *
 * Map "none-file" calls to the __passThru method which will pickup the filename and call the file version
 * of the method.
 * For Flip/Flop/Rotate - operate on the original file and reprocess the versions
 *
 * @var array
 * @access public
 */
	var $mapMethods = array(
		'/convert|crop|polaroid|perspective|reflection|resize|shear|thumbnail|trim/' => '__passThru',
		'/flip|flop|rotate/' => '__passThruReprocess',
	);
/**
 * current property
 *
 * The file currently being processed. stored to allow consecutive operations without deleting the original file
 *
 * @var bool false
 * @access private
 */
	var $__current = false;
/**
 * setup method
 *
 * Override defaults inherited from the upload behavior
 * Set allowed mimes to image types
 * Set allowed extension to matching mimes
 * Set versions to create tiny, small, medium and large thumbs/versions
 *
 * @param mixed $model
 * @param array $config
 * @return void
 * @access public
 */
	function setup (&$model, $config = array()) {
		$this->_defaultSettings['allowedMime'] = array('image/jpeg', 'image/gif', 'image/png', 'image/bmp');
		$this->_defaultSettings['allowedExt'] = array('jpeg', 'jpg', 'gif', 'png', 'bmp');
		$this->_defaultSettings['versions'] = array(
			'thumb' => array(
				'callback' => array('resize', 50, 50)
			),
			'small' => array(
				'callback' => array('resize', 75, 75)
			),
			'medium' => array(
				'callback' => array('resize', 150, 150)
			),
			'large' => array(
				'callback' => array(
					array('resize', 600, 400)
				)
			),
		);
		parent::setup($model, $config);
	}
/**
 * convertFile method
 *
 * Directly call convert
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param array $params
 * @return void
 * @access public
 */
	function convertFile(&$model, $fullpath = null, $params = array()) {
		if ($params) {
			return $this->__imConvert($model, $fullpath, $params);
		}
		return false;
	}
/**
 * cropFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param array $params
 * @return void
 * @access public
 */
	function cropFile(&$model, $fullpath = null, $cropTo = null, $params = array()) {
		if (!$cropTo) {
			$params['gravity'] = 'Center';
			$cropTo = '50%\!';
		}
		$params = am($params, array('-crop' => $cropTo));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * flipFile method
 *
 * Flip (mirror) vertically
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param array $params
 * @return void
 * @access public
 */
	function flipFile(&$model, $fullpath = null, $params = array()) {
		$params = am($params, array('-flip'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * flopFile method
 *
 * Flop (mirror) horizontally
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param array $params
 * @return void
 * @access public
 */
	function flopFile(&$model, $fullpath = null, $params = array()) {
		$params = am($params, array('-flop'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * perspectiveFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param mixed $tl
 * @param mixed $tr
 * @param mixed $bl
 * @param mixed $br
 * @param array $params
 * @return void
 * @access public
 */
	function perspectiveFile(&$model, $fullpath = null, $tl = null, $tr = null, $bl = null, $br = null, $params = array()) {
		$dimensions = $this->__imIdentify($model, $fullpath, array('format' => '%wx%h'));
		if (!$dimensions) {
			return false;
		}
		list($width, $height) = explode('x', $dimensions);
		if (!$tl) {
			$tl = '0,0';
		}
		if (!$tr) {
			$tr = $width . ',0';
		}
		if (!$bl) {
			$bl = '0,' . $height;
		}
		if (!$br) {
			$br = $width . ',' . $height;
		}
		foreach (array('tl', 'tr', 'bl', 'br') as $var) {
			if (strpos($$var, '%')) {
				list($w, $h) = explode(',', $$var);
				if (strpos($w, '%')) {
					$w = str_replace('%', '', $w);
					$w = round($width * $w / 100);
				}
				if (strpos($h, '%')) {
					$h = str_replace('%', '', $h);
					$h = round($height * $h / 100);
				}
				$$var = $w . ',' . $h;
			}
		}
		$params = am($params, array(
			'-matte',
			'virtual-pixel' => 'transparent',
			'distort' => "Perspective '0,0 $tl $width,0 $tr 0,$height $bl $width,$height $br'",
		));
		$return = $this->__imConvert($model, $fullpath, $params);
	}
/**
 * polaroidFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param int $width
 * @param int $height
 * @param array $params
 * @return void
 * @access public
 */
	function polaroidFile(&$model, $fullpath = null, $width = 100, $height = 100, $params = array()) {
		$params = am($params, array('thumbnail' => $width . 'x' . $height, '+polaroid'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * reflectionFile method
 *
 * @TODO not fully implemented/tested
 * @param mixed $model
 * @param mixed $fullpath
 * @param mixed $reflectionDepth
 * @param array $params
 * @return void
 * @access public
 */
	function reflectionFile(&$model, $fullpath, $reflectionDepth = 0.5, $params = array()) {
		$upsideDown = dirname($fullpath) . DS . rand();
		$rightWayUp = dirname($fullpath) . DS . rand();
		copy($fullpath, $rightWayUp);
		copy($fullpath, $upsideDown);
		$this->resizeFile($model, $upsideDown, '100%', 200 * $reflectionDepth . '%');
		$this->flipFile($model, $upsideDown);
		$dimensions = $this->__imIdentify($model, $upsideDown, array('format' => '%wx%h'));
		list($width, $height) = explode('x', $dimensions);
		$height = $height * $reflectionDepth;
		$this->cropFile($model, $upsideDown, "{$width}x$height");
		$params = am($params, array('geometry' => '+1+1', 'tile' => '1x2'));
		$return = $this->__imMontage($model, array($rightWayUp, $upsideDown), $params, $fullpath);
	}
/**
 * resizeFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param int $width
 * @param int $height
 * @return void
 * @access public
 */
	function resizeFile(&$model, $fullpath = null, $width = 600, $height = 400, $params = array()) {
		$params = am($params, array('resize' => $width . 'x' . $height . '>'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * rotateFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param int $angle
 * @param array $params
 * @return void
 * @access public
 */
	function rotateFile(&$model, $fullpath = null, $angle = 90, $params = array()) {
		$params = am($params, array('rotate' => $angle));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * thumbnailFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param int $width
 * @param int $height
 * @return void
 * @access public
 */
	function thumbnailFile(&$model, $fullpath = null, $width = 100, $height = 100, $params = array()) {
		$params = am($params, array('thumbnail' => $width . 'x' . $height . '^', '-auto-orient'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * shearFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param int $x
 * @param int $y
 * @return void
 * @access public
 */
	function shearFile(&$model, $fullpath = null, $x = 0 , $y = 0, $params = array()) {
		if ($y === false && $x) {
			$params = am($params, array('shear' => $x));
		} else {
			$params = am($params, array('shear' => $x . 'x' . $y));
		}
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * trimFile method
 *
 * @param mixed $model
 * @param mixed $fullpath
 * @param array $params
 * @return void
 * @access public
 */
	function trimFile(&$model, $fullpath = null, $params = array()) {
		$params = am($params, array('-trim', '+repage'));
		return $this->__imConvert($model, $fullpath, $params);
	}
/**
 * beforeProcessUpload method
 *
 * Add width and height to the data to be saved
 *
 * @param mixed $model
 * @param mixed $data
 * @return void
 * @access protected
 */
	function _beforeProcessUpload(&$model, &$data) {
		parent::_beforeProcessUpload($model, $data);
		extract($this->settings[$model->alias]);
		list($width, $height) = getimagesize($data[$model->alias]['tempFile']);
		$data[$model->alias]['width'] = $width;
		$data[$model->alias]['height'] = $height;
		return !$this->errors;
	}
/**
 * passThru method
 *
 * @param mixed $model
 * @param mixed $method
 * @param mixed $firstParam
 * @return void
 * @access private
 */
	function __passThru(&$model, $method, $firstParam = null) {
		if (!$firstParam) {
			return $this->$method($model);
		}
		$params = func_get_args();
		array_shift($params);
		array_shift($params);
		$path = '';
		if (isset($this->settings[$model->alias]['versions'][$params[0]])) {
			$path = $model->absolutePath($params[0]);
		} elseif (is_numeric($params[0]) && $model->hasAny(array($model->primaryKey => $params[0]))) {
			$path = $model->absolutePath($params[0]);
		} else {
			$idSchema = $model->schema($model->primaryKey);
			if ($idSchema['length'] == 36 && $model->hasAny(array($model->primaryKey => $params[0]))) {
				$path = $model->absolutePath($params[0]);
			}
		}
		if ($path) {
			$params[0] = $path;
		} else {
			$this->errors[] = "method {$method} called but impossible to pass to {$method}File - couldn't determine the file to process";
			return false;
		}
		$method = $method . 'File';
		switch (count($params)) {
			case 1:
				return $this->{$method}($model, $params[0]);
			case 2:
				return $this->{$method}($model, $params[0], $params[1]);
			case 3:
				return $this->{$method}($model, $params[0], $params[1], $params[2]);
			case 4:
				return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3]);
			case 5:
				return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3], $params[4]);
			default:
				array_unshift($params, $model);
				return call_user_func_array(array(&$this, $method), $params);
			break;
		}
	}
/**
 * passThruReprocess method
 *
 * @param mixed $model
 * @param mixed $method
 * @param mixed $id
 * @param array $params
 * @return void
 * @access private
 */
	function __passThruReprocess(&$model, $method, $id = null) {
		if (!$id) {
			if (!$model->id) {
				return false;
			}
			$id = $model->id;
		}
		$model->id = $id;
		$method = $method . 'File';
		$params = func_get_args();
		$params[1] = $this->absolutePath($model);
		$return = call_user_func_array(array(&$this, $method), $params);
		if ($return) {
			$return = $this->reprocess($model, true);
			return $return;
		}
		return false;
	}
/**
 * imConvert method
 *
 * @param mixed $input (filename)
 * @param array $params
 * @param mixed $output (filename)
 * @param bool $return
 * @return void
 * @access private
 */
	function __imConvert(&$model, $input, $params = array(), $output = null, $return = false) {
		if (isset($params['return'])) {
			$return = $params['return'];
			unset($params['return']);
		}
		if (!$output) {
			$output = $input;
		}
		if ($this->__current != $output) {
			$this->__current = $output;
			if (file_exists($output) && $output != $input) {
				unlink($output);
			}
			$input = $this->absolutePath($model);
		}
		$dir = dirname($output);
		if (!file_exists($dir)) {
			new Folder($dir, true);
		}
		$params = $this->__imParseParams($params);
		$command = "convert $input $params $output";
		if ($return) {
			return $command;
		}
		$return = exec($command, $exitCode);
		//return $return;
		return true;
	}
/**
 * imMontage method
 *
 * @param mixed $model
 * @param mixed $input
 * @param array $params
 * @param mixed $output
 * @return void
 * @access private
 */
	function __imMontage(&$model, $input, $params = array(), $output = null) {
		if (!$output) {
			if (is_array($input)) {
				$output = $input[0];
			} else {
				$output = $input;
			}
		}
		if (is_array($input)) {
			$input = implode($input, ' ');
		}
		if ($this->__current != $output) {
			$this->__current = $output;
			if (file_exists($output) && $output != $input) {
				unlink($output);
			}
		}
		$dir = dirname($output);
		if (!file_exists($dir)) {
			new Folder($dir, true);
		}
		$params = $this->__imParseParams($params);
		$command = "montage $input $params $output";
		$return = exec($command, $exitCode);
		//return $return;
		return true;

	}
/**
 * imInfo method
 *
 * @param mixed $model
 * @param mixed $path
 * @param array $params
 * @return void
 * @access private
 */
	function __imIdentify(&$model, $path = null, $params = array()) {
		if (!$path || !file_exists($path)) {
			$path = $this->absolutePath($model);
		}
		$params = $this->__imParseParams($params);
		$return = exec('identify ' . $params . ' ' . $path );
		return $return;
	}

/**
 * imParseParams method
 *
 * @param mixed $params
 * @param mixed $key
 * @return void
 * @access private
 */
	function __imParseParams($params, $key = null) {
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				$params[$key] = $this->__imParseParams($value, $key);
			} elseif (is_numeric(($key))) {
				$params[$key] = $value;
			} elseif (!in_array($key[0], array('-', '+'))) {
				$this->__imEscape($value);
				$params[$key] = '-' . $key . ' ' . $value;
			} else {
				$this->__imEscape($value);
				$params[$key] = $key . ' ' . $value;
			}
		}
		return implode($params, ' ');
	}
/**
 * imEscape method
 *
 * @param mixed $value
 * @return void
 * @access private
 */
	function __imEscape(&$value) {
		$value = str_replace('>', '\>', $value);
	}
}
?>