<?php /* SVN FILE: $Id: toc.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<div class="toc">
<?php
echo '<h2>' . $data[0]['Revision']['title'] . '</h2>';
echo '<h3>' . __('Table of Contents', true) . '</h3><div class="tree">';
$selected = array();
if (isset($currentPath)) {
	$currentNode = array_pop($currentPath);
	$selected = array($currentNode['Node']['lft'], $currentNode['Node']['rght'], 'active', 'selected');
}
echo $tree->generate($data, array ('element' => 'toc/public_item', 'class' => 'tree', 'model' => 'Node', 'autoPath' => $selected));?>
</div></div>