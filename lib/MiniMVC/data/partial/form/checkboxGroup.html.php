    <?php if ($element->label && (!isset($label) || $label !== false)): ?> 
    <label><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <?php endif; ?>
    <ul>
    <?php foreach ($element->elements as $check => $value): ?>
        <?php if (is_array($value)): ?>
        <li>
            <strong><?php echo htmlspecialchars($check) ?></strong>
            <ul>
                 <?php foreach ($value as $c => $v): ?>
                <li>
                    <input type="checkbox"
                           <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
                           <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
                           name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>[]"
                           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $c) ?>"
                           value="<?php echo htmlspecialchars($c) ?>"
                           <?php if (is_array($element->value) && in_array($c, $element->value)): ?>checked<?php endif; ?> />
                    <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $c) ?>"><?php echo htmlspecialchars($v) ?></label>
                </li>              
                <?php endforeach; ?>
            </ul>
        </li>
        <?php else: ?>
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
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
