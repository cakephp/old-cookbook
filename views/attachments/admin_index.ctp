<h2>All Attachments</h2>
<table>
<?php
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action); // temp
$paginator->options(array('url' => $pass));
$th = array(
	$paginator->sort('id'),
	$paginator->sort('pic'),
	$paginator->sort('size', 'filesize'),
	$paginator->sort('checksum'),
	'associated',
);
echo $html->tableHeaders($th);
foreach ($data as $row) {
	if (isset($row[$class])) {
		$other = $row['Attachment']['class'];
		$otherController = Inflector::pluralize($other);
		if (isset($row[$other]['id'])) {
			$associated = $html->link($row[$other]['display_field'],array('admin' => false, 'controller' => low($otherController), 'action' => 'view', $row[$other]['id']));
		} else {
			$associated = 'none';
		}
	} else {
		$associated = 'none';
	}
	if ($width && $height) {
		$size = $width . '&nbsp;x&nbsp;' . $height;
	} else {
		$size = $number->toReadableSize($filesize);
	}
	$thumb = $html->image('/img' . $versions['thumb'], array('alt' => 'filename: ' . $filename . ' ' . $description));
	$tr = array (
		$html->link($id, array('action' => 'view', $id)),
		$html->link($thumb, array('action' => 'view', $id), null, null, false),
		$size,
		$checksum,
		$associated,
	);
	echo $html->tableCells($tr);
}
?>
</table>
<?php echo $this->element('paging'); ?>