    <button <?php if ($element->getOption('type')): ?>type="<?php echo $element->getOption('type'); ?>"<?php endif; ?>
           <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
           <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
           name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></button>
