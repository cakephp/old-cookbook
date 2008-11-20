<?php
/* SVN FILE: $Id: comment_fixture.php 693 2008-11-05 13:01:32Z AD7six $ */
/**
 * Short description for comment_fixture.php
 *
 * Long description for comment_fixture.php
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
 * @version       $Revision: 693 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 14:01:32 +0100 (Wed, 05 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CommentFixture class
 *
 * @package       cookbook
 * @subpackage    cookbook.tests.fixtures
 */
class CommentFixture extends CakeTestFixture {
	/**
	 * name property
	 *
	 * @var string 'Comment'
	 * @access public
	 */
	var $name = 'Comment';
	/**
	 * fields property
	 *
	 * @var array
	 * @access public
	 */
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'node_id' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'revision_id' => array('type'=>'integer', 'null' => true, 'default' => '0', 'length' => 10),
		'lang' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 2),
		'title' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 150),
		'author' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'email' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'url' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'body' => array('type'=>'text', 'null' => true, 'default' => NULL),
		'published' => array('type'=>'boolean', 'null' => false, 'default' => '1'),
		'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	/**
	 * records property
	 *
	 * @var array
	 * @access public
	 */
	var $records = array(
		array('id' => '1', 'node_id' => '1', 'user_id' => '1', 'revision_id' => '1', 'lang' => 'en', 'title' => 'Title',
		'body' => 'Body', 'published' => '1', ),
	);
}
?>