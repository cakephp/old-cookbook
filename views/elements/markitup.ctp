<?php /* SVN FILE: $Id: markitup.ctp 673 2008-10-06 14:05:17Z AD7six $ */
$javascript->link('markitup/jquery.markitup.pack.js', false);
$javascript->link('markitup/sets/default/set.js', false);
$html->css(array('/js/markitup/skins/markitup/style.css', '/js/markitup/sets/default/style.css'), null, array(), false);
?>
<script type="text/javascript" >
   $(document).ready(function() {
	   $("<?php echo $process?>").markItUp(mySettings);
   });
</script>