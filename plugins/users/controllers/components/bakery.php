<?php
/*
define('ADMIN', '800');
define('EDITOR', '700');
define('MODERATOR', '600');
define('COMMENTER', '300');
define('READ', '200');
define('NONE', '100');
define('INVALID', '0');
*/
class BakeryComponent extends Object {

	var $components = array('Auth');

	function initialize() {
		$this->Auth->loginAction = '/users/login';
		$this->Auth->logoutRedirect = '/';

		$this->Auth->fields = array('username'=> 'username', 'password'=>'psword');
		$this->Auth->authorize = 'object';
		$this->Auth->object = $this;
		$this->Auth->authenticate = $this;
	}

	function startup(&$controller) {
		if($auth = $this->Auth->user()) {
			if (!empty($auth['User']) && empty($auth['User']['Level'])) {
				$model = $this->Auth->getModel();
				$model->recursive = 0;
				$user = $model->read(array('User.id', 'User.username', 'User.email', 'Level.*', 'Group.*'), $auth['User']['id']);
				$this->Auth->Session->write('Auth.User', $user['User']);
				$this->Auth->Session->write('Auth.User.Level', $user['Level']['value']);
				$this->Auth->Session->write('Auth.User.Group', $user['Group']['name']);
			}
		}
	}

	function hashPasswords($data) {
		if(!empty($data['User']['psword'])) {
			$data['User']['psword'] = Security::hash($data['User']['psword']);
		}
		return $data;
	}

	function isAuthorized($user, $controller, $action) {
		if($this->Auth->user('Level') == ADMIN) {
			return true;
		}

		if($action === 'admin_delete') {
			if($this->Auth->user('Level') >= EDITOR) {
				return true;
			}
			return false;
		}

		if (in_array($controller, array('Nodes', 'Comments'))) {
			if($this->Auth->user('Level') >= COMMENTER) {
				return true;
			}
		}

		return false;
	}

	function beforeRender(&$controller) {
		$user = $this->Auth->user();
		if(empty($user)) {
			$user = array('User' => array('Level' => READ, 'Group' => null));
		}
		$controller->set('auth', $user);
	}
}
?>