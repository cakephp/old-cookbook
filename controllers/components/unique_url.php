<?php
/* SVN FILE: $Id$ */
/**
 * Short description for unique_url.php
 *
 * Long description for unique_url.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       cakebook
 * @subpackage    cakebook.controllers.components
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
 * Doesn't do anything for admin methods or if data has been submitted, otherwise, check slugs
 * and 301 redirect to the correct url if the url doesn't match route definitions
 *
 * @param string $defaultLang
 * @return void
 * @access public
 */
	function check ($defaultLang = 'en') {
		if (isset($this->controller->params['admin']) ||
			$this->controller->data
		) {
			return;
		}
		$this->here = $here = '/' . trim($this->controller->params['url']['url'], '/');
		$params =& $this->controller->params;
		$pass =& $this->controller->params['pass'];
		if ($params['lang'] === $defaultLang) {
			if ($here === '/') {
				return;
			}
		} elseif ($here === '/' . $params['lang']) {
			return;
		}
		if ($this->controller->action === 'view' && isset($pass[0]) &&
			$pass[0] == Configure::read('Site.homeNode') && $params['url']['ext'] === 'html') {
			if ($params['lang'] === $defaultLang) {
				return $this->controller->redirect('/', 301);
			}
			return $this->controller->redirect('/' . $params['lang'], 301);
		}
		if (in_array($this->controller->action, array('view', 'single_page', 'toc'))) {
			list($pass) = array_chunk($pass, 2);
		}
		//$pass = am($pass, $params['named']);
		if ($params['lang'] !== $defaultLang) {
			$pass['lang'] = $params['lang'];
		}
		if ($params['url']['ext'] !== 'html') {
			$pass['ext'] = $params['url']['ext'];
		}
		if (isset($pass[0])) {
			$this->controller->Node->id = $this->controller->currentNode = $pass[0];
			$conditions = array();
			$conditions['Node.id'] = $pass[0];
			$fields = array ('Node.id', 'Revision.slug');
			$recursive = 0;
			$result = $this->controller->Node->find('first', compact('conditions', 'fields', 'recursive'));
			if (!$result) {
				$this->controller->redirect($this->controller->Session->read('referer'), null, true);
			}
			$pass[1] = $result['Revision']['slug'];
		}
		$normalized = Router::normalize($pass);
		if ($normalized !== $here) {
			return $this->controller->redirect($normalized, 301);
		}
	}
}