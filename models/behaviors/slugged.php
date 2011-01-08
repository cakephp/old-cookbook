<?php
/**
 * Short description for slugged.php
 *
 * Part based/inspired by the sluggable behavior of Mariano Iglesias
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
/**
 * SluggedBehavior class
 *
 * @uses          ModelBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class SluggedBehavior extends ModelBehavior {
/**
 * name property
 *
 * @var string 'Slugged'
 * @access public
 */
	var $name = 'Slugged';
/**
 * defaultSettings property
 *
 * overwrite has 2 values
 * 	false - once the slug has been saved, do not change it (use if you are doing lookups based on slugs)
 * 	true - if the label field values change, regenerate the slug (use if you are the slug is just window-dressing)
 * unique has 2 values
 * 	false - will not enforce a unique slug, whatever the label is is direclty slugged without checking for duplicates
 * 	true - use if you are doing lookups based on slugs (see overwrite)
 * mode has the following values
 * 	ascii - retuns an ascii slug generated using the core Inflector::slug() function
 * 	display - a dummy mode which returns a slug legal for display - removes illegal (not unprintable) characters
 * 	url - returns a slug appropriate to put in a url
 * 	class - a dummy mode which returns a slug appropriate to put in a html class (there are no restrictions)
 * 	id - retuns a slug appropriate to use in a html id
 *
 * @var array
 * @access protected
 */
	var $_defaultSettings = array(
		'label' => null,
		'slugField' => 'slug',
		'mode' => 'url',
		'seperator' => '-',
		'length' => 100,
		'overwrite' => true,
		'unique' => false,
		'notices' => true,
		'multi-byte' => false,
	);
/**
 * setup method
 *
 * Use the model's label field as the default field on which to base the slug, the label can be made up of multiple
 * fields by specifying an array of fields
 *
 * @param mixed $model
 * @param array $config
 * @access public
 * @return void
 */
	function setup(&$model, $config = array()){
		$this->_defaultSettings['label'] = array($model->displayField);
		$this->settings[$model->alias] = am($this->_defaultSettings, $config);
		extract ($this->settings[$model->alias]);
		$label = $this->settings[$model->alias]['label'] = (array)$label;
		foreach($label as $field) {
			if ($notices) {
				if (!$model->hasField($field)){
					trigger_error('(SluggedBehavior::setup) model ' . $model->name . ' is missing the field ' . $field . ' specified in the setup.', E_USER_WARNING);
					$model->Behaviors->disable($this->name);
				}
			}
		}
	}
/**
 * beforeSave method
 *
 * if a new row, or overwrite is set to true, check for a change to a label field and add the slug to the data
 * to be saved
 * If no slug at all is returned (should not be permitted and prevented by validating the label fields) the model
 * alias will be used as a slug.
 * If unique is set to true, check for a unique slug and if unavailable suffix the slug with -1, -2, -3 etc.
 * until a unique slug is found
 *
 * @param mixed $model
 * @access public
 * @return void
 */
	function beforeSave(&$model) {
		extract ($this->settings[$model->alias]);
		if (!$model->hasField($slugField)){
			return true;
		}
		if ($overwrite || !$model->id) {
			$somethingToDo = false;
			foreach($label as $field) {
				if (isset($model->data[$model->alias][$field])) {
					$somethingToDo = true;
				}
			}
			if (!$somethingToDo) {
				return true;
			}
			$slug = array();
			foreach($label as $field) {
				if (isset($model->data[$model->alias][$field])) {
					$slug[] = $model->data[$model->alias][$field];
				} elseif ($model->id) {
					$slug[] = $model->field($field);
				}
			}
			$slug = implode($slug, $seperator);
			$slug = $this->slug($model, $slug);
			if (!$slug) {
				$slug = $model->alias;
			}
			if ($unique) {
				$conditions = array($model->alias . '.' . $slugField => $slug);
				if ($model->id) 					{
					$conditions['NOT'][$model->alias . '.' . $model->primaryKey] = $model->id;
				}
				$fields = array($model->primaryKey, $slugField);
				$recursive = -1;
				$i = 0;
				$suffix = '';
				while($model->find('count', compact('condition',  'fields', 'recursive'))) {
					$i++;
					$suffix	= $seperator . $i;
					$conditions[$model->alias . '.' . $slugField] = $slug . $suffix;
				}
				if ($suffix) {
					$slug .= $suffix;
				}
			}
			$this->_addToWhitelist($model, array($slugField));
			$model->data[$model->alias][$slugField] = $slug;
		}
		return true;
	}
