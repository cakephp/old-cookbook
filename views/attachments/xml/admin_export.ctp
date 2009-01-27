<?php
$uuid = String::uuid();
$from = array_pop(explode('-', $uuid));
$content = $xml->serialize(array('meta' => array('from' => $from, 'on' => date('Y-m-d H:i'))), array('format' => 'tags'));
foreach ($data as $row) {
	extract($row);
	extract($Attachment);
	$Attachment['source'] = base64_encode(file_get_contents(APP . 'uploads' . DS  . $dir . DS . $filename));
	$content .= $xml->serialize(array('Attachment' => $Attachment), array('format' => 'tags'));
}
echo $xml->elem('contents', null, $content);
?>