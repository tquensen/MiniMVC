<ul id="<?php echo htmlspecialchars($form->getName())?>__errors" class="formErrors">
 <?php foreach ($elements as $currentElement): ?>
    <?php if (!$currentElement->isValid()):?>
        <li><?php echo $currentElement->label ? htmlspecialchars($currentElement->label) . ': ' : ''; ?><span class="formError"><?php echo htmlspecialchars($currentElement->errorMessage)?></span>
    <?php endif; ?>
<?php endforeach; ?>
</ul>