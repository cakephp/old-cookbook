<h2>Comment</h2>
<table>
<?php
	echo $html->tableCells(array('id',$data['Comment']['id']));
	echo $html->tableCells(array('Node', $data['Node']?$html->link($data['Node']['sequence'], array('controller' => 'nodes', 'action' => 'view', $data['Comment']['node_id'])):''));
	echo $html->tableCells(array('User', $data['User']?$html->link($data['User']['username'], array('controller' => 'users', 'action' => 'view', $data['Comment']['user_id'])):''));
	echo $html->tableCells(array('class',$data['Comment']['class']));
	echo $html->tableCells(array('lang',$data['Comment']['lang']));
	echo $html->tableCells(array('title',$data['Comment']['title']));
	echo $html->tableCells(array('author',$data['Comment']['author']));
	echo $html->tableCells(array('email',$data['Comment']['email']));
	echo $html->tableCells(array('url',$data['Comment']['url']));
	echo $html->tableCells(array('body',$data['Comment']['body']));
	echo $html->tableCells(array('published',$data['Comment']['published']));
	echo $html->tableCells(array('created',$data['Comment']['created']));
	echo $html->tableCells(array('modified',$data['Comment']['modified']));
?>
</table>