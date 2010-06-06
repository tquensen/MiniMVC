    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <input type="text"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->value) ?>" />
