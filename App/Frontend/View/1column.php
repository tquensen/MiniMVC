<!DOCTYPE html>
<html>
<head>
<?php echo $this->getSlot('meta')?>
<?php echo $this->getSlot('css')?>
</head>
<body>
<?php echo $this->getSlot('navigation')?>

<?php echo $this->getSlot('main')?>

<?php echo $this->getSlot('javascript')?>
</body>
</html>