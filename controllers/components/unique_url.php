<?php
/* SVN FILE: $Id$ */
/**
 * Short description for unique_url.php
 *
 * Long description for unique_url.php
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
 * @subpackage    cookbook.controllers.components
 * @since         v 1.0
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class UniqueUrlComponent extends Object {
/**
 * here property
 *
 * @var mixed null
 * @access public
 */
	var $here = null;
/**
 * initialize method
 *
 * @param mixed $controller
 * @return void
 * @access public
 */
	function initialize (&$controller) {
		$this->controller =& $controller;
	}
/**
 * Verify that the current url is the right url to view the content
 *
 * Disabled if debug is > 1
 * Doesn't do anything for admin methods or if data has been submitted, otherwise, check slugs
 * and 301 redirect to the correct url if the url doesn't match route definitions
 *
 * @param string $defaultLang
 * @return void
 * @access public
 */
	function check ($defaultLang = 'en') {
		if (isset($this->controller->params['requested']) || isset($this->controller->params['admin']) ||
			$this->controller->data) {
			return;
		}
		$this->here = $here = '/' . trim($this->controller->params['url']['url'], '/');
		$params =& $this->controller->params;
		$defaults = array(
			'controller' => Inflector::underscore($this->controller->name),
			'action' => $this->controller->action,
			'admin' => !empty($this->controller->params['admin']),
			'lang' => $params['lang'],
			'theme' => $params['theme'],
		);
		$url = am($defaults, $this->controller->passedArgs);
		if (isset($url[0]) && $params['url']['ext'] === 'html') {
			$id = Configure::read('Site.homeNode');
			if (empty($url['admin']) && $url['controller'] === 'nodes' && $url['action'] === 'view' && $url[0] == $id) {
				$url = am($defaults, array('action' => 'index'));
			}
		}
		if (isset($url[0])) {
			$conditions = array();
			$conditions['Node.id'] = $url[0];
			$fields = array ('Node.id', 'Revision.slug');
			$recursive = 0;
			$result = $this->controller->Node->find('first', compact('conditions', 'fields', 'recursive'));
			if (!$result) {
				$this->controller->redirect($this->controller->Session->read('referer'), null, true);
			}
			$url[1] = $result['Revision']['slug'];
		}
		$normalized = Router::normalize($url);
		if ($normalized !== $here) {
			return $this->controller->redirect($normalized, 301);
		}
	}
}