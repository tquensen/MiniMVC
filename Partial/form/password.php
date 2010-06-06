    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <input type="password"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           value="" />
