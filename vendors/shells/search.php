<?php
/**
 * Short description for search.php
 *
 * Long description for search.php
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
 * SearchShell class
 *
 * @uses          Shell
 * @package       cookbook
 * @subpackage    cookbook.vendors.shells
 */
class SearchShell extends Shell {
/**
 * main method
 *
 * @return void
 * @access public
 */
	function main() {
		$this->help();
	}
/**
 * startup method
 *
 * @return void
 * @access public
 */
	function startup() {
                if (!function_exists('iconv')) {
                        function iconv($inCharset, $outCharset, $string) {
                                return $string;
                        }
                        function iconv_strlen($string) {
                                //return strlen(utf8_decode($string));
                                return mb_strlen($string, 'UTF-8');
                        }
                        function iconv_substr($string, $offset, $length) {
                                return mb_substr($string, $start, $length, 'UTF-8');
                        }
                }
		if(function_exists('ini_set')){
			ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP. 'vendors');
		}
		App::import('Vendor', 'Zend/Search/Lucene', array('file' => 'Zend/Search/Lucene.php'));
		config('lucene');
		if(class_exists('Lucene_Config')) {
			$this->config = &new Lucene_Config;
		}
		$config = 'default';
		if(!empty($this->params['config'])){
			$config = $this->params['config'];
		}
		$this->settings = $this->config->{$config};
		$this->index_file = TMP.$this->settings['index_file'];
		unset($this->settings['index_file']);

	}
/**
 * help method
 *
 * @return void
 * @access public
 */
	function help() {
		$this->out("The Search Shell gives the ability to build and search the index.");
		$this->hr();
		$this->out("Usage: cake schema <command> <arg1> <arg2>...");
		$this->hr();
		$this->out('Params:');
		$this->out("\n\t-connection <config>\n\t\tset db config <config>. uses 'default' if none is specified");
		$this->out('Commands:');
		$this->out("\n\tsearch help\n\t\tshows this help message.");
		$this->out("\n\tsearch build_index\n\t\tinitilize the index.");
		$this->out("\n\tsearch query\n\t\tquery the index.");
		$this->out("\n\tsearch optimize\n\t\toptimizes the index.");
		$this->out("");
		$this->stop();
	}
/**
 * stop method
 *
 * @return void
 * @access public
 */
	function stop(){
		return true;
	}

/**
 * rebuild the index
 *
 * @access public
 * @return void
 */
	function build_index(){
		$index = $this->__open(true);
		// uncomment to get faster indexing time (uses much ram and cpu)
		$index->setMergeFactor(2000);
		$index->setMaxBufferedDocs(500);
		$this->out('Building search index...');
		$this->out('This may take a while depending on the db size.');
		$start = time();
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
			$this->log($model->name.' find time: '.(time()-$start));
			$start = time();

			$count = count($results);
			$i =1;
			foreach($results as $result){
				$this->out($model->name.' :'.printf("%.1f", $i/$count * 100));
				$i++;

                                $doc = new Zend_Search_Lucene_Document();

				// add the model field
				$doc->addField(Zend_Search_Lucene_Field::Keyword('cake_model', $model->name, 'utf-8'));
				foreach($model_options['fields'] as $field_name => $options){
                                    if(!empty($options['prepare']) && function_exists($options['prepare'])){
                                        $result[$model->name][$field_name] = call_user_func($options['prepare'], $result[$model->name][$field_name]);
                                    }
					$alias = !empty($options['alias']) ? $options['alias'] : $field_name;
					$doc->addField(Zend_Search_Lucene_Field::$options['type']($alias, $result[$model->name][$field_name], 'utf-8'));
				}
				$index->addDocument($doc);
			}
			$this->log($model->name.' adding time: '.(time()-$start));
			$start = time();
		}
		$this->optimize($index);
		$index->commit();
		$this->log('Optimize+commit time: '.(time()-$start));
	}
/**
 * query function
 *
 * @access public
 * @return void
 */
	function query(){
		$query = implode(' ',$this->args);
		$index = $this->__open(false);
		$hits = $index->find($query);
		$this->out('Query results:');
		$this->out("Score\tModel\tID\tTitle");
		foreach ($hits as $hit) {
			$this->out(printf("%.2f",$hit->score)."\t".$hit->cake_model." \t".$hit->cake_id."\t".$hit->title);
			$this->out('id '.$hit->id);
		}
	}


/**
 * optimizes the index
 * TODO: add some loggin/printing
 *
 * @param mixed $index
 * @access public
 * @return void
 */
	function optimize($index = null){
		if(empty($index)){
			$index = $this->__open(false);
		}
		$this->out("Optimizing index...");
		$index->optimize();
		$this->out("Optimized index.");
	}
/**
 * opens or creates an index
 *
 * @param bool $create
 * @access private
 * @return void
 */
	function __open($create = false){
		try {
			return Zend_Search_Lucene::open($this->index_file);
		} catch (Zend_Search_Lucene_Exception $e) {
			if($create){
				try {
					return Zend_Search_Lucene::create($this->index_file);
				} catch(Zend_Search_Lucene_Exception $e) {
					echo "Unable to create the index: ". $e->getMessage();
				}
			} else {
				echo "Unable to open the index: ". $e->getMessage();
			}
		}
		return false;
	}
}
?>