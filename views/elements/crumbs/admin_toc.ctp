<?php /* SVN FILE: $Id: admin_toc.ctp 600 2008-08-07 17:55:23Z AD7six $ */
if (isset($crumbPath)&&($crumbPath)) {
	foreach($crumbPath as $linkInfo) {
		$html->addCrumb($linkInfo['Revision']['title'],array($linkInfo['Node']['id']));
	}
	echo $html->getCrumbs();
}
?>