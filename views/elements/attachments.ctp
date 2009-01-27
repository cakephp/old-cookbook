<div id='thumbs' class='clearfix'><p><?php __('Images/Files associated with this content') ?></p><?php
$out = array();
if (isset($attachments)) {
	foreach ($attachments as $row) {
		extract ($row['Attachment']);
		extract ($versions);
		if (isset($large)) {
			$path = '/img/' . $large;
		} else {
			$path = '/files/' . $dir . '/' . $filename;
		}
		$thumb = $html->image($thumb);
		$div = '<div>';
		$div .= $html->link($thumb, $path, array('style' => 'float:left;min-height:50px;max-width:50px'), null, false);
		$div .= '<p style="margin-left:55px">&lt;img src="' . $html->url($path) . '" alt="' . $description . '" /&gt;';
		$div .= "<br />&lt;p class='caption'&gt;$description&lt;/p&gt;";
		$div .= '</div>';
		$out [] = $div;
	}
}
if ($out) {
	echo '<p>' . implode($out, '</p><p>') . '</p>';
} else {
	echo '<p>None</p>';
}
?>
</div>