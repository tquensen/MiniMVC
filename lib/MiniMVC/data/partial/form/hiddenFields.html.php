<?php if (strtoupper($form->getOption('method')) != 'GET' && strtoupper($form->getOption('method')) != 'POST'): ?>
        <input type="hidden" name="REQUEST_METHOD" id="<?php echo htmlspecialchars($form->getName() . '__REQUEST_METHOD') ?>" value="<?php echo htmlspecialchars(strtoupper($form->getOption('method'))) ?>" />
<?php endif; ?>
<?php if ($formToken = $form->getFormToken()): ?>
        <input type="hidden" name="form_token" id="<?php echo htmlspecialchars($form->getName() . '___form_token') ?>" value="<?php echo htmlspecialchars($formToken) ?>" />
<?php endif; ?>
<?php foreach ($form->getElements() as $element): ?>
    <?php if ($element->getType() != 'hidden') { continue; }; ?>
    <input type="hidden"
           <?php if ($element->attributes): foreach ((array) $element->attributes as $attr => $attrValue): ?> <?php echo ' '.$attr.'="'.$attrValue.'"'; ?><?php endforeach; endif; ?>
           <?php if ($element->class): ?> class="<?php echo $element->class; ?>"<?php endif; ?>
           name="<?php echo $element->forceName ? htmlspecialchars($element->forceName) : htmlspecialchars($element->getForm()->getName() . '[' . $element->getName() . ']') ?>"
           id="<?php echo htmlspecialchars($element->getForm()->getName() . '__' . $element->getName()) ?>"
           value="<?php echo htmlspecialchars($element->alwaysDisplayDefault ? $element->defaultValue : $element->value) ?>" />
<?php endforeach; ?>