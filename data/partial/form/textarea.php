    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <textarea
        rows="5" cols="30"
        name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
        id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->value) ?></textarea>
