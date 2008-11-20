<?php

class Lucene_Config {
	var $default = array(
		'index_file' => 'search_index',
		'Revision' => array(
			'find_options' => array('conditions' => array('Revision.status' => 'current'), 'order' => 'Revision.id ASC'),
			'fields' => array(
				'id' => array('alias' => 'cake_id','type' => 'Keyword'),
				'slug' => array('type' => 'UnIndexed'),
				'title' => array('type' => 'Text'),
                                'content' => array('type' => 'Text', 'prepare' => 'strip_tags'),
				'lang' => array('type' => 'Keyword'),
				'book' => array('type' => 'Keyword'),
				'collection' => array('type' => 'Keyword'),
				'node_id' => array('type' => 'Keyword'),
			),
		),
		'Comment' => array(
			'find_options' => array('conditions' => array('Comment.published' => 1), 'order' => 'Comment.id ASC'),
			'fields' => array(
				'id' => array('alias' => 'cake_id','type' => 'UnIndexed'),
				'title' => array('type' => 'Text'),
				'lang' => array('type' => 'Keyword'),
				'body' => array('type' => 'Text', 'alias' => 'content'),
			),
		)
	);
}
?>