    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <select
        name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>">
            <?php foreach ($element->options as $option => $value): ?>
                    <option value="<?php echo htmlspecialchars($option) ?>"
                        <?php if ($element->value == $option): ?> selected="selected"
                        <?php endif; ?>><?php echo htmlspecialchars($value) ?></option>
            <?php endforeach; ?>
    </select>
