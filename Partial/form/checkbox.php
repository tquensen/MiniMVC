    <input type="checkbox"
           name="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"
           <?php if ($element->value): ?> checked="checked" <?php endif; ?> /> <label
                      for="<?php echo htmlspecialchars($element->getForm()->getName() . '_' . $element->getName()) ?>"><?php echo htmlspecialchars($element->label) ?></label>