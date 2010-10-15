    <input type="submit"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName().']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->label) ?>" />
