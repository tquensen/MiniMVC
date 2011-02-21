<ul id="<?php echo htmlspecialchars($form->getName())?>__errors" class="formErrors">
    <?php foreach ($form->getErrors() as $error): ?>
        <li><?php if ($error['label']): ?><span><?php $o->esc($error['label']) ?>:</span> <?php endif; ?><?php $o->esc($error['message']) ?></li>
    <?php endforeach; ?>
</ul>