/**
 * slug method
 *
 * For the given string, generate a slug. The replacements used are based on the mode setting, If tidy is false
 * (only possible if directly called - primarily for tracing and testing) seperators will not be cleaned up
 * and so slugs like "-----as---df-----" are possible, which by default would otherwise be returned as "as-df".
 * If the mode is "id" and the first charcter of the regex-ed slug is numeric, it will be prefixed with an x.
 *
 * @param mixed $model
 * @param mixed $string
 * @param bool $tidy
 * @return string a slug
 * @access public
 */
	function slug($model, $string, $tidy = true) {
		extract ($this->settings[$model->alias]);
		if ($mode == 'ascii') {
			$slug = Inflector::slug($string, $seperator);
		} else {
			$regex = $this->__regex($mode);
			if ($regex) {
				$slug = preg_replace('@[' . $regex . ']@Su', $seperator, $string);
			} else {
				$slug = $string;
			}
		}
		if ($tidy) {
			$slug = preg_replace('/' . $seperator . '+/', $seperator, $slug);
			$slug = trim($slug, $seperator);
			if ($slug && $mode == 'id' && is_numeric($slug[0])) {
				$slug = 'x' . $slug;
			}
		}
		if (strlen($slug) > $length) {
			$slug = substr ($slug, 0, $length);
		}
		if ($multibyte && function_exists('mb_convert_encoding')) {
			$encoding = $multibyte;
			if ($multibyte == true) {
				$encoding = Configure::read('App.encoding');
			}
			$slug = mb_convert_encoding($slug, $encoding, $encoding);
		}
		return $slug;
	}
/**
 * resetSlugs method
 *
 * Regenerate all slugs. On large dbs this can take more than 30 seconds - a time limit is set to allow a minimum
 * 100 updates per second as a preventative measure.
 *
 * @param AppModel $model
 * @param array $conditions
 * @param int $recursive
 * @return bool true on success false otherwise
 * @access public
 */
	function resetSlugs(&$model, $conditions = array(), $recursive = -1) {
		extract ($this->settings[$model->alias]);
		if (!$model->hasField($slugField)) {
			return false;
		}
		$fields = array_merge((array)$model->primaryKey, $label);
		$sort = $model->displayField . ' ASC';
		$count = $model->find('count');
		set_time_limit (max(30, $count / 100));
		foreach ($model->find('all', compact('conditions', 'fields', 'sort', 'recursive')) as $row) {
			$model->create();
			$model->save($row);
		}
		return true;
	}
