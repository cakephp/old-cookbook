<?php
/* SVN FILE: $Id: app_helper.php 689 2008-11-05 10:30:07Z AD7six $ */
/**
 * Short description for file.
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 689 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 11:30:07 +0100 (Wed, 05 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_helper.php
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake
 */
class AppHelper extends Helper {
/**
 * url function
 *
 * @param mixed $url
 * @param bool $full
 * @access public
 * @return void
 */
	function url($url = null, $full = false) {
		if (is_array($url)) {
			if (isset($url['lang'])) {
				if ($url['lang'] == 'en') {
					unset ($url['lang']);
				}
			} elseif (!empty($this->params['lang']) && !in_array($this->params['lang'], array(null, 'en'))) {
				$url['lang'] = $this->params['lang'];
			}
		}
		
		$return = Router::url($url, $full);
		
		$id = Configure::read('Site.homeNode');
		if (strpos($return, 'view/' . $id . '/')) {
			$return = $this->webroot;		
			if (isset($url['lang'])) {
				$return .= $url['lang'] . '/';
			}		
		}
		
		if ($prefix = Configure::read('Content.rewriteBase')) {
			$return = r($this->base, $this->base . '/' . $prefix, $return);
		}
		return $return;
	}
}
?>