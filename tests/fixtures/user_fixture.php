<?php
/**
 * Short description for user_fixture.php
 *
 * Long description for user_fixture.php
 *
 * PHP versions 4 and 5
 *
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cookbook
 * @subpackage    cookbook.tests.fixtures
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * UserFixture class
 *
 * @package       cookbook
 * @subpackage    cookbook.tests.fixtures
 */
class UserFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string 'User'
 * @access public
 */
	var $name = 'User';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'group_id' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'level_id' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'realname' => array('type'=>'string', 'null' => false),
		'username' => array('type'=>'string', 'null' => false, 'key' => 'unique'),
		'email' => array('type'=>'string', 'null' => false, 'key' => 'unique'),
		'psword' => array('type'=>'string', 'null' => false),
		'temppassword' => array('type'=>'string', 'null' => false),
		'tos' => array('type'=>'boolean', 'null' => false, 'default' => '0'),
		'mail_comments' => array('type'=>'boolean', 'null' => false, 'default' => '1'),
		'email_authenticated' => array('type'=>'boolean', 'null' => true, 'default' => NULL),
		'email_token' => array('type'=>'string', 'null' => false, 'length' => 45),
		'email_token_expires' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'display_name' => array('type'=>'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'USERNAME_UNIQUE_INDEX' => array('column' => 'username', 'unique' => 1),
			'EMAIL_UNIQUE_INDEX' => array('column' => 'email', 'unique' => 1)
		));
/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array(
			'id' => '1',
			'group_id' => '1',
			'level_id' => '1',
			'realname' => 'Test User',
			'username' => 'test',
			'email' => 'test@example.com',
		),
	);
}
?>