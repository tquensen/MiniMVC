    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <select
        multiple="multiple" size="<?php echo htmlspecialchars($element->size ? $element->size : 8) ?>"
        name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>[]"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>">
            <?php foreach ($element->options as $option => $value): ?>
                    <option value="<?php echo htmlspecialchars($option) ?>"
                        <?php if (is_array($element->value) && in_array($option, $element->value)): ?>selected="selected"<?php endif; ?>><?php
                        echo htmlspecialchars($value)
                    ?></option>
            <?php endforeach; ?>
    </select>
