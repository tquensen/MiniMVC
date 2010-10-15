    <input type="hidden"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->alwaysDisplayDefault ? $element->defaultValue : $element->value) ?>" />
