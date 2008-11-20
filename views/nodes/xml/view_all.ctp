<?php /* SVN FILE: $Id: view_all.ctp 607 2008-08-19 15:36:49Z AD7six $ */
$uuid = String::uuid();
$key = array_pop(explode('-', $uuid));
$content = $xml->serialize(array('meta' => array(
	'from' => env('HTTP_HOST'),
	'uuid_key' => $key,
	'on' => date('Y-m-d H:i'),
	'lang' => $this->params['lang'])), array('format' => 'tags'));
foreach ($data as $row) {
	if ($this->webroot != '/') {
		$row['Revision']['content'] = preg_replace('@(href|src)=(\'|")' . $this->webroot . '@', '\\1=\\2/', $row['Revision']['content']);
	}
	$content .= $xml->serialize($row, array('format' => 'tags'));
}
echo $xml->elem('contents', null, $content);
?>