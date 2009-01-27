<div>
<h2>Properties for <?php echo $this->data['Revision']['title']; ?></h2>
<?php
$parent = $currentPath[count($currentPath) - 2]['Revision']['title'];
$auth = array(
	ADMIN => 'Admin',
	EDITOR => 'Editor',
	MODERATOR => 'Moderator',
	COMMENTER => 'Commenter',
	READ => 'Read (to allow the link to the action to be publicly visible)'
);
echo $this->element('preview');
echo $form->create(null, array('url' => '/' . $this->params['url']['url']));
echo $form->inputs(array (
	'Node.comment_level' => array('options' => $auth),
	'Node.edit_level' => array('options' => $auth),
	'Node.show_in_toc' => array('type' => 'checkbox', 'label' => 'Show this section in the TOC? Setting to no will prevent this section from having a
	sequence number and make this section (and any sub sections) always appear inline when viewing "' . $parent . '"'),
	'Node.show_subsections_inline' => array('type' => 'checkbox', 'label' => 'Show subsections inline? This will set all subsections to not appear in the TOC (No effect if left unchecked).'),
));
echo $form->submit('save');
echo $form->end();
?>
</div>