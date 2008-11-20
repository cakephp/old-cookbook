<?php /* SVN FILE: $Id: admin_toc.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<h2><?php echo $data[0]['Revision']['title']; ?> Table of Contents</h2>
<div class="view">
<?php echo $tree->generate($data, array ('element' => 'toc/admin_item', 'class' => 'tree')); ?>
</div>