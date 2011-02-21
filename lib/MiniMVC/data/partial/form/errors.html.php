<ul id="<?php echo htmlspecialchars($form->getName())?>__errors" class="formErrors">
    <?php foreach ($form->getErrors() as $error): ?>
        <li>
            <?php if ($error['element']): ?>
                <label for="<?php echo htmlspecialchars($form->getName() . '__' . $form->getElement($error['element'])->getName()) ?>">
                    <span><?php $o->esc($form->getElement($error['element'])->label) ?>:</span>
                    <?php $o->esc($error['message']) ?>
                </label>
            <?php else: ?>
                <?php $o->esc($error['message']) ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>