<!DOCTYPE html>
<html>
<head>
<?php echo $layout->getSlot('meta')?>
<?php echo $layout->getSlot('css')?>
</head>
<body>
<?php echo $layout->getSlot('navigation')?>

<?php echo $layout->getSlot('main')?>

<?php echo $layout->getSlot('javascript')?>
</body>
</html>