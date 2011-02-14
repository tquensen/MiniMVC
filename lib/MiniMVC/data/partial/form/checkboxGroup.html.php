    <label><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <ul>
    <?php foreach ($element->elements as $check => $value): ?>
        <li>
            <input type="checkbox"
                   <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
                   <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
                   name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>[]"
                   id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $check) ?>"
                   value="<?php echo htmlspecialchars($check) ?>"
                   <?php if (is_array($element->value) && in_array($check, $element->value)): ?>checked<?php endif; ?> />
            <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $check) ?>"><?php echo htmlspecialchars($value) ?></label>
        </li>
    <?php endforeach; ?>
    </ul>
