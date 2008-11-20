<?php /* SVN FILE: $Id: crumbs.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<div class="crumbs">
<?php
if ($this->name == 'Revisions') {
	echo $this->element('crumbs/revisions');
} elseif ($this->action == 'admin_toc') {
	echo $this->element('crumbs/admin_toc');
//} elseif ($this->action == 'toc') {
//	echo $this->element('crumbs/toc');
} else {
	echo $this->element('crumbs/nodes');
}
?>
</div>