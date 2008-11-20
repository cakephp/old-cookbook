<?php /* SVN FILE: $Id: admin_view.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<h2>Comment</h2>
<table>
<?php
	extract($data);
	echo $html->tableCells(array('id',$Comment['id']));
	echo $html->tableCells(array('Node', $Node?$html->link($Node['sequence'], array('controller' => 'nodes', 'action' => 'view', $Comment['node_id'])):''));
	echo $html->tableCells(array('User', $User?$html->link($User['username'], array('controller' => 'users', 'action' => 'view', $Comment['user_id'])):''));
	echo $html->tableCells(array('class',$Comment['class']));
	echo $html->tableCells(array('lang',$Comment['lang']));
	echo $html->tableCells(array('title',$Comment['title']));
	echo $html->tableCells(array('author',$Comment['author']));
	echo $html->tableCells(array('email',$Comment['email']));
	echo $html->tableCells(array('url',$Comment['url']));
	echo $html->tableCells(array('body',$Comment['body']));
	echo $html->tableCells(array('published',$Comment['published']));
	echo $html->tableCells(array('created',$Comment['created']));
	echo $html->tableCells(array('modified',$Comment['modified']));
?>
</table>