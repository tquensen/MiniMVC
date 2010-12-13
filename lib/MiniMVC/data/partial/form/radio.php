    <label><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <ul>
    <?php foreach ($element->elements as $radio => $value): ?>
        <li>
            <input type="radio"
                   name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
                   id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $radio) ?>"
                   value="<?php echo htmlspecialchars($radio) ?>"
                   <?php if ($element->value == $radio): ?>checked="checked"<?php endif; ?> />
            <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $radio) ?>"><?php echo htmlspecialchars($value) ?></label>
        </li>
    <?php endforeach; ?>
    </ul>
