<?php
/* Cookbook schema generated on: 2008-07-14 11:07:35 : 1216026935*/
class CookbookUuidSchema extends CakeSchema {
	var $name = 'CookbookUuid';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $changes = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'revision_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
			'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
			'author_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
			'status_from' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 10),
			'status_to' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 10),
			'comment' => array('type'=>'string', 'null' => false, 'default' => NULL),
			'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $comments = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'node_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36),
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
	var $nodes = array(
			'id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
			'lft' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'rght' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'parent_id' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 36),
			'status' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'comment_level' => array('type'=>'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'edit_level' => array('type'=>'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'depth' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'sequence' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 20),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'LFT_RGHT' => array('column' => array('lft', 'rght'), 'unique' => 0), 'RGHT_LFT' => array('column' => array('lft', 'rght', 'rght', 'lft'), 'unique' => 0))
		);
	var $revisions = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'node_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'index'),
			'under_node_id' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 36),
			'after_node_id' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 36),
			'status' => array('type'=>'string', 'null' => false, 'default' => 'pending', 'length' => 30),
			'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10),
			'lang' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 3),
			'slug' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 30),
			'title' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 200),
			'content' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'type' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'reason' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 300),
			'flags' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'node_id' => array('column' => array('node_id', 'lang', 'status'), 'unique' => 0))
		);
}
?>