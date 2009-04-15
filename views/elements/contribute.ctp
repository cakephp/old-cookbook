<?php
$translatorHelp = $html->link(__('more informations about translations', true), array('action' => 'view', 818));
$data['Revision']['flags'] .= ';foo';
if (!$data['Revision']['id']) :
	echo '<p class="contribute">';
	echo sprintf(__('There is no translation yet for this section. Please help out and %1$s.', true),
		$html->link(
			__('translate this' , true),
			array('action'=>'edit',$data['Node']['id'], $data['Revision']['slug']),
			array('title' => __('There is no translation for this section please submit one', true))
		)
	);
	echo sprintf(__(' For more information see %1$s', true), $translatorHelp);
	echo '</p>';
elseif (strpos($data['Revision']['flags'], 'englishChanged') !== false) : ?>
<div class="contribute">
	<p><?php echo sprintf(__('The original text for this section has changed since it was translated. Please help resolve this difference. You can:', true)); ?></p>
	<ul>
		<li><?php
			if (!empty($data['Revision']['based_on_id'])) {
				$url = array('controller' => 'revision', 'action' => 'view', $data['Revision']['based_on_id']);
			} else {
				$url = array('controller' => 'nodes', 'action' => 'redirect_to_revision', $data['Node']['id']);
			}
			echo $html->link(__('See what has changed', true), $url);
		?></li>
		<li><?php
			$url = array('controller' => 'nodes', 'action' => 'compare', $data['Node']['id'], $data['Revision']['slug']);
			echo $html->link(__('Compare the current text to the original', true), $url)
		?></li>
	</ul>
	<p><?php echo $translatorHelp; ?></p>
</div>
<?php endif; ?>