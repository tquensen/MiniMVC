    <?php if ($element->label && (!isset($label) || $label !== false)): ?> 
    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <?php endif; ?>
    <input type="file"
           <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
           <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
           <?php if ($element->required): ?> required<?php endif; ?>
           name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           />