/**
 * regex method
 *
 * Based upon the mode return a partial regex to generate a valid string for the intended use. Note that you
 * can use almost litterally anything in a url - the limitation is only in what your own application
 * understands. See the test case for info on how these regex patterns were generated.
 *
 * @param string $mode
 * @return string a partial regex
 * @access private
 */
	function __regex($mode) {
		$return = '\x00-\x1f\x26\x3c\x7f-\x9f\x{d800}-\x{dfff}\x{fffe}-\x{ffff}';
		if ($mode == 'display') {
			return $return;
		}
		$return .= preg_quote(' \'"/:?<>.$/:;?@=+&', '@');
		if ($mode == 'url') {
			return $return;
		}
		$return .= '';
		if ($mode == 'class') {
			return $return;
		}
		if ($mode == 'id') {
			return '\x{0000}-\x{002f}\x{003a}-\x{0040}\x{005b}-\x{005e}\x{0060}\x{007b}-\x{007e}\x{00a0}-\x{00b6}' .
			'\x{00b8}-\x{00bf}\x{00d7}\x{00f7}\x{0132}-\x{0133}\x{013f}-\x{0140}\x{0149}\x{017f}\x{01c4}-\x{01cc}' .
			'\x{01f1}-\x{01f3}\x{01f6}-\x{01f9}\x{0218}-\x{024f}\x{02a9}-\x{02ba}\x{02c2}-\x{02cf}\x{02d2}-\x{02ff}' .
			'\x{0346}-\x{035f}\x{0362}-\x{0385}\x{038b}\x{038d}\x{03a2}\x{03cf}\x{03d7}-\x{03d9}\x{03db}\x{03dd}\x{03df}' .
			'\x{03e1}\x{03f4}-\x{0400}\x{040d}\x{0450}\x{045d}\x{0482}\x{0487}-\x{048f}\x{04c5}-\x{04c6}\x{04c9}-\x{04ca}' .
			'\x{04cd}-\x{04cf}\x{04ec}-\x{04ed}\x{04f6}-\x{04f7}\x{04fa}-\x{0530}\x{0557}-\x{0558}\x{055a}-\x{0560}' .
			'\x{0587}-\x{0590}\x{05a2}\x{05ba}\x{05be}\x{05c0}\x{05c3}\x{05c5}-\x{05cf}\x{05eb}-\x{05ef}\x{05f3}-\x{0620}' .
			'\x{063b}-\x{063f}\x{0653}-\x{065f}\x{066a}-\x{066f}\x{06b8}-\x{06b9}\x{06bf}\x{06cf}\x{06d4}\x{06e9}' .
			'\x{06ee}-\x{06ef}\x{06fa}-\x{0900}\x{0904}\x{093a}-\x{093b}\x{094e}-\x{0950}\x{0955}-\x{0957}' .
			'\x{0964}-\x{0965}\x{0970}-\x{0980}\x{0984}\x{098d}-\x{098e}\x{0991}-\x{0992}\x{09a9}\x{09b1}\x{09b3}-\x{09b5}' .
			'\x{09ba}-\x{09bb}\x{09bd}\x{09c5}-\x{09c6}\x{09c9}-\x{09ca}\x{09ce}-\x{09d6}\x{09d8}-\x{09db}\x{09de}' .
			'\x{09e4}-\x{09e5}\x{09f2}-\x{0a01}\x{0a03}-\x{0a04}\x{0a0b}-\x{0a0e}\x{0a11}-\x{0a12}\x{0a29}\x{0a31}\x{0a34}' .
			'\x{0a37}\x{0a3a}-\x{0a3b}\x{0a3d}\x{0a43}-\x{0a46}\x{0a49}-\x{0a4a}\x{0a4e}-\x{0a58}\x{0a5d}\x{0a5f}-\x{0a65}' .
			'\x{0a75}-\x{0a80}\x{0a84}\x{0a8c}\x{0a8e}\x{0a92}\x{0aa9}\x{0ab1}\x{0ab4}\x{0aba}-\x{0abb}\x{0ac6}\x{0aca}' .
			'\x{0ace}-\x{0adf}\x{0ae1}-\x{0ae5}\x{0af0}-\x{0b00}\x{0b04}\x{0b0d}-\x{0b0e}\x{0b11}-\x{0b12}\x{0b29}\x{0b31}' .
			'\x{0b34}-\x{0b35}\x{0b3a}-\x{0b3b}\x{0b44}-\x{0b46}\x{0b49}-\x{0b4a}\x{0b4e}-\x{0b55}\x{0b58}-\x{0b5b}\x{0b5e}' .
			'\x{0b62}-\x{0b65}\x{0b70}-\x{0b81}\x{0b84}\x{0b8b}-\x{0b8d}\x{0b91}\x{0b96}-\x{0b98}\x{0b9b}\x{0b9d}' .
			'\x{0ba0}-\x{0ba2}\x{0ba5}-\x{0ba7}\x{0bab}-\x{0bad}\x{0bb6}\x{0bba}-\x{0bbd}\x{0bc3}-\x{0bc5}\x{0bc9}' .
			'\x{0bce}-\x{0bd6}\x{0bd8}-\x{0be6}\x{0bf0}-\x{0c00}\x{0c04}\x{0c0d}\x{0c11}\x{0c29}\x{0c34}\x{0c3a}-\x{0c3d}' .
			'\x{0c45}\x{0c49}\x{0c4e}-\x{0c54}\x{0c57}-\x{0c5f}\x{0c62}-\x{0c65}\x{0c70}-\x{0c81}\x{0c84}\x{0c8d}\x{0c91}' .
			'\x{0ca9}\x{0cb4}\x{0cba}-\x{0cbd}\x{0cc5}\x{0cc9}\x{0cce}-\x{0cd4}\x{0cd7}-\x{0cdd}\x{0cdf}\x{0ce2}-\x{0ce5}' .
			'\x{0cf0}-\x{0d01}\x{0d04}\x{0d0d}\x{0d11}\x{0d29}\x{0d3a}-\x{0d3d}\x{0d44}-\x{0d45}\x{0d49}\x{0d4e}-\x{0d56}' .
			'\x{0d58}-\x{0d5f}\x{0d62}-\x{0d65}\x{0d70}-\x{0e00}\x{0e2f}\x{0e3b}-\x{0e3f}\x{0e4f}\x{0e5a}-\x{0e80}\x{0e83}' .
			'\x{0e85}-\x{0e86}\x{0e89}\x{0e8b}-\x{0e8c}\x{0e8e}-\x{0e93}\x{0e98}\x{0ea0}\x{0ea4}\x{0ea6}\x{0ea8}-\x{0ea9}' .
			'\x{0eac}\x{0eaf}\x{0eba}\x{0ebe}-\x{0ebf}\x{0ec5}\x{0ec7}\x{0ece}-\x{0ecf}\x{0eda}-\x{0f17}\x{0f1a}-\x{0f1f}' .
			'\x{0f2a}-\x{0f34}\x{0f36}\x{0f38}\x{0f3a}-\x{0f3d}\x{0f48}\x{0f6a}-\x{0f70}\x{0f85}\x{0f8c}-\x{0f8f}\x{0f96}' .
			'\x{0f98}\x{0fae}-\x{0fb0}\x{0fb8}\x{0fba}-\x{109f}\x{10c6}-\x{10cf}\x{10f7}-\x{10ff}\x{1101}\x{1104}\x{1108}' .
			'\x{110a}\x{110d}\x{1113}-\x{113b}\x{113d}\x{113f}\x{1141}-\x{114b}\x{114d}\x{114f}\x{1151}-\x{1153}' .
			'\x{1156}-\x{1158}\x{115a}-\x{115e}\x{1162}\x{1164}\x{1166}\x{1168}\x{116a}-\x{116c}\x{116f}-\x{1171}\x{1174}' .
			'\x{1176}-\x{119d}\x{119f}-\x{11a7}\x{11a9}-\x{11aa}\x{11ac}-\x{11ad}\x{11b0}-\x{11b6}\x{11b9}\x{11bb}' .
			'\x{11c3}-\x{11ea}\x{11ec}-\x{11ef}\x{11f1}-\x{11f8}\x{11fa}-\x{1dff}\x{1e9c}-\x{1e9f}\x{1efa}-\x{1eff}' .
			'\x{1f16}-\x{1f17}\x{1f1e}-\x{1f1f}\x{1f46}-\x{1f47}\x{1f4e}-\x{1f4f}\x{1f58}\x{1f5a}\x{1f5c}\x{1f5e}' .
			'\x{1f7e}-\x{1f7f}\x{1fb5}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fc5}\x{1fcd}-\x{1fcf}\x{1fd4}-\x{1fd5}\x{1fdc}-\x{1fdf}' .
			'\x{1fed}-\x{1ff1}\x{1ff5}\x{1ffd}-\x{20cf}\x{20dd}-\x{20e0}\x{20e2}-\x{2125}\x{2127}-\x{2129}' .
			'\x{212c}-\x{212d}\x{212f}-\x{217f}\x{2183}-\x{3004}\x{3006}\x{3008}-\x{3020}\x{3030}\x{3036}-\x{3040}' .
			'\x{3095}-\x{3098}\x{309b}-\x{309c}\x{309f}-\x{30a0}\x{30fb}\x{30ff}-\x{3104}\x{312d}-\x{4dff}' .
			'\x{9fa6}-\x{abff}\x{d7a4}-\x{d7ff}\x{e000}-\x{ffff}';
		}
		return false;
	}
}
?>