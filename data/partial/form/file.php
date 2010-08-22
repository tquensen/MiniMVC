    <label
        for="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>
    <input type="file"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           />
