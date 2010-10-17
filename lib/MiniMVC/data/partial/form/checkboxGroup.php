    <label><?php echo htmlspecialchars($element->label) ?></label>
    <ul>
    <?php foreach ($element->elements as $check => $value): ?>
        <li>
            <input type="checkbox"
                   name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>[]"
                   id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $check) ?>"
                   value="<?php echo htmlspecialchars($check) ?>"
                   <?php if (is_array($element->value) && in_array($check, $element->value)): ?>checked="checked"<?php endif; ?> />
            <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $check) ?>"><?php echo htmlspecialchars($value) ?></label>
        </li>
    <?php endforeach; ?>
    </ul>
