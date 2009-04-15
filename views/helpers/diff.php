<?php
/**
 * DiffHelper class
 *
 * This class is a wrapper for PEAR Text_Diff with modified renderers from Horde
 * You need the stable Text_Diff from PEAR and (if you want to use them) two
 * renderers attached with this helper (sidebyside.php and character.php)
 *
 * To use this helper you either have to a) have pear libraries in your path
 * b) can use ini_set to set the path (default is app/vendors/)
 * c) change all requires in Text_Diff ;)
 *
 * @uses          AppHelper
 * @author        Marcin Domanski aka kabturek <blog@kabturek.info>
 * @package       Dressing
 * @subpackage    .dressing.views.helpers
 */
class DiffHelper extends AppHelper {

/**
 * name of the helper
 *
 * @var string
 * @access public
 */
	var $name = 'Diff';

/**
 * what engine should Text_Diff use.
 * Avaible: auto (chooses best), native, xdiff
 *
 * @var string
 * @access public
 */
	var $engine = 'auto';

/**
 * what renderer to use ?
 * for avaible renderers look in Text/Diff/Renderer/*
 * Standard: unified, context, inline
 * Additional: sidebyside
 *
 * @var string
 * @access public
 */
	var $renderer = 'sidebyside';

/**
 * Do you want to use the Character diff renderer additionally to the sidebyside renderer ?
 * sidebyside renderer is the only one supporting the additional renderer
 *
 * @var bool
 * @access public
 */
	var $character_diff = true;

/**
 * If the params are strings on what characters do you want to explode the string?
 * Can be an array if you want to explode on multiple chars
 *
 * @var mixed
 * @access public
 */
	var $explode_on = array("\n");

/**
 * How many context lines do you want to see around the changed line?
 *
 * @var int
 * @access public
 */
	var $context_lines = 4;


/**
 * construct function
 *
 * @param mixed $one
 * @param mixed $two
 * @param mixed $three
 * @access private
 * @return void
 */
	function __construct($one = null, $two = null, $three = null) {
		parent::__construct($one, $two, $three);
		if(function_exists('ini_set')){
			ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP. 'vendors');
		}
		App::import('Vendor', 'Text', array('file' => 'Text/Diff.php'));
		App::import('Vendor', 'Renderer', array('file' => 'Text/Diff/Renderer.php'));
	}
/**
 * compare function
 * Compares two strings/arrays using the specified method and renderer
 *
 * @param mixed $original
 * @param mixed $changed
 * @access public
 * @return void
 */
	function compare($original, $changed){
		if(!is_array($original)){
			$original = $this->__explode($original);
		}
		if(!is_array($changed)){
			$changed = $this->__explode($changed);
		}
		$rendererClassName = 'Text_Diff_Renderer_'.$this->renderer;
		if(!class_exists($rendererClassName)) {
			App::import('Vendor', $this->renderer.'Renderer', array('file' => 'Text/Diff/Renderer/'.$this->renderer.'.php'));
		}
		$renderer = new $rendererClassName(array('context_lines' => $this->context_lines, 'character_diff' =>$this->character_diff));
		$diff = new Text_Diff($this->engine, array($original, $changed));
		return $this->output($renderer->render($diff));
	}

/**
 * explodes the string into an array
 *
 * @param string $text
 * @access private
 * @return void
 */
	function __explode($text){
		$text = preg_replace('/\&gt;\s*\&lt;/m', "&gt;\n&lt;", $text);
		if(is_array($this->explode_on)){
			foreach($this->explode_on as $explode_on){
				$text =  explode($explode_on, $text);
			}
			return $text;
		}
		return explode($this->explode_on, $text);
	}
}
?>