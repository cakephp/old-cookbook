<?php
/**
 * Short description for file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cookbook
 * @subpackage    cookbook.controllers
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * RedirectController class used to process requests for pages that come from manual.cakephp.org and send
 * them to the right page on book.cakephp.org
 *
 * @uses          AppController
 * @package       cookbook
 * @subpackage    cookbook.controllers
 */
class RedirectController extends AppController {
/**
 * name variable
 *
 * @var string
 * @access public
 */
	var $name = 'Redirect';
/**
 * uses variable
 *
 * @var array
 * @access public
 */
	var $uses = array('Node');
/**
 * beforeFilter function. Allow access to the process function, don't call the parent and set the language
 * to english (so that the subsequent redirect has it available)
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		$this->Auth->allow('process');
	}
/**
 * process function. Based on the passed params, send the user to the equivalent page.
 *  example, user enteres /chapter/intro, the later redirect should send them to /view/307/slug-for-307
 *
 * @param string $section
 * @param string $name
 * @access public
 */
	function process ($section = null, $name = null) {
		$node = null;
		if ($section == 'chapter') {
			switch ($name) {
				case 'intro':
					$node = 307;
					break;
				case 'basic_concepts':
					$node = 309;
					break;
				case 'installing':
					$node = 308;
					break;
				case 'configuration':
					$node = 310;
					break;
				case 'scaffolding':
					$node = 311;
					break;
				case 'models':
					$node = 312;
					break;
				case 'controllers':
					$node = 313;
					break;
				case 'views':
					$node = 314;
					break;
				case 'components':
					$node = 315;
					break;
				case 'helpers':
					$node = 316;
					break;
				case 'constants':
					$node = 317;
					break;
				case 'validation':
					$node = 318;
					break;
				case 'plugins':
					$node = 319;
					break;
				case 'acl':
					$node = 320;
					break;
				case 'sanitize':
					$node = 321;
					break;
				case 'session':
					$node = 322;
					break;
				case 'request_handler':
					$node = 323;
					break;
				case 'security':
					$node = 324;
					break;
				case 'view_cache':
					$node = 325;
					break;
			}
		} elseif ($section == 'appendix') {
			switch ($name) {
				case 'blog_tutorial':
					$node = 326;
					break;
				case 'simple_user_auth':
					$node = 327;
					break;
				case 'conventions':
					$node = 328;
					break;
			}
		}
		if ($node) {
			$slug = $this->Node->Revision->field('slug', array('node_id' => $node));
			$this->redirect(array('controller' => 'nodes', 'action' => 'view', $node, $slug), 301);
		}
		// Optional "didn't find what you were looking for" message
		//$this->Session->setFlash('Ooops couldn\'t find the corresponding section - however you\'ll find what you are looking for somewhere in the Cookbook');
		$this->redirect('/');
	}
}
?>