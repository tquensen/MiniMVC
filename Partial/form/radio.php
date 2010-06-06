    <label><?php echo htmlspecialchars($element->label) ?></label>
    <?php foreach ($element->elements as $radio => $value): ?>
        <span>
            <input type="radio"
                   name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
                   id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName() . '_' . $radio) ?>"
                   value="<?php echo htmlspecialchars($radio) ?>"
                   <?php if ($element->value == $radio): ?>checked="checked"<?php endif; ?> />
            <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName() . '_' . $radio) ?>"><?php echo htmlspecialchars($value) ?></label>
        </span>
    <?php endforeach; ?>
