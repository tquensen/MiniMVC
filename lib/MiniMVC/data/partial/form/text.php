    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?><?php if ($element->required): ?><?php echo htmlspecialchars($element->getForm()->getOption('requiredMark')); ?><?php endif; ?></label>
    <input type="text"
           name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->value) ?>" />
