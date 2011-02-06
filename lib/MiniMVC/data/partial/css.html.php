<?php foreach ($files as $file): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $file['url']?>" media="<?php echo $file['media']?>" />
<?php endforeach; ?>