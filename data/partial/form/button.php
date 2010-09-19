    <button <?php if ($element->getOption('type')): ?>type="<?php echo $element->getOption('type'); ?>"<?php endif; ?>
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></button>
