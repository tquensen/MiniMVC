<ul id="<?php echo htmlspecialchars($form->getName())?>__errors" class="formErrors">
 <?php foreach ($form->getErrors() as $error): ?>
        <li><?php echo htmlspecialchars($error)?></li>
<?php endforeach; ?>
</ul>