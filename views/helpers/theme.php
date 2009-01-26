<?php
/**
 * ThemeHelper class
 *
 * A helper to correct links embedded in contents to stay within the current theme
 *
 * @uses          AppHelper
 * @package       cakebook
 * @subpackage    cakebook.views.helpers
 */
class ThemeHelper extends AppHelper {
/**
 * name property
 *
 * @var string 'Theme'
 * @access public
 */
	var $name = 'Theme';
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html');
/**
 * out method
 *
 * @param mixed $html
 * @return void
 * @access public
 */
	function out($html) {
		// TODO Identify why this is problematic
		//$html = $this->Html->clean($html);
		if ($this->params['theme'] === 'default') {
			return $html;
		}
		$root = trim(Router::url('/'), '/');
		if ($root) {
			$root .= '/';
		}
		$find = '@href="/' . $root . '@';
		$replace = 'href="/' . $root . $this->params['theme'][0] . '/';
		$html = preg_replace($find, $replace, $html);
		return $html;
	}
}