<?php foreach ($messages as $type => $typeMessages): ?>
    <?php if (count($typeMessages)): ?>
    <ul class="messages <?php echo $type; ?>Messages">
        <?php foreach ($typeMessages as $message): ?>
        <li><?php echo htmlspecialchars($message); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
<?php endforeach; ?>