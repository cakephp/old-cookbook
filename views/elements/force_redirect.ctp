<p><?php echo $html->link('redirecting', $url, array('id' => 'redirect')); ?></p>
<script type="text/javascript">
$(document).ready(function() {
	window.location = "<?php echo $html->url($url) ?>";
});
</script>