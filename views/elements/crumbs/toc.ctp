<?php /* SVN FILE: $Id: toc.ctp 600 2008-08-07 17:55:23Z AD7six $ */
if (isset($crumbPath)&&($crumbPath)) {
	//$html->addCrumb('Collection Index',array('action'=>'index'));
	foreach($crumbPath as $linkInfo) {
			if ($linkInfo['Node']['depth'] <= $viewAllLevel) {
				$html->addCrumb($linkInfo['Revision']['title'],array('action'=>'view',$linkInfo['Node']['id'],$linkInfo['Revision']['slug']));
				$lastNode = $linkInfo;
			} else {
				$html->addCrumb($linkInfo['Revision']['title'],array('action'=>'view',$lastNode['Node']['id'],$lastNode['Revision']['slug'], '#' => $linkInfo['Revision']['slug']));
			}
	}
	echo $html->getCrumbs();
}
?>