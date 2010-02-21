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
 * @subpackage    csf.plugins.users.controllers
 * @since         CSF v 1.0.0.0
 * @license       http://www.cakefoundation.org/licenses/csfl/  The CSFL License
 */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package       csf
 * @subpackage    csf.plugins.users.controllers
 * @since         CSF v 1.0.0.0
 *
 */
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('Email', 'Cookie');

	var $helpers = array('Html', 'Form');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('logout', 'reset', 'verify');
	}

	function beforeRender() {
		parent::beforeRender();
		$this->set('loginFields', $this->Auth->fields);
	}

	function login() {
		if ($this->data && $this->Auth->user('id')) {
			if (!empty($this->data['User']['remember_me'])) {
				$this->User->id = $this->Auth->user('id');
				$token = $this->User->token(null, array('length' => 100, 'fields' => array(
					$this->Auth->fields['username'], $this->Auth->fields['password']
				)));
				$this->Cookie->write('User.id', $this->User->id, true, '+2 weeks');
				$this->Cookie->write('User.token', $token, true, '+2 weeks');
			}
			$display = $this->User->display();
			$this->Session->setFlash(sprintf(__('Welcome back %1$s.', true), $display));
			return $this->redirect($this->Auth->redirect());
		}
		if ($this->Auth->user('id')) {
			if(!empty($this->data['User']['redirect'])){
				$this->redirect($this->data['User']['redirect'], null, true);
			} else {
				$this->redirect($this->Auth->redirect());
			}
		}
	}

	function logout() {
		if ($this->Auth->user()) {
			$this->Cookie->delete('User');
			$this->Session->destroy();
			$this->Session->setFlash(__('Bye for now!', true));
		}
		$this->redirect($this->Auth->logout());
	}

	function reset() {
		if(!empty($this->data['User']['email'])) {

			$email = $this->data['User']['email'];
			if(empty($email)) {
				$this->Session->setFlash('Please enter an email.');
				$this->set('error',array('email_missing' => true));
				$this->render(); exit;
			}

			$this->User->recursive = -1;
			if ($this->User->findCount(array('User.email' => $email), -1)) {
				$this->User->saveTempPassword($email);
				$user = $this->User->find(array('User.email' => $email));
				$this->Email->to = $user['User']['email'];
				$this->Email->from = Configure::read('Site.email');
				$this->Email->subject = Configure::read('Site.name') . ' new password request' ;
				$this->Email->template = null;

				$content[] = 'A request to reset you password has been submitted.';
				$content[] = 'Please visit the following url to have your temporary password sent';
				$content[] = Router::url('/users/verify/reset/'.$user['User']['email_token'], true);

				if($this->Email->send($content)) {
					$this->Session->setFlash('You should receive an email with further instruction shortly');
					$this->set($user);
					$this->redirect('/', null, true);
				}
			} else {
				$this->User->invalidate('email', 'The email you entered was not found');
			}
		}
	}

	function verify($type = 'email') {
		if(isset($this->passedArgs['1'])){
			$token = $this->passedArgs['1'];
		} else {
			$this->Session->setFlash('Invalid verification token.');
			$this->render(); exit;
		}
		if($type === 'email') {
			$data = $this->User->validateToken($token);
		} elseif($type === 'reset') {
			$data = $this->User->validateToken($token, true);
		} else {
			$this->Session->setFlash('There url you accessed is no longer valid');
			$this->redirect(array('action' => 'login'));
		}

		$password = $data['User']['psword'];
		$data = $this->Auth->hashPasswords($data);

		if($data !== false){
			$email = $data['User']['email'];
			unset($data['User']['email']);
			if($this->User->save($data, false)) {
				if($type === 'reset'){
			        $this->Email->to = $email;
					$this->Email->from = Configure::read('Site.email');
					$this->Email->subject = Configure::read('Site.name') . ' password reset' ;
					$this->Email->template = null;
					$content[] = 'Your password has been reset';
					$content[] = 'Please login using';
					$content[] = 'Username: ' . $data['User']['username'];
					$content[] = 'Password: ' . $password;
					$this->Email->send($content);
					$this->Session->setFlash('Your password was sent to your registered email account');
				} else {
					$this->Session->setFlash('Your Email was validated, Please Login');
					$this->redirect(array('action' => 'login'));
				}
			} else {
				$this->Session->setFlash('There was an error trying to validate, check your email and the url entered');
			}
		} else {
			$this->Session->setFlash('There url you accessed is no longer valid');
			$this->redirect(array('action' => 'login'));
		}
	}
/**
 * Admin methods
 *
 **/

	function admin_login() {
		$this->login();
		$this->render('login');
	}

	function admin_logout() {
		$this->logout();
	}

	function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid User.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		$this->set('user', $this->User->read(null, $id));
	}
}
?>