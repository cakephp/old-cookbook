<h2>Image</h2>
<table>
<?php
	echo $html->tableCells(array('id',$data['Image']['id']));
	echo $html->tableCells(array('User',$data['User']?$html->link($data['User']['first_name'], array('controller' => 'users', 'action' => 'view', $data['Image']['user_id'])):''));
	echo $html->tableCells(array('class',$data['Image']['class']));
	echo $html->tableCells(array('foreign_id',$data['Image']['foreign_id']));
	echo $html->tableCells(array('original',$data['Image']['original']));
	echo $html->tableCells(array('filename',$data['Image']['filename']));
	echo $html->tableCells(array('dir',$data['Image']['dir']));
	echo $html->tableCells(array('mimetype',$data['Image']['mimetype']));
	echo $html->tableCells(array('filesize',$data['Image']['filesize']));
	echo $html->tableCells(array('height',$data['Image']['height']));
	echo $html->tableCells(array('width',$data['Image']['width']));
	echo $html->tableCells(array('thumb',$data['Image']['thumb']));
	echo $html->tableCells(array('description',$data['Image']['description']));
	echo $html->tableCells(array('slug',$data['Image']['slug']));
	echo $html->tableCells(array('status',$data['Image']['status']));
	echo $html->tableCells(array('created',$data['Image']['created']));
	echo $html->tableCells(array('modified',$data['Image']['modified']));
?>
</table>