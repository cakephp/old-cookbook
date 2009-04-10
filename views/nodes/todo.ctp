<div class="nodes view">
<div class="summary">
<?php
$i18n = I18n::getInstance();
$translatorHelp = $html->link(__('Please help out by updating the translation file', true), array('action' => 'view', 817));
if (file_exists(APP . 'locale' . DS . $i18n->l10n->locale . DS . 'LC_MESSAGES' . DS . 'default.po')) {
	echo '<p class="note">' . sprintf(__('Want to check the fixed texts used on the site for %1$s? %2$s', true),
		$i18n->l10n->language, $translatorHelp);
} else {
	echo '<p class="warning">' . sprintf(__('We don\'t have %1$s translations for the fixed texts used on the site. %2$s', true),
		$i18n->l10n->language, $translatorHelp);
}
echo '</p><p>' . __('These sections either do not have a translation, or the English text has changed since it was translated', true) . '</p>';
?>
</div>
<?php
foreach ($data as $id => $row) {
	$sequence = $row['Node']['sequence'];
	$sequence = $sequence?$sequence:'#';
	echo "<h2 id=\"{$row['Revision']['slug']}-{$row['Node']['id']}\">" .
		$html->link($sequence, array('action' => 'view', $row['Node']['id'], $row['Revision']['slug'])) . ' ' . h($row['Revision']['title']) . "</h2>";

	echo '<div class="options">';
		echo $this->element('node_options', array('data' => $row));
	echo '</div>';
}
?>
</div>
<?php echo $this->element('paging');