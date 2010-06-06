    <input type="hidden"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->getOption('alwaysDisplayDefault') ? $element->getOption('defaultValue') : $element->getOption('value')) ?>" />
