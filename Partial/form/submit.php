    <input type="submit"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->label) ?>" />
