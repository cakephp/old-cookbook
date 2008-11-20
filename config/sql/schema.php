<?php 
/* SVN FILE: $Id: schema.php 673 2008-10-06 14:05:17Z AD7six $ */
/* Cookbook schema generated on: 2008-07-28 20:07:15 : 1217271015*/
class CookbookSchema extends CakeSchema {
	var $name = 'Cookbook';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $attachments = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
			'class' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 30, 'key' => 'index'),
			'foreign_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 36),
			'filename' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'ext' => array('type'=>'string', 'null' => false, 'default' => 'gif', 'length' => 6),
			'dir' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'mimetype' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 30),
			'filesize' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'height' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'width' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'description' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100),
			'checksum' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 32),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idxfk_foreign' => array('column' => array('class', 'foreign_id'), 'unique' => 0))
		);
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
	var $nodes = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'lft' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'rght' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'parent_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'status' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'comment_level' => array('type'=>'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'edit_level' => array('type'=>'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'show_in_toc' => array('type'=>'boolean', 'null' => false, 'default' => '1'),
			'depth' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'sequence' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 20),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'LFT_RGHT' => array('column' => array('lft', 'rght'), 'unique' => 0), 'RGHT_LFT' => array('column' => array('lft', 'rght', 'rght', 'lft'), 'unique' => 0))
		);
	var $revisions = array(
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
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'node_id' => array('column' => array('node_id', 'lang', 'status'), 'unique' => 0))
		);
}
?>