    <label><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <ul>
    <?php foreach ($element->elements as $radio => $value): ?>
        <?php if (is_array($value)): ?>
        <li>
            <strong><?php echo htmlspecialchars($radio) ?></strong>
            <ul>
                <?php foreach ($value as $r => $v): ?>
                <input type="radio"
                       <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
                       <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
                       name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
                       id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $r) ?>"
                       value="<?php echo htmlspecialchars($r) ?>"
                       <?php if ($element->value == $r): ?>checked="checked"<?php endif; ?> />
                <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $r) ?>"><?php echo htmlspecialchars($v) ?></label>              
                <?php endforeach; ?>
            </ul>
        </li>
        <?php else: ?>
        <li>
            <input type="radio"
                   <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
                   <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
                   name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
                   id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $radio) ?>"
                   value="<?php echo htmlspecialchars($radio) ?>"
                   <?php if ($element->value == $radio): ?>checked="checked"<?php endif; ?> />
            <label for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName() . '__' . $radio) ?>"><?php echo htmlspecialchars($value) ?></label>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
