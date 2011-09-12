    <?php if ($element->label && (!isset($label) || $label !== false)): ?> 
    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <?php endif; ?>
    <select
        <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
        multiple="multiple"
        <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
        <?php if ($element->required): ?> required<?php endif; ?>
        name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>[]"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>">
            <?php foreach ($element->options as $option => $value): ?>
                <?php if (is_array($value)): ?>
                    <optgroup label="<?php echo htmlspecialchars($option) ?>">
                         <?php foreach ($value as $o => $v): ?>
                                <option value="<?php echo htmlspecialchars($o) ?>"
                                    <?php if (is_array($element->value) && in_array($o, $element->value)): ?>selected="selected"<?php endif; ?>><?php echo htmlspecialchars($v) ?></option>                 
                        <?php endforeach; ?>
                    </optgroup>
                <?php else: ?>
                    <option value="<?php echo htmlspecialchars($option) ?>"
                        <?php if (is_array($element->value) && in_array($option, $element->value)): ?>selected="selected"<?php endif; ?>><?php
                        echo htmlspecialchars($value)
                    ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
    </select>
