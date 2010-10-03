<ol>
 <?php foreach ($elements as $currentElement): ?>
    <?php if (!$currentElement->isValid()):?>
        <li><?php echo $currentElement->label ? htmlspecialchars($currentElement->label) . ': ' : ''; ?><span class="formError"><?php echo htmlspecialchars($currentElement->errorMessage)?></span>
    <?php endif; ?>
<?php endforeach; ?>
</ol>