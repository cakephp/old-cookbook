<?php
/* SVN FILE: $Id: searchable.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for searchable.php
 *
 * Long description for searchable.php
 *
 * PHP 5
 *
 * Copyright (c) 2008, Marcin Domanski
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Marcin Domanski
 * @link          www.kabturek.info
 * @package       
 * @subpackage    projects.cookbook.models.behaviors
 * @since         v 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * SearchableBehavior class
 *
 * @uses          ModelBehavior
 * @package       
 * @subpackage    searchable.models.behaviors.searchable
 */
class SearchableBehavior extends ModelBehavior {
/**
 * config variable
 *
 * @var mixed
 * @access public
 */
	var $config = null;
/**
 * Index variable
 *
 * @var mixed
 * @access public
 */
	var $Index = null;
/**
 * index_file variable
 *
 * @var mixed
 * @access public
 */
	var $index_file;
/**
 * terms variable
 *
 * @var array
 * @access public
 */
	var $terms = array();
/**
 * hits_count variable
 *
 * @var int
 * @access public
 */
	var $hits_count = 0;
/**
 * setup function
 *
 * @param mixed $Model
 * @param array $settings
 * @access public
 * @return void
 */
	function setup(&$Model, $settings = array()) {
		if (!function_exists('iconv')) {
			function iconv($inCharset, $outCharset, $string) {
				return $string;
			}
			function iconv_strlen($string) {
				//return strlen(utf8_decode($string));
				return mb_strlen($string, 'UTF-8');
			}
			function iconv_substr($string, $offset, $length) {
				return mb_substr($string, $offset, $length, 'UTF-8');
			}
		}
		if(function_exists('ini_set')){
			ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP. 'vendors');
		}
		// import the Zend_Search_Lucene library
		App::import('Vendor', 'Zend/Search/Lucene', array('file' => 'Zend/Search/Lucene.php'));
		// load app/config/lucene.php
		config('lucene');
		if(class_exists('Lucene_Config')) {
			$this->config = &new Lucene_Config;
		}
		$config = 'default';
		if(!empty($settings['config'])){
			$config = $settings['config'];
		}
		$this->settings = $this->config->{$config};
		$this->index_file = TMP.$this->settings['index_file'];
		unset($this->settings['index_file']);
	}
/**
 * afterSave callback
 *
 * Check whether the current rows data should be in the index, add if it should, call add_to_index
 *
 * @param mixed $Model
 * @param mixed $created
 * @access public
 * @return void
 */
	function afterSave(&$Model, $created){
		if (!$created) {
			$this->delete_from_index($Model, $Model->id);
		}
		if (isset($this->settings[$Model->alias]['find_options']['conditions'])) {
			$conditions = $this->settings[$Model->alias]['find_options']['conditions'];
			$conditions[$Model->alias . '.' . $Model->primaryKey] = $Model->id;
			if (!$Model->find('count', compact('conditions'))) {
				return;
			}
		}
		$this->add_to_index($Model, $Model->id);
	}
/**
 * afterDelete callback
 *
 * @param mixed $Model
 * @access public
 * @return void
 */
	function afterDelete(&$Model){
		$this->delete_from_index($Model, $Model->id);
	}
/**
 * open_index function
 * opens the index for manipulation/searching
 *
 * @access public
 * @return void
 */
	function open_index(&$Model) {
		if (empty($this->Index)) {
			try {
				$this->Index = Zend_Search_Lucene::open($this->index_file);
				return $this->Index;
			} catch (Zend_Search_Lucene_Exception $e) {
				$this->log("Unable to open the index: ". $e->getMessage(), 'searchable');
				$this->create_index($Model);
			}
		} else {
			return $this->Index;
		}
		return false;
	}
/**
 * create_index method
 *
 * @param mixed $Model
 * @return void
 * @access public
 */
	function create_index($Model){
		try {
			$this->Index = Zend_Search_Lucene::create($this->index_file);
			return $this->Index;
		} catch(Zend_Search_Lucene_Exception $e) {
			$this->log("Unable to create the index: ". $e->getMessage(), 'searchable');
			return false;
		}
	}

