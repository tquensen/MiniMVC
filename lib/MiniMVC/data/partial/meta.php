<title><?php echo htmlspecialchars($title); ?></title>
<?php foreach ($metas as $current): ?>
    <?php if (!empty($current['name'])): ?>
    <meta name="<?php echo $current['name']; ?>" content="<?php echo htmlspecialchars($current['content']); ?>" />
    <?php elseif (!empty($current['http-equiv'])): ?>
    <meta http-equiv="<?php echo $current['http-equiv']; ?>" content="<?php echo htmlspecialchars($current['content']); ?>" />
    <?php endif; ?>
<?php endforeach; ?>
<?php foreach ($links as $current): ?>
    <link
    <?php if (!empty($current['rel'])): ?>
        rel="<?php echo htmlspecialchars($current['rel']); ?>"
    <?php endif; ?>
    <?php if (!empty($current['title'])): ?>
        title="<?php echo htmlspecialchars($current['title']); ?>"
    <?php endif; ?>
    <?php if (!empty($current['type'])): ?>
        type="<?php echo htmlspecialchars($current['type']); ?>"
    <?php endif; ?>
    <?php if (!empty($current['href'])): ?>
        href="<?php echo htmlspecialchars($current['href']); ?>"
    <?php endif; ?>
    />
<?php endforeach; ?>