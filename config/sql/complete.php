<?php
/* SVN FILE: $Id$ */
/* Cakebook schema generated on: 2008-11-21 11:11:33 : 1227265113*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $attachments = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
			'class' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 30, 'key' => 'index'),
			'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36),
			'filename' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'ext' => array('type' => 'string', 'null' => false, 'default' => 'gif', 'length' => 6),
			'dir' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'mimetype' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 30),
			'filesize' => array('type' => 'integer', 'null' => true, 'default' => NULL),
			'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
			'checksum' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idxfk_foreign' => array('column' => array('class', 'foreign_id'), 'unique' => 0))
		);
	var $changes = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'revision_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
			'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
			'author_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
			'status_from' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 10),
			'status_to' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 10),
			'comment' => array('type' => 'string', 'null' => false, 'default' => NULL),
			'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $comments = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'node_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
			'revision_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 10),
			'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 2),
			'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 150),
			'author' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'email' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'body' => array('type' => 'text', 'null' => true, 'default' => NULL),
			'published' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $groups = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'level_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $levels = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
			'value' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $nodes = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'comment_level' => array('type' => 'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'edit_level' => array('type' => 'integer', 'null' => false, 'default' => '200', 'length' => 4),
			'show_in_toc' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
			'depth' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'sequence' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 20),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'LFT_RGHT' => array('column' => array('lft', 'rght'), 'unique' => 0), 'RGHT_LFT' => array('column' => array('rght', 'lft'), 'unique' => 0))
		);
	var $profiles = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'key' => 'unique'),
			'published' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
			'location' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'interests' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'occupation' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'icq' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 20),
			'aim' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'yahoo' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'msnm' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'jabber' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'time_zone' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'birthday' => array('type' => 'date', 'null' => true, 'default' => NULL),
			'user_icon' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'signature' => array('type' => 'text', 'null' => true, 'default' => NULL),
			'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
			'bio' => array('type' => 'text', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'USER_ID_UNIQUE_INDEX' => array('column' => 'user_id', 'unique' => 1))
		);
	var $revisions = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'under_node_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'after_node_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'status' => array('type' => 'string', 'null' => false, 'default' => 'pending', 'length' => 30),
			'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
			'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3),
			'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
			'content' => array('type' => 'text', 'null' => true, 'default' => NULL),
			'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'reason' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 300),
			'flags' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'node_id' => array('column' => array('node_id', 'lang', 'status'), 'unique' => 0))
		);
	var $users = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'group_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'level_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'realname' => array('type' => 'string', 'null' => false),
			'username' => array('type' => 'string', 'null' => false, 'key' => 'unique'),
			'email' => array('type' => 'string', 'null' => false, 'key' => 'unique'),
			'psword' => array('type' => 'string', 'null' => false),
			'temppassword' => array('type' => 'string', 'null' => false),
			'tos' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
			'mail_comments' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
			'email_authenticated' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
			'email_token' => array('type' => 'string', 'null' => false, 'length' => 45),
			'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
			'display_name' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'USERNAME_UNIQUE_INDEX' => array('column' => 'username', 'unique' => 1), 'EMAIL_UNIQUE_INDEX' => array('column' => 'email', 'unique' => 1))
		);
}
?>