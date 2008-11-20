<?php /* SVN FILE: $Id: revisions.ctp 600 2008-08-07 17:55:23Z AD7six $ */
if (isset($crumbPath)&&($crumbPath)) {
	foreach($crumbPath as $linkInfo) {
		$html->addCrumb($linkInfo['Revision']['title'],array('action'=>'view',$linkInfo['Node']['id']));
	}
	echo $html->getCrumbs();
}
?>