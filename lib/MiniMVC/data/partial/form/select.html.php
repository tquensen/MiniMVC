    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <select
        name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>">
            <?php foreach ($element->options as $option => $value): ?>
                    <option value="<?php echo htmlspecialchars($option) ?>"
                        <?php if ($element->value == $option): ?> selected="selected"
                        <?php endif; ?>><?php echo htmlspecialchars($value) ?></option>
            <?php endforeach; ?>
    </select>
