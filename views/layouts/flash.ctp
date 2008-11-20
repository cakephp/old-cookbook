<?php /* SVN FILE: $Id: flash.ctp 30 2007-01-14 03:20:46Z phpnut $ */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $page_title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php if(Configure::read() == 0) { ?>
<meta http-equiv="Refresh" content="<?php echo $pause?>;url=<?php echo $url?>"/>
<?php } ?>
<style><!--
P { text-align:center; font:bold 1.1em sans-serif }
A { color:#444; text-decoration:none }
A:HOVER { text-decoration: underline; color:#44E }
--></style>
</head>

<body>

<p><a href="<?php echo $url?>"><?php echo $message?></a></p>

</body>
</html>