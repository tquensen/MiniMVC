    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?><?php if ($element->required && $element->getForm()->getOption('requiredMark')): ?><?php echo '<span class="requiredMark">'.htmlspecialchars($element->getForm()->getOption('requiredMark')).'</span>'; ?><?php endif; ?></label>
    <textarea
        rows="5" cols="30"
        name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->value) ?></textarea>