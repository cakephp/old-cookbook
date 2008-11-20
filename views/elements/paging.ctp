<?php /* SVN FILE: $Id: paging.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<div class="paging">
<?php
echo '<p>' . sprintf(__('Page %s', true), $paginator->counter()) .'</p>';
echo $paginator->prev(__('<< previous', true), array(), null, array('class'=>'disabled'));
$numbers = $paginator->numbers();
echo $numbers ? ' | ' . $numbers . ' | ' : ' | ';
echo $paginator->next(__('Next >>', true), array(), null, array('class'=>'disabled'));
?>
</div>