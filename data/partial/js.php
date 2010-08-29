<script>
    var minimvc = minimvc || {};
    <?php foreach ($vars as $key => $var): ?>
    minimvc.<?php echo $key; ?> = <?php echo $var; ?>;
    <?php endforeach; ?>
</script>
<?php foreach ($files as $file): ?>
    <script src="<?php echo $file['url']?>"></script>
<?php endforeach; ?>
<?php if (count($inlineFiles)): ?>
    <script>
<?php foreach ($inlineFiles as $code): ?>
     <?php echo $file['url']."\n"?>
<?php endforeach; ?>
    </script>
<?php endif; ?>