/**
 * search function
 * searches the index
 *
 * @param mixed $Model
 * @param mixed $query
 * @param int $limit
 * @param int $page
 * @access public
 * @return void
 */
	function search(&$Model, $query, $limit = 20, $page = 1) {
		// open the index
		if(!$this->open_index($Model)){
			return false;
		}
		try {
			// set the default encoding
			Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
			// zend search results limiting (We will use the LimitIterator)
			// we can use it for some maximum value like 1000 if its likely that there could be more results
			Zend_Search_Lucene::setResultSetLimit(1000);
			// set the parser default operator to AND
			Zend_Search_Lucene_Search_QueryParser::setDefaultOperator(Zend_Search_Lucene_Search_QueryParser::B_AND);
			// utf-8 num analyzer
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(
				new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
			// parse the query
			$Query = Zend_Search_Lucene_Search_QueryParser::parse($query);
			$Terms = $Query->getQueryTerms();
			foreach($Terms as $Term){
				$this->terms[] = $Term->text;
			}
			// do the search
			$Hits = new ArrayObject($this->Index->find($Query));
		} catch (Zend_Search_Lucene_Exception $e) {
			$this->log("Zend_Search_Lucene error: ". $e->getMessage(), 'searchable');
		}
		$this->hits_count = count($Hits);

		if (!count($Hits)) {
			return null;
		}
		$Hits = new LimitIterator($Hits->getIterator(), ($page - 1) * $limit, $limit);
		$results = array();
		foreach ($Hits as $Hit) {
			$Document = $Hit->getDocument();
                        $fields = $Document->getFieldNames();
			$result = array();
			foreach($fields as $field){
				$result['Result'][$field] = $Document->{$field};
			}
			$results[] = $result;
                }
		return $results;
	}
/**
 * terms function
 * return the search terms
 *
 * @access public
 * @return void
 */
	function terms(){
		return $this->terms;
	}
/**
 * hits_count function
 * return the number of results
 *
 * @access public
 * @return void
 */
	function hits_count(){
		return $this->hits_count;
	}
/**
 * deletes an model row from the index
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function delete_from_index(&$Model, $id){
		// open the index
		if(!$this->open_index($Model)){
			return false;
		}
		try{
			$query = new Zend_Search_Lucene_Search_Query_MultiTerm();
			$query->addTerm(new Zend_Search_Lucene_Index_Term($id, 'cake_id'), true);
			$query->addTerm(new Zend_Search_Lucene_Index_Term($Model->alias, 'cake_model'), true);
			$Hits = $this->Index->find($query);
			foreach($Hits as $Hit){
				$this->Index->delete($Hit->id);
				if (Configure::read()) {
					$this->log('deleted index id:'.$id, 'searchable');
				}
				if(count($Hits)) {
					return true;
				} else {
					return false;
				}
			}
		} catch (Zend_Search_Lucene_Exception $e) {
			$this->log('Lucene exception:'. $e->getMessage(), 'searchable');
		}

	}
/**
 * add_to_index function
 *
 * deletes any exising index entries, and adds a document to the index
 *
 * @TODO Is this working properly
 * @param mixed $Model
 * @param mixed $id
 * @access public
 * @return void
 */
	function add_to_index(&$Model, $id){
		if(!empty($this->settings[$Model->alias])){
			if(!$this->open_index($Model)){
				return false;
			}
			try{
				$query = new Zend_Search_Lucene_Search_Query_MultiTerm();
				$query->addTerm(new Zend_Search_Lucene_Index_Term($id, 'cake_id'), true);
				$query->addTerm(new Zend_Search_Lucene_Index_Term($Model->alias, 'cake_model'), true);
				$Hits = $this->Index->find($query);
				// TODO there are never any hits - why
				if(count($Hits)){
					$this->delete_from_index($Model, $id);
				}

				if(method_exists($Model,'find_index')){
					$result = $Model->find_index('first', array('conditions' => array($Model->alias.'.id' => $id)));
				} else {
					$result = $Model->find('first',array('conditions' => array($Model->alias.'.id' => $id)));
				}
				if(!empty($result)){
					$doc = new Zend_Search_Lucene_Document();

					// add the model field
					$doc->addField(Zend_Search_Lucene_Field::Keyword('cake_model', $Model->alias, 'utf-8'));
					foreach($this->settings[$Model->alias]['fields'] as $field_name => $options){
						if(!empty($options['prepare']) && function_exists($options['prepare'])){
							$result[$Model->alias][$field_name] = call_user_func($options['prepare'], $result[$Model->alias][$field_name]);
						}
						$alias = !empty($options['alias']) ? $options['alias'] : $field_name;
						$doc->addField(Zend_Search_Lucene_Field::$options['type']($alias, $result[$Model->alias][$field_name], 'utf-8'));
					}
					$this->Index->addDocument($doc);
					$this->Index->commit();
					if (Configure::read()) {
						$Hits = $this->Index->find($query);
						// TODO why isn't it possible to find what was just added
						if(!count($Hits)){
							debug ('Tried to add to the search index, no errors but couldnt find what was just added!');
						}
						$this->log('added to index id:'.$id, 'searchable');
					}
				}
			} catch (Zend_Search_Lucene_Exception $e) {
				$this->log('Lucene exception:'. $e->getMessage(), 'searchable');
			}
		}
	}
/**
 * build_index method
 *
 * Rebuild the entire search index.
 *
 * $mergefactor - increase this value for faster indexing. Reduce this value to tax the server less
 * $maxBufferDocs - increase this value for faster indexing. Reduce this value to tax the server less
 *
 * @param mixed $Model
 * @param int $mergeFactor
 * @param int $maxBufferDocs
 * @return void
 * @access public
 */
	function build_index($Model, $mergeFactor = 2000, $maxBufferDocs = 500){
		if (Configure::read()) {
			$this->log('Starting to build index.', 'searchable');
			$start = getMicrotime();
		}
		if(!$this->create_index($Model)){
			return false;
		}
		$this->Index->setMergeFactor($mergeFactor);
		$this->Index->setMaxBufferedDocs($maxBufferDocs);
		foreach($this->settings as $model => $model_options){
			App::import('Model', $model);
			$model = new $model();
			if(empty($model_options['find_options'])) {
				$model_options['find_options'] = array();
			}

			if(method_exists($model,'find_index')){
				$results = $model->find_index('all', $model_options['find_options']);
			} else {
				$results = $model->find('all', $model_options['find_options']);
			}
			if (Configure::read()) {
				$time = round(getMicrotime() - $start, 1);
				$count = count($results);
				$this->log('Found ' . $count . ' ' . $model->name . ' results ' . $time . 's', 'searchable');
			}
			foreach($results as $i => $result) {
				if (Configure::read()) {
					$time = round(getMicrotime() - $start, 1);
					$this->log('Processing ' . $model->name . ' result ' . ($i + 1) . '/' .	$count .
						' (id:' . $result[$model->alias][$model->primaryKey] . ', ' .
						$result[$model->alias][$model->displayField] . ') ' . $time .
						's', 'searchable');
				}
				$doc = new Zend_Search_Lucene_Document();

				// add the model field
				$doc->addField(Zend_Search_Lucene_Field::Keyword('cake_model', $model->name, 'utf-8'));
				foreach($model_options['fields'] as $field_name => $options){
					if(!empty($options['prepare']) && function_exists($options['prepare'])){
						$result[$model->name][$field_name] =
							call_user_func($options['prepare'], $result[$model->name][$field_name]);
					}
					$alias = !empty($options['alias']) ? $options['alias'] : $field_name;
					$doc->addField(Zend_Search_Lucene_Field::$options['type']($alias,
						$result[$model->name][$field_name], 'utf-8'));
				}
				$this->Index->addDocument($doc);
			}
			if (Configure::read()) {
				$time = round(getMicrotime() - $start, 1);
				$this->log('Finished processing ' . Inflector::pluralize($model->name) . ' ' .$time . 's',
					'searchable');
			}
		}
		$this->Index->commit();
	}
}
?>