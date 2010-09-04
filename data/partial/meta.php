<title><?php echo htmlspecialchars($title); ?></title>
<?php foreach ($meta as $current): ?>
    <?php if (!empty($current['name'])): ?>
    <meta name="<?php echo $current['name']; ?>" content="<?php echo htmlspecialchars($current['content']); ?>" />
    <?php elseif (!empty($current['http-equiv'])): ?>
    <meta http-equiv="<?php echo $current['http-equiv']; ?>" content="<?php echo htmlspecialchars($current['content']); ?>" />
    <?php endif; ?>
<?php endforeach; ?>