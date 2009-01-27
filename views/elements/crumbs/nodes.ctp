<?php
if (isset($crumbPath) && count($crumbPath) > 1) {
	//$html->addCrumb('Collection Index',array('action'=>'index'));
	if (isset($this->params['admin']) && $this->params['admin']) {
		foreach($crumbPath as $linkInfo) {
			$html->addCrumb($linkInfo['Revision']['title'],array($linkInfo['Node']['id'],$linkInfo['Revision']['slug']));
		}
	} else {
		foreach($crumbPath as $linkInfo) {
			$html->addCrumb($linkInfo['Revision']['title'],array($linkInfo['Node']['id'],$linkInfo['Revision']['slug']));
		}
	}
	echo $html->getCrumbs();
}
?>