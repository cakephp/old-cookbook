<?php
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Cake Foundation
 *
 * Copyright (c) 2006,	Cake Software Foundation, Inc.
 * 							1785 E. Sahara Avenue, Suite 490-204
 * 							Las Vegas, Nevada 89104
 *
 * Licensed under the CAKE SOFTWARE FOUNDATION LICENSE(CSFL) version 1.0
 * Redistributions of files must retain the above copyright notice.
 * You may not use this file except in compliance with the License.
 *
 * You may obtain a copy of the License at:
 * License page: http://www.cakefoundation.org/licenses/csfl/
 * Copyright page: http://www.cakefoundation.org/copyright/
 *
 * @filesource
 * @copyright     Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       csf
 * @subpackage    csf.plugins.users.models
 * @since         CSF v 1.0.0.0
 * @license       http://www.cakefoundation.org/licenses/csfl/  The CSFL License
 */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package       csf
 * @subpackage    csf.plugins.users.models
 * @since         CSF v 1.0.0.0
 *
 */
class User extends UsersAppModel {

	var $name = 'User';

	var $displayField = 'username';

	var $belongsTo = array('Group' => array('className' => 'Users.Group'), 'Level' => array('className' => 'Users.Level'));

	var $hasOne = array('Users.Profile');

	var $hasMany = array('Revision', 'Comment');

	function beforeValidate() {
		if(!$this->id) {
			$this->recursive = -1;
			if ($this->findCount(array('User.username' => $this->data['User']['username'])) > 0) {
				$this->invalidate('username_unique');
			}
			$this->recursive = -1;
			if ($this->findCount(array('User.email' => $this->data['User']['email'])) > 0) {
				$this->invalidate('email_unique');
			}
		}
		return true;
	}

	function display($id = null) {
		if (!$id) {
			if (!$this->id) {
				return false;
			}
			$id = $this->id;
		}
		return current($this->find('list', array('conditions' => array($this->alias . '.id' => $id))));
	}

	function token($data = array(), $params = array()) {
		$conditions = array('id' => $this->id);
		$fields = '*';
		$recursive = -1;
		extract($params);
		if (!$data) {
			$data = $this->find('first', compact('conditions', 'fields', 'recursive'));
		}
		$return = Security::hash(serialize($data), null, true);
		if ($length) {
			while(strlen($return) < $length) {
				$return .= Security::hash($return, null, true);
			}
			$return = substr($return, 0, $length);
		}
		return $return;
	}


	function beforeSave() {
		if(!$this->id) {
			$this->data['User']['email_token'] = $this->__generateToken();
			$this->data['User']['email_token_expires'] = date('Y-m-d H:i:s', time() + (86400 * 2));
		}
		return true;
	}

	function saveTempPassword($email){
		$this->id = $this->field('id', array('User.email' => $email));
		$this->data['User']['temppassword'] = $this->__genpassword();
		$this->data['User']['email_token'] = $this->__generateToken();
		$sixtyMins = time() + 43000;
		$this->data['User']['email_token_expires'] = date('Y-m-d H:i:s', $sixtyMins);
		if($this->save($this->data)){
			return true;
		} else {
			return false;
		}
	}

	function validateToken($id = null, $reset = false) {
		$this->recursive = '-1';
		$match = $this->find(array('User.email_token' => $id), 'id, username, email, temppassword, email_token_expires');
		if(!empty($match)){
			$expires = strtotime($match['User']['email_token_expires']);
			if($expires > time()){
				$data['User']['id'] = $match['User']['id'];
				$data['User']['username'] = $match['User']['username'];
				$data['User']['email'] = $match['User']['email'];
				$data['User']['email_authenticated'] = '1';

				if($reset === true) {
					$data['User']['psword'] = $match['User']['temppassword'];
					$data['User']['temppassword'] = null;
				}
				$data['User']['email_token'] = null;
				$data['User']['email_token_expires'] = null;
			}
			return $data;
		}
		return false;
	}

	/* Private Methods */
	function __password($compareTo, $password, $check = true){
		$security = Security::getInstance();
		$salt = Configure::read('Security.salt');
		if($check === true){
			if($security->hash($salt . $password) === $compareTo){
				return true;
			} else {
				return false;
			}
		} else {
			$genPassword = $security->hash($salt . $password);
			return $genPassword;
		}
	}

	function __genpassword($length = 10) {
		srand((double)microtime()*1000000);
		$password = '';
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
							"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		for($i = 0; $i < $length; $i++){
			$password .= $cons[mt_rand(0, 31)] . $vowels[mt_rand(0, 4)];
		}
		return substr($password, 0, $length);
	}

	function __generateToken() {
		$possible = "0123456789abcdfghijklmnopqrstvwxyz";
		$id = "";
		$i = 0;
		while ($i < 20) {
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			if (!strstr($id, $char)) {
				$id .= $char;
				$i++;
			}
		}
		return $id;
	}
}