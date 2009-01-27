<h2>Image</h2>
<table>
<?php
	extract($data);
	echo $html->tableCells(array('id',$Image['id']));
	echo $html->tableCells(array('User',$User?$html->link($User['first_name'], array('controller' => 'users', 'action' => 'view', $Image['user_id'])):''));
	echo $html->tableCells(array('class',$Image['class']));
	echo $html->tableCells(array('foreign_id',$Image['foreign_id']));
	echo $html->tableCells(array('original',$Image['original']));
	echo $html->tableCells(array('filename',$Image['filename']));
	echo $html->tableCells(array('dir',$Image['dir']));
	echo $html->tableCells(array('mimetype',$Image['mimetype']));
	echo $html->tableCells(array('filesize',$Image['filesize']));
	echo $html->tableCells(array('height',$Image['height']));
	echo $html->tableCells(array('width',$Image['width']));
	echo $html->tableCells(array('thumb',$Image['thumb']));
	echo $html->tableCells(array('description',$Image['description']));
	echo $html->tableCells(array('slug',$Image['slug']));
	echo $html->tableCells(array('status',$Image['status']));
	echo $html->tableCells(array('created',$Image['created']));
	echo $html->tableCells(array('modified',$Image['modified']));
?>
</table>