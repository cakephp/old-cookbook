<?php
/* SVN FILE: $Id: revision_fixture.php 693 2008-11-05 13:01:32Z AD7six $ */
/**
 * Short description for revision_fixture.php
 *
 * Long description for revision_fixture.php
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
 * RevisionFixture class
 *
 * @package       cookbook
 * @subpackage    cookbook.tests.fixtures
 */
class RevisionFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string 'Revision'
 * @access public
 */
	var $name = 'Revision';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'node_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'under_node_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'after_node_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'status' => array('type'=>'string', 'null' => false, 'default' => 'pending', 'length' => 30),
			'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10),
			'lang' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 3),
			'slug' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'title' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 200),
			'content' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'type' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'reason' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 300),
			'flags' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array(
				'PRIMARY' => array('column' => 'id', 'unique' => 1),
				'node_id' => array('column' => array('node_id', 'lang', 'status'), 'unique' => 0)
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
			'node_id' => '1',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Your-Collections',
			'title' => 'Your Collections',
			'content' => '<p>Edit the collection index to change this text</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:17',
			'modified' => '2008-11-05 12:49:17',
		),
		array(
			'id' => '2',
			'node_id' => '2',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Collection-1',
			'title' => 'Collection 1',
			'content' => '<p>a collection of books</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:18',
			'modified' => '2008-11-05 12:49:18',
		),
		array(
			'id' => '3',
			'node_id' => '3',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Book-1',
			'title' => 'Book 1',
			'content' => '<p>a book about... 1</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:18',
			'modified' => '2008-11-05 12:49:18',
		),
		array(
			'id' => '4',
			'node_id' => '4',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-4',
			'title' => 'Section id 4',
			'content' => '<p>Section 4 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:18',
			'modified' => '2008-11-05 12:49:18',
		),
		array(
			'id' => '5',
			'node_id' => '5',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-5',
			'title' => 'Section id 5',
			'content' => '<p>Section 5 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:18',
			'modified' => '2008-11-05 12:49:18',
		),
		array(
			'id' => '6',
			'node_id' => '6',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-6',
			'title' => 'Section id 6',
			'content' => '<p>Section 6 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:18',
			'modified' => '2008-11-05 12:49:18',
		),
		array(
			'id' => '7',
			'node_id' => '7',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-7',
			'title' => 'Section id 7',
			'content' => '<p>Section 7 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:19',
			'modified' => '2008-11-05 12:49:19',
		),
		array(
			'id' => '8',
			'node_id' => '8',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-8',
			'title' => 'Section id 8',
			'content' => '<p>Section 8 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:19',
			'modified' => '2008-11-05 12:49:19',
		),
		array(
			'id' => '9',
			'node_id' => '9',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-9',
			'title' => 'Section id 9',
			'content' => '<p>Section 9 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:19',
			'modified' => '2008-11-05 12:49:19',
		),
		array(
			'id' => '10',
			'node_id' => '10',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-10',
			'title' => 'Section id 10',
			'content' => '<p>Section 10 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:20',
			'modified' => '2008-11-05 12:49:20',
		),
		array(
			'id' => '11',
			'node_id' => '11',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-11',
			'title' => 'Section id 11',
			'content' => '<p>Section 11 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:20',
			'modified' => '2008-11-05 12:49:20',
		),
		array(
			'id' => '12',
			'node_id' => '12',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-12',
			'title' => 'Section id 12',
			'content' => '<p>Section 12 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:21',
			'modified' => '2008-11-05 12:49:21',
		),
		array(
			'id' => '13',
			'node_id' => '13',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-13',
			'title' => 'Section id 13',
			'content' => '<p>Section 13 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:21',
			'modified' => '2008-11-05 12:49:21',
		),
		array(
			'id' => '14',
			'node_id' => '14',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-14',
			'title' => 'Section id 14',
			'content' => '<p>Section 14 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:21',
			'modified' => '2008-11-05 12:49:21',
		),
		array(
			'id' => '15',
			'node_id' => '15',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-15',
			'title' => 'Section id 15',
			'content' => '<p>Section 15 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:21',
			'modified' => '2008-11-05 12:49:21',
		),
		array(
			'id' => '16',
			'node_id' => '16',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-16',
			'title' => 'Section id 16',
			'content' => '<p>Section 16 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:22',
			'modified' => '2008-11-05 12:49:22',
		),
		array(
			'id' => '17',
			'node_id' => '17',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-17',
			'title' => 'Section id 17',
			'content' => '<p>Section 17 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:22',
			'modified' => '2008-11-05 12:49:22',
		),
		array(
			'id' => '18',
			'node_id' => '18',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Book-2',
			'title' => 'Book 2',
			'content' => '<p>a book about... 2</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:22',
			'modified' => '2008-11-05 12:49:22',
		),
		array(
			'id' => '19',
			'node_id' => '19',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-19',
			'title' => 'Section id 19',
			'content' => '<p>Section 19 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:22',
			'modified' => '2008-11-05 12:49:22',
		),
		array(
			'id' => '20',
			'node_id' => '20',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-20',
			'title' => 'Section id 20',
			'content' => '<p>Section 20 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:23',
			'modified' => '2008-11-05 12:49:23',
		),
		array(
			'id' => '21',
			'node_id' => '21',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-21',
			'title' => 'Section id 21',
			'content' => '<p>Section 21 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:23',
			'modified' => '2008-11-05 12:49:23',
		),
		array(
			'id' => '22',
			'node_id' => '22',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-22',
			'title' => 'Section id 22',
			'content' => '<p>Section 22 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:24',
			'modified' => '2008-11-05 12:49:24',
		),
		array(
			'id' => '23',
			'node_id' => '23',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-23',
			'title' => 'Section id 23',
			'content' => '<p>Section 23 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:25',
			'modified' => '2008-11-05 12:49:25',
		),
		array(
			'id' => '24',
			'node_id' => '24',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-24',
			'title' => 'Section id 24',
			'content' => '<p>Section 24 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:25',
			'modified' => '2008-11-05 12:49:25',
		),
		array(
			'id' => '25',
			'node_id' => '25',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-25',
			'title' => 'Section id 25',
			'content' => '<p>Section 25 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:25',
			'modified' => '2008-11-05 12:49:25',
		),
		array(
			'id' => '26',
			'node_id' => '26',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-26',
			'title' => 'Section id 26',
			'content' => '<p>Section 26 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:26',
			'modified' => '2008-11-05 12:49:26',
		),
		array(
			'id' => '27',
			'node_id' => '27',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-27',
			'title' => 'Section id 27',
			'content' => '<p>Section 27 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:26',
			'modified' => '2008-11-05 12:49:26',
		),
		array(
			'id' => '28',
			'node_id' => '28',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-28',
			'title' => 'Section id 28',
			'content' => '<p>Section 28 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:26',
			'modified' => '2008-11-05 12:49:26',
		),
		array(
			'id' => '29',
			'node_id' => '29',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-29',
			'title' => 'Section id 29',
			'content' => '<p>Section 29 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:26',
			'modified' => '2008-11-05 12:49:26',
		),
		array(
			'id' => '30',
			'node_id' => '30',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-30',
			'title' => 'Section id 30',
			'content' => '<p>Section 30 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:27',
			'modified' => '2008-11-05 12:49:27',
		),
		array(
			'id' => '31',
			'node_id' => '31',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-31',
			'title' => 'Section id 31',
			'content' => '<p>Section 31 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:27',
			'modified' => '2008-11-05 12:49:27',
		),
		array(
			'id' => '32',
			'node_id' => '32',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-32',
			'title' => 'Section id 32',
			'content' => '<p>Section 32 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:27',
			'modified' => '2008-11-05 12:49:27',
		),
		array(
			'id' => '33',
			'node_id' => '33',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Collection-2',
			'title' => 'Collection 2',
			'content' => '<p>a collection of books</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:28',
			'modified' => '2008-11-05 12:49:28',
		),
		array(
			'id' => '34',
			'node_id' => '34',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Book-1',
			'title' => 'Book 1',
			'content' => '<p>a book about... 1</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:28',
			'modified' => '2008-11-05 12:49:28',
		),
		array(
			'id' => '35',
			'node_id' => '35',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-35',
			'title' => 'Section id 35',
			'content' => '<p>Section 35 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:28',
			'modified' => '2008-11-05 12:49:28',
		),
		array(
			'id' => '36',
			'node_id' => '36',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-36',
			'title' => 'Section id 36',
			'content' => '<p>Section 36 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:29',
			'modified' => '2008-11-05 12:49:29',
		),
		array(
			'id' => '37',
			'node_id' => '37',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-37',
			'title' => 'Section id 37',
			'content' => '<p>Section 37 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:29',
			'modified' => '2008-11-05 12:49:29',
		),
		array(
			'id' => '38',
			'node_id' => '38',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-38',
			'title' => 'Section id 38',
			'content' => '<p>Section 38 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:29',
			'modified' => '2008-11-05 12:49:29',
		),
		array(
			'id' => '39',
			'node_id' => '39',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-39',
			'title' => 'Section id 39',
			'content' => '<p>Section 39 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:30',
			'modified' => '2008-11-05 12:49:30',
		),
		array(
			'id' => '40',
			'node_id' => '40',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-40',
			'title' => 'Section id 40',
			'content' => '<p>Section 40 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:30',
			'modified' => '2008-11-05 12:49:30',
		),
		array(
			'id' => '41',
			'node_id' => '41',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-41',
			'title' => 'Section id 41',
			'content' => '<p>Section 41 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:30',
			'modified' => '2008-11-05 12:49:30',
		),
		array(
			'id' => '42',
			'node_id' => '42',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-42',
			'title' => 'Section id 42',
			'content' => '<p>Section 42 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:31',
			'modified' => '2008-11-05 12:49:31',
		),
		array(
			'id' => '43',
			'node_id' => '43',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-43',
			'title' => 'Section id 43',
			'content' => '<p>Section 43 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:31',
			'modified' => '2008-11-05 12:49:31',
		),
		array(
			'id' => '44',
			'node_id' => '44',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-44',
			'title' => 'Section id 44',
			'content' => '<p>Section 44 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:31',
			'modified' => '2008-11-05 12:49:31',
		),
		array(
			'id' => '45',
			'node_id' => '45',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-45',
			'title' => 'Section id 45',
			'content' => '<p>Section 45 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:32',
			'modified' => '2008-11-05 12:49:32',
		),
		array(
			'id' => '46',
			'node_id' => '46',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-46',
			'title' => 'Section id 46',
			'content' => '<p>Section 46 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:32',
			'modified' => '2008-11-05 12:49:32',
		),
		array(
			'id' => '47',
			'node_id' => '47',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-47',
			'title' => 'Section id 47',
			'content' => '<p>Section 47 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:32',
			'modified' => '2008-11-05 12:49:32',
		),
		array(
			'id' => '48',
			'node_id' => '48',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-48',
			'title' => 'Section id 48',
			'content' => '<p>Section 48 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:33',
			'modified' => '2008-11-05 12:49:33',
		),
		array(
			'id' => '49',
			'node_id' => '49',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Book-2',
			'title' => 'Book 2',
			'content' => '<p>a book about... 2</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:33',
			'modified' => '2008-11-05 12:49:33',
		),
		array(
			'id' => '50',
			'node_id' => '50',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-50',
			'title' => 'Section id 50',
			'content' => '<p>Section 50 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:33',
			'modified' => '2008-11-05 12:49:33',
		),
		array(
			'id' => '51',
			'node_id' => '51',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-51',
			'title' => 'Section id 51',
			'content' => '<p>Section 51 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:34',
			'modified' => '2008-11-05 12:49:34',
		),
		array(
			'id' => '52',
			'node_id' => '52',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-52',
			'title' => 'Section id 52',
			'content' => '<p>Section 52 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:34',
			'modified' => '2008-11-05 12:49:34',
		),
		array(
			'id' => '53',
			'node_id' => '53',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-53',
			'title' => 'Section id 53',
			'content' => '<p>Section 53 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:34',
			'modified' => '2008-11-05 12:49:34',
		),
		array(
			'id' => '54',
			'node_id' => '54',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-54',
			'title' => 'Section id 54',
			'content' => '<p>Section 54 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:35',
			'modified' => '2008-11-05 12:49:35',
		),
		array(
			'id' => '55',
			'node_id' => '55',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-55',
			'title' => 'Section id 55',
			'content' => '<p>Section 55 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:35',
			'modified' => '2008-11-05 12:49:35',
		),
		array(
			'id' => '56',
			'node_id' => '56',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-56',
			'title' => 'Section id 56',
			'content' => '<p>Section 56 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:35',
			'modified' => '2008-11-05 12:49:35',
		),
		array(
			'id' => '57',
			'node_id' => '57',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-57',
			'title' => 'Section id 57',
			'content' => '<p>Section 57 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:36',
			'modified' => '2008-11-05 12:49:36',
		),
		array(
			'id' => '58',
			'node_id' => '58',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-58',
			'title' => 'Section id 58',
			'content' => '<p>Section 58 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:37',
			'modified' => '2008-11-05 12:49:37',
		),
		array(
			'id' => '59',
			'node_id' => '59',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-59',
			'title' => 'Section id 59',
			'content' => '<p>Section 59 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:37',
			'modified' => '2008-11-05 12:49:37',
		),
		array(
			'id' => '60',
			'node_id' => '60',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-60',
			'title' => 'Section id 60',
			'content' => '<p>Section 60 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:38',
			'modified' => '2008-11-05 12:49:38',
		),
		array(
			'id' => '61',
			'node_id' => '61',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-61',
			'title' => 'Section id 61',
			'content' => '<p>Section 61 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:38',
			'modified' => '2008-11-05 12:49:38',
		),
		array(
			'id' => '62',
			'node_id' => '62',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-62',
			'title' => 'Section id 62',
			'content' => '<p>Section 62 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:39',
			'modified' => '2008-11-05 12:49:39',
		),
		array(
			'id' => '63',
			'node_id' => '63',
			'under_node_id' => '',
			'after_node_id' => '',
			'status' => 'current',
			'user_id' => 1,
			'lang' => 'en',
			'slug' => 'Section-id-63',
			'title' => 'Section id 63',
			'content' => '<p>Section 63 content</p>',
			'type' => '',
			'reason' => '',
			'flags' => '',
			'created' => '2008-11-05 12:49:40',
			'modified' => '2008-11-05 12:49:40',
		),
	);
}
?>